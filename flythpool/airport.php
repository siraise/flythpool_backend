<?php
// Подключение к базе данных
$pdo = new PDO('mysql:host=localhost;dbname=flythpool;charset=utf8', 'root', null, [
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
]);

// Получение параметра запроса
$query = isset($_GET['query']) ? trim($_GET['query']) : null;

// Проверка: передан ли параметр query
if (!$query) {
    http_response_code(422);
    echo json_encode([
        "error" => [
            "code" => 422,
            "message" => "Validation error",
            "errors" => [
                "query" => ["query is required"]
            ]
        ]
    ]);
    exit;
}

// Приведение параметра к нижнему регистру для нечувствительности к регистру
$queryLower = strtolower($query);

// Подготовка и выполнение SQL-запроса
$stmt = $pdo->prepare("SELECT name, iata FROM airports WHERE LOWER(name) LIKE :query OR LOWER(city) LIKE :query OR LOWER(iata) = :query_exact");
$stmt->execute([
    'query' => "%$queryLower%",
    'query_exact' => $queryLower
]);

// Получение результатов
$airports = $stmt->fetchAll();

// Формирование и отправка ответа
http_response_code(200);
header('Content-Type: application/json');
echo json_encode([
    "data" => [
        "items" => $airports
    ]
]);
