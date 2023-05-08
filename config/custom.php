<?php

return [
    'upload' => [
        'disk' => 'public', // can be publi or s3
        'user' => [
            'document_path' => 'user/documents',
            'profile' => 'user/profile',
            'document_permits' => 'user/document_permits',
            
        ],
        'vehicle' => 'vehicle',
        'category' => 'category',
        'currency' => 'currency',
        'addmoney' => 'addmoney',
        'sendmoney' => 'sendmoney',
        'withdrawmoney' => 'withdrawmoney',
        'socialmedia' => 'socialmedia',
        'flags' => 'flags'
    ],
];