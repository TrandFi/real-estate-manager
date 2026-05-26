<?php
// api/users.php

// Подключаем глобальный файл инициализации
require_once __DIR__ . '/bootstrap.php';

// Проверяем авторизацию пользователя
$user = requireAuth($db);

$method = $_SERVER['REQUEST_METHOD'];
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Вспомогательные функции маппинга ролей (прямое сопоставление)
function mapDbRoleToFrontend($dbRole) {
    return $dbRole;
}

function mapFrontendRoleToDb($feRole) {
    return $feRole;
}

if ($method === 'GET') {
    // ================= GET USERS =================
    // Доступно любому вошедшему пользователю для добавления участников сделок
    try {
        if ($id > 0) {
            // Получить одного пользователя
            $stmt = $db->prepare("
                SELECT id, fullname as full_name, email, meta_info as group_name, is_active
                FROM users
                WHERE id = ?
            ");
            $stmt->execute([$id]);
            $usr = $stmt->fetch();
            
            if (!$usr) {
                sendResponse(false, 'Пользователь не найден.', [], 404);
            }
            
            $usr['id'] = (int)$usr['id'];
            $usr['is_active'] = (int)$usr['is_active'];
            
            // Получаем его глобальные роли
            $stmtRoles = $db->prepare("
                SELECT r.name 
                FROM roles r
                JOIN user_roles ur ON ur.role_id = r.id
                WHERE ur.user_id = ?
            ");
            $stmtRoles->execute([$id]);
            $dbRoles = $stmtRoles->fetchAll(PDO::FETCH_COLUMN);
            
            $usr['roles'] = array_map('mapDbRoleToFrontend', $dbRoles);
            
            sendResponse(true, '', ['user' => $usr]);
            
        } else {
            // Получить список всех пользователей
            $stmt = $db->query("
                SELECT id, fullname as full_name, email, meta_info as group_name, is_active
                FROM users
                ORDER BY fullname ASC
            ");
            $usersList = $stmt->fetchAll();
            
            // Загружаем глобальные роли для каждого пользователя
            foreach ($usersList as &$usr) {
                $usr['id'] = (int)$usr['id'];
                $usr['is_active'] = (int)$usr['is_active'];
                
                $stmtRoles = $db->prepare("
                    SELECT r.name 
                    FROM roles r
                    JOIN user_roles ur ON ur.role_id = r.id
                    WHERE ur.user_id = ?
                ");
                $stmtRoles->execute([$usr['id']]);
                $dbRoles = $stmtRoles->fetchAll(PDO::FETCH_COLUMN);
                
                $usr['roles'] = array_map('mapDbRoleToFrontend', $dbRoles);
            }
            
            sendResponse(true, '', ['users' => $usersList]);
        }
        
    } catch (PDOException $e) {
        sendResponse(false, 'Ошибка базы данных при получении списка пользователей: ' . $e->getMessage());
    }
}

if ($method === 'PUT') {
    // ================= UPDATE USER (ADMIN ONLY) =================
    // Редактирование прав пользователей доступно исключительно глобальным администраторам
    if (!isAdmin($user)) {
        sendResponse(false, 'У вас нет прав администратора для совершения этой операции.', [], 403);
    }
    
    if ($id <= 0) {
        sendResponse(false, 'Не указан идентификатор пользователя.');
    }
    
    $input = getJsonInput();
    
    // Входные данные содержат массив ролей (roles) и активность (is_active)
    $targetRoles = isset($input['roles']) && is_array($input['roles']) ? $input['roles'] : [];
    $isActive = isset($input['is_active']) ? (int)$input['is_active'] : 1;
    
    // Запретить администратору блокировать самого себя или снимать с себя роль admin
    if ($id === $user['id']) {
        sendResponse(false, 'Вы не можете изменить статус или права собственной учетной записи.');
    }
    
    try {
        $db->beginTransaction();
        
        // 1. Обновляем статус активности пользователя
        $stmtUpdateUser = $db->prepare("UPDATE users SET is_active = ? WHERE id = ?");
        $stmtUpdateUser->execute([$isActive, $id]);
        
        // 2. Обновляем глобальные роли пользователя
        if (!empty($targetRoles)) {
            // Удаляем старые связи
            $stmtDelRoles = $db->prepare("DELETE FROM user_roles WHERE user_id = ?");
            $stmtDelRoles->execute([$id]);
            
            // Вставляем новые роли
            foreach ($targetRoles as $feRole) {
                $dbRoleName = mapFrontendRoleToDb($feRole);
                
                // Получаем ID роли
                $stmtGetRole = $db->prepare("SELECT id FROM roles WHERE name = ?");
                $stmtGetRole->execute([$dbRoleName]);
                $roleId = $stmtGetRole->fetchColumn();
                
                if ($roleId) {
                    $stmtInsRole = $db->prepare("INSERT INTO user_roles (user_id, role_id) VALUES (?, ?)");
                    $stmtInsRole->execute([$id, $roleId]);
                }
            }
        }
        
        $db->commit();
        sendResponse(true, 'Учетная запись пользователя успешно обновлена.');
        
    } catch (PDOException $e) {
        if ($db->inTransaction()) {
            $db->rollBack();
        }
        sendResponse(false, 'Ошибка базы данных при обновлении пользователя: ' . $e->getMessage());
    }
}

if ($method === 'DELETE') {
    // ================= DELETE USER (ADMIN ONLY) =================
    if (!isAdmin($user)) {
        sendResponse(false, 'У вас нет прав администратора для совершения этой операции.', [], 403);
    }
    
    if ($id <= 0) {
        sendResponse(false, 'Не указан идентификатор пользователя.');
    }
    
    // Запретить администратору удалять самого себя
    if ($id === $user['id']) {
        sendResponse(false, 'Вы не можете удалить собственную учетную запись.');
    }
    
    try {
        $db->beginTransaction();
        
        // 1. Удаляем связи с ролями в user_roles
        $stmtDelRoles = $db->prepare("DELETE FROM user_roles WHERE user_id = ?");
        $stmtDelRoles->execute([$id]);
        
        // 2. Очищаем привязки пользователя к недвижимости (как риелтора или покупателя)
        $stmtClearRealtor = $db->prepare("UPDATE properties SET realtor_id = NULL, realtor_accepted = 0 WHERE realtor_id = ?");
        $stmtClearRealtor->execute([$id]);
        
        $stmtClearBuyer = $db->prepare("UPDATE properties SET buyer_id = NULL, buyer_approved = 0 WHERE buyer_id = ?");
        $stmtClearBuyer->execute([$id]);
        
        // 3. Удаляем из property_agents
        $stmtDelAgents = $db->prepare("DELETE FROM property_agents WHERE user_id = ?");
        $stmtDelAgents->execute([$id]);
        
        // 4. Удаляем записи из истории смены статусов
        $stmtDelHistory = $db->prepare("DELETE FROM property_status_history WHERE changed_by = ?");
        $stmtDelHistory->execute([$id]);
        
        // 5. Удаляем объявленные им объекты (если он создатель/продавец)
        $stmtProps = $db->prepare("SELECT id FROM properties WHERE creator_id = ?");
        $stmtProps->execute([$id]);
        $propIds = $stmtProps->fetchAll(PDO::FETCH_COLUMN);
        
        if (!empty($propIds)) {
            $inClause = implode(',', array_fill(0, count($propIds), '?'));
            
            // Удаляем историю для этих объектов
            $stmtDelHistoryProps = $db->prepare("DELETE FROM property_status_history WHERE property_id IN ($inClause)");
            $stmtDelHistoryProps->execute($propIds);
            
            // Удаляем агентов для этих объектов
            $stmtDelAgentsProps = $db->prepare("DELETE FROM property_agents WHERE property_id IN ($inClause)");
            $stmtDelAgentsProps->execute($propIds);
            
            // Удаляем сами объекты
            $stmtDelProps = $db->prepare("DELETE FROM properties WHERE creator_id = ?");
            $stmtDelProps->execute([$id]);
        }
        
        // 6. Удаляем самого пользователя
        $stmtDelUser = $db->prepare("DELETE FROM users WHERE id = ?");
        $stmtDelUser->execute([$id]);
        
        $db->commit();
        sendResponse(true, 'Пользователь успешно удален.');
        
    } catch (PDOException $e) {
        if ($db->inTransaction()) {
            $db->rollBack();
        }
        sendResponse(false, 'Ошибка базы данных при удалении пользователя: ' . $e->getMessage());
    }
}
