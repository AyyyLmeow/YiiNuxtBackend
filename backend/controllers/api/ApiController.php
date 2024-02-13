<?php

namespace backend\controllers\api;

use common\models\User;
use yii\rest\Controller;
use yii\web\Response;
use sizeg\jwt\Jwt;

class ApiController extends Controller
{

    public static function allowedDomains()
    {
        return [
            '*',
        ];
    }

    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            'corsFilter'  => [
                'class' => \yii\filters\Cors::className(),
                'cors'  => [
                    'Origin' => ['*'],
                    'Access-Control-Request-Method'    => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'HEAD', 'OPTIONS'],
                    'Access-Control-Allow-Credentials' => true,
                    'Access-Control-Max-Age'           => 3600 * 7,                 // Cache (seconds)
                ],
            ],
        ]);
    }

    public function beforeAction($action)
    {
        if (\Yii::$app->request->headers->get('Authorization')) {
            \Yii::$app->user->identity = $this->getUser();
        }
        \Yii::$app->response->format = Response::FORMAT_JSON;


        return parent::beforeAction($action);
    }

    protected function getUser()
    {
        return \JWTHelper::getUserByToken(\Yii::$app->request->headers->get('Authorization'));
    }
}