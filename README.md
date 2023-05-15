# Prediction game
> Prediction game is a web application that allows users to predict the outcome of football matches and earn points based on the accuracy of their predictions.

## Technologies Used
The following technologies were used to build prediction game:
- PHP 8.2
- MySQL 8
- Symfony 6.2
- Symfony UX
- JavaScript (Ajax)
- Bootstrap 5

## Installation
To install and run prediction game, follow these steps:
## Step 1: Clone the repository and install dependencies
```
git clone https://github.com/danijelsugar/prediction-game
cd prediction-game
composer install
```
## Step 2: Configure the database
Customize the DATABASE_URL inside the .env file to match your database settings. For example:
```
DATABASE_URL="mysql://db_user:db_password@127.0.0.1:3306/db_name?serverVersion=8"
```
## Step 3: Create the database
Create the database using the following command:
```
php bin/console doctrine:database:create
```
## Step 4: Run migrations
To create the database tables/schema, run the following commands:
```
php bin/console make:migration
php bin/console doctrine:migrations:migrate
```
## Step 5: Build assets
Build the assets by running the following commands:
```
npm install
npm run dev
```
## Step 6: Start the web server
Finally, start the web server by running the following command:
```
symfony serve
```

## Usage

## Retrieving Football Data
To retrieve football data, you'll need to create a free account at https://www.football-data.org/ and obtain an API key. After obtaining your API key, enter it under APP_FOOTBALL_API=YOUR_API_KEY in the .env file:

```
APP_FOOTBALL_API=YOUR_API_KEY
```
You can then use the following commands to retrieve the football data:
 - to retrieve all competitions
```
php bin/console app:get:competition
```
 - to retrieve rounds info for each competition
```
php bin/console app:get:competition:round
```
 - to retrieve matches for each round of each competition
```
php bin/console app:get:competition:round:match
```
## User Registration and Verification
In order to successfully register:
 - configure MAILER_DSN and EMAIL_FROM in the .env file:
```
MAILER_DSN=null://null
EMAIL_FROM=your.email@gmail.com
```

## Features
- Login
- Registration with email verification
- Data retrieval from football data API
- Placing predictions for available matches
- Calculating points for each prediction based on the outcome of the match
- Standings

## Credits
- This project was inspired by https://www.prediction-game.com/