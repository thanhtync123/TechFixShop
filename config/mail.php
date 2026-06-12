<?php
// Nội dung chuẩn và duy nhất của file TechFixPHP/config/mail.php

return [
    'host'       => getenv('MAIL_HOST') ?: 'smtp.gmail.com',
    'port'       => (int) (getenv('MAIL_PORT') ?: 465),
    'secure'     => getenv('MAIL_SECURE') ?: 'ssl', 

    'username'   => getenv('MAIL_USERNAME') ?: '22004073@st.vlute.edu.vn',
    'password'   => getenv('MAIL_PASSWORD') ?: 'dehf ycwy urqg wzmi',

    'from_email' => getenv('MAIL_FROM_ADDRESS') ?: '22004073@st.vlute.edu.vn',
    'from_name'  => getenv('MAIL_FROM_NAME') ?: 'TECHFIX Support',
];