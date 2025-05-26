### #1. Сборка и запуск контейнеров
```bash
  docker-compose up --build
```

### #2. Установка пакетов
```bash
  docker compose exec pizza.shop composer install
```

### #3. Запуск миграций и сидеров для основной БД
```bash
  docker compose exec pizza.shop php artisan migrate:fresh --seed
```

### #4. Создание тестовой БД testing
```bash
  docker compose exec pgsql sh -c 'psql -U "$POSTGRES_USER" -d "$POSTGRES_DB" -c "CREATE DATABASE testing;"'
```

### #5. Проверка списка всех баз данных
```bash
  docker compose exec pgsql psql -U sail -d postgres -c "\l"
```

### #6. Запуск воркер очереди Laravel (процесс, который обрабатывает jobs из очереди с именем "emails")
```bash
  docker-compose exec pizza.shop php artisan queue:work --queue=emails
```

### #7. Запуск тестов
```bash
  docker compose exec pizza.shop php artisan test
```

### #8. Проверка через phpstan (8 уровень проверки)
```bash
  docker compose exec pizza.shop ./vendor/bin/phpstan analyse
```



