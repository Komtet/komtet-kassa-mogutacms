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
____
## Установка CMS
* Запустить проект:
```shell
make start
```
* Проект будет доступен по адресу: `localhost:8100`
* Настройки подключения к бд MySQL:
```shell
Сервер: mysql
Пользователь: devuser
Пароль: devpass
БД: test_db
```
* После установки магазина, в файл `/php/index.php` первой строкой добавить: `$_SERVER['SERVER_NAME'] = 'localhost:8100';`

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
