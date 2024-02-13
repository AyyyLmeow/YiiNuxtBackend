<?php

namespace common\helpers;

use common\models\User;

class JWTHelper
{
    public static function getUserByToken($token)
    {
        $token = str_replace('Bearer ', '', $token);
        $token = \Yii::$app->jwt->getParser()->parse((string) $token); // Parses from a string
        return User::findIdentity($token->getClaim('uid'));
    }
}