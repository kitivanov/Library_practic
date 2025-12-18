@echo off
setlocal

echo 1. Initialization DB PHP
php ./legacy-php/scripts/init_db.php
if %ERRORLEVEL% neq 0 (
    echo Ошибка при выполнении PHP скрипта!
    pause
    exit /b %ERRORLEVEL%
)

echo 3. Initialization requirements JS
cd modern
cmd /c "npm install || exit /b 0"

cd ..

echo 4. Initialization MongoDB: modern/init_scripts/initData.js
node modern/init_scripts/initData.js
if %ERRORLEVEL% neq 0 (
    echo Error Node script!
    pause
    exit /b %ERRORLEVEL%
)

echo Initialization complete.
pause
