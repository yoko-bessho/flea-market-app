# flea-market-app(フリマアプリ)

## 環境構築

### Dockerビルド
1.  `git clone `
2.  DockerDesktopアプリ立ち上げる. 
3.  `docker compose up -d --build`

   MacのM1・M2チップのPCの場合、no matching manifest for linux/arm64/v8 in the manifest list entriesのメッセージが表示されビルドができないことがあります。 エラーが発生する場合は、docker-compose.ymlファイルの「mysql」と「myadmin」内に「platform」の項目を追加で記載してください

   ```
   mysql:
   platform: linux/x86_64   //(この文追加)
   image: mysql:8.0.26
   ```
   ```
   phpmyadmin:
   platform: linux/x86_64   //(この文追加)
   image: phpmyadmin/phpmyadmin
   ```

### Laravel環境構築
1.  `docker-compose exec php bash`
2.  `composer install`
3.  「.env.example」ファイルをコピーして,「.env」へ名称変更
   `cp .env.example .env`


4.   .envに以下の環境変数を追加. 
   ```
   DB_CONNECTION=mysql
   DB_HOST=mysql
   DB_PORT=3306
   DB_DATABASE=laravel_db
   DB_USERNAME=laravel_user
   DB_PASSWORD=laravel_pass
   ```

5. アプリケーションキーの作成. 
   `php artisan key:generate`

6. マイグレーションの実行. 
   `php artisan (ファイル名入れる )`

7. シーディングの実行. 
   `php artisan db:seed`

8. シンボリックリンク作成. 
   `php artisan storage:link`


### 使用技術（実行環境）
　・ PHP 7.4.9. 
　・ Laravel Framework 8.83.8. 
　・ mysql  Ver 15.1. 

### テーブル設計
| テーブル名           | カラム名                 | 型                          | PRIMARY KEY | UNIQUE KEY                | NOT NULL | FOREIGN KEY    |
|-----------------|----------------------|----------------------------|-------------|---------------------------|----------|----------------|
| users           |                      |                            |             |                           |          |                |
|                 | id                   | unsigned bigint            | ○           |                           | ○        |                |
|                 | name                 | varchar(255)               |             | ○                         | ○        |                |
|                 | email                | varchar(255)               |             | ○                         | ○        |                |
|                 | password             | varchar(255)               |             | ○                         | ○        |                |
|                 | profile_image        | varchar(255)               |             |                           |          |                |
|                 | postal_code          | varchar(8)                 |             |                           | ○        |                |
|                 | address              | varchar(255)               |             |                           | ○        |                |
|                 | building             | varchar(255)               |             |                           |          |                |
|                 | created_at           | timestamp                  |             |                           |          |                |
|                 | updated_at           | timestamp                  |             |                           |          |                |
|                 | deleted_at           | timestamp                  |             |                           |          |                |
|                 |                      |                            |             |                           |          |                |
|                 |                      |                            |             |                           |          |                |
| items           |                      |                            |             |                           |          |                |
|                 | id                   | unsigned bigint            | ○           |                           | ○        |                |
|                 | user_id              | unsigned bigint            |             |                           | ○        | users(id)      |
|                 | title                | varchar(255)               |             |                           | ○        |                |
|                 | description          | text                       |             |                           | ○        |                |
|                 | price                | unsignedsmallInteger       |             |                           | ○        |                |
|                 | category_id          | unsigned bigint            |             |                           | ○        | categories(id) |
|                 | is_sold              | varchar(20)                |             |                           | ○        |                |
|                 | image_path           | varchar(255)               |             |                           | ○        |                |
|                 | created_at           | timestamp                  |             |                           |          |                |
|                 | updated_at           | timestamp                  |             |                           |          |                |
|                 | deleted_at           | timestamp                  |             |                           |          |                |
|                 |                      |                            |             |                           |          |                |
|                 |                      |                            |             |                           |          |                |
| categories      |                      |                            |             |                           |          |                |
|                 | id                   | unsigned bigint            | ○           |                           | ○        |                |
|                 | name                 | varchar(255)               |             | ○                         | ○        |                |
|                 | created_at           | timestamp                  |             |                           |          |                |
|                 |                      |                            |             |                           |          |                |
|                 |                      |                            |             |                           |          |                |
| item_categories |                      |                            |             |                           |          |                |
|                 | id                   | unsigned bigint            | ○           |                           | ○        |                |
|                 | item_id              | unsigned bigint            |             |                           | ○        | items(id)      |
|                 | category_id          | unsigned bigint            |             |                           | ○        | categories(id) |
|                 |                      |                            |             |                           |          |                |
|                 |                      |                            |             |                           |          |                |
| likes           |                      |                            |             |                           |          |                |
|                 | id                   | unsigned bigint            | ○           |                           | ○        |                |
|                 | user_id              | unsigned bigint            |             | UNIQUE (user_id, item_id) | ○        | users(id)      |
|                 | item_id              | unsigned bigint            |             | UNIQUE (user_id, item_id) | ○        | items(id)      |
|                 | created_at           | timestamp                  |             |                           |          |                |
|                 |                      |                            |             |                           |          |                |
|                 |                      |                            |             |                           |          |                |
| comments        |                      |                            |             |                           |          |                |
|                 | id                   | unsigned bigint            | ○           |                           | ○        |                |
|                 | user_id              | unsigned bigint            |             |                           | ○        | users(id)      |
|                 | item_id              | unsigned bigint            |             |                           | ○        | items(id)      |
|                 | text                 | text                       |             |                           | ○        |                |
|                 | created_at           | timestamp                  |             |                           |          |                |
|                 | deleted_at           | timestamp                  |             |                           |          |                |
|                 |                      |                            |             |                           |          |                |
|                 |                      |                            |             |                           |          |                |
| purchases       |                      |                            |             |                           |          |                |
|                 | id                   | unsigned bigint            | ○           |                           | ○        |                |
|                 | item_id              | unsigned bigint            |             |                           | ○        | items(id)      |
|                 | buyer_id             | unsigned bigint            |             |                           | ○        | users(id)      |
|                 | shipping_postal_code | varchar(8)                 |             |                           | ○        |                |
|                 | shipping_address     | varchar(255)               |             |                           | ○        |                |
|                 | shipping_building    | varchar(255)               |             |                           |          |                |
|                 | payment_method       | varchar(20)+enum(laravel側) |             |                           | ○        |                |
|                 | payment_status       | varchar(20)+enum(laravel側) |             |                           | ○        |                |
|                 | paid_at              | timestamp                  |             |                           |          |                |
|                 | order_status         | varchar(20)+enum(laravel側) |             |                           | ○        |                |
|                 | created_at           | timestamp                  |             |                           |          |                |
|                 | updated_at           | timestamp                  |						

### ER図

![ER図](er.svg)


### URL
　・ 開発環境：http://localhost/
　・ phpMyadmin：http://localhost:8080/
