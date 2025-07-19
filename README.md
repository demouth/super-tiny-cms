# Super Tiny CMS

[ English | [日本語](README-ja.md) ]

Super Tiny CMS is an ultra-lightweight CMS using a file-based database.
It works by simply uploading the source code without any complicated configuration.
It's perfect when you want to use a CMS but WordPress is too heavy.


## Features

- ✨ **2-column responsive layout** with sidebar navigation
- 🌐 **Multi-language support** (English/Japanese auto-detection)
- ⚙️ **Configurable file paths** via config.php
- 📱 **Mobile-friendly** interface
- 📁 **File-based database** - no MySQL required


## How to use

1. Write the schema definition in `schemas.json`
2. Grant write permission to the `.data/` directory
3. Upload the src directory and set up user authentication to prevent ordinary users from accessing the src directory
4. Optionally customize settings in `config.php`


## Configuration (config.php)

You can customize the CMS behavior by editing `src/config.php`:

```php
<?php

return [
    'paths' => [
        'data_dir' => __DIR__ . '/.data',        // Data storage directory
        'schemas_file' => __DIR__ . '/schemas.json', // Schema definition file
    ],
    'language' => [
        'default' => 'auto', // Language setting: 'auto', 'en', 'ja'
    ],
];
```

### Language Settings

- `'auto'` - Automatically detect language from browser settings
- `'en'` - Force English interface
- `'ja'` - Force Japanese interface


## Schema Definition (schemas.json)

- First-level attribute names describe schema names: `[a-z_-]{1,10}`
- Second-level attribute names are column names, and values are field types
- Available field types:
    - `text` - Single line text input
    - `textarea` - Multi-line text input
    - `url` - URL input with validation
    - `date` - Date picker

### Example Configuration

```json
{
  "news": {
    "title":"text",
    "detail":"textarea",
    "url": "url",
    "date": "date"
  },
  "articles": {
    "title":"text",
    "content":"textarea",
    "published_date": "date"
  }
}
```


## Accessing Registered Data

You can register, modify, and delete data from the admin interface for the data structure defined in schemas.json.

The registered data can be accessed as follows:

```php
require_once '/path/to/src/libs/Database.php';
require_once '/path/to/src/libs/RecordSet.php';
require_once '/path/to/src/libs/Record.php';

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


## Admin Interface

Access the admin interface by navigating to `/src/` in your browser. The interface features:

- **Sidebar navigation** for easy schema switching
- **Responsive design** that works on desktop and mobile
- **Multi-language support** with automatic language detection
- **Clean, modern UI** using Bootstrap


## Requirements

- PHP 7.4 or higher
- Web server (Apache, Nginx, etc.)
- Write permissions for the data directory