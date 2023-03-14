# Prediction game
> Prediction game is a web application in which users can predict the outcome of a football match and earn points based on how accurate their predictions were.

## Technologies Used
- PHP >= 8.1
- MySQL 8
- Symfony 6.2
- Symfony UX
- JavaScript (Ajax)
- Bootstrap 5

## Instalation
Clone the repository and install the dependencies:
```
$ git clone https://github.com/danijelsugar/prediction-game
$ cd prediction-game
$ composer install
```
Initialize the database:
 - customize DATABASE_URL inside .env
```
DATABASE_URL="mysql://db_user:db_password@127.0.0.1:3306/db_name?serverVersion=8"
```
 - create database
```
$ php bin/console doctrine:database:create
```
  - creating the database tables/schema (migrations)
```
$ php bin/console make:migration
```
 - execute migrations
```
$ php bin/console doctrine:migrations:migrate
```
Football data API key:
 - on https://www.football-data.org/ create free account to get API key 
 - add API key to .env
```
APP_FOOTBALL_API=YOUR_API_KEY
```
Build the assets:
```
$ npm install
$ npm run dev
```
Start the web server:
```
$ symfony serve
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