# Changelog

このプロジェクトの主な変更点を記録します。

書式は [Keep a Changelog](https://keepachangelog.com/ja/1.1.0/) に、バージョン番号は [セマンティック バージョニング](https://semver.org/lang/ja/) に従います。

<!--
【書き方メモ】(このコメントは表示されません)

■ 分類(使うものだけ書く)
- Added      : 新機能の追加
- Changed    : 既存機能の変更
- Deprecated : 近いうちに削除する予定の機能
- Removed    : 削除した機能
- Fixed      : バグ修正
- Security   : 脆弱性への対応

■ バージョン番号(MAJOR.MINOR.PATCH)
- MAJOR : 互換性がなくなる大きな変更(例:1.x → 2.0.0)
- MINOR : 機能の追加(例:1.2.0 → 1.3.0)
- PATCH : バグ修正・セキュリティ対応(例:1.2.0 → 1.2.1)

■ 運用の流れ
1. 開発中の変更は、まず [Unreleased] に書きためる
2. リリースするときに [Unreleased] の内容を新しいバージョン見出しへ移す
3. git tag vX.Y.Z を打ち、GitHub Releases にも同じ内容を載せる
4. ファイル末尾の比較リンクを更新する

■ 注意
- サーバーのIPアドレス、パス、設定などの内部情報は書かない
- 未対応の脆弱性は、対応が完了するまで書かない
-->

## [Unreleased]

### Added
-

### Changed
-

### Fixed
-

## [0.4.0] - 2026-10-20

### Added
- 集計（月別・週別・日別）と入力チェックのテストを追加
- GitHub Actions による自動テスト（`main` への push とプルリクエストのたびに MySQL 8.0 で実行）

### Changed
- ヘッダーの「MoneyLog」をタップするとホーム画面に戻れるように変更
- スマホでの文字サイズを 16px に統一し、各ボタンと入力フォームに影を追加
- `Transaction` モデルに `casts` を追加し、`date` を Carbon、`amount` を整数として扱うように変更
- README にテスト一覧と技術スタックを追記

## [0.3.0] - 2026-10-18

### Added
- サインアップ機能
- ログイン・ログアウト機能（ログイン状態の保持つき）
- 記録をユーザーごとに分離（他のユーザーの記録は閲覧・編集・削除できない）

### Changed
- 日付タブの幅とメモ欄の文字サイズを調整

### Security
- パスワードをハッシュ化して保存
- ログイン・サインアップに連続試行の制限（1分あたり5回）を追加

## [0.2.0] - 2026-10-16

### Added
- 表示の時間帯を `APP_TIMEZONE` で切り替えられるように対応
- CSS の URL に更新日時を付けてキャッシュバスティング

### Changed
- アプリ全体を寒色系のガラス風スタイルに刷新
- 公開先を Heroku から自前のサーバーへ移行
- `forceScheme` をやめて `trustProxies` に置き換え、プロキシ配下でも `https` の URL を生成するように変更
- README を玄関用に整理し、要件定義書と本番環境の資料を `docs/` に分離
- テストの時間帯を固定し、実行時刻によって結果が変わらないように変更

## [0.1.0] - 2026-10-01

### Added
- 収入・支出の登録・編集・削除
- ホーム画面（その月の収支、週ごとの収支内訳、日別の収支一覧）
- 前後の月への移動
- カテゴリの選択（食費・日用品・交通費・趣味・光熱費・その他）
- 「ホームに戻る」ボタン
- `transactions` テーブルと `Transaction` モデル
- データベースに MySQL を使用

[Unreleased]: https://github.com/kentaro-nakayama/MoneyLog/compare/v0.4.0...HEAD
[0.4.0]: https://github.com/kentaro-nakayama/MoneyLog/compare/v0.3.0...v0.4.0
[0.3.0]: https://github.com/kentaro-nakayama/MoneyLog/compare/v0.2.0...v0.3.0
[0.2.0]: https://github.com/kentaro-nakayama/MoneyLog/compare/v0.1.0...v0.2.0
[0.1.0]: https://github.com/kentaro-nakayama/MoneyLog/releases/tag/v0.1.0
