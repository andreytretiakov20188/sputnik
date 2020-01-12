- Имя: Шилов Андрей andrey.dry7.shilov@yandex.ru
- Сервис выполнен в виде консольной команды. Логика вынесена в сервисы и модели.
- Использовал Laravel т.к. удобней всего с ним работать. + Guzzle для работы с сетью.
- Установка: ```docker-compose up --b```
- Тесты: ```docker-compose run --rm phpunit``` (Coverage report будет в /storage/coverage)
- Запуск системы: ```docker-compose run --rm php php artisan sputnik:control-panel``` (ENV можно указать в .env файле в корне проекта, либо в docker-compose.yaml)
- Трудозатраты оценивал в 10 часов, реально вышло >30
- Используемые версии ПО: Ubuntu 18.04, Docker 18.06, Docker Compose 1.23.2, PHP 7.4
