# Super Tiny CMS

[ [English](README.md) | 日本語 ]

ファイルベースデータベースを使用した超軽量CMSです。
複雑な設定なしで、ソースをそのままアップロードすれば動作します。
CMSを使いたいけどWordPressは大げさだなぁ、という時にちょうど良いです。


## 使い方

- `schemas.json` に定義を記述する
- `.data/` に書き込み権限を付与する
- srcディレクトリをアップし、一般ユーザーがsrcディレクトリにはアクセスできないよう、なんらかのユーザー認証を設定する


## schemas.json

- 第一階層の属性名はスキーマ名を記載します `[a-z_-]{1,10}`
- 第二階層の属性名はカラム名で、値は型を記載します
- 型は次の4種類です
    - text
    - textarea
    - url
    - date

設定例

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


## CMSで登録したデータへのアクセス

schemas.jsonで定義したデータ構造に対して、管理画面からデータを登録・変更・削除することができます。

そうして登録されたデータへは、次のようにして読み取ることができます。

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
