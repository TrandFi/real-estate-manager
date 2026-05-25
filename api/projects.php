<?php
// api/projects.php

// Подключаем глобальный файл инициализации
require_once __DIR__ . '/bootstrap.php';

// Проверяем авторизацию пользователя
$user = requireAuth($db);

// Автоматическое добавление продавцов (создателей) в участники сделки, если они отсутствуют
try {
    $db->query("
        INSERT INTO property_agents (property_id, user_id, role)
        SELECT id, creator_id, 'seller' FROM properties p
        WHERE NOT EXISTS (
            SELECT 1 FROM property_agents pa 
            WHERE pa.property_id = p.id AND pa.user_id = p.creator_id AND pa.role = 'seller'
        )
    ");
} catch (PDOException $e) {
    // Игнорируем возможные ошибки при автомиграции
}

$action = isset($_GET['action']) ? $_GET['action'] : '';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$method = $_SERVER['REQUEST_METHOD'];

// Вспомогательные функции маппинга ролей
function mapDbRoleToFrontend($dbRole) {
    switch ($dbRole) {
        case 'lead_agent': return 'teamlead';
        case 'co_agent':   return 'member';
        case 'buyer':      return 'supervisor';
        case 'seller':     return 'seller';
        default:           return 'member';
    }
}

function mapFrontendRoleToDb($feRole) {
    switch ($feRole) {
        case 'teamlead':   return 'lead_agent';
        case 'member':     return 'co_agent';
        case 'supervisor': return 'buyer';
        case 'seller':     return 'seller';
        default:           return 'co_agent';
    }
}

// Вспомогательная функция для получения участников проекта
function getProjectMembers($db, $projectId) {
    $stmt = $db->prepare("
        SELECT pa.user_id, u.fullname as full_name, u.email, u.meta_info as group_name, pa.role as project_role
        FROM property_agents pa
        JOIN users u ON u.id = pa.user_id
        WHERE pa.property_id = ?
    ");
    $stmt->execute([$projectId]);
    $members = $stmt->fetchAll();
    
    // Преобразуем роли для фронтенда
    foreach ($members as &$m) {
        $m['user_id'] = (int)$m['user_id'];
        $m['project_role'] = mapDbRoleToFrontend($m['project_role']);
    }
    return $members;
}

// Вспомогательная функция для проверки прав участника проекта
function getUserRoleInProject($db, $projectId, $userId, $globalRoles) {
    if (in_array('realtor', $globalRoles)) {
        return 'supervisor'; // Релтор/админ имеет максимальные права (supervisor)
    }
    
    $stmt = $db->prepare("SELECT role FROM property_agents WHERE property_id = ? AND user_id = ?");
    $stmt->execute([$projectId, $userId]);
    $roleRecord = $stmt->fetch();
    
    if ($roleRecord) {
        return mapDbRoleToFrontend($roleRecord['role']);
    }
    
    // Проверим, не является ли пользователь создателем объекта напрямую
    $stmtCreator = $db->prepare("SELECT creator_id FROM properties WHERE id = ?");
    $stmtCreator->execute([$projectId]);
    $creatorId = $stmtCreator->fetchColumn();
    if ($creatorId == $userId) {
        return 'teamlead'; // Создатель имеет права лида по умолчанию
    }
    
    return 'guest';
}

// ================= ОБРАБОТКА МЕТОДОВ API =================

if ($method === 'GET') {
    if ($action === 'statuses') {
        // ================= GET STATUSES =================
        try {
            $stmt = $db->query("SELECT id, name FROM property_statuses ORDER BY id ASC");
            $statuses = $stmt->fetchAll();
            sendResponse(true, '', ['statuses' => $statuses]);
        } catch (PDOException $e) {
            sendResponse(false, 'Ошибка при получении справочника стадий: ' . $e->getMessage());
        }
        
    } elseif ($id > 0) {
        // ================= GET PROJECT BY ID =================
        try {
            // Загружаем основные данные объявления
            $stmt = $db->prepare("
                SELECT p.*, u.fullname as creator_name, ps.name as status_name,
                       r.fullname as realtor_name, b.fullname as buyer_name
                FROM properties p
                JOIN users u ON u.id = p.creator_id
                JOIN property_statuses ps ON ps.id = p.status_id
                LEFT JOIN users r ON r.id = p.realtor_id
                LEFT JOIN users b ON b.id = p.buyer_id
                WHERE p.id = ?
            ");
            $stmt->execute([$id]);
            $project = $stmt->fetch();
            
            if (!$project) {
                sendResponse(false, 'Объект недвижимости не найден.', [], 404);
            }
            
            // Преобразование типов полей для соответствия схеме JS
            $project['id'] = (int)$project['id'];
            $project['creator_id'] = (int)$project['creator_id'];
            $project['status_id'] = (int)$project['status_id'];
            $project['progress_percent'] = (int)$project['progress_percent'];
            $project['realtor_id'] = $project['realtor_id'] ? (int)$project['realtor_id'] : null;
            $project['realtor_accepted'] = $project['realtor_accepted'] !== null ? (int)$project['realtor_accepted'] : 0;
            $project['buyer_id'] = $project['buyer_id'] ? (int)$project['buyer_id'] : null;
            $project['buyer_approved'] = $project['buyer_approved'] !== null ? (int)$project['buyer_approved'] : 0;
            
            $project['rooms'] = $project['rooms'] !== null ? (int)$project['rooms'] : null;
            $project['area'] = $project['area'] !== null ? (float)$project['area'] : null;
            $project['price'] = $project['price'] !== null ? (float)$project['price'] : null;
            $project['floor'] = $project['floor'] !== null ? (int)$project['floor'] : null;
            $project['build_year'] = $project['build_year'] !== null ? (int)$project['build_year'] : null;
            
            // Получаем список участников
            $members = getProjectMembers($db, $id);
            $project['members'] = $members;
            
            // Получаем историю изменений стадий
            $stmtHistory = $db->prepare("
                SELECT h.id, ps.name as status_name, u.fullname as changed_by_name, h.changed_at
                FROM property_status_history h
                JOIN property_statuses ps ON ps.id = h.status_id
                JOIN users u ON u.id = h.changed_by
                WHERE h.property_id = ?
                ORDER BY h.changed_at DESC, h.id DESC
            ");
            $stmtHistory->execute([$id]);
            $history = $stmtHistory->fetchAll();
            
            // Приводим типы
            foreach ($history as &$hist) {
                $hist['id'] = (int)$hist['id'];
            }
            $project['stage_history'] = $history;
            
            // Определяем роль текущего пользователя на проекте
            $project['current_user_project_role'] = getUserRoleInProject($db, $id, $user['id'], $user['roles']);
            
            sendResponse(true, '', ['project' => $project]);
            
        } catch (PDOException $e) {
            sendResponse(false, 'Ошибка при получении данных объекта: ' . $e->getMessage());
        }
        
    } else {
        // ================= GET PROJECTS LIST =================
        $search = isset($_GET['search']) ? trim($_GET['search']) : '';
        $statusId = isset($_GET['status_id']) ? (int)$_GET['status_id'] : 0;
        $myOnly = isset($_GET['my_only']) && (int)$_GET['my_only'] === 1;
        
        try {
            $sql = "
                SELECT p.id, p.title, p.description, p.progress_percent, p.creator_id, p.status_id,
                       p.realtor_id, p.realtor_accepted, p.buyer_id, p.buyer_approved,
                       p.address, p.rooms, p.area, p.price, p.floor, p.house_type, p.district, p.build_year, p.renovation,
                       u.fullname as creator_name, ps.name as status_name,
                       r.fullname as realtor_name, b.fullname as buyer_name,
                       (SELECT COUNT(*) FROM property_agents WHERE property_id = p.id) as member_count
                FROM properties p
                JOIN users u ON u.id = p.creator_id
                JOIN property_statuses ps ON ps.id = p.status_id
                LEFT JOIN users r ON r.id = p.realtor_id
                LEFT JOIN users b ON b.id = p.buyer_id
                WHERE 1=1
            ";
            
            $params = [];

            // Покупатель видит ТОЛЬКО те объявления, у которых назначен и подтвердил риелтор
            if (in_array('buyer', $user['roles'])) {
                $sql .= " AND p.realtor_id IS NOT NULL AND p.realtor_accepted = 1";
            }
            
            // Риелтор видит только свои объекты и новые созданные (без риелтора)
            if (in_array('realtor', $user['roles']) && !in_array('admin', $user['roles'])) {
                $sql .= " AND (p.realtor_id = ? OR p.realtor_id IS NULL)";
                $params[] = $user['id'];
            }
            
            if (!empty($search)) {
                $sql .= " AND (p.title LIKE ? OR p.description LIKE ?)";
                $params[] = "%$search%";
                $params[] = "%$search%";
            }
            
            if ($statusId > 0) {
                $sql .= " AND p.status_id = ?";
                $params[] = $statusId;
            }
            
            if ($myOnly) {
                $sql .= " AND (p.creator_id = ? OR EXISTS (SELECT 1 FROM property_agents WHERE property_id = p.id AND user_id = ?))";
                $params[] = $user['id'];
                $params[] = $user['id'];
            }
            
            $sql .= " ORDER BY p.id DESC";
            
            $stmt = $db->prepare($sql);
            $stmt->execute($params);
            $projects = $stmt->fetchAll();
            
            // Преобразуем типы полей и определяем роли текущего пользователя
            foreach ($projects as &$p) {
                $p['id'] = (int)$p['id'];
                $p['creator_id'] = (int)$p['creator_id'];
                $p['status_id'] = (int)$p['status_id'];
                $p['progress_percent'] = (int)$p['progress_percent'];
                $p['member_count'] = (int)$p['member_count'];
                $p['realtor_id'] = $p['realtor_id'] ? (int)$p['realtor_id'] : null;
                $p['realtor_accepted'] = $p['realtor_accepted'] !== null ? (int)$p['realtor_accepted'] : 0;
                $p['buyer_id'] = $p['buyer_id'] ? (int)$p['buyer_id'] : null;
                $p['buyer_approved'] = $p['buyer_approved'] !== null ? (int)$p['buyer_approved'] : 0;
                
                $p['rooms'] = $p['rooms'] !== null ? (int)$p['rooms'] : null;
                $p['area'] = $p['area'] !== null ? (float)$p['area'] : null;
                $p['price'] = $p['price'] !== null ? (float)$p['price'] : null;
                $p['floor'] = $p['floor'] !== null ? (int)$p['floor'] : null;
                $p['build_year'] = $p['build_year'] !== null ? (int)$p['build_year'] : null;
                
                $p['current_user_project_role'] = getUserRoleInProject($db, $p['id'], $user['id'], $user['roles']);
            }
            
            sendResponse(true, '', ['projects' => $projects]);
            
        } catch (PDOException $e) {
            sendResponse(false, 'Ошибка при получении списка объектов: ' . $e->getMessage());
        }
    }
}

if ($method === 'POST') {
    $input = getJsonInput();
    
    if ($action === 'accept_realtor') {
        if ($id <= 0) {
            sendResponse(false, 'Не указан идентификатор объекта.');
        }
        if (!in_array('realtor', $user['roles'])) {
            sendResponse(false, 'Только риелторы могут принимать объекты на ведение продажи.', [], 403);
        }
        try {
            $db->beginTransaction();
            $stmtCheck = $db->prepare("SELECT realtor_id, realtor_accepted FROM properties WHERE id = ?");
            $stmtCheck->execute([$id]);
            $proj = $stmtCheck->fetch();
            if (!$proj) {
                sendResponse(false, 'Объект не найден.', [], 404);
            }
            if ((int)$proj['realtor_id'] !== (int)$user['id']) {
                sendResponse(false, 'Вы не назначены риелтором для этого объекта.', [], 403);
            }
            $stmtUpdate = $db->prepare("UPDATE properties SET realtor_accepted = 1, status_id = 2 WHERE id = ?");
            $stmtUpdate->execute([$id]);
            
            // Добавляем риелтора как lead_agent в property_agents
            $stmtAgentCheck = $db->prepare("SELECT 1 FROM property_agents WHERE property_id = ? AND user_id = ?");
            $stmtAgentCheck->execute([$id, $user['id']]);
            if (!$stmtAgentCheck->fetch()) {
                $stmtInsertAgent = $db->prepare("INSERT INTO property_agents (property_id, user_id, role) VALUES (?, ?, 'lead_agent')");
                $stmtInsertAgent->execute([$id, $user['id']]);
            }
            
            // Добавляем запись в историю статусов (ID 2 - Готов к просмотру)
            $stmtHistory = $db->prepare("INSERT INTO property_status_history (property_id, status_id, changed_by, changed_at) VALUES (?, 2, ?, NOW())");
            $stmtHistory->execute([$id, $user['id']]);
            
            $db->commit();
            sendResponse(true, 'Вы успешно приняли объект на ведение продажи.');
        } catch (PDOException $e) {
            $db->rollBack();
            sendResponse(false, 'Ошибка базы данных: ' . $e->getMessage());
        }
    }
    elseif ($action === 'decline_realtor') {
        if ($id <= 0) {
            sendResponse(false, 'Не указан идентификатор объекта.');
        }
        if (!in_array('realtor', $user['roles'])) {
            sendResponse(false, 'Только риелторы могут отказываться от ведения продажи.', [], 403);
        }
        try {
            $db->beginTransaction();
            $stmtCheck = $db->prepare("SELECT realtor_id FROM properties WHERE id = ?");
            $stmtCheck->execute([$id]);
            $proj = $stmtCheck->fetch();
            if (!$proj) {
                sendResponse(false, 'Объект не найден.', [], 404);
            }
            if ((int)$proj['realtor_id'] !== (int)$user['id']) {
                sendResponse(false, 'Вы не назначены риелтором для этого объекта.', [], 403);
            }
            $stmtUpdate = $db->prepare("UPDATE properties SET realtor_id = NULL, realtor_accepted = 0 WHERE id = ?");
            $stmtUpdate->execute([$id]);
            
            $stmtDelAgent = $db->prepare("DELETE FROM property_agents WHERE property_id = ? AND user_id = ?");
            $stmtDelAgent->execute([$id, $user['id']]);
            
            $db->commit();
            sendResponse(true, 'Вы отказались от ведения продажи этого объекта.');
        } catch (PDOException $e) {
            $db->rollBack();
            sendResponse(false, 'Ошибка базы данных: ' . $e->getMessage());
        }
    }
    elseif ($action === 'propose_realtor') {
        if ($id <= 0) {
            sendResponse(false, 'Не указан идентификатор объекта.');
        }
        if (!in_array('realtor', $user['roles'])) {
            sendResponse(false, 'Только риелторы могут запрашивать ведение объекта.', [], 403);
        }
        try {
            $db->beginTransaction();
            $stmtCheck = $db->prepare("SELECT realtor_id, realtor_accepted FROM properties WHERE id = ?");
            $stmtCheck->execute([$id]);
            $proj = $stmtCheck->fetch();
            if (!$proj) {
                sendResponse(false, 'Объект не найден.', [], 404);
            }
            if (!empty($proj['realtor_id'])) {
                sendResponse(false, 'У этого объекта уже назначен риелтор.', [], 400);
            }
            // Set realtor_id to current realtor, realtor_accepted to 2 (pending seller approval)
            $stmtUpdate = $db->prepare("UPDATE properties SET realtor_id = ?, realtor_accepted = 2 WHERE id = ?");
            $stmtUpdate->execute([$user['id'], $id]);
            
            $db->commit();
            sendResponse(true, 'Запрос на ведение объекта успешно отправлен владельцу.');
        } catch (PDOException $e) {
            $db->rollBack();
            sendResponse(false, 'Ошибка базы данных: ' . $e->getMessage());
        }
    }
    elseif ($action === 'accept_realtor_proposal') {
        if ($id <= 0) {
            sendResponse(false, 'Не указан идентификатор объекта.');
        }
        try {
            $db->beginTransaction();
            $stmtCheck = $db->prepare("SELECT creator_id, realtor_id, realtor_accepted FROM properties WHERE id = ?");
            $stmtCheck->execute([$id]);
            $proj = $stmtCheck->fetch();
            if (!$proj) {
                sendResponse(false, 'Объект не найден.', [], 404);
            }
            if ((int)$proj['creator_id'] !== (int)$user['id']) {
                sendResponse(false, 'Вы не являетесь владельцем этого объекта.', [], 403);
            }
            if ((int)$proj['realtor_accepted'] !== 2) {
                sendResponse(false, 'Нет активных предложений от риелтора для этого объекта.', [], 400);
            }
            
            // Accept realtor and change status to 2 (Готов к просмотру)
            $stmtUpdate = $db->prepare("UPDATE properties SET realtor_accepted = 1, status_id = 2 WHERE id = ?");
            $stmtUpdate->execute([$id]);
            
            // Add realtor to property_agents
            $stmtAgentCheck = $db->prepare("SELECT 1 FROM property_agents WHERE property_id = ? AND user_id = ?");
            $stmtAgentCheck->execute([$id, $proj['realtor_id']]);
            if (!$stmtAgentCheck->fetch()) {
                $stmtInsertAgent = $db->prepare("INSERT INTO property_agents (property_id, user_id, role) VALUES (?, ?, 'lead_agent')");
                $stmtInsertAgent->execute([$id, $proj['realtor_id']]);
            }
            
            // Добавляем запись в историю статусов (ID 2 - Готов к просмотру)
            $stmtHistory = $db->prepare("INSERT INTO property_status_history (property_id, status_id, changed_by, changed_at) VALUES (?, 2, ?, NOW())");
            $stmtHistory->execute([$id, $user['id']]);
            
            $db->commit();
            sendResponse(true, 'Вы успешно приняли предложение риелтора.');
        } catch (PDOException $e) {
            $db->rollBack();
            sendResponse(false, 'Ошибка базы данных: ' . $e->getMessage());
        }
    }
    elseif ($action === 'decline_realtor_proposal') {
        if ($id <= 0) {
            sendResponse(false, 'Не указан идентификатор объекта.');
        }
        try {
            $db->beginTransaction();
            $stmtCheck = $db->prepare("SELECT creator_id, realtor_id, realtor_accepted FROM properties WHERE id = ?");
            $stmtCheck->execute([$id]);
            $proj = $stmtCheck->fetch();
            if (!$proj) {
                sendResponse(false, 'Объект не найден.', [], 404);
            }
            if ((int)$proj['creator_id'] !== (int)$user['id']) {
                sendResponse(false, 'Вы не являетесь владельцем этого объекта.', [], 403);
            }
            if ((int)$proj['realtor_accepted'] !== 2) {
                sendResponse(false, 'Нет активных предложений от риелтора для этого объекта.', [], 400);
            }
            
            // Reset realtor fields
            $stmtUpdate = $db->prepare("UPDATE properties SET realtor_id = NULL, realtor_accepted = 0 WHERE id = ?");
            $stmtUpdate->execute([$id]);
            
            $db->commit();
            sendResponse(true, 'Предложение риелтора отклонено.');
        } catch (PDOException $e) {
            $db->rollBack();
            sendResponse(false, 'Ошибка базы данных: ' . $e->getMessage());
        }
    }
    elseif ($action === 'assign_realtor') {
        if ($id <= 0) {
            sendResponse(false, 'Не указан идентификатор объекта.');
        }
        try {
            $db->beginTransaction();
            $stmtCheck = $db->prepare("SELECT creator_id, realtor_id FROM properties WHERE id = ?");
            $stmtCheck->execute([$id]);
            $proj = $stmtCheck->fetch();
            if (!$proj) {
                sendResponse(false, 'Объект не найден.', [], 404);
            }
            if ((int)$proj['creator_id'] !== (int)$user['id']) {
                sendResponse(false, 'Только создатель объявления может выбирать риелтора.', [], 403);
            }
            
            $targetRealtorId = isset($input['realtor_id']) ? (int)$input['realtor_id'] : 0;
            if ($targetRealtorId <= 0) {
                sendResponse(false, 'Не выбран риелтор.');
            }
            
            // Проверяем, является ли выбранный пользователь риелтором
            $stmtRoleCheck = $db->prepare("
                SELECT 1 FROM user_roles ur
                JOIN roles r ON r.id = ur.role_id
                WHERE ur.user_id = ? AND r.name = 'realtor'
            ");
            $stmtRoleCheck->execute([$targetRealtorId]);
            if (!$stmtRoleCheck->fetch()) {
                sendResponse(false, 'Выбранный пользователь не является риелтором.');
            }
            
            // Назначаем риелтора и ставим realtor_accepted = 0 (на согласовании)
            $stmtUpdate = $db->prepare("UPDATE properties SET realtor_id = ?, realtor_accepted = 0 WHERE id = ?");
            $stmtUpdate->execute([$targetRealtorId, $id]);
            
            $db->commit();
            sendResponse(true, 'Риелтор успешно выбран для ведения продажи.');
        } catch (PDOException $e) {
            $db->rollBack();
            sendResponse(false, 'Ошибка базы данных: ' . $e->getMessage());
        }
    }
    elseif ($action === 'buy_property') {
        if ($id <= 0) {
            sendResponse(false, 'Не указан идентификатор объекта.');
        }
        if (!in_array('buyer', $user['roles'])) {
            sendResponse(false, 'Только покупатели могут бронировать недвижимость.', [], 403);
        }
        try {
            $db->beginTransaction();
            $stmtCheck = $db->prepare("SELECT realtor_id, realtor_accepted, status_id FROM properties WHERE id = ?");
            $stmtCheck->execute([$id]);
            $proj = $stmtCheck->fetch();
            if (!$proj) {
                sendResponse(false, 'Объект не найден.', [], 404);
            }
            if (empty($proj['realtor_id']) || (int)$proj['realtor_accepted'] !== 1) {
                sendResponse(false, 'Нельзя забронировать этот объект: для него не назначен или не подтвержден Риелтор.', [], 400);
            }
            if ((int)$proj['status_id'] !== 2) {
                sendResponse(false, 'Объект должен быть в статусе "Готов к просмотру" для запроса просмотра.', [], 400);
            }
            
            // Update buyer fields (status remains 2 - Готов к просмотру)
            $stmtUpdate = $db->prepare("UPDATE properties SET buyer_id = ?, buyer_approved = 0 WHERE id = ?");
            $stmtUpdate->execute([$user['id'], $id]);
            
            $stmtBuyerCheck = $db->prepare("SELECT 1 FROM property_agents WHERE property_id = ? AND user_id = ?");
            $stmtBuyerCheck->execute([$id, $user['id']]);
            if (!$stmtBuyerCheck->fetch()) {
                $stmtInsertBuyer = $db->prepare("INSERT INTO property_agents (property_id, user_id, role) VALUES (?, ?, 'buyer')");
                $stmtInsertBuyer->execute([$id, $user['id']]);
            }
            
            $db->commit();
            sendResponse(true, 'Запрос на просмотр успешно отправлен риелтору.');
        } catch (PDOException $e) {
            $db->rollBack();
            sendResponse(false, 'Ошибка базы данных: ' . $e->getMessage());
        }
    }
    elseif ($action === 'confirm_deal') {
        if ($id <= 0) {
            sendResponse(false, 'Не указан идентификатор объекта.');
        }
        if (!in_array('realtor', $user['roles'])) {
            sendResponse(false, 'Только риелторы могут разрешать просмотр.', [], 403);
        }
        try {
            $db->beginTransaction();
            $stmtCheck = $db->prepare("SELECT realtor_id, buyer_id, status_id FROM properties WHERE id = ?");
            $stmtCheck->execute([$id]);
            $proj = $stmtCheck->fetch();
            if (!$proj) {
                sendResponse(false, 'Объект не найден.', [], 404);
            }
            if ((int)$proj['realtor_id'] !== (int)$user['id']) {
                sendResponse(false, 'Вы не являетесь риелтором для этого объекта.', [], 403);
            }
            if ((int)$proj['status_id'] !== 2) {
                sendResponse(false, 'Объект должен быть в статусе "Готов к просмотру".', [], 400);
            }
            
            // Update status to 3 (Забронировано)
            $stmtUpdate = $db->prepare("UPDATE properties SET status_id = 3 WHERE id = ?");
            $stmtUpdate->execute([$id]);
            
            $stmtHistory = $db->prepare("INSERT INTO property_status_history (property_id, status_id, changed_by, changed_at) VALUES (?, 3, ?, NOW())");
            $stmtHistory->execute([$id, $user['id']]);
            
            $db->commit();
            sendResponse(true, 'Просмотр разрешен. Объект переведен в статус "Забронировано".');
        } catch (PDOException $e) {
            $db->rollBack();
            sendResponse(false, 'Ошибка базы данных: ' . $e->getMessage());
        }
    }
    elseif ($action === 'confirm_purchase') {
        if ($id <= 0) {
            sendResponse(false, 'Не указан идентификатор объекта.');
        }
        if (!in_array('buyer', $user['roles'])) {
            sendResponse(false, 'Только покупатели могут подтверждать покупку.', [], 403);
        }
        try {
            $db->beginTransaction();
            $stmtCheck = $db->prepare("SELECT buyer_id, status_id FROM properties WHERE id = ?");
            $stmtCheck->execute([$id]);
            $proj = $stmtCheck->fetch();
            if (!$proj) {
                sendResponse(false, 'Объект не найден.', [], 404);
            }
            if ((int)$proj['buyer_id'] !== (int)$user['id']) {
                sendResponse(false, 'Вы не являетесь покупателем этого объекта.', [], 403);
            }
            if ((int)$proj['status_id'] !== 3) {
                sendResponse(false, 'Объект должен быть в статусе "Забронировано".', [], 400);
            }
            
            // Update status to 4 (Продано) and buyer_approved = 1
            $stmtUpdate = $db->prepare("UPDATE properties SET buyer_approved = 1, status_id = 4, progress_percent = 100 WHERE id = ?");
            $stmtUpdate->execute([$id]);
            
            $stmtHistory = $db->prepare("INSERT INTO property_status_history (property_id, status_id, changed_by, changed_at) VALUES (?, 4, ?, NOW())");
            $stmtHistory->execute([$id, $user['id']]);
            
            $db->commit();
            sendResponse(true, 'Покупка подтверждена. Объект успешно продан!');
        } catch (PDOException $e) {
            $db->rollBack();
            sendResponse(false, 'Ошибка базы данных: ' . $e->getMessage());
        }
    }
    elseif ($action === 'decline_deal') {
        if ($id <= 0) {
            sendResponse(false, 'Не указан идентификатор объекта.');
        }
        if (!in_array('realtor', $user['roles'])) {
            sendResponse(false, 'Только риелторы могут отклонять сделку.', [], 403);
        }
        try {
            $db->beginTransaction();
            $stmtCheck = $db->prepare("SELECT realtor_id, buyer_id FROM properties WHERE id = ?");
            $stmtCheck->execute([$id]);
            $proj = $stmtCheck->fetch();
            if (!$proj) {
                sendResponse(false, 'Объект не найден.', [], 404);
            }
            if ((int)$proj['realtor_id'] !== (int)$user['id']) {
                sendResponse(false, 'Вы не являетесь риелтором для этого объекта.', [], 403);
            }
            if (empty($proj['buyer_id'])) {
                sendResponse(false, 'Для объекта еще не выбран покупатель.', [], 400);
            }
            
            $stmtDelBuyer = $db->prepare("DELETE FROM property_agents WHERE property_id = ? AND user_id = ? AND role = 'buyer'");
            $stmtDelBuyer->execute([$id, $proj['buyer_id']]);
            
            $stmtUpdate = $db->prepare("UPDATE properties SET buyer_id = NULL, buyer_approved = 0, status_id = 1 WHERE id = ?");
            $stmtUpdate->execute([$id]);
            
            $stmtHistory = $db->prepare("INSERT INTO property_status_history (property_id, status_id, changed_by, changed_at) VALUES (?, 1, ?, NOW())");
            $stmtHistory->execute([$id, $user['id']]);
            
            $db->commit();
            sendResponse(true, 'Запрос отклонен. Объект возвращен в статус "Создано".');
        } catch (PDOException $e) {
            $db->rollBack();
            sendResponse(false, 'Ошибка базы данных: ' . $e->getMessage());
        }
    }
    elseif ($action === 'member') {
        // ================= ADD MEMBER TO PROJECT =================
        if ($id <= 0) {
            sendResponse(false, 'Не указан идентификатор объекта.');
        }
        
        // Проверка прав: добавлять участников могут только создатель, админ, или лид/руководитель проекта
        $currentUserRole = getUserRoleInProject($db, $id, $user['id'], $user['roles']);
        if ($currentUserRole !== 'teamlead' && $currentUserRole !== 'supervisor') {
            sendResponse(false, 'У вас недостаточно прав для добавления участников.', [], 403);
        }
        
        $targetUserId = isset($input['user_id']) ? (int)$input['user_id'] : 0;
        $projectRole = isset($input['project_role']) ? trim($input['project_role']) : 'member'; // teamlead, supervisor, member
        
        if ($targetUserId <= 0) {
            sendResponse(false, 'Не указан пользователь для добавления.');
        }
        
        try {
            // Проверяем, существует ли пользователь в глобальной базе
            $stmtUserCheck = $db->prepare("SELECT id, fullname FROM users WHERE id = ?");
            $stmtUserCheck->execute([$targetUserId]);
            $targetUser = $stmtUserCheck->fetch();
            
            if (!$targetUser) {
                sendResponse(false, 'Указанный пользователь не существует.');
            }
            
            // Проверяем, не состоит ли он уже на этом объекте
            $stmtMemberCheck = $db->prepare("SELECT 1 FROM property_agents WHERE property_id = ? AND user_id = ?");
            $stmtMemberCheck->execute([$id, $targetUserId]);
            if ($stmtMemberCheck->fetch()) {
                sendResponse(false, 'Данный пользователь уже назначен на этот объект.');
            }
            
            // Если роль supervisor (руководитель/покупатель), то делаем дополнительную проверку глобальной роли
            if ($projectRole === 'supervisor') {
                $stmtGlobalRoleCheck = $db->prepare("
                    SELECT r.name 
                    FROM roles r
                    JOIN user_roles ur ON ur.role_id = r.id
                    WHERE ur.user_id = ?
                ");
                $stmtGlobalRoleCheck->execute([$targetUserId]);
                $globalUserRoles = $stmtGlobalRoleCheck->fetchAll(PDO::FETCH_COLUMN);
                
                // Только admin или agent (teacher) могут быть supervisor (руководителем сделки/покупки)
                if (!in_array('realtor', $globalUserRoles)) {
                    sendResponse(false, 'Назначить руководителем/агентом сделки можно только Риелтора.');
                }
            }
            
            $dbRole = mapFrontendRoleToDb($projectRole);
            
            // Добавляем запись в property_agents
            $stmtInsert = $db->prepare("INSERT INTO property_agents (property_id, user_id, role) VALUES (?, ?, ?)");
            $stmtInsert->execute([$id, $targetUserId, $dbRole]);
            
            // Возвращаем обновленный состав участников проекта
            $updatedMembers = getProjectMembers($db, $id);
            sendResponse(true, 'Участник успешно добавлен.', ['members' => $updatedMembers]);
            
        } catch (PDOException $e) {
            sendResponse(false, 'Ошибка базы данных при добавлении участника: ' . $e->getMessage());
        }
        
    } else {
        // ================= CREATE PROJECT =================
        if (!in_array('seller', $user['roles'])) {
            sendResponse(false, 'Только продавцы могут создавать новые объекты недвижимости.', [], 403);
        }
        $title = isset($input['title']) ? trim($input['title']) : '';
        $description = isset($input['description']) ? trim($input['description']) : '';
        $statusId = isset($input['status_id']) ? (int)$input['status_id'] : 1;
        $progressPercent = isset($input['progress_percent']) ? (int)$input['progress_percent'] : 0;
        $startDate = isset($input['start_date']) ? $input['start_date'] : null;
        $plannedEndDate = isset($input['planned_end_date']) ? $input['planned_end_date'] : null;
        $repositoryLink = isset($input['repository_link']) ? trim($input['repository_link']) : null;
        $realtorId = isset($input['realtor_id']) && (int)$input['realtor_id'] > 0 ? (int)$input['realtor_id'] : null;
        
        $address = isset($input['address']) ? trim($input['address']) : null;
        $rooms = isset($input['rooms']) && $input['rooms'] !== '' ? (int)$input['rooms'] : null;
        $area = isset($input['area']) && $input['area'] !== '' ? (float)$input['area'] : null;
        $price = isset($input['price']) && $input['price'] !== '' ? (float)$input['price'] : null;
        $floor = isset($input['floor']) && $input['floor'] !== '' ? (int)$input['floor'] : null;
        $houseType = isset($input['house_type']) ? trim($input['house_type']) : null;
        $district = isset($input['district']) ? trim($input['district']) : null;
        $buildYear = isset($input['build_year']) && $input['build_year'] !== '' ? (int)$input['build_year'] : null;
        $renovation = isset($input['renovation']) ? trim($input['renovation']) : null;
        
        if (empty($title)) {
            sendResponse(false, 'Пожалуйста, введите название объекта недвижимости.');
        }
        
        
        $realtorAccepted = 0;
        if (in_array('realtor', $user['roles']) && $realtorId === (int)$user['id']) {
            $realtorAccepted = 1;
        }
        
        try {
            $db->beginTransaction();
            
            // Создаем запись объекта недвижимости
            $stmt = $db->prepare("
                INSERT INTO properties (title, description, creator_id, status_id, progress_percent, start_date, planned_end_date, repository_link, realtor_id, realtor_accepted, address, rooms, area, price, floor, house_type, district, build_year, renovation)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $title,
                $description,
                $user['id'],
                $statusId,
                $progressPercent,
                $startDate,
                $plannedEndDate,
                $repositoryLink,
                $realtorId,
                $realtorAccepted,
                $address,
                $rooms,
                $area,
                $price,
                $floor,
                $houseType,
                $district,
                $buildYear,
                $renovation
            ]);
            
            $projectId = $db->lastInsertId();
            
            // Сразу добавляем создателя (продавца) в property_agents как seller
            $stmtSellerAgent = $db->prepare("INSERT INTO property_agents (property_id, user_id, role) VALUES (?, ?, 'seller')");
            $stmtSellerAgent->execute([$projectId, $user['id']]);
            
            // Если риелтор назначен и уже подтвердил, добавляем его в участники
            if ($realtorId && $realtorAccepted === 1) {
                $stmtMember = $db->prepare("INSERT INTO property_agents (property_id, user_id, role) VALUES (?, ?, 'lead_agent')");
                $stmtMember->execute([$projectId, $realtorId]);
            }
            
            // Записываем смену статуса в историю
            $stmtHistory = $db->prepare("
                INSERT INTO property_status_history (property_id, status_id, changed_by, changed_at)
                VALUES (?, ?, ?, NOW())
            ");
            $stmtHistory->execute([$projectId, $statusId, $user['id']]);
            
            $db->commit();
            
            // Готовим объект проекта для возврата фронтенду
            $stmtStatus = $db->prepare("SELECT name FROM property_statuses WHERE id = ?");
            $stmtStatus->execute([$statusId]);
            $statusName = $stmtStatus->fetchColumn();
            
            sendResponse(true, 'Объект недвижимости успешно создан.', [
                'project_id' => (int)$projectId,
                'project' => [
                    'id' => (int)$projectId,
                    'title' => $title,
                    'status_name' => $statusName,
                    'progress_percent' => $progressPercent,
                    'members' => [
                        [
                            'user_id' => (int)$user['id'],
                            'full_name' => $user['fullname'],
                            'project_role' => 'teamlead'
                        ]
                    ]
                ]
            ]);
            
        } catch (PDOException $e) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            sendResponse(false, 'Ошибка базы данных при создании объекта: ' . $e->getMessage());
        }
    }
}

if ($method === 'PUT') {
    $input = getJsonInput();
    
    if ($action === 'member') {
        // ================= UPDATE MEMBER ROLE =================
        if ($id <= 0) {
            sendResponse(false, 'Не указан идентификатор объекта.');
        }
        
        // Проверка прав: изменять роль участников могут создатель, админ, или лид/руководитель проекта
        $currentUserRole = getUserRoleInProject($db, $id, $user['id'], $user['roles']);
        if ($currentUserRole !== 'teamlead' && $currentUserRole !== 'supervisor') {
            sendResponse(false, 'У вас недостаточно прав для изменения роли участника.');
        }
        
        $targetUserId = isset($input['user_id']) ? (int)$input['user_id'] : 0;
        $newRole = isset($input['role']) ? trim($input['role']) : '';
        
        if ($targetUserId <= 0 || empty($newRole)) {
            sendResponse(false, 'Некорректные параметры запроса.');
        }
        
        try {
            $stmt = $db->prepare("UPDATE property_agents SET role = ? WHERE property_id = ? AND user_id = ?");
            $stmt->execute([$newRole, $id, $targetUserId]);
            
            $updatedMembers = getProjectMembers($db, $id);
            sendResponse(true, 'Роль участника успешно обновлена.', ['members' => $updatedMembers]);
        } catch (PDOException $e) {
            sendResponse(false, 'Ошибка при обновлении роли участника: ' . $e->getMessage());
        }
        
    } else {
        // ================= UPDATE PROJECT DETAILS =================
        if ($id <= 0) {
            sendResponse(false, 'Не указан идентификатор объекта.');
        }
        
        // Загружаем существующий объект для проверки прав и риелтора
        $stmtExist = $db->prepare("SELECT creator_id, realtor_id, realtor_accepted FROM properties WHERE id = ?");
        $stmtExist->execute([$id]);
        $existingProj = $stmtExist->fetch();
        if (!$existingProj) {
            sendResponse(false, 'Объект недвижимости не найден.', [], 404);
        }

        $isCreator = (int)$existingProj['creator_id'] === (int)$user['id'];
        $isAssignedRealtor = in_array('realtor', $user['roles']) && (int)$existingProj['realtor_id'] === (int)$user['id'] && (int)$existingProj['realtor_accepted'] === 1;
        
        if (in_array('admin', $user['roles'])) {
            sendResponse(false, 'Администратор не может изменять данные об объектах.', [], 403);
        }
        
        if (!$isCreator && !$isAssignedRealtor) {
            sendResponse(false, 'У вас недостаточно прав для редактирования этого объекта.', [], 403);
        }
        
        $title = isset($input['title']) ? trim($input['title']) : '';
        $description = isset($input['description']) ? trim($input['description']) : '';
        $statusId = isset($input['status_id']) ? (int)$input['status_id'] : 1;
        $progressPercent = isset($input['progress_percent']) ? (int)$input['progress_percent'] : 0;
        $startDate = isset($input['start_date']) ? $input['start_date'] : null;
        $plannedEndDate = isset($input['planned_end_date']) ? $input['planned_end_date'] : null;
        $repositoryLink = isset($input['repository_link']) ? trim($input['repository_link']) : null;
        $realtorId = isset($input['realtor_id']) && (int)$input['realtor_id'] > 0 ? (int)$input['realtor_id'] : null;
        
        $address = isset($input['address']) ? trim($input['address']) : null;
        $rooms = isset($input['rooms']) && $input['rooms'] !== '' ? (int)$input['rooms'] : null;
        $area = isset($input['area']) && $input['area'] !== '' ? (float)$input['area'] : null;
        $price = isset($input['price']) && $input['price'] !== '' ? (float)$input['price'] : null;
        $floor = isset($input['floor']) && $input['floor'] !== '' ? (int)$input['floor'] : null;
        $houseType = isset($input['house_type']) ? trim($input['house_type']) : null;
        $district = isset($input['district']) ? trim($input['district']) : null;
        $buildYear = isset($input['build_year']) && $input['build_year'] !== '' ? (int)$input['build_year'] : null;
        $renovation = isset($input['renovation']) ? trim($input['renovation']) : null;
        
        if (empty($title)) {
            sendResponse(false, 'Название объекта недвижимости не может быть пустым.');
        }

        try {
            $db->beginTransaction();
            
            // Вычисляем статус подтверждения риелтором при изменении
            $realtorAccepted = (int)$existingProj['realtor_accepted'];
            if ($realtorId !== ($existingProj['realtor_id'] ? (int)$existingProj['realtor_id'] : null)) {
                $realtorAccepted = 0; // Сбрасываем согласование при смене риелтора
                
                // Удаляем старого агента из property_agents
                $stmtDelOld = $db->prepare("DELETE FROM property_agents WHERE property_id = ? AND role = 'lead_agent'");
                $stmtDelOld->execute([$id]);
            }
            
            // Получаем старый статус для фиксации истории смены стадий
            $stmtOldStatus = $db->prepare("SELECT status_id FROM properties WHERE id = ?");
            $stmtOldStatus->execute([$id]);
            $oldStatusId = (int)$stmtOldStatus->fetchColumn();
            
            // Обновляем основные данные объявления
            $stmtUpdate = $db->prepare("
                UPDATE properties 
                SET title = ?, description = ?, status_id = ?, progress_percent = ?, start_date = ?, planned_end_date = ?, repository_link = ?, realtor_id = ?, realtor_accepted = ?,
                    address = ?, rooms = ?, area = ?, price = ?, floor = ?, house_type = ?, district = ?, build_year = ?, renovation = ?
                WHERE id = ?
            ");
            $stmtUpdate->execute([
                $title,
                $description,
                $statusId,
                $progressPercent,
                $startDate,
                $plannedEndDate,
                $repositoryLink,
                $realtorId,
                $realtorAccepted,
                $address,
                $rooms,
                $area,
                $price,
                $floor,
                $houseType,
                $district,
                $buildYear,
                $renovation,
                $id
            ]);
            
            // Если стадия изменилась — логируем смену статуса
            if ($oldStatusId !== $statusId) {
                $stmtHistory = $db->prepare("
                    INSERT INTO property_status_history (property_id, status_id, changed_by, changed_at)
                    VALUES (?, ?, ?, NOW())
                ");
                $stmtHistory->execute([$id, $statusId, $user['id']]);
            }
            
            $db->commit();
            sendResponse(true, 'Данные объекта успешно обновлены.');
            
        } catch (PDOException $e) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            sendResponse(false, 'Ошибка базы данных при редактировании объекта: ' . $e->getMessage());
        }
    }
}

if ($method === 'DELETE') {
    if ($action === 'member') {
        // ================= DELETE MEMBER FROM PROJECT =================
        if ($id <= 0) {
            sendResponse(false, 'Не указан идентификатор объекта.');
        }
        
        $targetUserId = isset($_GET['user_id']) ? (int)$_GET['user_id'] : 0;
        if ($targetUserId <= 0) {
            sendResponse(false, 'Не указан пользователь для удаления.');
        }
        
        // Проверка прав: удалять участников могут создатель, админ, или лид/руководитель проекта
        $currentUserRole = getUserRoleInProject($db, $id, $user['id'], $user['roles']);
        if ($currentUserRole !== 'teamlead' && $currentUserRole !== 'supervisor') {
            sendResponse(false, 'У вас недостаточно прав для удаления участников.', [], 403);
        }
        
        try {
            // Удаляем привязку к сделке
            $stmtDelete = $db->prepare("DELETE FROM property_agents WHERE property_id = ? AND user_id = ?");
            $stmtDelete->execute([$id, $targetUserId]);
            
            $updatedMembers = getProjectMembers($db, $id);
            sendResponse(true, 'Участник успешно удален с объекта.', ['members' => $updatedMembers]);
            
        } catch (PDOException $e) {
            sendResponse(false, 'Ошибка при удалении участника: ' . $e->getMessage());
        }
        
    } else {
        // ================= DELETE PROJECT =================
        if ($id <= 0) {
            sendResponse(false, 'Не указан идентификатор объекта.');
        }
        
        // Загружаем существующий объект для проверки прав
        $stmtExist = $db->prepare("SELECT creator_id, realtor_id, realtor_accepted FROM properties WHERE id = ?");
        $stmtExist->execute([$id]);
        $existingProj = $stmtExist->fetch();
        if (!$existingProj) {
            sendResponse(false, 'Объект недвижимости не найден.', [], 404);
        }

        $isCreator = (int)$existingProj['creator_id'] === (int)$user['id'];
        $isAssignedRealtor = in_array('realtor', $user['roles']) && (int)$existingProj['realtor_id'] === (int)$user['id'] && (int)$existingProj['realtor_accepted'] === 1;

        if (in_array('admin', $user['roles'])) {
            sendResponse(false, 'Администратор не может удалять объекты.', [], 403);
        }

        if (!$isCreator && !$isAssignedRealtor) {
            sendResponse(false, 'У вас недостаточно прав для удаления этого объекта.', [], 403);
        }
        
        try {
            $db->beginTransaction();
            
            // Удаляем зависимую историю изменений стадий
            $stmtDelHistory = $db->prepare("DELETE FROM property_status_history WHERE property_id = ?");
            $stmtDelHistory->execute([$id]);
            
            // Удаляем привязки агентов/участников сделки
            $stmtDelAgents = $db->prepare("DELETE FROM property_agents WHERE property_id = ?");
            $stmtDelAgents->execute([$id]);
            
            // Удаляем сам объект недвижимости
            $stmtDelProp = $db->prepare("DELETE FROM properties WHERE id = ?");
            $stmtDelProp->execute([$id]);
            
            $db->commit();
            sendResponse(true, 'Объект недвижимости успешно удален.');
            
        } catch (PDOException $e) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            sendResponse(false, 'Ошибка базы данных при удалении объекта: ' . $e->getMessage());
        }
    }
}
