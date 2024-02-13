<?php
return [
    'adminEmail' => 'admin@example.com',
    'supportEmail' => 'support@example.com',
    'senderEmail' => 'noreply@example.com',
    'senderName' => 'Example.com mailer',
    'user.passwordResetTokenExpire' => 3600,
    'user.passwordMinLength' => 8,
    'jwt' => [
        'issuer' => 'http://yiitask2front:81/',  //name of your project (for information only)
        'audience' => 'http://yiitask2front:81/',  //description of the audience, eg. the website using the authentication (for info only)
        'id' => 'UNIQUE-JWT-IDENTIFIER',  //a unique identifier for the JWT, typically a random string
        'expire' => 21600,  //the short-lived JWT token is here set to expire after 6 hrs.
        'jwtValidationData' => \backend\components\JwtValidationData::class,
    ],
];
