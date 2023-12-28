## Параметры деплоя
- Существует два окружения для деплоя кода: `production`, `test`  
- Весь код который мержится в мастер попадает в окружение `production`  
- Чтобы залить код на тестовый сервер, необходимо создать новый PR и в комментарии написать: `/deploy-test1`  
- Параметры окружения для `production`, `test` находятся в файлах: `.ops/laravel.production.env`, `.ops/laravel.test.env`  
- Миграции выполняются автоматически перед деплоем кода.
- Мержить код напрямую в master - запрещено. Только через создание нового PR.

## Окружение разработчика
 - Проверить, что порты в системе не заняты [compose.yaml](compose.yaml)
 - Убедиться, что установлена последняя версия Docker.
 - Запускать из под linux или WSL2.
 - Список допустимых команд окружения `make help`

### Запуск окружения:
1. Добавить виртуальный хост в `hosts` файл: `127.0.0.1`
2. Склонировать себе проект.
3. Получить ключ для авторизации на Github, для доступа к приватным докер контейнерам:  
        1. Open [`Github Settings`](https://github.com/settings) -> [`Developer settings`](https://github.com/settings/apps) -> [`Tokens (classic)`](https://github.com/settings/tokens) -> [`Generate New personal access token (classic)`](https://github.com/settings/tokens/new)  
        2.  Set a name to the token, expiration time and choose scope `read:packages` (it is good to keep the token limited with access)  
      ![github-pat.png](./.docs/img/github-pat.png)  
        3. Store the given token in some safe place, because Github will not show it to you again.  
        4. Execute command `docker login ghcr.io -u <Your Github Username>`  and provide your new token.  
    ```sh
    $ docker login ghcr.io -u beeyev
    $ Password:
    $ Login Succeeded
    ```
    > If you need to log out, use command
    > ```sh
    > docker logout ghcr.io
    > ```

4. Из каталога проекта выполнить команду `make up` чтобы запустить необходимые контейнеры.
5. Выполнить команду `make php` чтобы войти в консоль контейнера с PHP (`exit` - выход)
6. Внутри контейнера выполнить `composer install`
7. При необходимости запустить миграции `php artisan migrate`
8. Для сборки assets необходимо войти в контейнер NodeJS - `make nodejs`  
9. Ресурс будет доступен по адресу http://127.0.0.1/


## Сервсиы окружения:
 - phpMyAdmin - http://localhost:8080/
 - MySql - localhost:3306
