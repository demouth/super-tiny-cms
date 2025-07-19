# Super Tiny CMS

[ [English](README.md) | 日本語 ]

ファイルベースデータベースを使用した超軽量CMSです。
複雑な設定なしで、ソースコードをそのままアップロードするだけで動作します。
CMSを使いたいけれどWordPressは重すぎるという場合に最適です。


## 機能

- ✨ **2カラム レスポンシブレイアウト** サイドバーナビゲーション付き
- 🌐 **多言語対応** (日本語/英語の自動検出)
- 📸 **画像アップロード&管理** セキュアなファイル処理
- ⚙️ **設定可能なファイルパス** config.phpで設定
- 📱 **モバイル対応** インターフェース
- 📁 **ファイルベースデータベース** - MySQLは不要


## 使い方

1. `schemas.json` にスキーマ定義を記述する
2. `.data/` ディレクトリに書き込み権限を付与する
3. 画像アップロード用に `public/uploads/` ディレクトリを作成し、書き込み権限を付与する
4. srcディレクトリをアップロードし、publicディレクトリをWebサーバーの公開領域に配置し、admin部分へのアクセス制限を設定する
5. 必要に応じて `config.php` で設定をカスタマイズする


## 設定 (config.php)

`src/public/admin/config.php` を編集してCMSの動作をカスタマイズできます：

```php
<?php

return [
    'paths' => [
        'data_dir' => __DIR__ . '/../.data',        // データ保存ディレクトリ
        'schemas_file' => __DIR__ . '/schemas.json', // スキーマ定義ファイル
    ],
    'language' => [
        'default' => 'auto', // 言語設定: 'auto', 'en', 'ja'
    ],
    'uploads' => [
        'max_size' => 5 * 1024 * 1024,              // 最大ファイルサイズ (5MB)
        'allowed_types' => ['image/jpeg', 'image/png', 'image/gif'], // 許可される画像タイプ
        'upload_dir' => __DIR__ . '/../public/uploads', // サーバーのアップロードディレクトリ
        'public_url_path' => '/public/uploads',      // 画像へのWeb URLパス
    ],
];
```

### 言語設定

- `'auto'` - ブラウザ設定から言語を自動検出
- `'en'` - 英語インターフェースを強制
- `'ja'` - 日本語インターフェースを強制

### アップロード設定

- `max_size` - 画像アップロードの最大ファイルサイズ（バイト単位）
- `allowed_types` - アップロード可能なMIMEタイプの配列
- `upload_dir` - アップロードされたファイルが保存されるサーバーディレクトリ
- `public_url_path` - アップロードされた画像にアクセスするためのWeb URLパス


## スキーマ定義 (schemas.json)

- 第1階層の属性名はスキーマ名を記載します: `[a-z_-]{1,10}`
- 第2階層の属性名はカラム名で、値はフィールド型を記載します
- 利用可能なフィールド型:
    - `text` - 単一行テキスト入力
    - `textarea` - 複数行テキスト入力
    - `url` - URL入力（検証付き）
    - `date` - 日付ピッカー
    - `image` - アップロード済みファイルからの画像選択

### 設定例

```json
{
  "news": {
    "title":"text",
    "detail":"textarea",
    "thumbnail": "image",
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


## 登録データへのアクセス

schemas.jsonで定義したデータ構造に対して、管理画面からデータを登録・変更・削除することができます。

登録されたデータには次のようにしてアクセスできます：

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
    echo "タイトル: " . $r->get('title') . "\n";
    echo "詳細: " . $r->get('detail') . "\n";
}
```

### アップロードされた画像へのアクセス

メディアマネージャーでアップロードされた画像はフロントエンドでアクセスできます：

```php
require_once '/path/to/src/public/admin/libs/MediaManager.php';

use stcms\MediaManager;

// アップロードされた全ての画像を取得
$images = MediaManager::getUploadedFiles();
foreach($images as $image) {
    echo '<img src="' . MediaManager::getPublicUrl($image['filename']) . '" alt="">';
}
```


## 管理インターフェース

ブラウザで `/src/public/admin/` にアクセスして管理画面を利用できます。インターフェースの特徴：

- **サイドバーナビゲーション** で簡単なスキーマ切り替え
- **コンテンツ管理** で完全なCRUD操作
- **画像アップロード&ギャラリー** セキュアなファイル処理
- **レスポンシブデザイン** でデスクトップとモバイルに対応
- **多言語対応** で言語の自動検出
- **クリーンでモダンなUI** Bootstrapを使用

### メディア管理

CMSには組み込みのメディア管理システムが含まれています：

- **画像アップロード** ドラッグ&ドロップまたはファイル選択
- **画像ギャラリー** サムネイルプレビュー付き
- **フルスクリーン画像プレビュー** （任意の画像をクリック）
- **セキュアファイル保存** ハッシュベースのファイル名
- **自動ファイル検証** サポートされる形式の検証


## 動作要件

- PHP 7.4 以上
- Webサーバー (Apache、Nginxなど)
- データディレクトリへの書き込み権限