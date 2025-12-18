@echo off
setlocal

echo 1. Инициализация БД PHP
php ./legacy-php/scripts/init_db.php
if %ERRORLEVEL% neq 0 (
    echo Ошибка при выполнении PHP скрипта!
    pause
    exit /b %ERRORLEVEL%
)

echo 2. Инициализация npm проекта
cd modern
npm init -y
if %ERRORLEVEL% neq 0 (
    echo Ошибка при npm init!
    pause
    exit /b %ERRORLEVEL%
)

echo 3. Установка зависимостей JS
npm install
if %ERRORLEVEL% neq 0 (
    echo Ошибка при установке npm пакетов!
    pause
    exit /b %ERRORLEVEL%
)

cd ..

echo 4. Инициализация MongoDB: modern/init_scripts/initData.js
node modern/init_scripts/initData.js
if %ERRORLEVEL% neq 0 (
    echo Ошибка при выполнении Node скрипта!
    pause
    exit /b %ERRORLEVEL%
)

echo Все команды выполнены успешно!
pause
