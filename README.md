# Ventas+

Sistema base de ventas

## MySql en Docker

docker run -d \
-p 3306:3306 \
--name mysql \
-e MYSQL_ROOT_PASSWORD=Adm1ns \
-e MYSQL_DATABASE=ventas \
mysql

## Php en Docker

docker run -d \
-p 80:80 \
--name php_ventas \
-v "$PWD":/var/www/html \
php:7.4-apache
