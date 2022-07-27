# Super Tiny CMS

[ English | [日本語](README-ja.md) ]

Super-Tiny-CMS is an super-lightweight CMS using a file-based database.
It works by uploading the source as is, without complicated configuration.
It is just right for when you want to use a CMS but WordPress is too big.


## How to use

- Write the definition in `schemas.json` .
- Grant write permission to `.data/` .
- Upload the src directory and set up some kind of user authentication so that ordinary users cannot access the src directory.


## Definition of schemas.json

- Attribute names in the first level describe the schema name. `[a-z_-]{1,10}`
- Attribute names in the second level are column names and values are types.
- There are four types.
    - text
    - textarea
    - url
    - date

Setting Example

```json
{
  "news": {
    "title":"text",
    "detail":"textarea",
    "url": "url",
    "date": "date"
  },
  "article": {
    "title":"text",
    "detail":"textarea",
    "date": "date"
  }
}
```


## Accessing Registered Data

Data can be registered, modified, and deleted from the management page for the data structure defined in schemas.json.

The registered data can be read as follows.

```php
require_once '/path/to/src/libs/Database.php';
require_once '/path/to/src/libs/RecordSet.php';
require_once '/path/to/src/libs/Record.php';

use stcms\Database;

$db = new Database('news');
$rs = $db->get();
foreach($rs->getAll() as $id => $r) {
    if ($r->deleted()) continue;
    var_dump($id);
    var_dump($r);
}
```
