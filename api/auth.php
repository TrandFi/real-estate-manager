<?php
// api/auth.php

// Подключаем глобальный файл инициализации
require_once __DIR__ . '/bootstrap.php';

// Получаем действие из GET-параметра
$action = isset($_GET['action']) ? $_GET['action'] : '';

switch ($action) {
    case 'register':
        // ================= РЕГИСТРАЦИЯ ПОЛЬЗОВАТЕЛЯ =================
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            sendResponse(false, 'Метод запроса не поддерживается.', [], 405);
        }

        $input = getJsonInput();
        $fullName = isset($input['full_name']) ? trim($input['full_name']) : '';
        $email = isset($input['email']) ? trim($input['email']) : '';
        $password = isset($input['password']) ? trim($input['password']) : '';
        $groupName = isset($input['group_name']) ? trim($input['group_name']) : ''; // Сохраняем в meta_info
        $roleSlug = isset($input['role_slug']) ? trim($input['role_slug']) : 'buyer'; // buyer, seller или realtor

        // Валидация входных данных
        if (empty($fullName) || empty($email) || empty($password)) {
            sendResponse(false, 'Пожалуйста, заполните обязательные поля (ФИО, Email, Пароль).');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            sendResponse(false, 'Некорректный формат адреса электронной почты.');
        }

        try {
            // Проверяем, существует ли пользователь с таким email
            $stmtCheck = $db->prepare("SELECT id FROM users WHERE email = ?");
            $stmtCheck->execute([$email]);
            if ($stmtCheck->fetch()) {
                sendResponse(false, 'Пользователь с таким Email уже зарегистрирован.');
            }

            // Начинаем транзакцию для сохранения целостности
            $db->beginTransaction();

            // Хешируем пароль по стандарту BCrypt
            $passwordHash = password_hash($password, PASSWORD_BCRYPT);

            // Вставляем запись в таблицу пользователей
            $stmtInsert = $db->prepare("
                INSERT INTO users (fullname, email, password_hash, meta_info, is_active)
                VALUES (?, ?, ?, ?, 1)
            ");
            $stmtInsert->execute([$fullName, $email, $passwordHash, $groupName]);
            $userId = $db->lastInsertId();

            // Проверяем корректность роли
            $dbRoleName = $roleSlug;
            if (!in_array($dbRoleName, ['realtor', 'buyer', 'seller'])) {
                $dbRoleName = 'buyer';
            }

            // Получаем id роли в БД
            $stmtRole = $db->prepare("SELECT id FROM roles WHERE name = ?");
            $stmtRole->execute([$dbRoleName]);
            $role = $stmtRole->fetch();
            $roleId = $role ? $role['id'] : 2; // По умолчанию buyer (2)

            // Связываем пользователя с его глобальной ролью
            $stmtUserRole = $db->prepare("INSERT INTO user_roles (user_id, role_id) VALUES (?, ?)");
            $stmtUserRole->execute([$userId, $roleId]);

            $db->commit();

            // Автоматически авторизуем нового пользователя (создаем сессию)
            $_SESSION['user_id'] = $userId;

            // Отправляем успешный ответ с данными пользователя в формате, ожидаемом JS
            sendResponse(true, 'Регистрация выполнена успешно.', [
                'user' => [
                    'id' => (int) $userId,
                    'full_name' => $fullName,
                    'email' => $email,
                    'group_name' => $groupName,
                    'is_active' => 1,
                    'roles' => [$roleSlug]
                ]
            ]);

        } catch (PDOException $e) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            sendResponse(false, 'Ошибка при записи в базу данных: ' . $e->getMessage());
        }
        break;

    case 'login':
        // ================= АВТОРИЗАЦИЯ ПОЛЬЗОВАТЕЛЯ =================
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            sendResponse(false, 'Метод запроса не поддерживается.', [], 405);
        }

        $input = getJsonInput();
        $email = isset($input['email']) ? trim($input['email']) : '';
        $password = isset($input['password']) ? trim($input['password']) : '';

        if (empty($email) || empty($password)) {
            sendResponse(false, 'Введите адрес электронной почты и пароль.');
        }

        try {
            // Получаем пользователя по email
            $stmtUser = $db->prepare("
                SELECT id, fullname, email, password_hash, meta_info, is_active 
                FROM users 
                WHERE email = ?
            ");
            $stmtUser->execute([$email]);
            $user = $stmtUser->fetch();

            // Проверка пароля и существования учетной записи
            if (!$user || !password_verify($password, $user['password_hash'])) {
                sendResponse(false, 'Неверный Email или Пароль.');
            }

            // Проверка активности аккаунта
            if ((int) $user['is_active'] !== 1) {
                sendResponse(false, 'Ваш аккаунт деактивирован администратором.');
            }

            // Записываем сессию
            $_SESSION['user_id'] = $user['id'];

            // Получаем глобальные роли
            $stmtRoles = $db->prepare("
                SELECT r.name 
                FROM roles r
                JOIN user_roles ur ON ur.role_id = r.id
                WHERE ur.user_id = ?
            ");
            $stmtRoles->execute([$user['id']]);
            $dbRoles = $stmtRoles->fetchAll(PDO::FETCH_COLUMN);

            // Возвращаем роли напрямую
            $frontendRoles = $dbRoles;

            sendResponse(true, 'Вход выполнен успешно.', [
                'user' => [
                    'id' => (int) $user['id'],
                    'full_name' => $user['fullname'],
                    'email' => $user['email'],
                    'group_name' => $user['meta_info'],
                    'is_active' => (int) $user['is_active'],
                    'roles' => $frontendRoles
                ]
            ]);

        } catch (PDOException $e) {
            sendResponse(false, 'Ошибка базы данных при авторизации: ' . $e->getMessage());
        }
        break;

    case 'logout':
        // ================= ВЫХОД ИЗ СИСТЕМЫ =================
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            sendResponse(false, 'Метод запроса не поддерживается.', [], 405);
        }

        // Очищаем сессионные переменные
        $_SESSION = [];

        // Удаляем куки сессии
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }

        // Уничтожаем саму сессию
        session_destroy();

        sendResponse(true, 'Выход из системы выполнен успешно.');
        break;

    case 'me':
        // ================= ПОЛУЧЕНИЕ ТЕКУЩЕГО ПОЛЬЗОВАТЕЛЯ =================
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            sendResponse(false, 'Метод запроса не поддерживается.', [], 405);
        }

        $user = getCurrentUser($db);
        if (!$user) {
            sendResponse(false, 'Пользователь не авторизован или сессия истекла.', [], 401);
        }

        // Возвращаем роли напрямую
        $frontendRoles = $user['roles'];

        sendResponse(true, '', [
            'user' => [
                'id' => (int) $user['id'],
                'full_name' => $user['fullname'],
                'email' => $user['email'],
                'group_name' => $user['meta_info'],
                'is_active' => (int) $user['is_active'],
                'roles' => $frontendRoles
            ]
        ]);
        break;

    default:
        sendResponse(false, 'Запрошенное действие не поддерживается или не указано.');
        break;
}
