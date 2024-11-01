<?php

// Подключение к базе данных
$host = 'localhost'; // адрес сервера
$db = 'flythpool'; // имя базы данных
$userDb = 'root'; // имя пользователя
$pass = ''; // пароль

try {
    $pdo = new PDO("mysql:host=$host;dbname=flythpool;charset=utf8", $userDb, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => "Database connection failed: " . $e->getMessage()]);
    exit;
}

// Данные которые передали через Authorization
$token = getallheaders()['Authorization'] ?? '';

// Переменная , в которую необходимо будет записать пользователя из бд
$user = [];

// Проверяем наличие токена
if (!empty($token)) {
    // Подготовка SQL-запроса для поиска пользователя по токену
    $stmt = $pdo->prepare("SELECT first_name, last_name, phone, document_number FROM users WHERE token = :token");
    $stmt->execute(['token' => $token]);
    
    // Получаем данные пользователя
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Условие выполнится, если переменная $user не пустая
if (!empty($user)) {
    http_response_code(200);
    echo json_encode($user);
} else {
    // Условие выполнится, если переменная $user пустая
    http_response_code(401);
    echo json_encode([
        "error" => [
            "code" => 401,
            "message" => "Unauthorized"
        ]
    ]);
}

?>