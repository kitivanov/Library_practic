<?php
$config = require __DIR__ . '/config.php';

$sqlitePath = __DIR__ . '/../' . $config['php']['db_name'];

try {
    $pdo = new PDO('sqlite:' . $sqlitePath);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "Подключение к SQLite успешно<br>";


    $pdo->exec("
        CREATE TABLE IF NOT EXISTS physical_books (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            inventory_number TEXT UNIQUE NOT NULL,
            title TEXT NOT NULL,
            author TEXT NOT NULL,
            year INTEGER,
            location TEXT,
            status TEXT CHECK(status IN ('available', 'borrowed', 'lost')) DEFAULT 'available'
        );
    ");

    echo "Таблица physical_books создана<br>";


    $pdo->exec("
        CREATE TABLE IF NOT EXISTS physical_loans (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            book_id INTEGER NOT NULL,
            reader_card TEXT NOT NULL,
            date_taken DATE NOT NULL,
            date_returned DATE,
            FOREIGN KEY (book_id) REFERENCES physical_books(id)
        );
    ");

    echo "Таблица physical_loans создана<br>";

    $pdo->exec("
        INSERT OR IGNORE INTO physical_books
        (inventory_number, title, author, year, location, status)
        VALUES
        ('LIB-2024-001', 'Страх и ненависть в Лас-Вегасе', 'Хантер С. Томпсон', 1971, 'Секция А, стеллаж 1', 'available'),
        ('LIB-2024-002', '1984', 'Джордж Оруэлл', 1949, 'Секция Б, стеллаж 2', 'available'),
        ('LIB-2024-003', 'Автостопом по галактике', 'Дуглас Адамс', 1979, 'Секция В, стеллаж 3', 'available'),
        ('LIB-2024-004', 'Мёртвые души', 'Николай Гоголь', 1842, 'Секция Классики, стеллаж 1', 'available');
    ");

    $pdo->exec("
        INSERT OR IGNORE INTO physical_books
        (inventory_number, title, author, year, location, status)
        VALUES
        ('LIB-2024-001', 'Страх и ненависть в Лас-Вегасе', 'Хантер С. Томпсон', 1971, 'Секция А, стеллаж 1', 'available'),
        ('LIB-2024-002', '1984', 'Джордж Оруэлл', 1949, 'Секция Б, стеллаж 2', 'available'),
        ('LIB-2024-003', 'Автостопом по галактике', 'Дуглас Адамс', 1979, 'Секция В, стеллаж 3', 'available'),
        ('LIB-2024-004', 'Мёртвые души', 'Николай Гоголь', 1842, 'Секция Классики, стеллаж 1', 'available'),
        ('LIB-2024-005', 'Преступление и наказание', 'Фёдор Достоевский', 1866, 'Секция Классики, стеллаж 2', 'available'),
        ('LIB-2024-006', 'Гарри Поттер и философский камень', 'Дж. К. Роулинг', 1997, 'Секция Фэнтези, стеллаж 1', 'available');
    ");

    $pdo->exec("
        INSERT OR IGNORE INTO physical_loans
        (book_id, reader_card, date_taken, date_returned)
        VALUES
        ((SELECT id FROM physical_books WHERE inventory_number='LIB-2024-005'), 'CARD-101', date('now', '-20 days'), NULL),
        ((SELECT id FROM physical_books WHERE inventory_number='LIB-2024-006'), 'CARD-102', date('now', '-25 days'), NULL);
    ");
    
    echo "Тестовые данные добавлены<br>";

    echo "<br><b>База данных успешно инициализирована</b>";

} catch (PDOException $e) {
    echo "Ошибка: " . $e->getMessage();
}
