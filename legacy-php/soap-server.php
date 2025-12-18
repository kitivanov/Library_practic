<?php
ini_set("soap.wsdl_cache_enabled", "0");
ini_set("display_errors", 1);
error_reporting(E_ALL);


class LibraryService {
    protected $pdo;

    public function __construct() {
        $config = require __DIR__ . '/config.php';
        $sqlitePath = 'sqlite:' . __DIR__ . '/' . $config['php']['db_name'];
        $this->pdo = new PDO($sqlitePath);
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    public function getBookByInventory($params) {
        $inventory_number = $params->inventory_number;

        $stmt = $this->pdo->prepare("SELECT * FROM physical_books WHERE inventory_number = ?");
        $stmt->execute([$inventory_number]);
        $book = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$book) {
            return ['title'=>'', 'author'=>'', 'status'=>'not found'];
        }

        return [
            'title' => $book['title'],
            'author' => $book['author'],
            'status' => $book['status']
        ];
    }

    public function searchBooksByAuthor($params) {
        $author = $params->author;

        $stmt = $this->pdo->prepare("SELECT * FROM physical_books WHERE author LIKE ?");
        $stmt->execute(['%' . $author . '%']);
        $books = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return ['book' => $books];
    }

    public function registerLoan($params) {
        $inventory_number = $params->inventory_number;
        $reader_card = $params->reader_card;

        $stmt = $this->pdo->prepare("SELECT * FROM physical_books WHERE inventory_number = ?");
        $stmt->execute([$inventory_number]);
        $book = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$book) {
            return ['success'=>false, 'message'=>'Книга не найдена', 'loan_id'=>null];
        }

        if ($book['status'] !== 'available') {
            return ['success'=>false, 'message'=>'Книга уже выдана или недоступна', 'loan_id'=>null];
        }

        $stmt = $this->pdo->prepare("INSERT INTO physical_loans (book_id, reader_card, date_taken) VALUES (?, ?, date('now'))");
        $stmt->execute([$book['id'], $reader_card]);
        $loan_id = $this->pdo->lastInsertId();

        $stmt = $this->pdo->prepare("UPDATE physical_books SET status='borrowed' WHERE id=?");
        $stmt->execute([$book['id']]);

        return [
            'success'=>true,
            'message'=>"Книга успешно выдана читателю $reader_card",
            'loan_id'=> (int)$loan_id
        ];
    }

    public function returnBook($params) {
        $inventory_number = $params->inventory_number;

        $stmt = $this->pdo->prepare("SELECT * FROM physical_books WHERE inventory_number = ?");
        $stmt->execute([$inventory_number]);
        $book = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$book) {
            return ['success'=>false, 'message'=>'Книга не найдена', 'loan_id'=>null];
        }

        $stmt = $this->pdo->prepare("SELECT * FROM physical_loans WHERE book_id=? AND date_returned IS NULL");
        $stmt->execute([$book['id']]);
        $loan = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$loan) {
            return ['success'=>false, 'message'=>'Нет активной выдачи для этой книги', 'loan_id'=>null];
        }

        $stmt = $this->pdo->prepare("UPDATE physical_loans SET date_returned=date('now') WHERE id=?");
        $stmt->execute([$loan['id']]);

        $stmt = $this->pdo->prepare("UPDATE physical_books SET status='available' WHERE id=?");
        $stmt->execute([$book['id']]);

        return [
            'success'=>true,
            'message'=>'Книга успешно возвращена',
            'loan_id'=> (int)$loan['id']
        ];
    }

}

$server = new SoapServer(__DIR__ . '/library.wsdl');
$server->setClass('LibraryService');
$server->handle();
