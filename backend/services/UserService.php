<?php

namespace backend\services;
use backend\models\UserInfo;
use common\models\User;
use yii\data\Pagination;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;

class UserService
{
    function index(){
        $query = User::find();
        // Добавляем параметры сортировки
        $sortAttribute = \Yii::$app->request->get('sort', ''); // Получаем параметр сортировки из запроса
        $sortOrder = \Yii::$app->request->get('order', 'asc'); // Получаем параметр направления сортировки из запроса

        if ($sortAttribute) {
            $sort = [$sortAttribute => ($sortOrder === 'asc' ? SORT_DESC : SORT_ASC)];
            // Добавляем сортировку в запрос
            $query->orderBy($sort);
        }

        $countQuery = clone $query;
        $pages = new Pagination(['totalCount' => $countQuery->count(), 'pageSize' => 10]);
        $models = $query->offset($pages->offset)
            ->limit($pages->limit)
            ->all();

        // Создаем массив данных для ответа
        $response = [
            'models' => $models,
            'pages' => [
                'totalCount' => $pages->totalCount,
                'pageCount' => $pages->getPageCount(),
                'currentPage' => $pages->getPage() + 1, // Текущая страница начинается с 0, поэтому добавляем 1
                'pageSize' => $pages->getPageSize(),
            ],
        ];

        return $response; // Yii2 автоматически преобразует массив в формат JSON
    }
    function updateById($id, $data)
    {
        $model = $this->getModel($id);
        if ($model->userInfo->birth_data == null) {
            $model->userInfo->birth_data = '';
        }

        if ($model->load($data, '') && $model->userInfo->load($data, '') && $model->userInfo->save() && $model->hashOnlyPassAndSave($model)) {
            return $model->toArray();
        }

        return array_merge($model->getErrors(), $model->userInfo->getErrors());
    }
    function createUser($data)
    {
        $model = new User();
        $userInfo = new UserInfo();

        if ($model->load($data, '') && $userInfo->load($data, '') && $model->save()) {
            $userInfo->auth_id = $model->id;
            $userInfo->save();
            return $model;
        }

        return array_merge($model->getErrors(), $userInfo->getErrors());


    }
    function activateUser($id)
    {
        $model = $this->getModel($id);

        $model->status = User::STATUS_ACTIVE;
        if ($model->save()) {
            return $model->toArray();
        }
        return array_merge($model->getErrors(), $model->userInfo->getErrors());
    }
    function banUser($id)
    {
        $model = $this->getModel($id);

        $model->status = User::STATUS_BANNED;
        if ($model->save()) {
            return $model->toArray();
        }


        return array_merge($model->getErrors(), $model->userInfo->getErrors());
    }

    function uploadUserPicture($id)
    {
        $model = $this->getModel($id);
        $userInfo = $model->userInfo;
        if (\Yii::$app->request->isPost) {
            $model->userInfo->eventImage = UploadedFile::getInstance($userInfo, 'photo_url');
            if ($model->userInfo->upload()) {
                return $model->toArray();

            }
        }

        return array_merge($model->getErrors(), $model->userInfo->getErrors());
    }

    private function getModel($id)
    {
        $model = User::findById($id);
        if (!$model) {
            throw new NotFoundHttpException('User does not exist.');
        }
        return $model;
    }
}