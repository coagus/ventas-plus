# Ventas+

Sistema base de ventas

## MySql en Docker

docker run -d -p 3306:3306 --name mysql -e MYSQL_ROOT_PASSWORD=Adm1ns -e MYSQL_DATABASE=sales mysql

## Php en Docker

docker run -d -p 80:80 --name php_sales -v "$PWD":/var/www/html coagus/lamp:0.5

## DUPM

docker exec -it mysql /bin/bash
cd home
mysqldump --no-data -u root -pAdm1ns sales > sales_structure.sql
docker cp mysql:/home/sales_structure.sql ./rsc/sales_structure.sql
