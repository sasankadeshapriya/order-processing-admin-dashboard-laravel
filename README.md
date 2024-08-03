# Web Application (Admin Dashboard)

A user-friendly admin dashboard for managing users, products, batches, and reports, designed for efficient business management.

![order-processing](https://github.com/user-attachments/assets/d3ecb8dd-a97d-4954-b998-66176a1535d9)


## How to Setup on local

01. Clone the Repository
	```bash
	git clone https://github.com/sasankadeshapriya/order-processing-backend-laravel.git 
	cd order-processing-backend-laravel
	
02. Copy the Example Environment File<br> This will create a new .env file in your project directory.</br>
	```bash
	cp .env.example .env

03. Update the `.env` File<br>
Open the .env file and configure the settings according to your environment.<br>
GOOGLE_MAPS_API_KEY, API_URL, DB_DATABASE, DB_USERNAME, DB_PASSWORD <br>
Here's a sample configuration (Make sure to replace placeholder values with your own configuration details.
):
	```bash
	APP_NAME=Laravel
	APP_ENV=local
	APP_KEY=base64:cP/7cLh2aVWOwPgElwOarscmtjxuNqXxpvhkgtJoOYM=
	APP_DEBUG=true
	APP_TIMEZONE=UTC
	APP_URL=http://localhost

	APP_LOCALE=en
	APP_FALLBACK_LOCALE=en
	APP_FAKER_LOCALE=en_US

	APP_MAINTENANCE_DRIVER=file
	APP_MAINTENANCE_STORE=database

	BCRYPT_ROUNDS=12

	LOG_CHANNEL=stack
	LOG_STACK=single
	LOG_DEPRECATIONS_CHANNEL=null
	LOG_LEVEL=debug

	DB_CONNECTION=mysql
	DB_HOST=127.0.0.1
	DB_PORT=3306
	DB_DATABASE=dashboard
	DB_USERNAME=root
	DB_PASSWORD=

	SESSION_DRIVER=database
	SESSION_LIFETIME=120
	SESSION_ENCRYPT=false
	SESSION_PATH=/
	SESSION_DOMAIN=null
	SESSION_EXPIRE_ON_CLOSE=true

	BROADCAST_CONNECTION=log
	FILESYSTEM_DISK=local
	QUEUE_CONNECTION=database

	CACHE_STORE=database
	CACHE_PREFIX=

	MEMCACHED_HOST=127.0.0.1

	REDIS_CLIENT=phpredis
	REDIS_HOST=127.0.0.1
	REDIS_PASSWORD=null
	REDIS_PORT=6379

	MAIL_MAILER=log
	MAIL_HOST=127.0.0.1
	MAIL_PORT=2525
	MAIL_USERNAME=null
	MAIL_PASSWORD=null
	MAIL_ENCRYPTION=null
	MAIL_FROM_ADDRESS="hello@example.com"
	MAIL_FROM_NAME="${APP_NAME}"

	AWS_ACCESS_KEY_ID=
	AWS_SECRET_ACCESS_KEY=
	AWS_DEFAULT_REGION=us-east-1
	AWS_BUCKET=
	AWS_USE_PATH_STYLE_ENDPOINT=false

	VITE_APP_NAME="${APP_NAME}"
	GOOGLE_MAPS_API_KEY="your_key"

	API_URL=http://api.mysite.com


04. Install Dependencies
	```bash
	composer install
    php artisan key:generate

05. Run Migrations
	```bash
	php artisan migrate

06. Serve the Application <br> The application will be available at http://localhost:8000 by default.
	```bash
	php artisan serve

## Related Repositories
```bash
git clone https://github.com/sasankadeshapriya/order-processing-api-nodejs.git
git clone https://github.com/sasankadeshapriya/order-processing-app-flutter.git
