<?php
return [
    'app' => [
        'name' => 'Laundry Firebase'
    ],
    'firebase' => [
        'url' => getenv('FIREBASE_DB_URL') ?: 'https://your-project-id.firebaseio.com',
        'secret' => getenv('FIREBASE_DB_SECRET') ?: ''
    ]
];
