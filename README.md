# API Test Solution

Решение тестового задания для собеседования - взаимодействие с API через цепочку HTTP-запросов.

## 📋 Описание

PHP-скрипт выполняет последовательность из трех HTTP-запросов:
1. POST-запрос для получения первой части кода
2. GET-запрос для получения второй части кода  
3. POST-запрос с объединенным кодом для получения финального результата

## 🚀 Быстрый старт

### Требования
- PHP >= 7.0
- Расширение cURL
- Расширение JSON

### Проверка требований
```bash
php -v
php -m | grep curl
php -m | grep json
```

### Установка и запуск
```bash
# Клонируйте репозиторий
git clone https://github.com/Azizbekutkirovich/ICORP-interview.git
cd ICORP-interview

# Запустите скрипт
php index.php
```

## 📁 Структура проекта

```
api-test-solution/
├── index.php          # Основной файл с классом ApiTest и выполнением
├── README.md          # Документация
```

## 💻 Инструкции запуска

### Способ 1: Командная строка
```bash
php index.php
```

### Способ 2: Через веб-сервер
```bash
# Запустите встроенный сервер PHP
php -S localhost:8000

# Откройте в браузере
http://localhost:8000/
```

### Способ 3: На веб-сервере
```bash
# Скопируйте файл в директорию веб-сервера
cp index.php /var/www/html/
# Откройте http://your-domain/index.php
```

## 🔧 Использование

### Прямое выполнение
Просто запустите файл - все готово к работе:
```bash
php index.php
```

### Модификация параметров
Отредактируйте переменные в начале `index.php`:
```php
// Измените endpoint
$client->endpoint = "https://your-api.com/endpoint";

// Измените timeout
$client->timeout = 30;
```

### Интеграция в другой проект
```php
<?php
// Скопируйте класс ApiTest из index.php
// Или подключите файл:
require_once 'index.php';

// Использование без вывода результата
$client = new ApiTest();
$result = $client->execute();
// Обработайте $result по своему усмотрению
?>
```

## 📤 Пример вывода

### Успешное выполнение
```
Array
(
    [status] => success
    [message] => Задание выполнено успешно!
    [code] => xK9mP27qR8nZ
    [timestamp] => 2025-08-27 15:30:45
    [parts] => Array
        (
            [part1] => xK9mP2
            [part2] => 7qR8nZ
        )
)
```

### При ошибке подключения
```
Ошибка: CURL Error: Could not resolve host: test.icorp.uz
```

### При ошибке сервера
```
Ошибка: HTTP Error: 500
```

### При ошибке JSON
```
Ошибка: Decode error: Syntax error | Ответ: <html><body>Server Error</body></html>
```

## 🏗️ Структура кода в index.php

### Класс ApiTest

#### Свойства
```php
public $endpoint = "https://test.icorp.uz/private/interview.php";
public $timeout = 20;
```

#### Основные методы
| Метод | Описание |
|-------|----------|
| `execute()` | Выполняет полную цепочку запросов |
| `firstRequest()` | POST-запрос с msg="Hello", uri="test" |
| `secondRequest($url)` | GET-запрос по полученному URL |
| `finalRequest($code)` | POST-запрос с объединенным кодом |
| `sendRequest($url, $method, $data)` | Универсальный HTTP-клиент |

#### Вспомогательные методы
| Метод | Описание |
|-------|----------|
| `checkingError($condition, $message)` | Проверка условий и генерация исключений |

### Порядок выполнения в index.php
```php
// 1. Определение класса ApiTest
class ApiTest { ... }

// 2. Создание экземпляра
$client = new ApiTest();

// 3. Выполнение запросов
$result = $client->execute();

// 4. Вывод результата
print_r($result);
```

## 🔧 Конфигурация

### Изменение настроек
Отредактируйте свойства класса в `index.php`:
```php
public $endpoint = "https://your-custom-api.com/endpoint";
public $timeout = 30; // увеличить timeout
```

### Изменение payload первого запроса
```php
public function firstRequest() {
    $payload = [
        "msg" => "CustomMessage", // изменить сообщение
        "uri" => "custom"         // изменить URI
    ];
    // ...
}
```

## 🧪 Тестирование

### Простая проверка работы
```bash
# Запустите и проверьте вывод
php index.php

# Должен быть массив с результатом или сообщение об ошибке
```

### Проверка компонентов
```bash
# Проверка cURL
php -r "echo extension_loaded('curl') ? 'cURL OK' : 'cURL missing';"

# Проверка JSON
php -r "echo extension_loaded('json') ? 'JSON OK' : 'JSON missing';"

# Проверка доступности API
curl -I https://test.icorp.uz/private/interview.php
```

## 🐛 Отладка

### Включение детального вывода
Добавьте в начало `index.php`:
```php
<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
```

### Логирование запросов
Добавьте в метод `sendRequest()`:
```php
// Логирование запроса
error_log("API Request: $method $url " . json_encode($data));

// Логирование ответа  
error_log("API Response: $response");
```

### Просмотр логов
```bash
# На Linux/Mac
tail -f /var/log/php_errors.log

# На Windows с XAMPP
tail -f C:\xampp\php\logs\php_error_log
```

## 📋 Возможные ошибки и решения

| Ошибка | Возможная причина | Решение |
|--------|-------------------|---------|
| `Fatal error: Class 'ApiTest' not found` | Синтаксическая ошибка в классе | Проверить синтаксис PHP |
| `CURL Error: Could not connect` | Проблемы с сетью | Проверить интернет-соединение |
| `HTTP Error: 404` | Неверный endpoint | Проверить URL API |
| `Decode error: Syntax error` | Сервер вернул не JSON | Проверить ответ сервера |
| `Неожиданный формат ответа` | API изменил формат | Обновить валидацию полей |

## 🚀 Развертывание

### Локальный веб-сервер
```bash
php -S localhost:8000
# Откройте http://localhost:8000
```

### Apache/Nginx
```bash
# Скопируйте в document root
cp index.php /var/www/html/
chmod 644 /var/www/html/index.php
```

### Docker
```dockerfile
FROM php:8.1-apache
RUN docker-php-ext-install curl json
COPY index.php /var/www/html/
EXPOSE 80
```

```bash
docker build -t api-test .
docker run -p 8080:80 api-test
# Откройте http://localhost:8080
```

## 📝 Алгоритм работы

```
index.php запуск
       ↓
1. POST /interview.php {msg: "Hello", uri: "test"}
       ↓
   Получение {part1, uri}
       ↓  
2. GET {полученный_uri}
       ↓
   Получение {part2}
       ↓
3. POST /interview.php {code: part1+part2}
       ↓
   Финальный результат
       ↓
   print_r($result)
```

## 💡 Особенности реализации

- **Все в одном файле**: класс и исполняемый код в `index.php`
- **Автоматический запуск**: код выполняется сразу при запуске
- **Обработка ошибок**: try-catch в методе `execute()`
- **Универсальный HTTP-клиент**: метод `sendRequest()` для всех типов запросов
- **Валидация ответов**: проверка обязательных полей

## 📄 Лицензия

MIT License - создано для тестового задания

## 👨‍💻 Автор

Создано в рамках технического собеседования
GitHub: [@username](https://github.com/Azizbekutkirovich)

---