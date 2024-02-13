<?php

namespace backend\controllers\api;

use backend\services\NoteService;
use yii\web\ForbiddenHttpException;
use yii\web\Response;

class NoteController extends \yii\rest\ActiveController
{
    public $enableCsrfValidation = false;
    public $modelClass = 'common\models\User';
    private $service;

    public function __construct($id, $module, $config = [], NoteService $service)
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
                    'Origin' => ['*'],
                    'Access-Control-Request-Method' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'HEAD', 'OPTIONS'],
                    'Access-Control-Allow-Credentials' => true,
                    'Access-Control-Max-Age' => 3600 * 7,                 // Cache (seconds)
                ],
            ],
            'authenticator' => [
                'class' => \sizeg\jwt\JwtHttpBearerAuth::class,
                'optional' => ['index'],
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
        if (in_array($action->id, ['index', 'create'])) {
            return true;
        } elseif(in_array($action->id,['update','delete'] ) && $_GET['id'] == $user->id) {
            return true;
        } else {
            throw new ForbiddenHttpException('Access denied');
        }
    }

    public function actions()
    {
        $actions = parent::actions();

        unset($actions['update'], $actions['create'], $actions['index'], $actions['delete']);

        return $actions;
    }
    function actionIndex()
    {
        return $this->service->index();
    }

    public function actionCreate()
    {
        return $this->service->createNote($this->request->post());
    }
    public function actionUpdate()
    {
        $isPost = $this->request->isPost;
        return $this->service->updateNote($this->request->get('id'), $this->request->post());
    }
    public function actionDelete()
    {
        return $this->service->deleteNote($this->request->get('id'));
    }
}