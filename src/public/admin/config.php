<?php
/**
 * Super Tiny CMS Configuration File
 * 
 * This file contains all the configuration settings for the CMS.
 * Modify these settings according to your environment and requirements.
 */

return [
    // File and directory paths
    'paths' => [
        // Directory where JSON data files are stored
        'data_dir' => __DIR__ . '/../../.data',
        
        // File that defines database schemas
        'schemas_file' => __DIR__ . '/schemas.json',
    ],
    
    // Language and internationalization settings
    'language' => [
        // Default language: 'auto' = browser detection, 'en' = English, 'ja' = Japanese
        'default' => 'auto',
    ],
    
    // Image upload settings
    'uploads' => [
        // Maximum file size in bytes (5MB = 5 * 1024 * 1024)
        'max_size' => 5 * 1024 * 1024,
        
        // Allowed image MIME types for upload
        'allowed_types' => ['image/jpeg', 'image/png', 'image/gif'],
        
        // Server directory where uploaded files are stored
        'upload_dir' => __DIR__ . '/../uploads',
        
        // Web URL path to access uploaded files (from document root)
        'public_url_path' => '/public/uploads',
    ],
];