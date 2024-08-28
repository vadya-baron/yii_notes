<?php

use components\auth\handlers\VKontakte;

return [
    'adminEmail' => $_ENV['APP_ADMIN_EMAIL'] ?? 'admin@example.com',
    'senderEmail' => $_ENV['APP_SENDER_EMAIL'] ?? 'noreply@example.com',
    'senderName' => $_ENV['APP_SENDER_NAME'] ?? 'Notes',
    'auth' => [
        'vkontakte' => [
            'handler' => VKontakte::class,
            'clientId' => $_ENV['VKONTAKTE_CLIENT_ID'] ?? '',
            'clientSecret' => $_ENV['VKONTAKTE_CLIENT_SECRET'] ?? '',
            'scope' => $_ENV['VKONTAKTE_SCOPE'] ?? '',
            'redirectUri' => '/auth/vk?client=vkontakte',
            'enablePKCE' => true,
        ],
    ],
];
