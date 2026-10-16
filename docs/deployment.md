# 本番環境とデプロイ

MoneyLog は、ConoHa VPS 上に自分で構築した Linux サーバーで公開しています。

- **公開URL**: https://catmoneylog-2438.com/

## 構成

```
ブラウザ
  │  https://catmoneylog-2438.com（Let's Encrypt の証明書、自動更新）
  ▼
DNS（ConoHa DNS）── A レコード ──▶ VPS
  ▼
[ConoHa セキュリティグループ] 22・80・443番だけを通す
  ▼
[fail2ban] SSHのログインに何度も失敗した相手をブロック
  ▼
[UFW] 22・80・443番だけを通す
  ▼
Nginx ──(UNIXソケット)──▶ PHP-FPM 8.5 + Laravel 13 ──▶ MySQL（同じサーバー内のみ）
```

| 項目 | 内容 |
|---|---|
| サーバー | ConoHa VPS（メモリ2GB / CPU 3コア / SSD 100GB） |
| OS | Ubuntu 24.04 LTS（x86_64） |
| Webサーバー | Nginx（`server_tokens off`、HTTPはHTTPSへリダイレクト） |
| PHP | PHP 8.5 + PHP-FPM（`ppa:ondrej/php`） |
| DB | MySQL 8.0（アプリ専用ユーザーは `laravel_app` データベースだけに権限） |
| HTTPS | Let's Encrypt + certbot（90日ごとに自動更新） |
| ドメイン | ConoHa ドメイン + ConoHa DNS |

### PHP 8.5 を PPA から入れている理由
`composer.lock` に含まれる Symfony 8.1 の部品が PHP 8.4.1 以上を必要とするため。Ubuntu 24.04 標準の PHP 8.3 では `composer install` できない。開発環境（Mac）と同じ PHP 8.5 にそろえている。

## セキュリティ
- SSH は鍵でのログインのみ。パスワードでのログインと root でのログインは禁止（`/etc/ssh/sshd_config.d/00-hardening.conf`）
- 作業用ユーザーで入り、管理者の権限が必要なときだけ `sudo` を使う
- セキュリティの更新は unattended-upgrades で毎日自動
- `APP_DEBUG=false`。`.env` は `640`（持ち主と www-data だけが読める）
- Nginx の公開ディレクトリは `public/` だけ。`.` で始まるファイルは見せない（`.well-known` を除く）

## アプリ側の設定

### プロキシとHTTPS（`bootstrap/app.php`）
`trustProxies(at: '*')` で、`X-Forwarded-Proto` などのヘッダーから元のスキーム（http / https）を判断する。ブラウザが偽のヘッダーを送ってきても信用しないように、Nginx で次のように本当の値に上書きしてから PHP-FPM に渡している。

```nginx
fastcgi_param HTTP_X_FORWARDED_FOR   $remote_addr;
fastcgi_param HTTP_X_FORWARDED_PROTO $scheme;
fastcgi_param HTTP_X_FORWARDED_HOST  $host;
fastcgi_param HTTP_X_FORWARDED_PORT  $server_port;
fastcgi_param HTTP_X_FORWARDED_PREFIX "";
```

### 時間帯（`config/app.php`）
`APP_TIMEZONE` で切り替える（本番は `Asia/Tokyo`）。未設定なら UTC。

### CSSのキャッシュ対策（`resources/views/common/head.blade.php`）
CSS の URL に `?v=ファイルの更新日時` を付け、CSS を変えてデプロイしたらブラウザが新しいファイルを取りに来るようにしている。

### 本番の `.env`（値は例。秘密の値は書かない）

```dotenv
APP_ENV=production
APP_DEBUG=false
APP_URL=https://catmoneylog-2438.com
APP_TIMEZONE=Asia/Tokyo
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel_app
DB_USERNAME=laravel_user
DB_PASSWORD='（パスワード管理アプリに保管）'
```

`.env` を変えたら `php artisan optimize` をやり直す（設定をまとめたキャッシュを作り直すため）。

## デプロイ

1. Mac でコードを変えて `main` に push する
2. VPS で `~/deploy-moneylog.sh` を実行する

`git push` だけでは本番は変わらない。デプロイスクリプトは次の順に実行し、途中で失敗したらそこで止まる（`set -euo pipefail`）。終わると、成功・失敗にかかわらずメンテナンス中の表示を解除する。

| 順番 | 内容 |
|---|---|
| 1 | `php artisan down --retry=15`（メンテナンス中の表示） |
| 2 | `git pull --ff-only origin main` |
| 3 | `composer install --no-dev --optimize-autoloader --no-interaction` |
| 4 | `php artisan migrate --force` |
| 5 | `php artisan optimize` |
| 6 | `sudo systemctl reload php8.5-fpm`（OPcache の古いコードを捨てる） |

デプロイ中は数秒〜数十秒メンテナンス中になる。

## バックアップ

| 項目 | 内容 |
|---|---|
| VPS | cron で毎日 3:00 に `~/backup-moneylog.sh` を実行。`mysqldump --single-transaction` を gzip で `~/backups/` に保存し、7日より古いものは削除。記録は `~/backups/backup.log` |
| Mac | `rsync -av moneylog-vps:backups/ ~/Backups/moneylog-vps/` で取ってくる（週に1回）。Mac 側では削除しない |
| 復元 | `php artisan down` → `zcat バックアップ.sql.gz \| mysql --defaults-extra-file=… laravel_app` → `php artisan up`。DB全体がバックアップの時点に戻るので、先に今の状態のバックアップを取る |

DB のパスワードは、`chmod 600` の `~/.moneylog-db.cnf` に置き、コマンドには書かない。

## 運用

| やること | 頻度 |
|---|---|
| `cat ~/backups/backup.log` でバックアップを確認 | 週に1回 |
| Mac にバックアップを取ってくる | 週に1回 |
| ログインして `System restart required` が出ていたら再起動 | 出たとき |
| `sudo fail2ban-client status sshd`、`sudo certbot certificates` | 月に1回 |
| VPS とドメインの支払い・更新期限 | 期限の前 |

### 困ったときに見るログ

| 症状 | 見る場所 |
|---|---|
| 500 エラー | `storage/logs/laravel.log` |
| 502 エラー | `/var/log/nginx/error.log`、`systemctl status php8.5-fpm` |
| ずっと読み込み中 | ConoHa のセキュリティグループと UFW |
| 変えたのに見た目が変わらない | デプロイしたか、ブラウザで再読み込みしたか |
