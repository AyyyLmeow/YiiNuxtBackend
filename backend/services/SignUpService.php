<?php

namespace backend\services;

use backend\models\SignupForm;
use yii\web\UploadedFile;

class SignUpService
{
    public function actionSignup()
    {
        $model = new SignupForm();
        $model->password = \yii::$app->request->post('password');
        $model->username = \yii::$app->request->post('username');
        $model->email = \yii::$app->request->post('email');
        $model->eventImage = UploadedFile::getInstance($model, 'photo_url');
        if ($model->validate() && $model->signup()) {
            return $model;
        }
        return $model->getErrors();
    }
}