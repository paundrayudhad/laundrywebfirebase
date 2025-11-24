<?php
return [
    'app' => [
        'name' => 'Laundry Firebase'
    ],
    'firebase' => [
        'url' => 'https://laundry-57c23-default-rtdb.asia-southeast1.firebasedatabase.app',
        'secret' => getenv('FIREBASE_DB_SECRET') ?: ''
    ]
];
