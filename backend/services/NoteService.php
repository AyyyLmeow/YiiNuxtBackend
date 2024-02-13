<?php

namespace backend\services;

use app\models\Notes;
use common\models\User;
use yii\data\Pagination;
use yii\web\NotFoundHttpException;

class NoteService
{
    function index(){
        $query = Notes::find();
        $countQuery = clone $query;
        $pages = new Pagination(['totalCount' => $countQuery->count(), 'pageSize' => 10]);
        $models = $query->offset($pages->offset)
            ->limit($pages->limit)
            ->all();
        {
            return $models;
        }
     }
     function createNote($data){
         $model = new Notes();
         $data['user_id'] = \yii::$app->user->identity->id;
         if ($model->load($data, '') && $model->save()) {
             return $model;
         }

         return $model->getErrors();
     }

     function updateNote($id, $data){//todo допиши дату блять и про рбак не забудь, сука
         $model = $this->getModel($id);
         $a = \Yii::$app->request->isPost;
         $b = $model->load($data, '');
         $c = $model->save();
         if (\Yii::$app->request->isPost && $model->load($data, '') && $model->save()){
             return $model;
         }

         return $model->getErrors();
     }

     function deleteNote($id){
         $this->getModel($id)->delete();

         return true;
     }

    private function getModel($id)
    {
        $model = Notes::findById($id);
        if (!$model) {
            throw new NotFoundHttpException('This note does not exist.');
        }
        return $model;
    }
}