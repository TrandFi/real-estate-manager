<?php
// api/bootstrap.php

// 1. Включаем отображение ошибок для отладки во время разработки
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// 2. Инициализируем сессию
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 3. Заголовки CORS и типа контента (JSON)
header("Content-Type: application/json; charset=utf-8");
header("Access-Control-Allow-Origin: *"); // В продакшене рекомендуется заменить на конкретный домен
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

// Обработка Preflight OPTIONS запросов
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// 4. Подключение к базе данных
$db = require_once __DIR__ . '/../config/database.php';

// ================= ВСПОМОГАТЕЛЬНЫЕ ФУНКЦИИ API =================

/**
 * Отправляет JSON ответ клиенту и завершает выполнение скрипта.
 *
 * @param bool $success Статус операции
 * @param string $message Сообщение
 * @param array $data Дополнительные данные
 * @param int $statusCode HTTP-код ответа
 */
function sendResponse($success, $message = '', $data = [], $statusCode = 200)
{
    http_response_code($statusCode);

    $response = array_merge([
        'success' => $success
    ], $data);

    if (!empty($message)) {
        $response['message'] = $message;
    }

    echo json_encode($response, JSON_UNESCAPED_UNICODE);
    exit;
}

/**
 * Возвращает данные текущего авторизованного пользователя с его глобальными ролями.
 *
 * @param PDO $db Экземпляр подключения к БД
 * @return array|null Данные пользователя или null
 */
function getCurrentUser($db)
{
    if (!isset($_SESSION['user_id'])) {
        return null;
    }

    try {
        // Получаем информацию о пользователе
        $stmt = $db->prepare("
            SELECT id, fullname, email, meta_info, is_active 
            FROM users 
            WHERE id = ? AND is_active = 1
        ");
        $stmt->execute([$_SESSION['user_id']]);
        $user = $stmt->fetch();

        if (!$user) {
            return null;
        }

        // Получаем список глобальных ролей пользователя
        $stmtRoles = $db->prepare("
            SELECT r.name 
            FROM roles r
            JOIN user_roles ur ON ur.role_id = r.id
            WHERE ur.user_id = ?
        ");
        $stmtRoles->execute([$user['id']]);
        $user['roles'] = $stmtRoles->fetchAll(PDO::FETCH_COLUMN);

        return $user;

    } catch (PDOException $e) {
        return null;
    }
}

/**
 * Ограничивает доступ к эндпоинту только для авторизованных пользователей.
 * Если пользователь не авторизован, отправляет 401 ошибку.
 *
 * @param PDO $db Экземпляр подключения к БД
 * @return array Данные текущего пользователя
 */
function requireAuth($db)
{
    $user = getCurrentUser($db);
    if (!$user) {
        sendResponse(false, 'Требуется авторизация. Пожалуйста, войдите в систему.', [], 401);
    }
    return $user;
}

/**
 * Проверяет, является ли пользователь администратором.
 *
 * @param array $user Массив данных пользователя из getCurrentUser
 * @return bool
 */
function isAdmin($user)
{
    return isset($user['roles']) && in_array('admin', $user['roles']);
}

/**
 * Проверяет, является ли пользователь риелтором.
 *
 * @param array $user Массив данных пользователя из getCurrentUser
 * @return bool
 */
function isAgent($user)
{
    return isset($user['roles']) && in_array('realtor', $user['roles']);
}

/**
 * Читает JSON-данные из тела HTTP-запроса (для методов POST/PUT).
 *
 * @return array Декодированный массив данных
 */
function getJsonInput()
{
    $rawInput = file_get_contents('php://input');
    $decoded = json_decode($rawInput, true);
    return is_array($decoded) ? $decoded : [];
}
