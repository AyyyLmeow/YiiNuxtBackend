<?php

namespace backend\controllers\api;

use backend\models\UserInfo;
use backend\services\UserService;
use common\models\user;
use http\Url;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\rest\Action;
use yii\web\ForbiddenHttpException;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\UploadedFile;
use yii\data\Pagination;

class UserController extends \yii\rest\ActiveController
{

    public $enableCsrfValidation = false;
    public $modelClass = 'common\models\User';
    private $service;

    public function __construct($id, $module, $config = [], UserService $service)
    {
        parent::__construct($id, $module, $config);
        $this->service = $service;
    }

    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            'corsFilter' => [
                'class' => \yii\filters\Cors::className(),
                'cors' => [
                    'Origin' => ['http://yiitask2front:81/'],
                    'Access-Control-Request-Method' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'HEAD', 'OPTIONS'],
                    'Access-Control-Allow-Credentials' => true,
                    'Access-Control-Max-Age' => 3600 * 7,                 // Cache (seconds)
                ],
            ],
            'authenticator' => [
                'class' => \sizeg\jwt\JwtHttpBearerAuth::class,
                'optional' => ['login','logout', 'sign-up'],
            ],
        ]);
    }

    protected function verbs()
    {
        $verbs = parent::verbs();
        $verbs['update'] = ['PUT', 'PATCH', 'OPTIONS', 'POST',];
        $verbs['create'] = ['POST', 'OPTIONS'];
        $verbs['upload'] = ['PUT', 'PATCH', 'OPTIONS', 'POST',];
        return $verbs;
    }

    public function beforeAction($action)
    {
        parent::beforeAction($action);
        $user = \Yii::$app->user->identity;
        \Yii::$app->response->format = Response::FORMAT_JSON;

        if (\Yii::$app->user->can('admin')) {
            return true;
        }

        if (in_array($action->id, ['index', 'view', 'update', 'upload']) && \Yii::$app->user->can('baseUser')){
            if ($action->id != 'index' && $_GET['id'] == $user->id) {
                return true;
            }else{
                throw new ForbiddenHttpException('Access denied');
            }
        }
        if (\Yii::$app->user->can($action->id)) {
            return true;
        }

        throw new ForbiddenHttpException('Access denied');
    }

    public function actions()
    {
        $actions = parent::actions();

        unset($actions['update'], $actions['create'], $actions['index']);

        return $actions;
    }

    public function actionUpdate()
    {
        return $this->service->updateById($this->request->get('id'), $this->request->post());
    }

    public function actionCreate()
    {
        return $this->service->createUser($this->request->post());
    }

    public function actionActivate()
    {
        return $this->service->activateUser($this->request->get('id'));

    }

    public function actionBan()
    {
        return $this->service->banUser($this->request->get('id'));
    }


    public function actionUpload()
    {
        return $this->service->uploadUserPicture($this->request->get('id'));

    }

    function actionIndex()
    {
        return $this->service->index();
    }
}