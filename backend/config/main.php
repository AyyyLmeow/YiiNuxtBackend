<?php
$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return [
    'id' => 'app-backend',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'backend\controllers',
    'bootstrap' => ['log'],
    'modules' => [],
    'components' => [
        'request' => [
            'csrfParam' => '_csrf-backend',
        ],
        'user' => [
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => true,
            'identityCookie' => ['name' => '_identity-backend', 'httpOnly' => true],
        ],
        'session' => [
            // this is the name of the session cookie used for login on the backend
            'name' => 'advanced-backend',
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => \yii\log\FileTarget::class,
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'enableStrictParsing' => false,
            'showScriptName' => false,
            'rules' => [
                ['class' => 'yii\rest\UrlRule', 'controller' => 'api/user'], 
                '' => 'site/index',                                
                '<controller:(\w+)>/<action:\w+>/' => '<controller>/<action>',
            ],
        ],
        'parsers' => [
            'application/json' => 'yii\web\JsonParser',
            'class' => yii\web\JsonParser::class,
        ],
        'jwt' =>[
            'class' => \sizeg\jwt\Jwt::class,
            'key' => 'Secret',
            'jwtValidationData' => \backend\components\JwtValidationData::class,
        ]
        // 'request' => [
        //     'baseUrl' => '/admin',
        // ],
    ],
    'params' => $params,
];
