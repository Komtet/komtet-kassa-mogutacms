# Модуль КОМТЕТ Кассы для Moguta CMS

## Запуск проекта
* Склонировать репозиторий
* Создать в корневом каталоге папки php и data
* Скопировать файл index.php в папку /php
* Запустить сборку проекта:
```shell
make build 
```   
* Установить права на папку php:
```shell
sudo chmod -R 777 php
```
* Добавить запись в /etc/hosts
```sh
127.0.0.1       moguta-kassa.localhost.com
```
* Добавить nginx конфиги
```sh
cd /etc/nginx/sites-enabled
sudo ln -s [путь_до_проекта]/komtet-kassa-mogutacms/nginx.cfg /etc/nginx/sites-enabled/moguta.cfg
sudo nginx -t
sudo nginx -s reload
```
____
## Установка CMS
* Запустить проект:
```shell
make start
```
* Проект будет доступен по адресу: `http://moguta-kassa.localhost.com/`
* Настройки подключения к бд MySQL:
```shell
Сервер: mysql
Пользователь: devuser
Пароль: devpass
БД: test_db
```

____
## Доступные команды из Makefile
* Собрать проект
```shell
make build
```
* Запустить проект
```shell
make start
```
* Остановить проект
```shell
make stop
```
