# MoneyLog

スマホで手軽に収入・支出を記録して、月ごと・週ごとの収支をすぐ確認できる家計簿アプリです。

- **公開URL**: https://catmoneylog-2438.com/

## 画面
スマホでの利用を想定しているアプリのため、スクリーンショットもスマホサイズで撮影しています。

### ホーム画面
<img src="docs/screenshots/home.png" alt="ホーム画面のスクリーンショット" width="320">

その月の収支（今月の収支・収入・支出の合計）と、週ごとの収支内訳をまとめて確認できます。画面右下の「＋」ボタンから新規登録画面へ移動できます。

### 新規登録画面
<img src="docs/screenshots/register.png" alt="新規登録画面のスクリーンショット" width="320">

収入・支出の種別、金額、カテゴリ、日付、メモを入力して家計簿に記録できます。カテゴリはボタンをタップするだけで選択できます。

## 主な機能
- 収入・支出の登録・編集・削除
- 月別の収支（収入・支出・差引）の表示と、前後の月への移動
- 週ごとの収支内訳と、今週の収支の表示
- カテゴリ（食費・日用品・交通費・趣味・光熱費・その他）の選択

## 技術スタック

| 分類 | 使用技術 |
|---|---|
| バックエンド | PHP 8.5 / Laravel 13 |
| フロントエンド | Blade / CSS（ビルドツールなし） |
| データベース | MySQL |
| テスト | PHPUnit |
| 本番環境 | ConoHa VPS（Ubuntu 24.04）/ Nginx / PHP-FPM / MySQL 8.0 / Let's Encrypt |

本番環境は、Heroku から VPS に移し、サーバーを自分で構築して公開しています。構成とデプロイの方法は [本番環境とデプロイ](docs/deployment.md) にまとめています。

## ローカルでの動かし方

### 必要なもの
- PHP 8.4 以上（開発では 8.5 を使用）と、拡張機能 `pdo_mysql`・`mbstring`・`xml`・`curl`・`zip`・`bcmath`・`intl`
- Composer
- MySQL

### 手順
1. リポジトリを取ってくる
   ```bash
   git clone https://github.com/kentaro-nakayama/MoneyLog.git
   cd MoneyLog
   ```
2. ライブラリを入れる
   ```bash
   composer install
   ```
3. 設定ファイルを作る
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
   `.env` の `DB_` で始まる行のコメント（`#`）を外し、自分の MySQL のデータベース名・ユーザー名・パスワードに書き換えます。
4. テーブルを作る
   ```bash
   php artisan migrate
   ```
5. 起動する
   ```bash
   php artisan serve
   ```
   ブラウザで http://localhost:8000 を開きます。

## テスト
```bash
php artisan test
```
`tests/Feature/ExampleTest.php` はホーム画面を開くため、MySQL が起動していて `.env` の DB 設定が正しい必要があります。

## 資料
- [要件定義書](docs/requirements.md)：想定ユーザー、機能要件（MoSCoW）、画面構成、データ設計、技術選定の経緯
- [本番環境とデプロイ](docs/deployment.md)：サーバー構成、セキュリティ、デプロイ・バックアップ・運用の方法
