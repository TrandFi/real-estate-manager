// assets/app.js

// ================= СОСТОЯНИЕ ПРИЛОЖЕНИЯ =================
const state = {
    user: null,             // Данные текущего авторизованного пользователя
    projects: [],           // Список проектов с сервера
    statuses: [],           // Список доступных статусов проектов
    usersList: [],          // Список всех пользователей системы (для команды / администрирования)
    selectedProjectId: null, // ID текущего открытого проекта
    activeTab: 'all'        // Текущая активная вкладка статистики ('all', 'active', 'completed', 'my')
};

// ================= DOM ЭЛЕМЕНТЫ =================
const DOM = {
    // Экраны
    loader: document.getElementById('global-loader'),
    toastContainer: document.getElementById('toast-container'),
    authScreen: document.getElementById('auth-screen'),
    appScreen: document.getElementById('app-screen'),
    loginCard: document.getElementById('login-card'),
    registerCard: document.getElementById('register-card'),
    tabDashboard: document.getElementById('tab-dashboard'),
    tabAdmin: document.getElementById('tab-admin'),

    // Кнопки переключения авторизации
    toRegisterBtn: document.getElementById('to-register-btn'),
    toLoginBtn: document.getElementById('to-login-btn'),

    // Формы авторизации
    loginForm: document.getElementById('login-form'),
    registerForm: document.getElementById('register-form'),
    loginEmail: document.getElementById('login-email'),
    loginPassword: document.getElementById('login-password'),


    // Навигация
    navDashboardBtn: document.getElementById('nav-dashboard-btn'),
    navAdminBtn: document.getElementById('nav-admin-btn'),
    logoutBtn: document.getElementById('logout-btn'),

    // Данные юзера в шапке
    headerUserName: document.getElementById('header-user-name'),
    headerUserMeta: document.getElementById('header-user-meta'),
    headerAvatarCircle: document.getElementById('header-avatar-circle'),

    // Статистика
    statTotal: document.getElementById('stat-total'),
    statActive: document.getElementById('stat-active'),
    statCompleted: document.getElementById('stat-completed'),
    statMy: document.getElementById('stat-my'),
    cardTotal: document.getElementById('card-total'),
    cardActive: document.getElementById('card-active'),
    cardCompleted: document.getElementById('card-completed'),
    cardMy: document.getElementById('card-my'),

    // Фильтрация
    filterSearch: document.getElementById('filter-search'),
    filterStatus: document.getElementById('filter-status'),
    filterMyOnly: document.getElementById('filter-my-only'),

    // Списки проектов
    projectsList: document.getElementById('projects-list'),

    // Карточка детальной информации
    projectPlaceholder: document.getElementById('project-card-placeholder'),
    projectDetails: document.getElementById('project-card-details'),
    detailBadgeStatus: document.getElementById('detail-badge-status'),
    detailTitle: document.getElementById('detail-title'),
    detailCreatorName: document.getElementById('detail-creator-name'),
    detailProgressLabel: document.getElementById('detail-progress-label'),
    detailProgressBar: document.getElementById('detail-progress-bar'),
    detailDescription: document.getElementById('detail-description'),
    detailStartDate: document.getElementById('detail-start-date'),
    detailEndDate: document.getElementById('detail-end-date'),
    detailRepoLink: document.getElementById('detail-repo-link'),
    detailRepoText: document.getElementById('detail-repo-text'),

    // Участники и Команда
    membersList: document.getElementById('project-members-list'),
    addMemberBtn: document.getElementById('add-member-btn'),
    addMemberFormContainer: document.getElementById('add-member-form-container'),
    addMemberForm: document.getElementById('add-member-form'),
    memberSelectUser: document.getElementById('member-select-user'),
    memberSelectRole: document.getElementById('member-select-role'),
    cancelAddMemberBtn: document.getElementById('cancel-add-member-btn'),

    // История стадий
    historyTimeline: document.getElementById('project-history-timeline'),

    // Действия с проектом
    editProjectBtn: document.getElementById('edit-project-btn'),
    deleteProjectBtn: document.getElementById('delete-project-btn'),

    // Модальное окно Проекта
    projectModal: document.getElementById('project-modal'),
    projectForm: document.getElementById('project-form'),
    modalProjectTitle: document.getElementById('modal-project-title'),
    formProjectId: document.getElementById('form-project-id'),
    formTitle: document.getElementById('form-title'),
    formDescription: document.getElementById('form-description'),
    formStatus: document.getElementById('form-status'),
    formProgress: document.getElementById('form-progress'),
    formStartDate: document.getElementById('form-start-date'),
    formEndDate: document.getElementById('form-end-date'),
    formRepo: document.getElementById('form-repo'),
    formRealtor: document.getElementById('form-realtor'),
    formRealtorGroup: document.getElementById('form-realtor-group'),
    formAddress: document.getElementById('form-address'),
    formRooms: document.getElementById('form-rooms'),
    formArea: document.getElementById('form-area'),
    formPrice: document.getElementById('form-price'),
    formFloor: document.getElementById('form-floor'),
    formHouseType: document.getElementById('form-house-type'),
    formDistrict: document.getElementById('form-district'),
    formBuildYear: document.getElementById('form-build-year'),
    formRenovation: document.getElementById('form-renovation'),

    // Details characteristics elements
    detailAddress: document.getElementById('detail-address'),
    detailRooms: document.getElementById('detail-rooms'),
    detailArea: document.getElementById('detail-area'),
    detailPrice: document.getElementById('detail-price'),
    detailFloor: document.getElementById('detail-floor'),
    detailHouseType: document.getElementById('detail-house-type'),
    detailDistrict: document.getElementById('detail-district'),
    detailBuildYear: document.getElementById('detail-build-year'),
    detailRenovation: document.getElementById('detail-renovation'),

    openCreateModalBtn: document.getElementById('open-create-modal-btn'),
    closeProjectModalBtn: document.getElementById('close-project-modal-btn'),
    btnCancelProjectModal: document.getElementById('btn-cancel-project-modal'),
    btnSaveProject: document.getElementById('btn-save-project'),

    // Workflow детали и кнопки
    detailRealtorName: document.getElementById('detail-realtor-name'),
    detailRealtorStatusBadge: document.getElementById('detail-realtor-status-badge'),
    detailBuyerRow: document.getElementById('detail-buyer-row'),
    detailBuyerName: document.getElementById('detail-buyer-name'),
    detailBuyerStatusBadge: document.getElementById('detail-buyer-status-badge'),
    btnAcceptRealtor: document.getElementById('btn-accept-realtor'),
    btnDeclineRealtor: document.getElementById('btn-decline-realtor'),
    btnProposeRealtor: document.getElementById('btn-propose-realtor'),
    btnBuyProperty: document.getElementById('btn-buy-property'),
    btnConfirmDeal: document.getElementById('btn-confirm-deal'),
    btnConfirmPurchase: document.getElementById('btn-confirm-purchase'),
    btnDeclineDeal: document.getElementById('btn-decline-deal'),
    btnFinishDeal: document.getElementById('btn-finish-deal'),

    detailParticipantSeller: document.getElementById('detail-participant-seller'),
    detailParticipantRealtor: document.getElementById('detail-participant-realtor'),
    detailParticipantBuyer: document.getElementById('detail-participant-buyer'),

    sellerRealtorSelectContainer: document.getElementById('seller-realtor-select-container'),
    dealRealtorSelect: document.getElementById('deal-realtor-select'),
    btnAssignDealRealtor: document.getElementById('btn-assign-deal-realtor'),

    // Панель администратора
    adminUsersTbody: document.getElementById('admin-users-tbody')
};

// ================= ВСПОМОГАТЕЛЬНЫЕ ФУНКЦИИ (УВЕДОМЛЕНИЯ) =================
function showLoader(show = true) {
    if (show) DOM.loader.classList.remove('hidden');
    else DOM.loader.classList.add('hidden');
}

function showToast(message, type = 'info') {
    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;

    let iconClass = 'fa-info-circle';
    if (type === 'success') iconClass = 'fa-check-circle';
    if (type === 'warning') iconClass = 'fa-exclamation-triangle';
    if (type === 'danger') iconClass = 'fa-times-circle';

    toast.innerHTML = `
        <i class="toast-icon fa-solid ${iconClass}"></i>
        <div class="toast-message">${message}</div>
    `;

    DOM.toastContainer.appendChild(toast);

    setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transform = 'translateX(50px)';
        setTimeout(() => toast.remove(), 300);
    }, 4000);
}

// ================= СЕТЕВЫЕ ЗАПРОСЫ (API WRAPPER) =================
async function apiFetch(url, options = {}) {
    try {
        const response = await fetch(url, {
            headers: {
                'Content-Type': 'application/json'
            },
            ...options
        });

        const data = await response.json();

        if (!response.ok) {
            throw new Error(data.message || `Ошибка HTTP: ${response.status}`);
        }

        return data;
    } catch (err) {
        showToast(err.message, 'danger');
        throw err;
    }
}

// ================= ИНИЦИАЛИЗАЦИЯ И СЛУШАТЕЛИ =================
document.addEventListener('DOMContentLoaded', async () => {
    setupEventListeners();
    await checkAuth();
});

function setupEventListeners() {
    // Переключение форм авторизации
    DOM.toRegisterBtn.addEventListener('click', (e) => {
        e.preventDefault();
        DOM.loginCard.classList.add('hidden');
        DOM.registerCard.classList.remove('hidden');
    });

    DOM.toLoginBtn.addEventListener('click', (e) => {
        e.preventDefault();
        DOM.registerCard.classList.add('hidden');
        DOM.loginCard.classList.remove('hidden');
    });

    // Отправка форм входа и регистрации
    DOM.loginForm.addEventListener('submit', handleLogin);
    DOM.registerForm.addEventListener('submit', handleRegister);

    // Маска для ввода номера телефона
    const phoneInput = document.getElementById('register-group');
    if (phoneInput) {
        phoneInput.addEventListener('input', function (e) {
            let input = e.target.value.replace(/\D/g, '');
            if (input.startsWith('7') || input.startsWith('8')) {
                input = input.substring(1);
            }
            
            let formatted = '';
            if (input.length > 0) {
                formatted += '+7 (' + input.substring(0, 3);
            }
            if (input.length >= 4) {
                formatted += ') ' + input.substring(3, 6);
            }
            if (input.length >= 7) {
                formatted += '-' + input.substring(6, 8);
            }
            if (input.length >= 9) {
                formatted += '-' + input.substring(8, 10);
            }
            
            e.target.value = formatted || (e.target.value ? '+7 (' : '');
        });

        phoneInput.addEventListener('keydown', function (e) {
            if (e.key === 'Backspace' && e.target.value.length <= 4) {
                e.target.value = '';
            }
        });
    }

    // Кнопка Выйти
    DOM.logoutBtn.addEventListener('click', handleLogout);

    // Навигация
    DOM.navDashboardBtn.addEventListener('click', () => switchTab('dashboard'));
    DOM.navAdminBtn.addEventListener('click', () => switchTab('admin'));

    // Клик на карточки-вкладки статистики
    DOM.cardTotal.addEventListener('click', () => setActiveStatTab('all'));
    DOM.cardActive.addEventListener('click', () => setActiveStatTab('active'));
    DOM.cardCompleted.addEventListener('click', () => setActiveStatTab('completed'));
    DOM.cardMy.addEventListener('click', () => setActiveStatTab('my'));

    // Фильтры
    DOM.filterSearch.addEventListener('input', debounce(() => loadProjects(), 400));
    DOM.filterStatus.addEventListener('change', () => loadProjects());
    DOM.filterMyOnly.addEventListener('change', () => loadProjects());

    // Модальное окно создания проекта
    DOM.openCreateModalBtn.addEventListener('click', () => openProjectModal());
    DOM.closeProjectModalBtn.addEventListener('click', closeProjectModal);
    DOM.btnCancelProjectModal.addEventListener('click', closeProjectModal);
    DOM.projectForm.addEventListener('submit', handleProjectSubmit);
    DOM.deleteProjectBtn.addEventListener('click', handleProjectDelete);

    // Управление участниками
    if (DOM.addMemberBtn) DOM.addMemberBtn.addEventListener('click', toggleAddMemberForm);
    if (DOM.cancelAddMemberBtn) DOM.cancelAddMemberBtn.addEventListener('click', toggleAddMemberForm);
    if (DOM.addMemberForm) DOM.addMemberForm.addEventListener('submit', handleAddMemberSubmit);

    if (DOM.btnAssignDealRealtor) DOM.btnAssignDealRealtor.addEventListener('click', handleAssignDealRealtor);
}

// Функция debounce для поиска
function debounce(func, timeout = 300) {
    let timer;
    return (...args) => {
        clearTimeout(timer);
        timer = setTimeout(() => { func.apply(this, args); }, timeout);
    };
}

// ================= АВТОРИЗАЦИЯ И РЕГИСТРАЦИЯ =================
async function checkAuth() {
    showLoader(true);
    try {
        const res = await fetch('api/auth.php?action=me');
        if (res.ok) {
            const data = await res.json();
            if (data.success && data.user) {
                onUserLoggedIn(data.user);
            } else {
                showAuthScreen();
            }
        } else {
            showAuthScreen();
        }
    } catch (err) {
        showAuthScreen();
    } finally {
        showLoader(false);
    }
}

function showAuthScreen() {
    state.user = null;
    DOM.authScreen.classList.remove('hidden');
    DOM.appScreen.classList.add('hidden');
}

function onUserLoggedIn(user) {
    state.user = user;
    DOM.authScreen.classList.add('hidden');
    DOM.appScreen.classList.remove('hidden');

    // Отображаем инфо в шапке
    DOM.headerUserName.textContent = user.full_name;

    // Отображаем текстовое название роли
    let roleText = 'Покупатель';
    if (user.roles.includes('admin')) {
        roleText = 'Администратор';
        DOM.navAdminBtn.classList.remove('hidden');
    } else {
        DOM.navAdminBtn.classList.add('hidden');
    }
    if (user.roles.includes('realtor')) roleText = 'Риелтор';
    if (user.roles.includes('seller')) roleText = 'Продавец';

    // Показываем кнопку создания недвижимости только продавцам
    if (user.roles.includes('seller')) {
        DOM.openCreateModalBtn.classList.remove('hidden');
    } else {
        DOM.openCreateModalBtn.classList.add('hidden');
    }

    DOM.headerUserMeta.textContent = `${roleText} • ${user.group_name || 'Контакты не указаны'}`;

    // Первая буква имени для аватара
    DOM.headerAvatarCircle.innerHTML = `<span>${user.full_name.charAt(0).toUpperCase()}</span>`;

    // Настройка видимости вкладок статистики в зависимости от роли
    if (user.roles.includes('realtor')) {
        DOM.cardTotal.classList.remove('hidden');
        DOM.cardActive.classList.remove('hidden');
        DOM.cardCompleted.classList.remove('hidden');
        DOM.cardMy.classList.add('hidden');
    } else if (user.roles.includes('seller') || user.roles.includes('buyer')) {
        DOM.cardTotal.classList.remove('hidden');
        DOM.cardActive.classList.add('hidden');
        DOM.cardCompleted.classList.add('hidden');
        DOM.cardMy.classList.remove('hidden');
    } else { // admin
        DOM.cardTotal.classList.remove('hidden');
        DOM.cardActive.classList.add('hidden');
        DOM.cardCompleted.classList.add('hidden');
        DOM.cardMy.classList.add('hidden');
    }

    // Сброс активной вкладки
    setActiveStatTab('all');

    switchTab('dashboard');
    initializeDashboardData();
}

async function handleLogin(e) {
    e.preventDefault();
    showLoader(true);

    const email = DOM.loginEmail.value;
    const password = DOM.loginPassword.value;

    try {
        const data = await apiFetch('api/auth.php?action=login', {
            method: 'POST',
            body: JSON.stringify({ email, password })
        });

        if (data.success) {
            showToast(data.message, 'success');
            DOM.loginForm.reset();
            onUserLoggedIn(data.user);
        }
    } catch (err) {
        // Ошибка обрабатывается в apiFetch
    } finally {
        showLoader(false);
    }
}

async function handleRegister(e) {
    e.preventDefault();
    showLoader(true);

    const body = {
        full_name: document.getElementById('register-name').value,
        email: document.getElementById('register-email').value,
        password: document.getElementById('register-password').value,
        group_name: document.getElementById('register-group').value,
        role_slug: document.getElementById('register-role').value
    };

    try {
        const data = await apiFetch('api/auth.php?action=register', {
            method: 'POST',
            body: JSON.stringify(body)
        });

        if (data.success) {
            showToast(data.message, 'success');
            DOM.registerForm.reset();
            onUserLoggedIn(data.user);
        }
    } catch (err) {
        // Обрабатывается в apiFetch
    } finally {
        showLoader(false);
    }
}

async function handleLogout() {
    showLoader(true);
    try {
        const data = await apiFetch('api/auth.php?action=logout', {
            method: 'POST'
        });
        if (data.success) {
            showToast(data.message, 'success');
            showAuthScreen();
        }
    } catch (err) {
        // Ошибка в apiFetch
    } finally {
        showLoader(false);
    }
}

// ================= НАВИГАЦИЯ МЕЖДУ ВКЛАДКАМИ =================
function switchTab(tabId) {
    if (tabId === 'dashboard') {
        DOM.navDashboardBtn.classList.add('active');
        DOM.navAdminBtn.classList.remove('active');
        DOM.tabDashboard.classList.remove('hidden');
        DOM.tabAdmin.classList.add('hidden');
        loadProjects(); // Перезагружаем проекты
    } else if (tabId === 'admin') {
        DOM.navAdminBtn.classList.add('active');
        DOM.navDashboardBtn.classList.remove('active');
        DOM.tabAdmin.classList.remove('hidden');
        DOM.tabDashboard.classList.add('hidden');
        loadAdminUsersTable(); // Загружаем список пользователей для админа
    }
}

// ================= ПАНЕЛЬ УПРАВЛЕНИЯ (DASHBOARD) =================
async function initializeDashboardData() {
    showLoader(true);
    try {
        // 1. Получаем справочник статусов
        const statusesData = await apiFetch('api/projects.php?action=statuses');
        state.statuses = statusesData.statuses;

        // Заполняем фильтр статусов
        DOM.filterStatus.innerHTML = '<option value="0">Все статусы</option>';
        statusesData.statuses.forEach(status => {
            DOM.filterStatus.innerHTML += `<option value="${status.id}">${status.name}</option>`;
        });

        // Заполняем форму создания/редактирования
        DOM.formStatus.innerHTML = '';
        statusesData.statuses.forEach(status => {
            DOM.formStatus.innerHTML += `<option value="${status.id}">${status.name}</option>`;
        });

        // 2. Загружаем список пользователей для быстрого добавления в команду
        const usersData = await apiFetch('api/users.php');
        state.usersList = usersData.users;

        // Наполняем select добавления участника
        updateMemberSelectOptions();

        // 3. Загружаем проекты
        await loadProjects();

    } catch (err) {
        console.error(err);
    } finally {
        showLoader(false);
    }
}

function updateMemberSelectOptions() {
    DOM.memberSelectUser.innerHTML = '<option value="">Выберите пользователя...</option>';
    state.usersList.forEach(u => {
        // Исключаем тех, кто уже в проекте (это фильтруется при загрузке карточки)
        DOM.memberSelectUser.innerHTML += `<option value="${u.id}">${u.full_name} (${u.email})</option>`;
    });
}

async function loadProjects() {
    const search = DOM.filterSearch.value;
    const statusId = DOM.filterStatus.value;
    const myOnly = DOM.filterMyOnly.checked ? 1 : 0;

    let queryParams = [];
    if (search) queryParams.push(`search=${encodeURIComponent(search)}`);
    if (statusId > 0) queryParams.push(`status_id=${statusId}`);
    if (myOnly) queryParams.push(`my_only=1`);

    const queryString = queryParams.length > 0 ? '?' + queryParams.join('&') : '';

    try {
        const data = await apiFetch(`api/projects.php${queryString}`);
        state.projects = data.projects;
        renderProjectsList();
        calculateStatistics();
        checkRealtorProposals();
    } catch (err) {
        console.error(err);
    }
}

function calculateStatistics() {
    // Подсчет статистики на основе общего списка проектов
    const total = state.projects.length;
    let active = 0;
    let completed = 0;
    let myCount = 0;

    state.projects.forEach(p => {
        // "В работе" и "Завершено сделок" доступны только риелторам и там отображаются сделки, которые они ведут
        const isRealtorOfProperty = p.realtor_id === state.user.id && p.realtor_accepted === 1;
        if (isRealtorOfProperty) {
            if (p.status_id !== 4) {
                active++;
            }
            if (p.status_id === 4) {
                completed++;
            }
        }

        // "Мои сделки" отображаются продавцам и покупателям, в которых они участвуют (являются продавцом или покупателем сделки)
        const isMyProperty = p.creator_id === state.user.id || p.buyer_id === state.user.id;
        if (isMyProperty) {
            myCount++;
        }
    });

    DOM.statTotal.textContent = total;
    DOM.statActive.textContent = active;
    DOM.statCompleted.textContent = completed;
    DOM.statMy.textContent = myCount;
}

function setActiveStatTab(tabId) {
    state.activeTab = tabId;

    // Снимаем класс active со всех вкладок
    DOM.cardTotal.classList.remove('active');
    DOM.cardActive.classList.remove('active');
    DOM.cardCompleted.classList.remove('active');
    DOM.cardMy.classList.remove('active');

    // Добавляем class active на выбранную
    if (tabId === 'all') DOM.cardTotal.classList.add('active');
    else if (tabId === 'active') DOM.cardActive.classList.add('active');
    else if (tabId === 'completed') DOM.cardCompleted.classList.add('active');
    else if (tabId === 'my') DOM.cardMy.classList.add('active');

    // Сбрасываем выбранный объект при смене вкладки
    state.selectedProjectId = null;
    if (DOM.projectPlaceholder) DOM.projectPlaceholder.classList.remove('hidden');
    if (DOM.projectDetails) DOM.projectDetails.classList.add('hidden');

    renderProjectsList();
}

function renderProjectsList() {
    DOM.projectsList.innerHTML = '';

    // Фильтруем список проектов на основе активной вкладки
    let filteredProjects = state.projects;
    if (state.activeTab === 'active') {
        filteredProjects = state.projects.filter(p => {
            const isRealtorOfProperty = p.realtor_id === state.user.id && p.realtor_accepted === 1;
            return isRealtorOfProperty && p.status_id !== 4;
        });
    } else if (state.activeTab === 'completed') {
        filteredProjects = state.projects.filter(p => {
            const isRealtorOfProperty = p.realtor_id === state.user.id && p.realtor_accepted === 1;
            return isRealtorOfProperty && p.status_id === 4;
        });
    } else if (state.activeTab === 'my') {
        filteredProjects = state.projects.filter(p => {
            return p.creator_id === state.user.id || p.buyer_id === state.user.id;
        });
    }

    if (filteredProjects.length === 0) {
        DOM.projectsList.innerHTML = `
            <div class="text-center" style="color:var(--text-muted); padding: 40px 10px;">
                <i class="fa-solid fa-folder-open" style="font-size: 2.2rem; margin-bottom:12px; opacity:0.3;"></i>
                <p>Объекты не найдены</p>
            </div>
        `;
        return;
    }

    filteredProjects.forEach(project => {
        const isSelected = state.selectedProjectId === project.id;

        // Подбираем класс бейджа
        let badgeClass = 'badge-idea';
        if (project.status_id === 2) badgeClass = 'badge-work';
        if (project.status_id === 3) badgeClass = 'badge-review';
        if (project.status_id === 4) badgeClass = 'badge-completed';

        const card = document.createElement('div');
        card.className = `project-item-card ${isSelected ? 'active' : ''}`;
        card.setAttribute('data-id', project.id);
        card.innerHTML = `
            <div class="project-item-header">
                <h4>${escapeHtml(project.title)}</h4>
                <span class="badge ${badgeClass}">${project.status_name}</span>
            </div>
            <div class="project-item-meta">
                <span>Создатель: <b>${escapeHtml(project.creator_name)}</b></span>
                <span>Команда: <b>${project.member_count} чел.</b></span>
            </div>
            <div class="progress-container">
                <div class="progress-bar-bg">
                    <div class="progress-bar-fill" style="width: ${project.progress_percent}%"></div>
                </div>
                <span>${project.progress_percent}%</span>
            </div>
        `;

        card.addEventListener('click', () => selectProject(project.id));
        DOM.projectsList.appendChild(card);
    });
}

// ================= ДЕТАЛЬНАЯ КАРТОЧКА ПРОЕКТА =================
async function selectProject(projectId) {
    state.selectedProjectId = projectId;

    // Подсвечиваем активный проект в списке по data-id
    const cards = DOM.projectsList.querySelectorAll('.project-item-card');
    cards.forEach(card => {
        if (parseInt(card.getAttribute('data-id')) === projectId) {
            card.classList.add('active');
        } else {
            card.classList.remove('active');
        }
    });

    showLoader(true);
    try {
        const data = await apiFetch(`api/projects.php?id=${projectId}`);
        renderProjectDetails(data.project);
    } catch (err) {
        state.selectedProjectId = null;
        DOM.projectPlaceholder.classList.remove('hidden');
        DOM.projectDetails.classList.add('hidden');
    } finally {
        showLoader(false);
    }
}

function renderProjectDetails(project) {
    DOM.projectPlaceholder.classList.add('hidden');
    DOM.projectDetails.classList.remove('hidden');

    // Тексты метаданных
    DOM.detailTitle.textContent = project.title;
    DOM.detailCreatorName.textContent = project.creator_name;
    DOM.detailDescription.textContent = project.description || 'Описание отсутствует.';
    DOM.detailStartDate.textContent = formatDate(project.start_date);
    DOM.detailEndDate.textContent = formatDate(project.planned_end_date);

    // Новые характеристики
    DOM.detailAddress.textContent = project.address || 'Не указан';
    DOM.detailRooms.textContent = project.rooms || '—';
    DOM.detailArea.textContent = project.area ? `${project.area} кв. м` : '—';
    DOM.detailPrice.textContent = project.price ? `${new Intl.NumberFormat('ru-RU').format(project.price)} руб.` : '—';
    DOM.detailFloor.textContent = project.floor || '—';
    DOM.detailHouseType.textContent = project.house_type || '—';
    DOM.detailDistrict.textContent = project.district || '—';
    DOM.detailBuildYear.textContent = project.build_year || '—';
    DOM.detailRenovation.textContent = project.renovation || '—';

    // Риелтор и его статус
    DOM.detailRealtorName.textContent = project.realtor_name || 'Не назначен';
    const realtorBadge = DOM.detailRealtorStatusBadge;
    if (project.realtor_id) {
        realtorBadge.style.display = 'inline-block';
        if (project.realtor_accepted === 0) {
            realtorBadge.textContent = 'На согласовании';
            realtorBadge.style.backgroundColor = '#f57c00';
            realtorBadge.style.color = '#fff';
        } else if (project.realtor_accepted === 1) {
            realtorBadge.textContent = 'Принят риелтором';
            realtorBadge.style.backgroundColor = '#2e7d32';
            realtorBadge.style.color = '#fff';
        } else if (project.realtor_accepted === -1) {
            realtorBadge.textContent = 'Отклонен риелтором';
            realtorBadge.style.backgroundColor = '#c62828';
            realtorBadge.style.color = '#fff';
        }
    } else {
        realtorBadge.style.display = 'none';
    }

    // Покупатель
    if (project.buyer_id) {
        DOM.detailBuyerRow.style.display = 'block';
        DOM.detailBuyerName.textContent = project.buyer_name || 'Клиент-Покупатель';
        const buyerBadge = DOM.detailBuyerStatusBadge;
        if (project.buyer_approved === 1) {
            buyerBadge.textContent = 'Сделка подтверждена';
            buyerBadge.style.backgroundColor = '#2e7d32';
            buyerBadge.style.color = '#fff';
        } else {
            buyerBadge.textContent = 'Ожидает подтверждения';
            buyerBadge.style.backgroundColor = '#f57c00';
            buyerBadge.style.color = '#fff';
        }
    } else {
        DOM.detailBuyerRow.style.display = 'none';
    }

    // Заполняем участников
    DOM.detailParticipantSeller.textContent = project.creator_name || 'Не назначен';

    if (project.realtor_id) {
        let realtorStatus = '';
        if (project.realtor_accepted === 0) realtorStatus = ' (На согласовании)';
        else if (project.realtor_accepted === 1) realtorStatus = ' (Подтвержден)';
        else if (project.realtor_accepted === 2) realtorStatus = ' (Предложение отправлено)';
        else if (project.realtor_accepted === -1) realtorStatus = ' (Отклонен)';
        DOM.detailParticipantRealtor.textContent = (project.realtor_name || 'Риелтор') + realtorStatus;
        DOM.detailParticipantRealtor.style.display = 'inline';
        DOM.sellerRealtorSelectContainer.classList.add('hidden');
    } else {
        DOM.detailParticipantRealtor.textContent = 'Не назначен';

        // Если текущий пользователь является продавцом-создателем, даем ему выбрать риелтора
        if (parseInt(project.creator_id) === parseInt(state.user.id) && state.user.roles.includes('seller')) {
            DOM.detailParticipantRealtor.style.display = 'none';
            DOM.sellerRealtorSelectContainer.classList.remove('hidden');

            DOM.dealRealtorSelect.innerHTML = '<option value="">-- Выберите риелтора --</option>';
            const realtors = state.usersList.filter(u => u.roles.includes('realtor'));
            realtors.forEach(r => {
                DOM.dealRealtorSelect.innerHTML += `<option value="${r.id}">${escapeHtml(r.full_name)}</option>`;
            });
        } else {
            DOM.detailParticipantRealtor.style.display = 'inline';
            DOM.sellerRealtorSelectContainer.classList.add('hidden');
        }
    }

    if (project.buyer_id) {
        let buyerStatus = project.buyer_approved === 1 ? ' (Покупка подтверждена)' : ' (Бронь/Просмотр)';
        DOM.detailParticipantBuyer.textContent = (project.buyer_name || 'Покупатель') + buyerStatus;
    } else {
        DOM.detailParticipantBuyer.textContent = 'Не назначен';
    }

    // Логика отображения кнопок workflow
    DOM.btnAcceptRealtor.classList.add('hidden');
    DOM.btnDeclineRealtor.classList.add('hidden');
    DOM.btnProposeRealtor.classList.add('hidden');
    DOM.btnBuyProperty.classList.add('hidden');
    DOM.btnConfirmDeal.classList.add('hidden');
    DOM.btnConfirmPurchase.classList.add('hidden');
    DOM.btnDeclineDeal.classList.add('hidden');
    DOM.btnFinishDeal.classList.add('hidden');

    const isUserRealtor = state.user.roles.includes('realtor');
    const isUserBuyer = state.user.roles.includes('buyer');
    const hasRealtor = !!project.realtor_id;
    const hasBuyer = !!project.buyer_id;

    if (isUserRealtor) {
        // 1. Предложить ведение, если у объекта нет риелтора и он в статусе Создано
        if (!hasRealtor && parseInt(project.status_id) === 1) {
            DOM.btnProposeRealtor.classList.remove('hidden');
        }
        // 2. Принять/отклонить, если назначен текущему риелтору и ждет подтверждения
        if (hasRealtor && parseInt(project.realtor_id) === parseInt(state.user.id) && project.realtor_accepted === 0) {
            DOM.btnAcceptRealtor.classList.remove('hidden');
            DOM.btnDeclineRealtor.classList.remove('hidden');
        }
        // 3. Подтвердить/отклонить действия, если подтвержденный риелтор
        if (hasRealtor && parseInt(project.realtor_id) === parseInt(state.user.id) && project.realtor_accepted === 1) {
            // Запись на просмотр при статусе "Готов к просмотру" (2)
            if (parseInt(project.status_id) === 2 && hasBuyer) {
                DOM.btnConfirmDeal.classList.remove('hidden');
                DOM.btnDeclineDeal.classList.remove('hidden');
            }
            // Квартира "На просмотре" (5)
            if (parseInt(project.status_id) === 5 && hasBuyer) {
                DOM.btnDeclineDeal.classList.remove('hidden');
            }
            // Сделка забронирована (3)
            if (parseInt(project.status_id) === 3 && hasBuyer) {
                DOM.btnFinishDeal.classList.remove('hidden');
                DOM.btnDeclineDeal.classList.remove('hidden');
            }
        }
    }

    if (isUserBuyer) {
        // 1. Записаться на просмотр, если объект "Готов к просмотру" и нет покупателя
        if (parseInt(project.status_id) === 2 && hasRealtor && project.realtor_accepted === 1 && !hasBuyer) {
            DOM.btnBuyProperty.classList.remove('hidden');
        }
        // 2. Купить квартиру, если статус "На просмотре" и текущий пользователь - покупатель
        if (parseInt(project.status_id) === 5 && parseInt(project.buyer_id) === parseInt(state.user.id)) {
            DOM.btnConfirmPurchase.classList.remove('hidden');
        }
    }

    // Статус-бейдж
    DOM.detailBadgeStatus.textContent = project.status_name;
    DOM.detailBadgeStatus.className = 'badge';
    if (project.status_name === 'Создано' || parseInt(project.status_id) === 1) DOM.detailBadgeStatus.classList.add('badge-idea');
    if (project.status_name === 'На просмотре' || parseInt(project.status_id) === 5) DOM.detailBadgeStatus.classList.add('badge-work');
    if (project.status_name === 'Забронировано' || parseInt(project.status_id) === 3) DOM.detailBadgeStatus.classList.add('badge-review');
    if (project.status_name === 'Продано' || parseInt(project.status_id) === 4) DOM.detailBadgeStatus.classList.add('badge-completed');

    // Прогресс
    DOM.detailProgressLabel.textContent = `${project.progress_percent}%`;
    DOM.detailProgressBar.style.width = `${project.progress_percent}%`;

    // Ссылка на репозиторий
    if (project.repository_link) {
        DOM.detailRepoLink.href = project.repository_link;
        DOM.detailRepoText.textContent = project.repository_link;
        DOM.detailRepoLink.style.display = 'inline-flex';
    } else {
        DOM.detailRepoLink.removeAttribute('href');
        DOM.detailRepoText.textContent = 'Ссылка отсутствует';
        DOM.detailRepoLink.style.display = 'inline-flex';
    }

    // Состав команды
    renderMembers(project);

    // История стадий
    renderHistory(project);

    // РАЗГРАНИЧЕНИЕ ПРАВ ДОСТУПА В КАРТОЧКЕ
    const isGlobalAdmin = state.user.roles.includes('admin');
    const isCreator = project.creator_id === state.user.id;
    const isAssignedRealtor = state.user.roles.includes('realtor') && project.realtor_id === state.user.id && project.realtor_accepted === 1;

    const canModify = isGlobalAdmin || isCreator || isAssignedRealtor;

    if (canModify) {
        DOM.editProjectBtn.classList.remove('hidden');
        DOM.deleteProjectBtn.classList.remove('hidden');
    } else {
        DOM.editProjectBtn.classList.add('hidden');
        DOM.deleteProjectBtn.classList.add('hidden');
    }
}

function renderMembers(project) {
    if (!DOM.membersList) return;
    DOM.membersList.innerHTML = '';

    const isGlobalAdmin = state.user.roles.includes('admin');
    const isCreator = project.creator_id === state.user.id;
    const isProjectManager = project.current_user_project_role === 'supervisor' || project.current_user_project_role === 'teamlead';

    // Список участников для фильтрации выпадающего списка select-user
    // Исключаем тех, кто уже состоит в проекте
    const currentMemberIds = project.members.map(m => m.user_id);
    const availableUsers = state.usersList.filter(u => !currentMemberIds.includes(u.id));

    if (DOM.memberSelectUser) {
        DOM.memberSelectUser.innerHTML = '<option value="">Выберите пользователя...</option>';
        availableUsers.forEach(u => {
            DOM.memberSelectUser.innerHTML += `<option value="${u.id}">${u.full_name} (${u.email})</option>`;
        });
    }

    project.members.forEach(member => {
        let roleName = 'Участник';
        let badgeClass = 'role-member';

        if (member.project_role === 'supervisor') {
            roleName = 'Руководитель';
            badgeClass = 'role-supervisor';
        } else if (member.project_role === 'teamlead') {
            roleName = 'Тимлид';
            badgeClass = 'role-teamlead';
        }

        // Кнопка удаления доступна если пользователь админ/менеджер, и мы не удаляем единственного руководителя
        // А также нельзя удалить самого себя, если ты единственный тимлид
        const canDeleteMember = (isGlobalAdmin || isCreator || isProjectManager) && (member.user_id !== state.user.id || project.members.length > 1);
        const canEditMemberRole = (isGlobalAdmin || isCreator || isProjectManager);

        const item = document.createElement('div');
        item.className = 'member-item';

        let actionsHtml = '';
        if (canEditMemberRole || canDeleteMember) {
            actionsHtml = '<div class="member-actions">';

            // Если есть права смены роли
            if (canEditMemberRole) {
                actionsHtml += `
                    <select class="member-role-inline-select" data-user-id="${member.user_id}" style="padding: 2px 4px; font-size: 0.75rem; width: auto; background: var(--bg-card);">
                        <option value="member" ${member.project_role === 'member' ? 'selected' : ''}>Участник</option>
                        <option value="teamlead" ${member.project_role === 'teamlead' ? 'selected' : ''}>Тимлид</option>
                        <option value="supervisor" ${member.project_role === 'supervisor' ? 'selected' : ''}>Руководитель</option>
                    </select>
                `;
            }

            if (canDeleteMember) {
                actionsHtml += `
                    <button class="btn-icon-danger delete-member-btn-trigger" data-user-id="${member.user_id}" title="Удалить из проекта">
                        <i class="fa-solid fa-user-minus"></i>
                    </button>
                `;
            }
            actionsHtml += '</div>';
        }

        item.innerHTML = `
            <div class="member-user-info">
                <div class="member-icon">
                    <span>${member.full_name.charAt(0).toUpperCase()}</span>
                </div>
                <div>
                    <span class="member-name">${escapeHtml(member.full_name)}</span>
                    <span class="member-role-badge ${badgeClass}" style="margin-left:8px;">${roleName}</span>
                </div>
            </div>
            ${actionsHtml}
        `;

        // Вешаем слушатели на inline действия
        if (canEditMemberRole) {
            const select = item.querySelector('.member-role-inline-select');
            select.addEventListener('change', async (e) => {
                await updateMemberRole(project.id, member.user_id, e.target.value);
            });
        }

        if (canDeleteMember) {
            const delBtn = item.querySelector('.delete-member-btn-trigger');
            delBtn.addEventListener('click', async () => {
                if (confirm(`Вы уверены, что хотите удалить ${member.full_name} из проекта?`)) {
                    await deleteMember(project.id, member.user_id);
                }
            });
        }

        DOM.membersList.appendChild(item);
    });
}

function renderHistory(project) {
    DOM.historyTimeline.innerHTML = '';

    if (project.stage_history.length === 0) {
        DOM.historyTimeline.innerHTML = '<p style="color:var(--text-muted); font-size:0.8rem;">История отсутствует.</p>';
        return;
    }

    project.stage_history.forEach((h, idx) => {
        const item = document.createElement('div');
        item.className = `timeline-item ${idx === 0 ? 'active' : ''}`;

        item.innerHTML = `
            <div class="timeline-content">
                <span class="timeline-status">${h.status_name}</span>
                <div class="timeline-meta">Изменил: <b>${escapeHtml(h.changed_by_name)}</b> • ${formatDateTime(h.changed_at)}</div>
            </div>
        `;

        DOM.historyTimeline.appendChild(item);
    });
}

// ================= УПРАВЛЕНИЕ УЧАСТНИКАМИ (КОМАНДА) =================
function toggleAddMemberForm() {
    if (DOM.addMemberFormContainer) {
        DOM.addMemberFormContainer.classList.toggle('hidden');
    }
}

async function handleAddMemberSubmit(e) {
    e.preventDefault();
    if (!DOM.memberSelectUser || !DOM.memberSelectRole) return;
    const userId = DOM.memberSelectUser.value;
    const projectRole = DOM.memberSelectRole.value;

    if (!userId) {
        showToast('Пожалуйста, выберите пользователя.', 'warning');
        return;
    }

    showLoader(true);
    try {
        const data = await apiFetch(`api/projects.php?action=member&id=${state.selectedProjectId}`, {
            method: 'POST',
            body: JSON.stringify({ user_id: userId, project_role: projectRole })
        });

        if (data.success) {
            showToast(data.message, 'success');
            toggleAddMemberForm();
            if (DOM.addMemberForm) DOM.addMemberForm.reset();
            // Перезагружаем карточку проекта
            await selectProject(state.selectedProjectId);
            // Перезагружаем список проектов, так как количество участников поменялось
            await loadProjects();
        }
    } catch (err) {
        // Ошибка в apiFetch
    } finally {
        showLoader(false);
    }
}

async function updateMemberRole(projectId, userId, newRole) {
    showLoader(true);
    try {
        const data = await apiFetch(`api/projects.php?action=member&id=${projectId}`, {
            method: 'PUT',
            body: JSON.stringify({ user_id: userId, project_role: newRole })
        });
        if (data.success) {
            showToast(data.message, 'success');
            await selectProject(projectId);
        }
    } catch (err) {
        // Ошибка в apiFetch
    } finally {
        showLoader(false);
    }
}

async function deleteMember(projectId, userId) {
    showLoader(true);
    try {
        const data = await apiFetch(`api/projects.php?action=member&id=${projectId}&user_id=${userId}`, {
            method: 'DELETE'
        });
        if (data.success) {
            showToast(data.message, 'success');
            await selectProject(projectId);
            await loadProjects();
        }
    } catch (err) {
        // Ошибка в apiFetch
    } finally {
        showLoader(false);
    }
}

function populateRealtorsDropdown(selectedRealtorId = null) {
    if (!DOM.formRealtor) return;
    DOM.formRealtor.innerHTML = '<option value="">-- Выберите риелтора --</option>';

    const realtors = state.usersList.filter(u => u.roles && u.roles.includes('realtor'));
    realtors.forEach(r => {
        const selected = (selectedRealtorId && parseInt(selectedRealtorId) === parseInt(r.id)) ? 'selected' : '';
        DOM.formRealtor.innerHTML += `<option value="${r.id}" ${selected}>${r.full_name}</option>`;
    });
}

// ================= СОЗДАНИЕ И РЕДАКТИРОВАНИЕ ПРОЕКТА (МОДАЛ) =================
function openProjectModal(projectId = null) {
    DOM.projectModal.classList.remove('hidden');

    // Выбор риелтора необязателен при первоначальном создании
    DOM.formRealtorGroup.classList.remove('hidden');
    DOM.formRealtor.removeAttribute('required');

    if (projectId) {
        // Режим редактирования
        DOM.modalProjectTitle.textContent = 'Редактирование проекта';
        DOM.btnSaveProject.textContent = 'Сохранить';

        // Находим проект в локальном состоянии
        const project = state.projects.find(p => p.id === projectId);
        if (project) {
            // Подтягиваем полную карточку
            showLoader(true);
            apiFetch(`api/projects.php?id=${projectId}`).then(data => {
                const fullProj = data.project;
                DOM.formProjectId.value = fullProj.id;
                DOM.formTitle.value = fullProj.title;
                DOM.formDescription.value = fullProj.description || '';
                DOM.formStatus.value = fullProj.status_id;
                DOM.formProgress.value = fullProj.progress_percent;
                DOM.formStartDate.value = fullProj.start_date || '';
                DOM.formEndDate.value = fullProj.planned_end_date || '';
                DOM.formRepo.value = fullProj.repository_link || '';

                DOM.formAddress.value = fullProj.address || '';
                DOM.formRooms.value = fullProj.rooms || '';
                DOM.formArea.value = fullProj.area || '';
                DOM.formPrice.value = fullProj.price || '';
                DOM.formFloor.value = fullProj.floor || '';
                DOM.formHouseType.value = fullProj.house_type || 'кирпичный';
                DOM.formDistrict.value = fullProj.district || 'Центральный';
                DOM.formBuildYear.value = fullProj.build_year || '';
                DOM.formRenovation.value = fullProj.renovation || 'Без ремонта';

                populateRealtorsDropdown(fullProj.realtor_id);
            }).finally(() => showLoader(false));
        }
    } else {
        // Режим создания
        DOM.modalProjectTitle.textContent = 'Создание нового проекта';
        DOM.btnSaveProject.textContent = 'Создать';

        DOM.formProjectId.value = '';
        DOM.projectForm.reset();
        // Устанавливаем статус по умолчанию "Идея" (id = 1)
        DOM.formStatus.value = 1;
        DOM.formProgress.value = 0;

        populateRealtorsDropdown();
    }
}

function closeProjectModal() {
    DOM.projectModal.classList.add('hidden');
}

// Открытие модала редактирования из детальной карточки
DOM.editProjectBtn.addEventListener('click', () => {
    if (state.selectedProjectId) {
        openProjectModal(state.selectedProjectId);
    }
});

async function handleProjectSubmit(e) {
    e.preventDefault();

    const projectId = DOM.formProjectId.value;
    const body = {
        title: DOM.formTitle.value,
        description: DOM.formDescription.value,
        status_id: parseInt(DOM.formStatus.value),
        progress_percent: parseInt(DOM.formProgress.value),
        start_date: DOM.formStartDate.value || null,
        planned_end_date: DOM.formEndDate.value || null,
        repository_link: DOM.formRepo.value || null,
        realtor_id: DOM.formRealtor.value ? parseInt(DOM.formRealtor.value) : null,

        address: DOM.formAddress.value,
        rooms: DOM.formRooms.value ? parseInt(DOM.formRooms.value) : null,
        area: DOM.formArea.value ? parseFloat(DOM.formArea.value) : null,
        price: DOM.formPrice.value ? parseFloat(DOM.formPrice.value) : null,
        floor: DOM.formFloor.value ? parseInt(DOM.formFloor.value) : null,
        house_type: DOM.formHouseType.value,
        district: DOM.formDistrict.value,
        build_year: DOM.formBuildYear.value ? parseInt(DOM.formBuildYear.value) : null,
        renovation: DOM.formRenovation.value
    };

    showLoader(true);
    try {
        let data;
        if (projectId) {
            // PUT редактирование
            data = await apiFetch(`api/projects.php?id=${projectId}`, {
                method: 'PUT',
                body: JSON.stringify(body)
            });
        } else {
            // POST создание
            data = await apiFetch('api/projects.php', {
                method: 'POST',
                body: JSON.stringify(body)
            });
        }

        if (data.success) {
            showToast(data.message, 'success');
            closeProjectModal();

            // Если создали, сбрасываем фильтры, чтобы увидеть проект в списке
            if (!projectId) {
                DOM.filterSearch.value = '';
                DOM.filterStatus.value = 0;
                DOM.filterMyOnly.checked = false;
            }

            await loadProjects();

            // Если редактировали, обновляем детальную карточку
            if (projectId) {
                await selectProject(parseInt(projectId));
            } else if (data.project_id) {
                // Если создали, автоматически открываем детальную карточку нового проекта
                await selectProject(data.project_id);
            }
        }
    } catch (err) {
        // Ошибка в apiFetch
    } finally {
        showLoader(false);
    }
}

async function handleProjectDelete() {
    if (!state.selectedProjectId) return;

    if (confirm('ВНИМАНИЕ! Вы действительно хотите безвозвратно удалить этот проект и всю историю его изменений?')) {
        showLoader(true);
        try {
            const data = await apiFetch(`api/projects.php?id=${state.selectedProjectId}`, {
                method: 'DELETE'
            });
            if (data.success) {
                showToast(data.message, 'success');
                state.selectedProjectId = null;
                DOM.projectPlaceholder.classList.remove('hidden');
                DOM.projectDetails.classList.add('hidden');
                await loadProjects();
            }
        } catch (err) {
            // Ошибка в apiFetch
        } finally {
            showLoader(false);
        }
    }
}

// ================= ВКЛАДКА: АДМИНИСТРИРОВАНИЕ (ADMIN PANEL) =================
async function loadAdminUsersTable() {
    showLoader(true);
    try {
        // Подтягиваем свежий список пользователей
        const usersData = await apiFetch('api/users.php');
        state.usersList = usersData.users;

        renderAdminUsersTable();
    } catch (err) {
        console.error(err);
    } finally {
        showLoader(false);
    }
}

function renderAdminUsersTable() {
    DOM.adminUsersTbody.innerHTML = '';

    state.usersList.forEach(u => {
        const tr = document.createElement('tr');

        // Предотвращаем смену роли или отключение самого себя
        const isSelf = u.id === state.user.id;
        const disabledAttr = isSelf ? 'disabled' : '';

        tr.innerHTML = `
            <td><b>${escapeHtml(u.full_name)}</b> ${isSelf ? '<span class="badge badge-idea" style="font-size:0.65rem; padding: 2px 6px;">Вы</span>' : ''}</td>
            <td>${escapeHtml(u.email)}</td>
            <td>${escapeHtml(u.group_name || '-')}</td>
            <td>
                <select class="admin-role-select" data-user-id="${u.id}" ${disabledAttr}>
                    <option value="buyer" ${u.roles.includes('buyer') ? 'selected' : ''}>Покупатель</option>
                    <option value="seller" ${u.roles.includes('seller') ? 'selected' : ''}>Продавец</option>
                    <option value="realtor" ${u.roles.includes('realtor') ? 'selected' : ''}>Риелтор</option>
                    <option value="admin" ${u.roles.includes('admin') ? 'selected' : ''}>Администратор</option>
                </select>
            </td>
            <td>
                ${isSelf ? '<span style="color: var(--text-muted);">-</span>' : `
                <label class="switch">
                    <input type="checkbox" class="admin-active-checkbox" data-user-id="${u.id}" ${u.is_active ? 'checked' : ''}>
                    <span class="slider"></span>
                </label>
                `}
            </td>
            <td>
                ${isSelf ? '<span style="color: var(--text-muted);">-</span>' : `
                <button class="btn btn-icon-danger btn-xs btn-delete-user-admin" data-user-id="${u.id}" title="Удалить пользователя">
                    <i class="fa-solid fa-trash-can"></i>
                </button>
                `}
            </td>
            <td>
                ${isSelf ? '<span style="color: var(--text-muted);">-</span>' : `
                <button class="btn btn-outline btn-xs btn-save-user-admin" data-user-id="${u.id}">
                    <i class="fa-solid fa-floppy-disk"></i> Сохранить
                </button>
                `}
            </td>
        `;

        // Навешиваем слушатели на кнопку сохранения в таблице админа
        if (!isSelf) {
            const saveBtn = tr.querySelector('.btn-save-user-admin');
            saveBtn.addEventListener('click', async () => {
                const roleSelect = tr.querySelector('.admin-role-select');
                const activeCheckbox = tr.querySelector('.admin-active-checkbox');
                await saveUserChangesAdmin(u.id, roleSelect.value, activeCheckbox.checked);
            });

            const deleteBtn = tr.querySelector('.btn-delete-user-admin');
            deleteBtn.addEventListener('click', async () => {
                await deleteUserAdmin(u.id);
            });
        }

        DOM.adminUsersTbody.appendChild(tr);
    });
}

async function saveUserChangesAdmin(userId, selectedRole, isActive) {
    showLoader(true);
    try {
        const data = await apiFetch(`api/users.php?id=${userId}`, {
            method: 'PUT',
            body: JSON.stringify({
                roles: [selectedRole],
                is_active: isActive ? 1 : 0
            })
        });

        if (data.success) {
            showToast(data.message, 'success');
            // Обновляем локальное состояние и перерендериваем
            await loadAdminUsersTable();
        }
    } catch (err) {
        // Ошибка в apiFetch
    } finally {
        showLoader(false);
    }
}

async function deleteUserAdmin(userId) {
    if (!confirm('Вы уверены, что хотите удалить этого пользователя и все связанные с ним данные?')) {
        return;
    }
    showLoader(true);
    try {
        const data = await apiFetch(`api/users.php?id=${userId}`, {
            method: 'DELETE'
        });

        if (data.success) {
            showToast(data.message, 'success');
            // Обновляем локальное состояние и перерендериваем
            await loadAdminUsersTable();
        }
    } catch (err) {
        // Ошибка в apiFetch
    } finally {
        showLoader(false);
    }
}

// ================= УТИЛИТАРНЫЕ ФУНКЦИИ (ДАТЫ, ЭКРАНИРОВАНИЕ) =================
function formatDate(dateStr) {
    if (!dateStr) return 'Не указана';
    const date = new Date(dateStr);
    if (isNaN(date.getTime())) return dateStr;
    return date.toLocaleDateString('ru-RU', { day: '2-digit', month: '2-digit', year: 'numeric' });
}

function formatDateTime(dateTimeStr) {
    if (!dateTimeStr) return '';
    const date = new Date(dateTimeStr);
    if (isNaN(date.getTime())) return dateTimeStr;
    return date.toLocaleDateString('ru-RU', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
}

function escapeHtml(text) {
    if (!text) return '';
    const map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };
    return text.replace(/[&<>"']/g, m => map[m]);
}

// ================= РАБОТА С КНОПКАМИ СДЕЛКИ (WORKFLOW) =================
async function executeWorkflowAction(action, projectId) {
    showLoader(true);
    try {
        const data = await apiFetch(`api/projects.php?action=${action}&id=${projectId}`, {
            method: 'POST',
            body: JSON.stringify({})
        });
        if (data.success) {
            showToast(data.message, 'success');
            await selectProject(projectId);
            await loadProjects();
        }
    } catch (err) {
        console.error(err);
    } finally {
        showLoader(false);
    }
}

DOM.btnAcceptRealtor.addEventListener('click', () => executeWorkflowAction('accept_realtor', state.selectedProjectId));
DOM.btnDeclineRealtor.addEventListener('click', () => executeWorkflowAction('decline_realtor', state.selectedProjectId));
DOM.btnProposeRealtor.addEventListener('click', () => executeWorkflowAction('propose_realtor', state.selectedProjectId));
DOM.btnBuyProperty.addEventListener('click', () => executeWorkflowAction('buy_property', state.selectedProjectId));
DOM.btnConfirmDeal.addEventListener('click', () => executeWorkflowAction('confirm_deal', state.selectedProjectId));
DOM.btnConfirmPurchase.addEventListener('click', () => executeWorkflowAction('confirm_purchase', state.selectedProjectId));
DOM.btnDeclineDeal.addEventListener('click', () => executeWorkflowAction('decline_deal', state.selectedProjectId));
DOM.btnFinishDeal.addEventListener('click', () => executeWorkflowAction('finish_deal', state.selectedProjectId));

function checkRealtorProposals() {
    const container = document.getElementById('realtor-notifications-container');
    if (!container) return;

    if (!state.user) {
        container.style.display = 'none';
        container.innerHTML = '';
        return;
    }

    const userId = parseInt(state.user.id);
    const isSeller = state.user.roles.includes('seller');
    const isRealtor = state.user.roles.includes('realtor');
    const isBuyer = state.user.roles.includes('buyer');

    let html = '';

    state.projects.forEach(p => {
        const pId = parseInt(p.id);
        const realtorId = p.realtor_id ? parseInt(p.realtor_id) : null;
        const buyerId = p.buyer_id ? parseInt(p.buyer_id) : null;
        const creatorId = p.creator_id ? parseInt(p.creator_id) : null;
        const statusId = parseInt(p.status_id);

        // 1. Seller Notifications
        if (isSeller && creatorId === userId) {
            // Realtor offers handling
            if (p.realtor_id && parseInt(p.realtor_accepted) === 2) {
                html += `
                    <div class="notification-bar" style="background: rgba(2, 136, 209, 0.2); border: 1px solid #0288d1; border-radius: 8px; padding: 12px 16px; margin-bottom: 15px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 4px 6px rgba(0,0,0,0.15);">
                        <div style="color: #fff; font-size: 0.95em;">
                            <i class="fa-solid fa-bell" style="color: #29b6f6; margin-right: 8px;"></i>
                            Риелтор <b>${escapeHtml(p.realtor_name || 'Неизвестный риелтор')}</b> предлагает вести объект <b>${escapeHtml(p.title)}</b> (${escapeHtml(p.address || '')})
                        </div>
                        <div style="display: flex; gap: 8px;">
                            <button class="btn btn-success btn-xs" onclick="acceptRealtorProposal(${pId})">Принять</button>
                            <button class="btn btn-danger btn-xs" onclick="declineRealtorProposal(${pId})">Отклонить</button>
                        </div>
                    </div>
                `;
            }
            // Property Sold notification
            if (statusId === 4 && !localStorage.getItem('dismissed_sold_' + pId)) {
                html += `
                    <div class="notification-bar" style="background: rgba(46, 125, 50, 0.2); border: 1px solid #2e7d32; border-radius: 8px; padding: 12px 16px; margin-bottom: 15px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 4px 6px rgba(0,0,0,0.15);">
                        <div style="color: #fff; font-size: 0.95em;">
                            <i class="fa-solid fa-circle-check" style="color: #81c784; margin-right: 8px;"></i>
                            Ваш объект <b>${escapeHtml(p.title)}</b> (${escapeHtml(p.address || '')}) успешно продан покупателю <b>${escapeHtml(p.buyer_name || 'Неизвестный покупатель')}</b>!
                        </div>
                        <div>
                            <button class="btn btn-primary btn-xs" onclick="dismissSoldNotification(${pId})">Ок</button>
                        </div>
                    </div>
                `;
            }
        }

        // 2. Realtor Notifications
        if (isRealtor && realtorId === userId && parseInt(p.realtor_accepted) === 1) {
            // Buyer requested a viewing
            if (statusId === 2 && buyerId) {
                html += `
                    <div class="notification-bar" style="background: rgba(2, 136, 209, 0.2); border: 1px solid #0288d1; border-radius: 8px; padding: 12px 16px; margin-bottom: 15px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 4px 6px rgba(0,0,0,0.15);">
                        <div style="color: #fff; font-size: 0.95em;">
                            <i class="fa-solid fa-calendar-day" style="color: #29b6f6; margin-right: 8px;"></i>
                            Покупатель <b>${escapeHtml(p.buyer_name || 'Неизвестный покупатель')}</b> записался на просмотр объекта <b>${escapeHtml(p.title)}</b> (${escapeHtml(p.address || '')}).
                        </div>
                        <div style="display: flex; gap: 8px;">
                            <button class="btn btn-success btn-xs" onclick="executeNotificationAction('confirm_deal', ${pId})">Подтвердить просмотр</button>
                            <button class="btn btn-danger btn-xs" onclick="executeNotificationAction('decline_deal', ${pId})">Отменить запись</button>
                        </div>
                    </div>
                `;
            }
            // Buyer clicked "Купить квартиру" (status_id === 3 "Забронировано")
            if (statusId === 3 && buyerId) {
                html += `
                    <div class="notification-bar" style="background: rgba(245, 124, 0, 0.2); border: 1px solid #f57c00; border-radius: 8px; padding: 12px 16px; margin-bottom: 15px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 4px 6px rgba(0,0,0,0.15);">
                        <div style="color: #fff; font-size: 0.95em;">
                            <i class="fa-solid fa-cart-shopping" style="color: #ffb74d; margin-right: 8px;"></i>
                            Покупатель <b>${escapeHtml(p.buyer_name || 'Неизвестный покупатель')}</b> подтвердил покупку объекта <b>${escapeHtml(p.title)}</b> (${escapeHtml(p.address || '')}). Завершите сделку!
                        </div>
                        <div style="display: flex; gap: 8px;">
                            <button class="btn btn-success btn-xs" onclick="executeNotificationAction('finish_deal', ${pId})">Подтвердить сделку</button>
                            <button class="btn btn-danger btn-xs" onclick="executeNotificationAction('decline_deal', ${pId})">Отклонить сделку</button>
                        </div>
                    </div>
                `;
            }
            // Property Sold notification
            if (statusId === 4 && !localStorage.getItem('dismissed_sold_' + pId)) {
                html += `
                    <div class="notification-bar" style="background: rgba(46, 125, 50, 0.2); border: 1px solid #2e7d32; border-radius: 8px; padding: 12px 16px; margin-bottom: 15px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 4px 6px rgba(0,0,0,0.15);">
                        <div style="color: #fff; font-size: 0.95em;">
                            <i class="fa-solid fa-circle-check" style="color: #81c784; margin-right: 8px;"></i>
                            Сделка по объекту <b>${escapeHtml(p.title)}</b> успешно завершена! Объект официально продан.
                        </div>
                        <div>
                            <button class="btn btn-primary btn-xs" onclick="dismissSoldNotification(${pId})">Ок</button>
                        </div>
                    </div>
                `;
            }
        }

        // 3. Buyer Notifications
        if (isBuyer && buyerId === userId) {
            // Property Sold notification
            if (statusId === 4 && !localStorage.getItem('dismissed_sold_' + pId)) {
                html += `
                    <div class="notification-bar" style="background: rgba(46, 125, 50, 0.2); border: 1px solid #2e7d32; border-radius: 8px; padding: 12px 16px; margin-bottom: 15px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 4px 6px rgba(0,0,0,0.15);">
                        <div style="color: #fff; font-size: 0.95em;">
                            <i class="fa-solid fa-circle-check" style="color: #81c784; margin-right: 8px;"></i>
                            Поздравляем! Ваша покупка объекта <b>${escapeHtml(p.title)}</b> (${escapeHtml(p.address || '')}) подтверждена. Квартира успешно приобретена!
                        </div>
                        <div>
                            <button class="btn btn-primary btn-xs" onclick="dismissSoldNotification(${pId})">Ок</button>
                        </div>
                    </div>
                `;
            }
        }
    });

    if (html === '') {
        container.style.display = 'none';
        container.innerHTML = '';
    } else {
        container.style.display = 'block';
        container.innerHTML = html;
    }
}

window.dismissSoldNotification = (projectId) => {
    localStorage.setItem('dismissed_sold_' + projectId, 'true');
    checkRealtorProposals();
};

window.executeNotificationAction = async (action, projectId) => {
    showLoader(true);
    try {
        const data = await apiFetch(`api/projects.php?id=${projectId}&action=${action}`, {
            method: 'POST',
            body: JSON.stringify({})
        });
        if (data.success) {
            showToast(data.message, 'success');
            await selectProject(projectId);
            await loadProjects();
        }
    } catch (e) {
        console.error(e);
    } finally {
        showLoader(false);
    }
};

window.acceptRealtorProposal = async (projectId) => {
    showLoader(true);
    try {
        const data = await apiFetch(`api/projects.php?id=${projectId}&action=accept_realtor_proposal`, {
            method: 'POST'
        });
        if (data.success) {
            showToast(data.message, 'success');
            await loadProjects();
        }
    } catch (e) { }
    finally { showLoader(false); }
};

window.declineRealtorProposal = async (projectId) => {
    showLoader(true);
    try {
        const data = await apiFetch(`api/projects.php?id=${projectId}&action=decline_realtor_proposal`, {
            method: 'POST'
        });
        if (data.success) {
            showToast(data.message, 'success');
            await loadProjects();
        }
    } catch (e) { }
    finally { showLoader(false); }
};

async function handleAssignDealRealtor() {
    const realtorId = DOM.dealRealtorSelect.value;
    if (!realtorId) {
        showToast('Пожалуйста, выберите риелтора.', 'warning');
        return;
    }

    showLoader(true);
    try {
        const data = await apiFetch(`api/projects.php?action=assign_realtor&id=${state.selectedProjectId}`, {
            method: 'POST',
            body: JSON.stringify({ realtor_id: realtorId })
        });

        if (data.success) {
            showToast(data.message, 'success');
            await selectProject(state.selectedProjectId);
            await loadProjects();
        }
    } catch (err) {
        console.error(err);
    } finally {
        showLoader(false);
    }
}
