<?php

$params = require(__DIR__ . '/params.php');

$config = [
    'id' => 'ya.graber',
    'basePath' => dirname(__DIR__),
    'vendorPath' => dirname(__DIR__) . '/../../vk.photos1/app.yii/vendor',
		'defaultRoute' => 'yandex',
    'bootstrap' => ['log'],
    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'parampampamy',
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
             ],
        'user' => [
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => true,
            'identityCookie' => ['name'=>'ya','httpOnly'=>true],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
             'transport' => [
                'class' => 'Swift_SmtpTransport',
                'plugins' => [
                    [
                        'class' => 'Swift_Plugins_LoggerPlugin',
                        'constructArgs' => [new Swift_Plugins_Loggers_ArrayLogger], //thanks @germansokolov13
                        // it could also be any Swift_Plugins_Logger implementation (e.g., the EchoLogger)
                    ],
                ],
            ],
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => require(__DIR__ . '/db.php'),
        
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
						  'image.png'=>'mailing/image',
						  'unsubscribe/?'=>'mailing/unsubscribe',
						  'forward'=>'mailing/forward',
							'mailing/get/spamming'=>'mailing/spamming',
							// 'mailing/stat?by=subid'=>'mailing/stat',
            ],
        ],
        
    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        'allowedIPs' => ['127.0.0.1', '37.193.217.126', '5.128.8.71', '::1'],
		];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
    ];
}

return $config;
