<?php
// config/database.php

// Конфигурация подключения к СУБД MySQL
define('DB_HOST', 'localhost');
define('DB_NAME', 'real_estate_manager_db');
define('DB_USER', 'root');
define('DB_PASS', 'root'); // Пароль 'root' по умолчанию для MAMP
define('DB_CHARSET', 'utf8mb4');


try {
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
    
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Выбрасывать исключения при ошибках
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Результаты запросов в виде ассоциативных массивов
        PDO::ATTR_EMULATE_PREPARES   => false,                  // Использовать реальные подготовленные выражения
    ];
    
    // Создаем экземпляр PDO
    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
    
    // Возвращаем объект соединения при подключении через require/include
    return $pdo;
    
} catch (PDOException $e) {
    // В случае сбоя подключения возвращаем JSON-ответ об ошибке 500
    // Это важно, чтобы клиентский Fetch не ломался при парсинге HTML-ошибок PHP
    header('Content-Type: application/json; charset=utf-8');
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Ошибка подключения к базе данных: ' . $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
    exit;
}
