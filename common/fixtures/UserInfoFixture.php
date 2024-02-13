<?php

namespace common\fixtures;

use backend\models\UserInfo;
use yii\test\ActiveFixture;

class UserInfoFixture extends ActiveFixture
{
    public $modelClass = 'backend\models\UserInfo';
    public $depends = ['common\fixtures\UserFixture'];
}