# drivingDailyReport

This is the Web application for drivers to make writing driving daily reports easier.

Only you have to do is to input data on this application when you start driving or park your car!

運転日報のWEBアプリケーションです。車を発進する時と駐車する時にアプリを立ち上げて必要な情報を入力すれば自動的にサマリーとしてデータがまとまります。

# Dependency
- PHP 7.1
- MySql 5.6.23

# Database

The database consists of two tables.
- driverecord ... assemble every single input one by one.
- recordSummary ... intagrates the data of the same day from driverecord table.

You need to executing two SQL statements which is written below

```SQL
CREATE TABLE `recordSummary` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `shimeicd` int(7) unsigned zerofill NOT NULL,
  `date` date NOT NULL,
  `shussha` int(8) DEFAULT NULL,
  `shukkin` int(10) DEFAULT NULL,
  `taikin` int(10) DEFAULT NULL,
  `kitaku` int(10) DEFAULT NULL,
  `shiyosoko` int(10) DEFAULT NULL,
  `keiro` varchar(100) DEFAULT NULL,
  `sumTimesParkFee` int(10) DEFAULT NULL,
  `sumReparkParkFee` int(10) DEFAULT NULL,
  `sumOtherParkFee` int(10) DEFAULT NULL,
  `kyuyuryo` float DEFAULT NULL,
  `kyuyuryokin` int(10) DEFAULT NULL,
  `kosokuryokin` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
);
```

```SQL
CREATE TABLE `driverecord` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `shimeicd` int(7) unsigned zerofill NOT NULL,
  `date` date NOT NULL,
  `time` time NOT NULL,
  `item` varchar(20) NOT NULL,
  `odometer` int(10) unsigned DEFAULT NULL,
  `place` varchar(40) DEFAULT NULL,
  `park_use` varchar(20) DEFAULT NULL,
  `park_fee` int(11) DEFAULT NULL,
  `highway_use` varchar(20) DEFAULT NULL,
  `highway_fee` int(11) DEFAULT NULL,
  `day_over` varchar(11) DEFAULT NULL,
  `oil_quantity` float DEFAULT NULL,
  `oil_pay` varchar(15) DEFAULT NULL,
  `oil_fee` int(11) DEFAULT NULL,
  `latitude` float DEFAULT NULL,
  `longitude` float DEFAULT NULL,
  PRIMARY KEY (`id`)
);
```

# Setup
- Excute two SQL statements avobe to make tables on your MySql server 
- Upload all files in this repository to the server which you like and set the details to connect to MySql (mysql_login.php)
- Start from `login.php`

I would say that you will be able to use after above setup.

#  GoogleMap image display function

This appication can record your place as latitude and longitude.

If you want to get the google map image where you are, please check here [Geo-location APIs | Google Maps Platform | 
Google Cloud](https://cloud.google.com/maps-platform/)

You need an account and API.

# License
This software is released under the MIT License, see LICENSE.

# Authors
Masataka Oba

# TO DO
Sorry, I know I should reduce a lot of files and divide hierarchy, so I promise to refactor this application someday...
