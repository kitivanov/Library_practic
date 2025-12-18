# Библиотечная система

## Описание проекта

- **Модуль "Современный" (Node.js + MongoDB)**  
  Управляет цифровыми ресурсами: электронные книги, аудиокниги, статьи.  
  Предоставляет REST API для фронтенда, работает как шлюз между UI и другими сервисами.

- **Модуль "Легаси" (PHP + SQLite)**  
  Управляет физическими носителями: бумажные книги, журналы.  
  Предоставляет SOAP API и XML/XSLT для админ-панели.

- **Фронтенд (HTML/CSS/JS)**  
  Единый интерфейс для работы с физическими и цифровыми ресурсами.  
  Через Node.js обращается к обоим модулям.

- **XML/XSLT**  
  Используется «легаси»-системой (PHP) для отдачи данных и преобразования их в HTML для админ-панели

## Запуск

Все параметры системы (хосты, порты, имена баз данных) хранятся в config/config.json.

Для удобства реализованы скрипты init.bat и start.bat. Параметры берутся из конфига.

Достаточно запустить эти скрипты и все заработает.

### init.bat

1. **Инициализация БД PHP**  
   - Выполняется скрипт `legacy-php/scripts/init_db.php`.  
   - Создаётся база данных SQLite `library.db`, создаются таблицы для физических носителей и наполняются тестовыми данными.  

2. **Инициализация npm проекта**  
   - Выполняется `npm init -y`

3. **Установка зависимостей JS**  
   - Выполняется `npm install`.  

4. **Инициализация MongoDB**  
   - Выполняется скрипт `modern/init_scripts/initData.js`.  
   - Создаются коллекции и наполняются тестовыми цифровыми ресурсами (электронные книги, аудиокниги, статьи).  

### start.bat
Просто запускает все необходимые сервисы.

## Доступ к интерфейсу

Фронтенд объединяет работу с обоими модулями через Node.js:

http://localhost:3000

Админка напрямую (также доступна с фронтенда):

http://localhost:8001/admin.php

WSDL:

http://localhost:8001/library.wsdl

**Отчеты и прочее досутпно по кнопкам интерфейса.**

## Инструкция по ручному запуску

**Инициализация PHP базы данных (легаси)**  

```
php legacy-php/scripts/init_db.php
```

**Инициализация Node.js (современный модуль)**  

```
cd modern  
npm init -y
npm install
node init_scripts/initData.js
```

**Запуск сервисов**  

- Запуск PHP сервисов (SOAP и админка):

```
php -S localhost:8000 -t ./legacy-php  
php -S localhost:8001 -t ./legacy-php
```

-  **Запуск Node.js и Фронтенд**:

```
node modern/index.js
```
---

## Конфигурация

Все параметры (порты, хосты, базы данных) хранятся в `config/config.json` и читаются PHP и Node.js модулями.

---

## Зависимости

- Node.js: express, mongoose, body-parser, cors, axios, soap, xml2js, mocha, chai
- PHP: sqlite3, soap, pdo_sqlite, php_xsl.dll

## Структура проекта

---
```
root/
│   init.bat
│   note.txt
│   start-services.js
│   start.bat
│
├── config/
│       config.json
│
├── frontend/
│       app.js
│       index.html
│       style.css
│
├── legacy-php/
│   │   admin.php
│   │   config.php
│   │   library.db
│   │   library.wsdl
│   │   report.php
│   │   report.xsl
│   │   soap-server.php
│   │
│   └── scripts/
│           init_db.php
│           tests.php
│
└── modern/
    │   config.js
    │   index.js
    │   package.json
    │
    ├── downloads/
    ├── init_scripts/
    │       initData.js
    │
    ├── models/
    │       DigitalResource.js
    │       DownloadLog.js
    │
    ├── routes/
    │       digital.js
    │       internal.js
    │       physical.js
    │
    ├── services/
    │       soapClient.js
    │
    └── test/
            api.test.mjs
```