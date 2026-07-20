# アプリケーション名  

勤怠管理アプリ  

# Dockerビルド  

1.git clone git@github.com:  
2.DockerDesktopアプリを立ち上げる  
3.docker-compose up -d --build  

# Laravel環境構築

1.docker-compose exec php bash  
2.composer install  
3..env.exampleを.envに変更。または.envファイルを新しく作成。  
4..envに以下の環境変数を追加

DB_CONNECTION=mysql  
DB_HOST=mysql  
DB_PORT=3306  
DB_DATABASE=laravel_db  
DB_USERNAME=laravel_user  
DB_PASSWORD=laravel_pass

5.アプリケーションキーの作成  



