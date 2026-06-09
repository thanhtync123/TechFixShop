<?php

return [
    'host'       => getenv('MAIL_HOST') ?: 'smtp.gmail.com',
    'port'       => (int) (getenv('MAIL_PORT') ?: 465),
    'secure'     => getenv('MAIL_SECURE') ?: 'ssl', 

    'username'   => getenv('MAIL_USERNAME') ?: 'your_email@gmail.com',
    'password'   => getenv('MAIL_PASSWORD') ?: 'your_app_password',

    'from_email' => getenv('MAIL_FROM_ADDRESS') ?: (getenv('MAIL_USERNAME') ?: 'your_email@gmail.com'),
    'from_name'  => getenv('MAIL_FROM_NAME') ?: 'TECHFIX Support',
];

