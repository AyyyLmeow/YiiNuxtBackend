<?php

namespace common\fixtures;

use common\models\User as User;
use backend\models\UserInfo;


return [
    'user1' => [
        'id' => '1',
        'username' => 'admin',
        'auth_key' => '123',
        'password_hash' => \Yii::$app->security->generatePasswordHash('123456789'),
        'password_reset_token' => '123',
        'email' => 'admin@admin.com',
        'status' => User::STATUS_ACTIVE,
        'created_at' => time(),
        'updated_at' => time(),
    ],
    'user2' => [
        'id' => '2',
        'username' => 'napoleon69',
        'auth_key' => '123321',
        'password_hash' => \Yii::$app->security->generatePasswordHash('123456789'),
        'password_reset_token' => '1234',
        'email' => 'aileen.barton@heaneyschumm.com',
        'status' => User::STATUS_ACTIVE,
        'created_at' => time(),
        'updated_at' => time(),
    ],
];