# Super Tiny CMS

[ English | [日本語](README-ja.md) ]

Super Tiny CMS is an ultra-lightweight CMS using a file-based database.
It works by simply uploading the source code without any complicated configuration.
It's perfect when you want to use a CMS but WordPress is too heavy.


## Features

- ✨ **2-column responsive layout** with sidebar navigation
- 🌐 **Multi-language support** (English/Japanese auto-detection)
- 📸 **Image upload & management** with secure file handling
- ⚙️ **Configurable file paths** via config.php
- 📱 **Mobile-friendly** interface
- 📁 **File-based database** - no MySQL required


## How to use

1. Write the schema definition in `schemas.json`
2. Grant write permission to the `.data/` directory
3. Create the `public/uploads/` directory with write permissions for image uploads
4. Upload the src directory, place the public directory in the web server's public area, and configure access restrictions for the admin section
5. Optionally customize settings in `config.php`


## Configuration (config.php)

You can customize the CMS behavior by editing `src/public/admin/config.php`:

```php
<?php

return [
    'paths' => [
        'data_dir' => __DIR__ . '/../.data',        // Data storage directory
        'schemas_file' => __DIR__ . '/schemas.json', // Schema definition file
    ],
    'language' => [
        'default' => 'auto', // Language setting: 'auto', 'en', 'ja'
    ],
    'uploads' => [
        'max_size' => 5 * 1024 * 1024,              // Maximum file size (5MB)
        'allowed_types' => ['image/jpeg', 'image/png', 'image/gif'], // Allowed image types
        'upload_dir' => __DIR__ . '/../public/uploads', // Server upload directory
        'public_url_path' => '/public/uploads',      // Web URL path for images
    ],
];
```

### Language Settings

- `'auto'` - Automatically detect language from browser settings
- `'en'` - Force English interface
- `'ja'` - Force Japanese interface

### Upload Settings

- `max_size` - Maximum file size in bytes for image uploads
- `allowed_types` - Array of allowed MIME types for uploads
- `upload_dir` - Server directory where uploaded files are stored
- `public_url_path` - Web URL path to access uploaded images


## Schema Definition (schemas.json)

- First-level attribute names describe schema names: `[a-z_-]{1,10}`
- Second-level attribute names are column names, and values are field types
- Available field types:
    - `text` - Single line text input
    - `textarea` - Multi-line text input
    - `url` - URL input with validation
    - `date` - Date picker
    - `image` - Single image selection from uploaded files
    - `images` - Multiple images with captions (sortable)

### Example Configuration

```json
{
  "news": {
    "title":"text",
    "detail":"textarea",
    "thumbnail": "image",
    "gallery": "images",
    "url": "url",
    "date": "date"
  },
  "articles": {
    "title":"text",
    "content":"textarea",
    "featured_image": "image",
    "published_date": "date"
  }
}
```


## Accessing Registered Data

You can register, modify, and delete data from the admin interface for the data structure defined in schemas.json.

The registered data can be accessed as follows:

```php
require_once '/path/to/src/public/admin/libs/Database.php';
require_once '/path/to/src/public/admin/libs/RecordSet.php';
require_once '/path/to/src/public/admin/libs/Record.php';

use stcms\Database;

$db = new Database('news');
$rs = $db->get();
foreach($rs->getAll() as $id => $r) {
    if ($r->deleted()) continue;
    echo "ID: " . $id . "\n";
    echo "Title: " . $r->get('title') . "\n";
    echo "Detail: " . $r->get('detail') . "\n";
}
```

### Accessing Uploaded Images

Images uploaded through the media manager can be accessed in your frontend:

```php
require_once '/path/to/src/public/admin/libs/MediaManager.php';

use stcms\MediaManager;

// Get all uploaded images
$images = MediaManager::getUploadedFiles();
foreach($images as $image) {
    echo '<img src="' . MediaManager::getPublicUrl($image['filename']) . '" alt="">';
}
```


## Admin Interface

Access the admin interface by navigating to `/src/public/admin/` in your browser. The interface features:

- **Sidebar navigation** for easy schema switching
- **Content management** with full CRUD operations
- **Image upload & gallery** with secure file handling
- **Responsive design** that works on desktop and mobile
- **Multi-language support** with automatic language detection
- **Clean, modern UI** using Bootstrap

### Media Management

The CMS includes a built-in media management system:

- **Upload images** with drag-and-drop or file selection
- **Image gallery** with thumbnail previews
- **Full-screen image preview** (click any image)
- **Secure file storage** with hash-based filenames
- **Automatic file validation** for supported formats


## Requirements

- PHP 7.4 or higher
- Web server (Apache, Nginx, etc.)
- Write permissions for the data directory

### PHP 5.3 Support

For legacy environments requiring PHP 5.3 compatibility, use the dedicated branch:

- **Branch**: [`php53`](https://github.com/demouth/super-tiny-cms/tree/php53)
- **Compatibility**: PHP 5.3.0 - PHP 8.x
- **Features**: Full functionality with legacy-compatible syntax