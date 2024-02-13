<?php
namespace console\controllers;

use console\rbac\UserGroupRule;
use yii\console\Controller;

class RbacController extends Controller
{
    public function actionInit()
    {
        $auth = \Yii::$app->authManager;

        $auth->removeAll(); //На всякий случай удаляем старые данные из БД...

        // Создадим роли админа и редактора новостей
        $admin = $auth->createRole('admin');
        $baseUser = $auth->createRole('baseUser');

        // запишем их в БД
        $auth->add($admin);
        $auth->add($baseUser);

        // Создаем разрешения. Например, просмотр админки viewAdminPage и редактирование новости updateNews
        $create = $auth->createPermission('create');
        $create->description = 'Создание пользователя';

        $index = $auth->createPermission('index');
        $index->description = 'Просмотр пользователей';

        $update = $auth->createPermission('update');
        $update->description = 'Редактирование пользователя';

        $activate = $auth->createPermission('activate');
        $activate->description = 'Активация пользователя';

        $ban = $auth->createPermission('ban');
        $ban->description = 'Бан пользователя';

        $upload = $auth->createPermission('upload');
        $upload->description = 'загрузка фото пользователя';

        $view = $auth->createPermission('view');
        $view->description = 'Просмотр конкретного пользователя';


        // Запишем эти разрешения в БД
        $auth->add($index);
        $auth->add($ban);
        $auth->add($activate);
        $auth->add($update);
        $auth->add($create);
        $auth->add($view);
        $auth->add($upload);

        $auth->addChild($baseUser,$update);

        $auth->addChild($admin, $baseUser);

        $auth->addChild($admin, $create);

        $auth->addchild($admin, $index);

        $auth->addchild($admin, $activate);

        $auth->addchild($admin, $ban);

        $auth->addchild($admin, $view);

        $auth->addChild($admin, $upload);

        // Назначаем роль admin пользователю с ID 1
        $auth->assign($admin, 1);

        // Назначаем роль editor пользователю с ID 2
        $auth->assign($baseUser, 2);
    }
    
}