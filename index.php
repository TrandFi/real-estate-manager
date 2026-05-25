<?php
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
?>
<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Real Estate Manager — Управление сделками с недвижимостью</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Outfit:wght@500;600;700&display=swap"
        rel="stylesheet">
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- CSS Stylesheet -->
    <link rel="stylesheet" href="assets/styles.css?v=3">
</head>

<body>

    <!-- Global Loading Overlay -->
    <div id="global-loader" class="loader-overlay hidden">
        <div class="spinner"></div>
    </div>

    <!-- Toast Notifications Container -->
    <div id="toast-container" class="toast-container"></div>

    <!-- 1. AUTHORIZATION SCREEN -->
    <div id="auth-screen" class="auth-screen">
        <div class="auth-bg">
            <div class="glow-orb orb-1"></div>
            <div class="glow-orb orb-2"></div>
        </div>
        <div class="auth-container">
            <div class="auth-logo">
                <i class="fa-solid fa-house-chimney-window"></i>
                <h1>Real Estate Manager</h1>
            </div>

            <!-- Login Card -->
            <div id="login-card" class="auth-card">
                <h2>Вход в систему</h2>
                <p class="subtitle">Введите свои учетные данные для доступа</p>
                <form id="login-form">
                    <div class="form-group">
                        <label for="login-email">Email</label>
                        <div class="input-wrapper">
                            <i class="fa-solid fa-envelope"></i>
                            <input type="email" id="login-email" required placeholder="name@example.com">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="login-password">Пароль</label>
                        <div class="input-wrapper">
                            <i class="fa-solid fa-lock"></i>
                            <input type="password" id="login-password" required placeholder="••••••••">
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block">Войти</button>
                </form>
                <div class="auth-footer">
                    <span>Нет аккаунта?</span>
                    <a href="#" id="to-register-btn">Зарегистрироваться</a>
                </div>
            </div>

            <!-- Register Card -->
            <div id="register-card" class="auth-card hidden">
                <h2>Регистрация</h2>
                <p class="subtitle">Создайте новый аккаунт для работы с системой</p>
                <form id="register-form">
                    <div class="form-group">
                        <label for="register-name">ФИО</label>
                        <div class="input-wrapper">
                            <i class="fa-solid fa-user"></i>
                            <input type="text" id="register-name" required placeholder="Иванов Иван Иванович">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="register-email">Email</label>
                        <div class="input-wrapper">
                            <i class="fa-solid fa-envelope"></i>
                            <input type="email" id="register-email" required placeholder="name@example.com">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="register-password">Пароль</label>
                        <div class="input-wrapper">
                            <i class="fa-solid fa-lock"></i>
                            <input type="password" id="register-password" required placeholder="••••••••">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="register-role">Роль в системе</label>
                        <div class="input-wrapper">
                            <i class="fa-solid fa-user-tag"></i>
                            <select id="register-role" required>
                                <option value="buyer">Покупатель</option>
                                <option value="seller">Продавец</option>
                                <option value="realtor">Риелтор</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="register-group">Телефон / Агентство</label>
                        <div class="input-wrapper">
                            <i class="fa-solid fa-phone"></i>
                            <input type="text" id="register-group" required placeholder="+7 (999) 123-45-67">
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block">Зарегистрироваться</button>
                </form>
                <div class="auth-footer">
                    <span>Уже есть аккаунт?</span>
                    <a href="#" id="to-login-btn">Войти</a>
                </div>
            </div>
        </div>
    </div>

    <!-- 2. MAIN APPLICATION SCREEN -->
    <div id="app-screen" class="app-screen hidden">
        <!-- Application Header -->
        <header class="app-header">
            <div class="header-logo">
                <i class="fa-solid fa-house-chimney-window"></i>
                <span class="logo-text">Real Estate Manager</span>
            </div>
            <nav class="header-nav">
                <button id="nav-dashboard-btn" class="nav-btn active">
                    <i class="fa-solid fa-chart-line"></i>Сделки и объекты
                </button>
                <button id="nav-admin-btn" class="nav-btn">
                    <i class="fa-solid fa-users-gear"></i>Администрирование
                </button>
            </nav>
            <div class="header-user">
                <div id="header-avatar-circle" class="user-avatar"></div>
                <div class="user-info">
                    <span id="header-user-name" class="user-name"></span>
                    <span id="header-user-meta" class="user-meta"></span>
                </div>
                <button id="logout-btn" class="btn-logout" title="Выйти">
                    <i class="fa-solid fa-right-from-bracket"></i>
                </button>
            </div>
        </header>

        <!-- Main Workspace Area -->
        <main class="app-main">
            <!-- 2.1 DASHBOARD TAB -->
            <div id="tab-dashboard" class="tab-content">
                <!-- Statistics Summary Cards -->
                <div class="stats-grid">
                    <div class="stat-card" id="card-total" style="cursor: pointer;">
                        <div class="stat-icon icon-blue">
                            <i class="fa-solid fa-database"></i>
                        </div>
                        <div class="stat-details">
                            <h3 id="stat-total">0</h3>
                            <p>Все объявления</p>
                        </div>
                    </div>
                    <div class="stat-card" id="card-active" style="cursor: pointer;">
                        <div class="stat-icon icon-orange">
                            <i class="fa-solid fa-briefcase"></i>
                        </div>
                        <div class="stat-details">
                            <h3 id="stat-active">0</h3>
                            <p>В работе</p>
                        </div>
                    </div>
                    <div class="stat-card" id="card-completed" style="cursor: pointer;">
                        <div class="stat-icon icon-green">
                            <i class="fa-solid fa-circle-check"></i>
                        </div>
                        <div class="stat-details">
                            <h3 id="stat-completed">0</h3>
                            <p>Завершено сделок</p>
                        </div>
                    </div>
                    <div class="stat-card" id="card-my" style="cursor: pointer;">
                        <div class="stat-icon icon-purple">
                            <i class="fa-solid fa-user-check"></i>
                        </div>
                        <div class="stat-details">
                            <h3 id="stat-my">0</h3>
                            <p>Мои сделки</p>
                        </div>
                    </div>
                </div>

                <!-- Realtor Proposals Notifications Container -->
                <div id="realtor-notifications-container" style="margin-top: 15px; display: none;"></div>

                <!-- Main Grid Layout -->
                <div class="dashboard-layout">
                    <!-- Left: Filters & Property List -->
                    <div class="left-column">
                        <div class="filter-panel">
                            <div class="filter-row">
                                <div class="search-box">
                                    <i class="fa-solid fa-magnifying-glass"></i>
                                    <input type="text" id="filter-search"
                                        placeholder="Поиск по названию или описанию...">
                                </div>
                                <div class="select-box">
                                    <select id="filter-status">
                                        <option value="">Все статусы</option>
                                        <!-- Will be dynamically populated by JS -->
                                    </select>
                                </div>
                            </div>
                            <div class="filter-footer">
                                <label class="checkbox-label">
                                    <input type="checkbox" id="filter-my-only">
                                    <span class="checkbox-custom"></span>
                                    <span>Только мои сделки</span>
                                </label>
                                <button id="open-create-modal-btn" class="btn btn-primary btn-sm">
                                    <i class="fa-solid fa-plus"></i>Добавить объект
                                </button>
                            </div>
                        </div>
                        <h3 class="section-title">Список объектов недвижимости</h3>
                        <div id="projects-list" class="projects-list">
                            <!-- Dynamic project items go here -->
                        </div>
                    </div>

                    <!-- Right: Property Detail Panel -->
                    <div class="right-column">
                        <!-- Placeholder card when no item is selected -->
                        <div id="project-card-placeholder" class="project-card-placeholder">
                            <i class="fa-solid fa-house-laptop"></i>
                            <h3>Объект не выбран</h3>
                            <p>Выберите объект недвижимости из списка слева для просмотра подробных сведений, состава
                                участников и истории статусов</p>
                        </div>

                        <!-- Detailed card container -->
                        <div id="project-card-details" class="project-details-card hidden">
                            <div class="details-header">
                                <span id="detail-badge-status" class="badge"></span>
                                <h2 id="detail-title"></h2>
                                <div class="creator-info">
                                    <span>Продавец: </span>
                                    <strong id="detail-creator-name"></strong>
                                </div>
                                <div class="realtor-info" style="margin-top: 6px; font-size: 0.9em; opacity: 0.9;">
                                    <span>Риелтор: </span>
                                    <strong id="detail-realtor-name">Не назначен</strong>
                                    <span id="detail-realtor-status-badge" class="badge"
                                        style="margin-left: 8px; font-size: 0.8em; padding: 2px 6px;"></span>
                                </div>
                                <div id="detail-buyer-row" class="buyer-info"
                                    style="margin-top: 6px; font-size: 0.9em; opacity: 0.9; display: none;">
                                    <span>Покупатель: </span>
                                    <strong id="detail-buyer-name"></strong>
                                    <span id="detail-buyer-status-badge" class="badge"
                                        style="margin-left: 8px; font-size: 0.8em; padding: 2px 6px;"></span>
                                </div>
                            </div>
                            <div class="details-body">
                                <!-- Progress -->
                                <div class="progress-section">
                                    <div class="progress-text-row">
                                        <span>Готовность сделки</span>
                                        <span id="detail-progress-label">0%</span>
                                    </div>
                                    <div class="progress-bar-bg">
                                        <div id="detail-progress-bar" class="progress-bar-fill" style="width: 0%;">
                                        </div>
                                    </div>
                                </div>

                                <!-- Description -->
                                <div class="info-section">
                                    <h4>Описание объекта</h4>
                                    <p id="detail-description"></p>
                                </div>

                                <!-- Characteristics of Property -->
                                <div class="info-section">
                                    <h4>Характеристики недвижимости</h4>
                                    <div class="meta-grid"
                                        style="grid-template-columns: repeat(3, 1fr); margin-top: 10px;">
                                        <div class="meta-item">
                                            <span class="meta-label">Адрес квартиры</span>
                                            <span id="detail-address" class="meta-value"></span>
                                        </div>
                                        <div class="meta-item">
                                            <span class="meta-label">Кол-во комнат</span>
                                            <span id="detail-rooms" class="meta-value"></span>
                                        </div>
                                        <div class="meta-item">
                                            <span class="meta-label">Площадь</span>
                                            <span id="detail-area" class="meta-value"></span>
                                        </div>
                                        <div class="meta-item">
                                            <span class="meta-label">Стоимость</span>
                                            <span id="detail-price" class="meta-value"
                                                style="color: var(--accent); font-weight: 600;"></span>
                                        </div>
                                        <div class="meta-item">
                                            <span class="meta-label">Этаж</span>
                                            <span id="detail-floor" class="meta-value"></span>
                                        </div>
                                        <div class="meta-item">
                                            <span class="meta-label">Тип дома</span>
                                            <span id="detail-house-type" class="meta-value"></span>
                                        </div>
                                        <div class="meta-item">
                                            <span class="meta-label">Район</span>
                                            <span id="detail-district" class="meta-value"></span>
                                        </div>
                                        <div class="meta-item">
                                            <span class="meta-label">Год постройки</span>
                                            <span id="detail-build-year" class="meta-value"></span>
                                        </div>
                                        <div class="meta-item">
                                            <span class="meta-label">Ремонт</span>
                                            <span id="detail-renovation" class="meta-value"></span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Metadata info grid -->
                                <div class="meta-grid">
                                    <div class="meta-item">
                                        <span class="meta-label">Дата начала</span>
                                        <span id="detail-start-date" class="meta-value"></span>
                                    </div>
                                </div>

                                <!-- Скрытые технические поля для поддержки JS логики карточки -->
                                <div style="display: none;">
                                    <span id="detail-end-date"></span>
                                    <a id="detail-repo-link" href="#">
                                        <span id="detail-repo-text"></span>
                                    </a>
                                </div>

                                <!-- Action Buttons for Realtor and Buyer workflows -->
                                <div id="workflow-actions-container" class="workflow-actions-container"
                                    style="margin: 16px 0; display: flex; gap: 10px; flex-wrap: wrap;">
                                    <button id="btn-accept-realtor" class="btn btn-success btn-sm hidden"
                                        style="flex: 1; min-width: 150px; background-color: #2e7d32; border-color: #2e7d32; color: #fff;">
                                        <i class="fa-solid fa-check" style="margin-right: 6px;"></i>Принять объект
                                    </button>
                                    <button id="btn-decline-realtor" class="btn btn-danger btn-sm hidden"
                                        style="flex: 1; min-width: 150px; background-color: #c62828; border-color: #c62828; color: #fff;">
                                        <i class="fa-solid fa-xmark" style="margin-right: 6px;"></i>Отклонить объект
                                    </button>
                                    <button id="btn-propose-realtor" class="btn btn-primary btn-sm btn-block hidden"
                                        style="width: 100%; background-color: #0288d1; border-color: #0288d1; color: #fff; padding: 10px 16px; font-size: 1.1em;">
                                        <i class="fa-solid fa-handshake" style="margin-right: 8px;"></i>Запросить
                                        ведение
                                    </button>
                                    <button id="btn-buy-property" class="btn btn-primary btn-sm btn-block hidden"
                                        style="width: 100%; background-color: #1565c0; border-color: #1565c0; color: #fff; padding: 10px 16px; font-size: 1.1em;">
                                        <i class="fa-solid fa-eye" style="margin-right: 8px;"></i>Хочу посмотреть
                                    </button>
                                    <button id="btn-confirm-deal" class="btn btn-success btn-sm hidden"
                                        style="flex: 1; min-width: 150px; background-color: #2e7d32; border-color: #2e7d32; color: #fff;">
                                        <i class="fa-solid fa-check" style="margin-right: 6px;"></i>Разрешить просмотр
                                    </button>
                                    <button id="btn-confirm-purchase" class="btn btn-success btn-sm btn-block hidden"
                                        style="width: 100%; background-color: #2e7d32; border-color: #2e7d32; color: #fff; padding: 10px 16px; font-size: 1.1em;">
                                        <i class="fa-solid fa-cart-shopping" style="margin-right: 8px;"></i>Подтвердить
                                        покупку
                                    </button>
                                    <button id="btn-decline-deal" class="btn btn-danger btn-sm hidden"
                                        style="flex: 1; min-width: 150px; background-color: #c62828; border-color: #c62828; color: #fff;">
                                        <i class="fa-solid fa-ban" style="margin-right: 6px;"></i>Отклонить бронь
                                    </button>
                                </div>

                                <!-- Team & Members -->
                                <div class="team-section">
                                    <h4>Участники сделки</h4>
                                    <div class="deal-participants-block"
                                        style="display: flex; flex-direction: column; gap: 8px; margin-top: 10px;">
                                        <div class="participant-item"
                                            style="display: flex; justify-content: space-between; align-items: center; font-size: 0.9em; padding: 6px 12px; background: rgba(255,255,255,0.05); border-radius: 6px; border: 1px solid rgba(255,255,255,0.1); min-height: 46px;">
                                            <span style="color: var(--text-muted);">Продавец:</span>
                                            <span id="detail-participant-seller" style="font-weight: 500;"></span>
                                        </div>
                                        <div class="participant-item"
                                            style="display: flex; justify-content: space-between; align-items: center; font-size: 0.9em; padding: 6px 12px; background: rgba(255,255,255,0.05); border-radius: 6px; border: 1px solid rgba(255,255,255,0.1); min-height: 46px;">
                                            <span style="color: var(--text-muted);">Риелтор:</span>
                                            <span id="detail-participant-realtor" style="font-weight: 500;"></span>
                                        </div>
                                        <div class="participant-item"
                                            style="display: flex; justify-content: space-between; align-items: center; font-size: 0.9em; padding: 6px 12px; background: rgba(255,255,255,0.05); border-radius: 6px; border: 1px solid rgba(255,255,255,0.1); min-height: 46px;">
                                            <span style="color: var(--text-muted);">Покупатель:</span>
                                            <span id="detail-participant-buyer" style="font-weight: 500;"></span>
                                        </div>
                                    </div>

                                    <!-- Выбор риелтора (вместо кнопки добавления участников) -->
                                    <div id="seller-realtor-select-container" class="hidden" style="margin-top: 12px; display: flex; gap: 8px; align-items: center;">
                                        <select id="deal-realtor-select" class="form-control" style="flex: 1; padding: 6px 12px; font-size: 0.9em; background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 6px; color: var(--text-color); height: 38px;">
                                            <option value="">-- Выберите риелтора --</option>
                                        </select>
                                        <button id="btn-assign-deal-realtor" class="btn btn-primary btn-sm" style="height: 38px; display: inline-flex; align-items: center; justify-content: center;">Выбрать риелтора</button>
                                    </div>
                                </div>

                                <!-- Timeline History -->
                                <div class="history-section" style="margin-top: 24px;">
                                    <h4>История изменения статусов</h4>
                                    <div id="project-history-timeline" class="timeline">
                                        <!-- Dynamic timeline items go here -->
                                    </div>
                                </div>

                                <!-- Deal Action Controls -->
                                <div
                                    style="display: flex; gap: 12px; margin-top: 24px; border-top: 1px solid var(--border-color); padding-top: 16px;">
                                    <button id="edit-project-btn" class="btn btn-outline btn-sm" style="flex: 1;">
                                        <i class="fa-solid fa-pen-to-square"></i>Редактировать
                                    </button>
                                    <button id="delete-project-btn" class="btn btn-danger btn-sm" style="flex: 1;">
                                        <i class="fa-solid fa-trash-can"></i>Удалить сделку
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2.2 ADMINISTRATION TAB -->
            <div id="tab-admin" class="tab-content hidden">
                <div class="admin-panel-card">
                    <div class="admin-header">
                        <h2>Администрирование пользователей</h2>
                        <p>Управление ролями пользователей и блокировкой учетных записей</p>
                    </div>
                    <div class="table-responsive">
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>Имя пользователя</th>
                                    <th>Email</th>
                                    <th>Телефон / Компания</th>
                                    <th>Роль</th>
                                    <th>Активность</th>
                                    <th>Действие</th>
                                </tr>
                            </thead>
                            <tbody id="admin-users-tbody">
                                <!-- Dynamic user rows go here -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- 3. PROJECT CRUD MODAL DIALOG -->
    <div id="project-modal" class="modal-overlay hidden">
        <div class="modal-card">
            <div class="modal-header">
                <h3 id="modal-project-title">Создание нового проекта</h3>
                <button id="close-project-modal-btn" class="modal-close-btn">&times;</button>
            </div>
            <form id="project-form">
                <div class="modal-body">
                    <input type="hidden" id="form-project-id">

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                        <div class="form-group">
                            <label for="form-title">Название объявления</label>
                            <input type="text" id="form-title" required placeholder="Например: Просторная евродвушка">
                        </div>
                        <div class="form-group">
                            <label for="form-address">Адрес квартиры</label>
                            <input type="text" id="form-address" required
                                placeholder="Например: ул. Ленина, д. 45, кв. 12">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="form-description">Описание объекта</label>
                        <textarea id="form-description" required rows="1"
                            style="height: 38px; min-height: 38px; resize: none;"
                            placeholder="Введите описание, условия сделки и прочие детали..."></textarea>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr 1.2fr; gap: 16px;">
                        <div class="form-group">
                            <label for="form-rooms">Кол-во комнат</label>
                            <input type="number" id="form-rooms" required min="1" max="20" placeholder="2">
                        </div>
                        <div class="form-group">
                            <label for="form-area">Площадь (кв. м)</label>
                            <input type="number" id="form-area" required step="0.01" min="1" placeholder="54.5">
                        </div>
                        <div class="form-group">
                            <label for="form-price">Стоимость (руб.)</label>
                            <input type="number" id="form-price" required min="1" placeholder="4500000">
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr 1.2fr; gap: 16px;">
                        <div class="form-group">
                            <label for="form-floor">Этаж</label>
                            <input type="number" id="form-floor" required min="1" max="100" placeholder="5">
                        </div>
                        <div class="form-group">
                            <label for="form-build-year">Год постройки</label>
                            <input type="number" id="form-build-year" required min="1800" max="2030" placeholder="2018">
                        </div>
                        <div class="form-group">
                            <label for="form-start-date">Дата начала сделки</label>
                            <input type="date" id="form-start-date" required>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 16px;">
                        <div class="form-group">
                            <label for="form-house-type">Тип дома</label>
                            <select id="form-house-type" required>
                                <option value="кирпичный">Кирпичный</option>
                                <option value="панельный">Панельный</option>
                                <option value="монолитный">Монолитный</option>
                                <option value="деревянный">Деревянный</option>
                                <option value="шлакобетонный">Шлакобетонный</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="form-district">Район</label>
                            <select id="form-district" required>
                                <option value="Центральный">Центральный</option>
                                <option value="Кировский">Кировский</option>
                                <option value="Индустриальный">Индустриальный</option>
                                <option value="Железнодорожный">Железнодорожный</option>
                                <option value="Краснофлотский">Краснофлотский</option>
                                <option value="Южный">Южный</option>
                                <option value="Северный">Северный</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="form-renovation">Ремонт</label>
                            <select id="form-renovation" required>
                                <option value="Без ремонта">Без ремонта</option>
                                <option value="дизайнерский">Дизайнерский</option>
                                <option value="черновая отделка">Черновая отделка</option>
                                <option value="чистовая отделка">Чистовая отделка</option>
                                <option value="евроремонт">Евроремонт</option>
                                <option value="косметический">Косметический</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group" id="form-realtor-group">
                        <label for="form-realtor">Выбрать риелтора для ведения продажи</label>
                        <select id="form-realtor">
                            <option value="">-- Выберите риелтора --</option>
                        </select>
                    </div>

                    <!-- Скрытые технические поля для поддержки JS логики -->
                    <div style="display: none;">
                        <input type="url" id="form-repo">
                        <select id="form-status">
                            <option value="1" selected>Создано</option>
                            <option value="2">Активно</option>
                            <option value="3">Забронировано</option>
                            <option value="4">Продано</option>
                        </select>
                        <input type="number" id="form-progress" value="0">
                        <input type="date" id="form-end-date">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" id="btn-cancel-project-modal" class="btn btn-outline">Отмена</button>
                    <button type="submit" id="btn-save-project" class="btn btn-primary">Сохранить</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Application Script -->
    <script src="assets/app.js?v=<?= time() ?>"></script>
</body>

</html>