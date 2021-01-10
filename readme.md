# Backtest - Laravel Demo App

Фоновый обработчик

Для работы интеграций необходимо выполнять задачи в фоне.
Например, нам пришла заявка - мы должны отправить ее на какой-то внешний сервис.

Есть тестовая библиотека с бизнес-логикой. Две функции: Account - processPayment и Amocrm - sendLead.
Список задач, которые нужно выполнить в файле tasks.json

Необходимо написать обработчик, который будет выполнять задачи, указанные в файле.
Важно, чтобы задачи выполнялись параллельно в несколько потоков (N - задается в конфиге).
Можно использовать любые технологии (очереди и тп).


## clone project
```
git clone remote_dd
```

### install composer
```
composer install
```

## add autoload files
```
composer dumpautoload
```

## copy .env file and setup how you want  or leave it as it was
```
cp .env.example .env
```

### in .env set task stream count for running tasks in background
```.dotenv
STREAM_NUMBER=3
```

### run migrations with tasks seeder
```
php artisan migrate --seed
```

# run scheduler to process task
```php
php artisan schedule:run
```

# Or simply add SUPERVISOR 
### go to supervisor path
```
cd  /etc/supervisor
```

### in order to see status
```
supervisorctl
```

### goto in supervisor.conf in root path of this project, change paths according to your project
### and past to supervisor
```
cd conf.d/
```
