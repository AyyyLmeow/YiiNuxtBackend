1. После установки на сервер необходимо запустить миграцию: php yii migrate
2. Применить фикстуры: yii fixture/load "*"
3. Для работы RBAC необходимо запустить миграцию: php yii migrate --migrationPath=@yii/rbac/migrations
4. После миграции для RBAC нужно запустить её инициализацию: php yii rbac/init
6. работает на сервере с конфигом apache версии 2.4 для php от 7.2 до 7.4; php версии 7.4
7. вход в аккаунт администратора, логин: admin; пароль: 123456789
8. остальный данные для входа находятся в данных фикстуры user.php (common/fixtures/data)
9. коллекция запросов постмана в файле YiiNuxtBackend.postman_collection.json
