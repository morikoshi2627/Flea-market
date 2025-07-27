# フリマアプリ

## 環境構築  

### 画像取り扱いについて  

- 画像は `storage/app/public/images` に保存されています。  
- 画像をブラウザからアクセスするために、`php artisan storage:link`コマンドを実行してから`php artisan migrate --seed`を実行してください。 

### テストユーザー（ダミーユーザー）情報  
1. 出品太郎（認証済み）  
- メールアドレス：seller@example.com  
- パスワード：password123  
  
2. 出品次郎(認証済みのダミー商品出品者です)  
- メールアドレス：seller2@example.com  
- パスワード：password456  
  
※ シーディング後すぐログイン可能です。  

### メール認証について  
- 登録後、自動でメール認証画面に遷移します。  
- MailHogを使って http://localhost:8025 でメールを確認してください。  

### Dockerビルド
1. git clone https://github.com/morikoshi2627/Flea-market.git  
2. cd Flea-market  
3. docker-compose build  
4. docker-compose up -d  
5. docker-compose exec php bash  

### Laravel環境構築
1. composer install  
2. cp .env.example .env  
3. php artisan key:generate  
4. `.env`のDB設定を以下のように修正  

    DB_CONNECTION=mysql  
    DB_HOST=mysql  
    DB_PORT=3306  
    DB_DATABASE=Flea-market_db  
    DB_USERNAME=Flea-market_user  
    DB_PASSWORD=Flea-market_pass  

5. `.env`に下記のメール認証設定を追記してください（Mailhog使用） 

    MAIL_MAILER=smtp
    MAIL_HOST=mailhog
    MAIL_PORT=1025
    MAIL_USERNAME=null
    MAIL_PASSWORD=null
    MAIL_ENCRYPTION=null
    MAIL_FROM_ADDRESS=test@example.com
    MAIL_FROM_NAME="Flea-market"

6. `.env` に Stripe の環境変数を追加してください  
（`.env.example` にすでに含まれているので、`cp .env.example .env` でコピーされます）

    STRIPE_KEY=your_stripe_publishable_key  
    STRIPE_SECRET=your_stripe_secret_key  
    STRIPE_WEBHOOK_SECRET=your_webhook_signing_secret

7. ストレージリンクを作成（画像表示のため）  
    php artisan storage:link  

8. データベースを初期化（マイグレーション＋シーディング）  
    php artisan migrate --seed  

### Stripe 決済連携 & Webhook 受信設定
本アプリでは、商品購入時に Stripe Checkout を利用したクレジットカード/コンビニ決済が可能です。

1.  Stripe CLI の導入（Webhook受信用）  
StripeからのWebhookイベント（決済完了通知など）をローカルで受信するには、Stripe CLI を導入してください。  

- Stripe CLI インストール（Homebrewの場合）  
brew install stripe/stripe-cli/stripe  

- Webhookをローカルに転送  
stripe listen --forward-to localhost:80/webhook/stripe  

2. 決済フローの概要  
- ユーザーが `/purchase/{item_id}` で商品を購入  
- `StripeCheckoutController@checkout` がStripeセッションを作成  
- 決済後、StripeがWebhook経由で `/webhook/stripe` に通知  
- `StripeWebhookController` がイベントを受信し、DBに購入情報を保存  

### 開発環境
- 商品一覧画面（トップ画面）: http://localhost/  
- 会員登録画面： http://localhost/register/   
- メール確認用（Mailhog）： http://localhost:8025/  
- 管理ツール（phpMyAdmin）： http://localhost:8080/  
  - ユーザー名: `Flea-market_user`  
  - パスワード: `Flea-market_pass`  

### 使用技術（実行環境）  
- Laravel 8.83.29  
- PHP 7.4.9-fpm  
- MySQL 8.0.26  
- Docker / Docker Compose  
- Mailhog（メール確認用）: http://localhost:8025/  
- Stripe（決済処理 / Webhook通知受信）  
  - Stripe Checkout API（クレジットカード/コンビニ支払い対応）  
  - Stripe CLI（Webhookイベントのローカル受信）  
  ※ StripeのAPIキーやWebhookシークレットキーは `.env` に個別設定してください。詳細は「Stripe 決済連携 & Webhook受信設定」セクションを参照。  

### ER図  
![ER図](public/images/Flea-market.png)  