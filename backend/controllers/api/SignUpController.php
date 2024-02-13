<?php

namespace backend\controllers\api;

use app\models\UserRefreshTokens;
use backend\models\UserInfo;
use common\models\LoginForm;
use common\models\User;
use backend\models\SignupForm;
use sizeg\jwt\JwtHttpBearerAuth;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\rest\Controller;
use yii\web\UploadedFile;

class SignUpController extends ApiController
{

    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'signup' => ['post'],
                ],
            ],
        ]);
    }
    public function actionSignup()
    {
        $model = new SignupForm();
        $model->password = Yii::$app->request->post('password');
        $model->username = Yii::$app->request->post('username');
        $model->email = Yii::$app->request->post('email');
        $model->eventImage = UploadedFile::getInstance($model, 'photo_url');
        if ($model->validate() && $model->signup()) {
            return true;
        }
        return $model->getErrors();
    }

}