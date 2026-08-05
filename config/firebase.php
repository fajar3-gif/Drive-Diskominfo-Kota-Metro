<?php

return [
    'api_key'            => env('VITE_FIREBASE_API_KEY', ''),
    'auth_domain'        => env('VITE_FIREBASE_AUTH_DOMAIN', ''),
    'project_id'         => env('VITE_FIREBASE_PROJECT_ID', ''),
    'storage_bucket'     => env('VITE_FIREBASE_STORAGE_BUCKET', ''),
    'messaging_sender_id'=> env('VITE_FIREBASE_MESSAGING_SENDER_ID', ''),
    'app_id'             => env('VITE_FIREBASE_APP_ID', ''),
    'measurement_id'     => env('VITE_FIREBASE_MEASUREMENT_ID', ''),
];
