<?php

namespace backend\controllers\api;

use app\models\UserRefreshTokens;
use common\models\LoginForm;
use common\models\User;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\rest\Controller;

class AuthController extends ApiController
{
    public $enableCsrfValidation = false;

    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'actions' => ['login', 'error'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['logout', 'index'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                ],

            ],
        ]);
    }

    public function actionLogin()
    {


        $model = new LoginForm();
        $model->password = Yii::$app->request->post('password');
        $model->username = Yii::$app->request->post('username');
        $bruh = $model->validate();
        if ($model->validate() && $model->login()) {
            $user = Yii::$app->user->identity;

            $token = $this->generateJwt($user);


            $refreshToken = $this->generateRefreshToken($user);

            return [
                'token' => (string) $token,
                'refreshToken' => (string) $refreshToken->urf_token,
            ];
        }
        Yii::$app->response->statusCode = 422;
        return $model->getErrors();
    }
    public function actionLogout()
    {
        $user = $this->getUser();
        $user->userRefreshToken->delete();
    }

    private function generateJwt(User $user) {
        $jwt = Yii::$app->jwt;
        $signer = $jwt->getSigner('HS256');
        $key = $jwt->getKey();
        $time = time();

        $jwtParams = Yii::$app->params['jwt'];

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
        $refreshToken = Yii::$app->security->generateRandomString(200);

        $userRefreshToken = new \app\models\UserRefreshTokens([
            'urf_userID' => $user->id,
            'urf_token' => $refreshToken,
            'urf_ip' => Yii::$app->request->userIP,
            'urf_user_agent' => Yii::$app->request->userAgent,
            'urf_created' => gmdate('Y-m-d H:i:s'),
        ]);
        if (!$userRefreshToken->save()) {
            throw new \yii\web\ServerErrorHttpException('Failed to save the refresh token: '. $userRefreshToken->getErrorSummary(true));
        }

        // Send the refresh-token to the user in a HttpOnly cookie that Javascript can never read and that's limited by path
        Yii::$app->response->cookies->add(new \yii\web\Cookie([
            'name' => 'refresh-token',
            'value' => $refreshToken,
            'httpOnly' => true,
            'sameSite' => 'none',
            'secure' => true,
            'path' => '/v1/auth/refresh-token',  //endpoint URI for renewing the JWT token using this refresh-token, or deleting refresh-token
        ]));

        return $userRefreshToken;
    }

    public function actionRefreshToken() {
        $refreshToken = Yii::$app->request->cookies->getValue('refresh-token', false);
        if (!$refreshToken) {
            return new \yii\web\UnauthorizedHttpException('No refresh token found.');
        }

        $userRefreshToken = UserRefreshTokens::findOne(['urf_token' => $refreshToken]);

        if (Yii::$app->request->getMethod() == 'POST') {
            // Getting new JWT after it has expired
            if (!$userRefreshToken) {
                return new \yii\web\UnauthorizedHttpException('The refresh token no longer exists.');
            }

            $user = User::find()  //adapt this to your needs
            ->where(['id' => $userRefreshToken->urf_userID])
                ->andWhere(['not', ['status' => 9]])
                ->one();
            if (!$user) {
                $userRefreshToken->delete();
                return new \yii\web\UnauthorizedHttpException('The user is inactive.');
            }

            $token = $this->generateJwt($user);

            return [
                'status' => 'ok',
                'token' => (string) $token,
            ];

        } elseif (Yii::$app->request->getMethod() == 'DELETE') {
            // Logging out
            if ($userRefreshToken && !$userRefreshToken->delete()) {
                return new \yii\web\ServerErrorHttpException('Failed to delete the refresh token.');
            }

            return ['status' => 'ok'];
        } else {
            return new \yii\web\UnauthorizedHttpException('The user is inactive.');
        }
    }
}