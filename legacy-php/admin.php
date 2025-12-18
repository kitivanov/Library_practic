<?php
ini_set("soap.wsdl_cache_enabled", "0");
ini_set("display_errors", 1);
error_reporting(E_ALL);

$config = require __DIR__ . '/config.php';

ini_set("soap.wsdl_cache_enabled", "0");
ini_set("display_errors", 1);
error_reporting(E_ALL);

$soapUrl = sprintf(
    'http://%s:%d/soap-server.php?wsdl',
    $config['php']['host'],
    $config['php']['port']
);

// $soapUrl = 'http://localhost:8000/soap-server.php?wsdl';
$client = new SoapClient($soapUrl);

$message = '';
$result_table = [];

// Обработка выдачи и возврата
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $inventory_number = $_POST['inventory_number'] ?? '';
    $reader_card = $_POST['reader_card'] ?? '';
    $action = $_POST['action'] ?? '';

    if ($action === 'loan') {
        $res = $client->registerLoan(['inventory_number'=>$inventory_number,'reader_card'=>$reader_card]);
        $message = $res->message ?? 'Ошибка';
    } elseif ($action === 'return') {
        $res = $client->returnBook(['inventory_number'=>$inventory_number]);
        $message = $res->message ?? 'Ошибка';
    } elseif ($action === 'search') {
        $author = $_POST['author'] ?? '';
        $res = $client->searchBooksByAuthor(['author'=>$author]);
        if (isset($res->book)) {
            $result_table = is_array($res->book) ? $res->book : [$res->book];
        }
    }
}

// Список всех книг
if (empty($result_table)) {
    $res = $client->searchBooksByAuthor(['author'=>'']);
    if (isset($res->book)) {
        $result_table = is_array($res->book) ? $res->book : [$res->book];
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
<meta charset="UTF-8">
<title>Админка библиотеки</title>
<style>
body { font-family: Arial, sans-serif; background: #f4f4f9; margin:0; padding:0; }
.container { max-width: 1000px; margin: 20px auto; padding: 20px; background: #fff; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);}
h1 { text-align: center; }
input, button { padding: 4px 8px; margin: 2px; border-radius: 4px; border: 1px solid #ccc; }
button { background: #007BFF; color: #fff; border: none; cursor: pointer; }
button:hover { background: #0056b3; }
table { width: 100%; border-collapse: collapse; margin-top: 10px; }
th, td { padding: 8px; border: 1px solid #ccc; text-align: left; }
th { background: #007BFF; color: #fff; }
.message { padding: 10px; background: #d4edda; color: #155724; margin-bottom: 10px; border-radius: 4px; }
form.inline { display:inline; }
a.button { display: inline-block; padding: 6px 10px; background: #6c757d; color: #fff; text-decoration: none; border-radius: 4px; margin-right:5px;}
a.button:hover { background: #5a6268; }
a.button.xml { background: #28a745; }
a.button.xml:hover { background: #218838; }
</style>
</head>
<body>
<div class="container">
<h1>Админка библиотеки</h1>

<?php if($message): ?>
    <div class="message"><?=htmlspecialchars($message)?></div>
<?php endif; ?>

<!-- Кнопки отчетов -->
<div style="margin-bottom: 20px;">
    <strong>Отчеты:</strong>
    <a class="button" href="report.php?type=overdue" target="_blank">Просроченные книги (HTML)</a>
    <a class="button xml" href="report.php?type=overdue&format=xml" target="_blank">Просроченные книги (XML)</a>
</div>

<!-- Поиск по автору -->
<form method="post" action="" style="margin-bottom:20px;">
    <strong>Поиск книг по автору:</strong>
    <input type="text" name="author" placeholder="Автор" />
    <button type="submit" name="action" value="search">Поиск</button>
    <a class="button" href="admin.php">Домой</a>
</form>

<!-- Таблица книг -->
<table>
<tr>
    <th>Инвентарный номер</th>
    <th>Название</th>
    <th>Автор</th>
    <th>Год</th>
    <th>Местоположение</th>
    <th>Статус</th>
    <th>Выдать</th>
</tr>
<?php foreach($result_table as $book): ?>
<tr>
    <td><?= htmlspecialchars($book->inventory_number ?? '') ?></td>
    <td><?= htmlspecialchars($book->title ?? '') ?></td>
    <td><?= htmlspecialchars($book->author ?? '') ?></td>
    <td><?= htmlspecialchars($book->year ?? '') ?></td>
    <td><?= htmlspecialchars($book->location ?? '') ?></td>
    <td><?= htmlspecialchars($book->status ?? '') ?></td>
    <td>
        <?php if(($book->status ?? '') === 'available'): ?>
        <form method="post" class="inline">
            <input type="hidden" name="inventory_number" value="<?= htmlspecialchars($book->inventory_number) ?>" />
            <input type="text" name="reader_card" placeholder="Читательский билет" required />
            <button type="submit" name="action" value="loan">Выдать</button>
        </form>
        <?php else: ?>
            <em>Недоступна</em>
        <?php endif; ?>
        <?php if(($book->status ?? '') === 'borrowed'): ?>
        <form method="post" class="inline">
            <input type="hidden" name="inventory_number" value="<?= htmlspecialchars($book->inventory_number) ?>" />
            <button type="submit" name="action" value="return">Вернуть</button>
        </form>
        <?php endif; ?>
    </td>
</tr>
<?php endforeach; ?>
</table>

</div>
</body>
</html>
