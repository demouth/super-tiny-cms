# Super Tiny CMS

[ [English](README.md) | 日本語 ]

ファイルベースデータベースを使用した超軽量CMSです。
複雑な設定なしで、ソースコードをそのままアップロードするだけで動作します。
CMSを使いたいけれどWordPressは重すぎるという場合に最適です。


## 機能

- ✨ **2カラム レスポンシブレイアウト** サイドバーナビゲーション付き
- 🌐 **多言語対応** (日本語/英語の自動検出)
- ⚙️ **設定可能なファイルパス** config.phpで設定
- 📱 **モバイル対応** インターフェース
- 📁 **ファイルベースデータベース** - MySQLは不要


## 使い方

1. `schemas.json` にスキーマ定義を記述する
2. `.data/` ディレクトリに書き込み権限を付与する
3. srcディレクトリをアップロードし、一般ユーザーがsrcディレクトリにアクセスできないようユーザー認証を設定する
4. 必要に応じて `config.php` で設定をカスタマイズする


## 設定 (config.php)

`src/config.php` を編集してCMSの動作をカスタマイズできます：

```php
<?php

return [
    'paths' => [
        'data_dir' => __DIR__ . '/.data',        // データ保存ディレクトリ
        'schemas_file' => __DIR__ . '/schemas.json', // スキーマ定義ファイル
    ],
    'language' => [
        'default' => 'auto', // 言語設定: 'auto', 'en', 'ja'
    ],
];
```

### 言語設定

- `'auto'` - ブラウザ設定から言語を自動検出
- `'en'` - 英語インターフェースを強制
- `'ja'` - 日本語インターフェースを強制


## スキーマ定義 (schemas.json)

- 第1階層の属性名はスキーマ名を記載します: `[a-z_-]{1,10}`
- 第2階層の属性名はカラム名で、値はフィールド型を記載します
- 利用可能なフィールド型:
    - `text` - 単一行テキスト入力
    - `textarea` - 複数行テキスト入力
    - `url` - URL入力（検証付き）
    - `date` - 日付ピッカー

### 設定例

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


## 登録データへのアクセス

schemas.jsonで定義したデータ構造に対して、管理画面からデータを登録・変更・削除することができます。

登録されたデータには次のようにしてアクセスできます：

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
    echo "タイトル: " . $r->get('title') . "\n";
    echo "詳細: " . $r->get('detail') . "\n";
}
```


## 管理インターフェース

ブラウザで `/src/` にアクセスして管理画面を利用できます。インターフェースの特徴：

- **サイドバーナビゲーション** で簡単なスキーマ切り替え
- **レスポンシブデザイン** でデスクトップとモバイルに対応
- **多言語対応** で言語の自動検出
- **クリーンでモダンなUI** Bootstrapを使用


## 動作要件

- PHP 7.4 以上
- Webサーバー (Apache、Nginxなど)
- データディレクトリへの書き込み権限