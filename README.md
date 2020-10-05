[to deployed front-end website to interact with api](https://www.google.com)
### Api routes for deployed project
- get document list `http://toons-csv.serverpi.ddns.me/csv/index`
- get specific document data `http://toons-csv.serverpi.ddns.me/csv/show/?name=(document name here)`
- post new document `http://toons-csv.serverpi.ddns.me/csv/create` with file and delimeter
- post edited file `http://toons-csv.serverpi.ddns.me/csv/edit` with data and tableName


# Instalation

- run `composer install`
- create your database and insert this table inside : 
```CREATE TABLE `tables_list` (
  `id` int(6) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `new` varchar(100) NOT NULL,
  `old` varchar(100) DEFAULT NULL );```
- copy `.env.example` and rename it to `.env`
- in .env file enter your database name and credentials
- if leave app_env to dev if using program such as xampp to run project, change if using virtualisation to run project