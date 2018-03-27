# JSONStorage
[Examination task](https://github.com/krsnv/elama-junior-exam-03-2018) in eLama Juniorlab. Storage of JSON data.

## Функционал
- Загрузка данных в формате JSON
- Валидация JSON (соответствие формату)
- Управление загруженными данными в формате JSON
- Генерация ссылки на загруженные данные с уникальным URL, устойчивым к перебору
- Удаление данных по таймеру или сразу после обращения к ним
- Листинг всех загруженных данных
- Отображение объема отдельно взятых данных
- Отображение общего объема загруженных данных
- Конвертация в XML
- Экспорт или сохранение в .json
- Приватные ссылки на данные защищенные паролем (Basic Auth)

## Окружение
Для запуска необходимо иметь:
- [Docker](https://www.docker.com/)
- [Docker-compose](https://docs.docker.com/compose/)
При их отсутствии, скрипт `run.sh` автоматически их установит.

## Запуск
1. Run in Terminal: `bash run.sh`
2. Go to [localhost](http://localhost)

## Особенности реализации
Приложение написано на PHP с использование Symfony Framework.
В качестве БД выбрана PostgreSQL.
Также используются:
- Doctrine ORM
- Шаблонизатор Twig
- [pg_cron extension](https://github.com/citusdata/pg_cron) для удаления файлов по времени
