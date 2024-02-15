<?php

namespace backend\services;

use common\models\LoginForm;
use common\models\User;

class AuthService
{
    function login(){
        $model = new LoginForm();
        $model->password = \yii::$app->request->post('password');
        $model->username = \yii::$app->request->post('username');
        if ($model->validate() && $model->login()) {
            $user = \yii::$app->user->identity;

            $token = $this->generateJwt($user);


            $refreshToken = $this->generateRefreshToken($user);

            return [
                'token' => (string) $token,
                'refreshToken' => (string) $refreshToken->urf_token,
            ];
        }
        \yii::$app->response->statusCode = 422;
        return $model->getErrors();
    }

    function logout($user){
        \yii::$app->user->logout();
        $user->userRefreshToken->delete();
    }

    private function generateJwt(User $user) {
        $jwt = \yii::$app->jwt;
        $signer = $jwt->getSigner('HS256');
        $key = $jwt->getKey();
        $time = time();

        $jwtParams = \yii::$app->params['jwt'];

        return $jwt->getBuilder()
            ->issuedBy($jwtParams['issuer'])
            ->permittedFor($jwtParams['audience'])
            ->identifiedBy($jwtParams['id'], true)
            ->issuedAt($time)
            ->expiresAt($time + $jwtParams['expire'])
            ->withClaim('uid', $user->id)
            ->getToken($signer, $key);
    }

    private function generateRefreshToken(User $user, User $impersonator = null): \app\models\UserRefreshTokens {
        $refreshToken = \yii::$app->security->generateRandomString(200);

        $userRefreshToken = new \app\models\UserRefreshTokens([
            'urf_userID' => $user->id,
            'urf_token' => $refreshToken,
            'urf_ip' => \yii::$app->request->userIP,
            'urf_user_agent' => \yii::$app->request->userAgent,
            'urf_created' => gmdate('Y-m-d H:i:s'),
        ]);
        if (!$userRefreshToken->save()) {
            throw new \yii\web\ServerErrorHttpException('Failed to save the refresh token: '. $userRefreshToken->getErrorSummary(true));
        }

        // Send the refresh-token to the user in a HttpOnly cookie that Javascript can never read and that's limited by path
        \yii::$app->response->cookies->add(new \yii\web\Cookie([
            'name' => 'refresh-token',
            'value' => $refreshToken,
            'httpOnly' => true,
            'sameSite' => 'none',
            'secure' => true,
            'path' => '/v1/authService/refresh-token',  //endpoint URI for renewing the JWT token using this refresh-token, or deleting refresh-token
        ]));

        return $userRefreshToken;
    }
}