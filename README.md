# アプリケーション名

勤怠管理アプリ

## 概要

Laravelを使用して制作した勤怠管理アプリです。

一般ユーザーは出勤、退勤、休憩登録、勤怠修正申請を行うことができます。

管理者は全ユーザーの勤怠情報確認、修正申請の承認、CSV出力を行うことができます。

## 使用技術

- Laravel 8.83.27
- PHP 8.3.0
- MySQL 8.0.26
- Docker
- Laravel Fortify
- MailHog
- PHPUnit

## 環境構築

### Dockerビルド

1. git@github.com:aiina-yya/kintai-app.git
2. DockerDesktopアプリを立ち上げる
3. 以下のコマンドを実行

```bash
docker-compose up -d --build
```

### Laravel環境構築

1. PHPコンテナへ移動

```bash
docker-compose exec php bash
```

2. Composerインストール

```bash
composer install
```

### データベース設定

.env.exampleを.envに変更。または.envファイルを新しく作成。

.envに以下の環境変数を追加

```env
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=laravel_db
DB_USERNAME=laravel_user
DB_PASSWORD=laravel_pass
```

### アプリケーションキーの作成

```bash
php artisan key:generate
```

### マイグレーション実行

```bash
php artisan migrate
```

### Seeder実行

```bash
php artisan db:seed
```

## URL

### アプリケーション

http://localhost/

### phpMyAdmin

http://localhost:8080

## メール認証機能の設定について

MailHogを使用してメール認証を確認します。  
メール認証機能を利用するには、.env にメール送信設定を行う必要があります。  

本アプリではローカル環境でのメール確認に MailHog を使用しています。  

以下の設定例を .env に記載してください。  

MAIL_MAILER=smtp  
MAIL_HOST=mailhog  
MAIL_PORT=1025  
MAIL_USERNAME=null  
MAIL_PASSWORD=null  
MAIL_ENCRYPTION=null  
MAIL_FROM_ADDRESS=test@example.com  
MAIL_FROM_NAME="${APP_NAME}"  

設定が不足している場合、メール送信時に以下のエラーが発生する可能性があります。  

Cannot send message without a sender address  

メール認証の動作確認を行う場合は、MailHog を起動した状態でユーザー登録を行い、受信した認証メールから認証処理を実行してください。  

### MailHog確認画面

http://localhost:8025

## ER図

![alt text](src/kintai-app.png)

## 主な機能

### 一般ユーザー

- 会員登録
- メール認証
- ログイン
- 出勤登録
- 退勤登録
- 休憩開始・終了
- 勤怠一覧確認
- 勤怠詳細画面
- 勤怠修正申請

### 管理者

- 管理者ログイン
- 全ユーザーの勤怠一覧確認
- 日付別勤怠確認
- スタッフ一覧確認
- スタッフ別勤怠確認
- 勤怠情報修正
- 修正申請の確認・承認
- CSV出力

## テストアカウント

### 一般ユーザー

- User１
メールアドレス：user1@example.com  
パスワード：password  
- User2
メールアドレス：user2@example.com  
パスワード：password  
- Factory生成ユーザー


### 管理者ユーザー

メールアドレス：user3@example.com  
パスワード：password  

## テスト

PHPUnitを用いて以下のテストを実施

### テスト項目

- 会員登録機能
- ログイン機能
- 管理者ログイン機能
- メール認証機能
- 出勤機能
- 退勤機能
- 休憩開始・終了機能
- 勤怠一覧表示機能
- 勤怠詳細表示機能
- 勤怠修正申請機能
- 管理者による勤怠情報修正機能
- 管理者による修正申請承認機能
- スタッフ一覧表示機能
- スタッフ別勤怠一覧表示機能
- CSV出力機能

## 工夫した点

### 複数回の休憩登録に対応したデータ設計

休憩時間を勤怠情報とは別テーブルで管理し、  
一日の勤務に対して複数回の休憩を登録できるようにしました。

AttendanceテーブルとAttendanceBreakテーブルをリレーションで紐づけることで、  
柔軟に休憩情報を管理できる設計にしました。
