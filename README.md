# フリマアプリ

## 環境構築  

### 画像取り扱いについて  

- 画像は `storage/app/public/images` に保存されています。  
- 画像をブラウザからアクセスするために、`php artisan storage:link`コマンドを実行してから`php artisan migrate --seed`を実行してください。 

### テストユーザー（ダミーユーザー）情報  
本アプリには、確認用の固定アカウントを用意しています。  
以下のメールアドレスとパスワードでログインしてください。  

1. 出品太郎（認証済み/1~5の商品を出品）  
- メールアドレス：seller@example.com  
- パスワード：password123  
  
2. 出品次郎(認証済み/6~10の商品を出品)  
- メールアドレス：seller2@example.com  
- パスワード：password456  
  
3. 出品三郎(認証済み/紐付けなし)  
- メールアドレス：seller3@example.com  
- パスワード：password789  

※ 上記アカウントは、php artisan migrate:fresh --seed を実行した場合に再作成されます。パスワードは暗号化（ハッシュ化）されていますが、上記で記載した平文パスワードで即ログイン可能です。    

### メール認証について  
- 新規登録ユーザーはメール認証必須です。  
- 上記固定ユーザー以外のダミーユーザーは通常どおり email_verified_at = null で作成されます。  
- 新規登録後は、自動でメール認証画面に誘導され、認証完了後に商品一覧へ遷移に遷移します。  
- メール送信の確認には MailHog というツールを使用しています。
- MailHog は docker-compose で自動的に起動します。追加の設定や会員登録は不要です。  
- DockerでMailHogを起動 (docker-compose up -d mailhog)した状態で、ブラウザから [http://localhost:8025](http://localhost:8025) にアクセスすると送信されたメールを確認できます。

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

### テストの実行方法
本アプリケーションには、フィーチャテストを用意しています。  
以下のコマンドで全テストを実行できます。  

  `php artisan test`  

テストケースには以下が含まれています：  
- 会員登録・ログイン認証機能のテスト  
- 商品一覧と詳細表示のテスト  
- マイリスト一覧・商品検索機能・いいね機能・コメント送信機能のテスト   
- 商品購入・支払い機能のテスト  
- メール認証機能のテスト  

### ER図  
![ER図](public/images/Flea-market.png)  


## ※今回追加実装した機能一覧(補足)  

### 1. 取引チャット機能（メッセージ送受信）  
- 出品者 & 購入者が商品ごとにチャット可能  
- 投稿時のバリデーションはformrequest（ChatRequest）で実装  
- 画像送信・編集・削除・既読管理を実装  
- 取引中の商品一覧は新規メッセージが来た順に表示  
- `session(... )` を利用し、「その他の取引」で別画面へ遷移する際、送信前のメッセージを一時保存（PHPのみでは「画面遷移時に入力値を自動送信する」ことができないため、隣接ページへの遷移時に hidden input に値をセットする処理を最小限の JavaScript で補っています）  

### 2. 取引評価機能（2者間レビュー）  
- 購入者が「取引完了」ボタン → 評価モーダルを表示  
- 購入者が評価後、出品者側でも評価モーダルが自動表示  
- 評価済み商品の取引完了ボタンを押下 → 商品一覧へ遷移
- 双方が評価完了すると「取引終了」とみなし、  
  マイページの「取引中の商品」から除外  

### 3. プロフィールでの評価平均表示  
- 評価が存在するユーザーのみ星5段階で平均を表示（評価がない場合は非表示）    
- 平均値は四捨五入して整数化  

### 4. 取引完了メール通知（Mailhog）  
- 購入者が評価したタイミングで出品者へメール送信（Blade テンプレート使用 / Mailable クラスで実装）  
- 送信内容：  
  - 出品者名  
  - 購入者名  
  - 商品名  
  - 評価依頼メッセージ  
- Mailhog（http://localhost:8025）で確認可能  