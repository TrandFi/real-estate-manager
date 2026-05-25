-- Скрипт наполнения базы данных real_estate_manager_db тестовыми данными

-- Отключаем проверки внешних ключей для безопасной очистки/наполнения
SET FOREIGN_KEY_CHECKS = 0;

-- Очищаем таблицы перед наполнением (используем DELETE, так как TRUNCATE блокируется ограничениями внешних ключей в MySQL)
DELETE FROM property_status_history;
DELETE FROM property_agents;
DELETE FROM properties;
DELETE FROM property_statuses;
DELETE FROM user_roles;
DELETE FROM users;
DELETE FROM roles;

SET FOREIGN_KEY_CHECKS = 1;

-- 1. Заполнение справочника ролей
INSERT INTO roles (id, name) VALUES 
(1, 'realtor'),
(2, 'buyer'),
(3, 'seller');

-- 2. Заполнение справочника стадий продажи недвижимости
INSERT INTO property_statuses (id, name) VALUES 
(1, 'Создано'),
(2, 'Готов к просмотру'),
(3, 'Забронировано'),
(4, 'Продано');

-- 3. Заполнение таблицы пользователей (10 пользователей)
-- Хэш пароля '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi' соответствует паролю 'password'
INSERT INTO users (id, fullname, email, password_hash, meta_info, is_active) VALUES
(1, 'Иванов Иван Иванович', 'ivanov@mail.ru', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '+7 (999) 111-22-33, Главный офис', 1),
(2, 'Петров Петр Петрович', 'petrov@agent.ru', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '+7 (999) 222-33-44, Агентство "ДомКлик"', 1),
(3, 'Сидоров Сидор Сидорович', 'sidorov@agent.ru', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '+7 (999) 333-44-55, Агентство "РеалЭстейт"', 1),
(4, 'Смирнова Анна Сергеевна', 'smirnova@agent.ru', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '+7 (999) 444-55-66, Частный риелтор', 1),
(5, 'Кузнецова Мария Дмитриевна', 'kuznetsova@client.ru', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '+7 (999) 555-66-77, Покупатель', 1),
(6, 'Васильев Алексей Игоревич', 'vasiliev@client.ru', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '+7 (999) 666-77-88, Покупатель', 1),
(7, 'Попов Дмитрий Александрович', 'popov@client.ru', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '+7 (999) 777-88-99, Продавец', 1),
(8, 'Соколов Михаил Юрьевич', 'sokolov@client.ru', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '+7 (999) 888-99-00, Покупатель', 1),
(9, 'Лебедев Артем Андреевич', 'lebedev@client.ru', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '+7 (999) 999-00-11, Продавец', 1),
(10, 'Козлов Егор Михайлович', 'kozlov@client.ru', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '+7 (999) 000-11-22, Покупатель', 1);

-- 4. Привязка пользователей к глобальным ролям
INSERT INTO user_roles (user_id, role_id) VALUES
(1, 1), -- Иванов (Realtor)
(2, 1), -- Петров (Realtor)
(3, 1), -- Сидоров (Realtor)
(4, 1), -- Смирнова (Realtor)
(5, 2), -- Кузнецова (Buyer)
(6, 2), -- Васильев (Buyer)
(7, 3), -- Попов (Seller)
(8, 2), -- Соколов (Buyer)
(9, 3), -- Лебедев (Seller)
(10, 2); -- Козлов (Buyer)

-- 5. Заполнение объявлений недвижимости (10 объявлений)
INSERT INTO properties (id, title, description, creator_id, status_id, progress_percent, start_date, planned_end_date, repository_link) VALUES
(1, '1-комнатная квартира, 40 кв.м, ул. Ленина, 12', 'Светлая квартира с евроремонтом на 5 этаже. Полностью меблирована, готова к заселению.', 2, 2, 10, '2026-05-01', '2026-08-01', 'https://my.matterport.com/show/?m=example1'),
(2, '2-комнатная квартира, 65 кв.м, пр. Мира, 45', 'Просторная квартира в новом ЖК. Панорамные окна, черновая отделка, парковочное место в комплекте.', 3, 2, 25, '2026-05-05', '2026-09-05', 'https://my.matterport.com/show/?m=example2'),
(3, '3-комнатная квартира, 88 кв.м, ул. Пушкина, 8', 'Исторический центр города. Высокие потолки, тихий двор, дизайнерский ремонт. Рядом парк.', 4, 3, 75, '2026-04-15', '2026-07-15', 'https://my.matterport.com/show/?m=example3'),
(4, 'Студия, 28 кв.м, ул. Гагарина, 30', 'Уютная студия для молодого специалиста или сдачи в аренду. Отличная транспортная развязка.', 2, 4, 100, '2026-03-10', '2026-05-20', 'https://my.matterport.com/show/?m=example4'),
(5, 'Коттедж, 150 кв.м, пос. Лесной, пер. Зеленый, 4', 'Двухэтажный дом из бруса с ухоженным участком 10 соток. Септик, скважина, газовое отопление.', 3, 2, 15, '2026-05-12', '2026-11-12', 'https://my.matterport.com/show/?m=example5'),
(6, 'Таунхаус, 120 кв.м, ул. Цветочная, 18', 'Современный загородный комплекс. Собственный гараж, зона барбекю, охраняемая территория.', 4, 1, 0, '2026-05-20', '2026-10-20', 'https://my.matterport.com/show/?m=example6'),
(7, '2-комнатная квартира, 54 кв.м, ул. Кирова, 7', 'Квартира улучшенной планировки. Раздельный санузел, застекленная лоджия, развитая инфраструктура.', 2, 2, 40, '2026-05-02', '2026-08-02', 'https://my.matterport.com/show/?m=example7'),
(8, '1-комнатная квартира, 35 кв.м, ул. Чехова, 22', 'Экономичный вариант. Требуется косметический ремонт. Отличный вид из окна на город.', 3, 3, 60, '2026-04-20', '2026-07-20', 'https://my.matterport.com/show/?m=example8'),
(9, 'Пентхаус, 210 кв.м, ул. Московская, 100', 'Элитное жилье на 25 этаже. Собственная терраса на крыше, система Умный дом, панорама 360°.', 1, 2, 50, '2026-04-01', '2026-10-01', 'https://my.matterport.com/show/?m=example9'),
(10, 'Земельный участок, 12 соток, с. Березовка', 'Участок под ИЖС. Электричество подведено, газ по границе участка. Ровный рельеф.', 4, 2, 30, '2026-05-10', '2026-09-10', 'https://my.matterport.com/show/?m=example10');

-- 6. Назначение агентов и покупателей на сделки
INSERT INTO property_agents (property_id, user_id, role) VALUES
(1, 2, 'lead_agent'),
(1, 5, 'buyer'),
(2, 3, 'lead_agent'),
(3, 4, 'lead_agent'),
(3, 3, 'co_agent'),
(3, 6, 'buyer'),
(4, 2, 'lead_agent'),
(4, 7, 'buyer'),
(5, 3, 'lead_agent'),
(7, 2, 'lead_agent'),
(8, 3, 'lead_agent'),
(8, 8, 'buyer'),
(9, 1, 'lead_agent'),
(9, 4, 'co_agent'),
(10, 4, 'lead_agent');

-- 7. История смены стадий объектов
INSERT INTO property_status_history (property_id, status_id, changed_by, changed_at) VALUES
(1, 1, 2, '2026-05-01 10:00:00'),
(1, 2, 2, '2026-05-01 12:00:00'),
(2, 1, 3, '2026-05-05 09:30:00'),
(2, 2, 3, '2026-05-05 11:00:00'),
(3, 1, 4, '2026-04-15 14:00:00'),
(3, 2, 4, '2026-04-16 10:00:00'),
(3, 3, 4, '2026-05-10 16:30:00'),
(4, 1, 2, '2026-03-10 09:00:00'),
(4, 2, 2, '2026-03-11 11:00:00'),
(4, 3, 2, '2026-04-20 15:00:00'),
(4, 4, 1, '2026-05-20 12:00:00');
