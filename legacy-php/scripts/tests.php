<?php
$config = require __DIR__ . '/config.php';

ini_set("soap.wsdl_cache_enabled", "0");
error_reporting(E_ALL);

$soapUrl = sprintf(
    'http://%s:%d/library.wsdl',
    $config['php']['host'],
    $config['php']['port']
);

try {
    $client = new SoapClient($soapUrl);

    echo "=== getBookByInventory ===\n";
    $book = $client->getBookByInventory(['inventory_number'=>'LIB-2024-001']);
    echo "Название: " . $book->title . "\n";
    echo "Автор: " . $book->author . "\n";
    echo "Статус: " . $book->status . "\n\n";

    echo "=== searchBooksByAuthor ===\n";
    $books = $client->searchBooksByAuthor(['author'=>'Хантер']);

    if (!empty($books->book)) {
        $list = is_array($books->book) ? $books->book : [$books->book];
        foreach ($list as $b) {
            echo "{$b->inventory_number}: {$b->title} ({$b->status})\n";
        }
    } else {
        echo "Книг не найдено\n";
    }
    echo "\n";

    echo "=== registerLoan ===\n";
    $loan = $client->registerLoan([
        'inventory_number'=>'LIB-2024-001',
        'reader_card'=>'R-12345'
    ]);
    echo "Сообщение: " . $loan->message . "\n";
    echo "ID выдачи: " . ($loan->loan_id ?? '-') . "\n\n";

    echo "=== returnBook ===\n";
    $return = $client->returnBook(['inventory_number'=>'LIB-2024-001']);
    echo "Сообщение: " . $return->message . "\n";
    echo "ID выдачи: " . ($return->loan_id ?? '-') . "\n\n";

} catch (SoapFault $e) {
    echo "SOAP Ошибка: " . $e->getMessage() . "\n";
}
