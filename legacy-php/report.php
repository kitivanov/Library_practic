<?php
$config = require __DIR__ . '/config.php';

$sqlitePath = __DIR__ . '/' . $config['php']['db_name'];
$pdo = new PDO('sqlite:' . $sqlitePath);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


$stmt = $pdo->query("
    SELECT
        b.inventory_number,
        b.title,
        b.author,
        l.reader_card,
        l.date_taken
    FROM physical_loans l
    JOIN physical_books b ON b.id = l.book_id
    WHERE l.date_returned IS NULL
      AND date(l.date_taken) <= date('now', '-14 day')
");

$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

$xml = new DOMDocument('1.0', 'UTF-8');
$xml->formatOutput = true;

$root = $xml->createElement('overdueBooks');
$xml->appendChild($root);

foreach ($rows as $row) {
    $book = $xml->createElement('book');
    foreach ($row as $key => $value) {
        $book->appendChild($xml->createElement($key, htmlspecialchars($value)));
    }
    $root->appendChild($book);
}

$wantXml = isset($_GET['format']) && $_GET['format'] === 'xml';
$browserHtml = !$wantXml && strpos($_SERVER['HTTP_ACCEPT'] ?? '', 'text/html') !== false;

if ($wantXml || !$browserHtml) {
    header('Content-Type: application/xml; charset=UTF-8');
    echo $xml->saveXML();
} else {
    header('Content-Type: text/html; charset=UTF-8');
    $xsl = new DOMDocument();
    $xsl->load(__DIR__ . '/report.xsl');

    $proc = new XSLTProcessor();
    $proc->importStylesheet($xsl);
    echo $proc->transformToXML($xml);
}
