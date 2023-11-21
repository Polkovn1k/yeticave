/*Очищаем таблицы*/
SET FOREIGN_KEY_CHECKS = 0;
TRUNCATE categories;
TRUNCATE users;
TRUNCATE lots;
TRUNCATE bets;
SET FOREIGN_KEY_CHECKS = 1;

/*Наполнение categories*/
INSERT INTO categories (name_category, character_code)
VALUES ('Доски и лыжи', 'boards'),
       ('Крепления', 'attachment'),
       ('Ботинки', 'boots'),
       ('Одежда', 'clothing'),
       ('Инструменты', 'tools'),
       ('Разное', 'other');

/*Наполнение users*/
INSERT INTO users (email, user_name, user_password, contacts)
VALUES ('admin@admin.com', 'Admin', '123456789012', 'Moscow'),
       ('default@google.com', 'Default user', 'qqqqqqqqqqqq', 'Kazan'),
       ('pretty@yandex.com', 'Elite', 'aaaaaaaaaaaa', 'Ust-Isum'),
       ('test@yahoo.com', 'Zombie111', 'zzzzzzzzzzzz', 'Tokyo, Qqqq 1 street'),
       ('123@burzum.com', 'WormUFC', 'q123Zadf45h4', 'New-York');

/*Наполнение lots*/
INSERT INTO lots (title, lot_description, img, start_price, date_end, step, user_id, winner_id, category_id)
VALUES ('2014 Rossignol District Snowboard', 'Элитный Сноуборд 2014 года', 'lot-1.jpg', 10999, '2023-10-04 21:19', 50, 2, null, 1),
       ('DC Ply Mens 2016/2017 Snowboard', 'Обычный Сноуборд 2016/2017 года', 'lot-2.jpg', 159999, '2023-11-06', 10, 3, null, 1),
       ('Крепления Union Contact Pro 2015 года размер L/XL', 'Супер крепкие крепления из бумаги', 'lot-3.jpg', 8000, '2023-11-18 19:30', 150, 3, null, 2),
       ('Ботинки для сноуборда DC Mutiny Charocal', 'Теплые шлепанцы для сноуборда', 'lot-4.jpg', 10999, '2023-12-03 21:03', 30, 4, null, 3),
       ('Куртка для сноуборда DC Mutiny Charocal', 'Дорогая шерстяная майка', 'lot-5.jpg', 7500, '2023-11-07 23:43', 15, 5, null, 4),
       ('Маска Oakley Canopy', 'Мемная маска Гая Фокса для low iq', 'lot-6.jpg', 5400, '2023-10-05 19:34', 70, 2, null, 6);

/*Наполнение bets*/
INSERT INTO bets (price_bet, user_id, lot_id)
VALUES (11099, 1, 1),
       (5610, 3, 6),
       (7500, 1, 5),
       (11119, 2, 4),
       (160009, 2, 2),
       (8900, 5, 3);

/*Получение всех категорий*/
SELECT name_category AS category FROM categories;

/*Получение самых новых открытых лотов - название, стартовую цену, ссылку на изображение, название категории*/
SELECT title, start_price, img, name_category
FROM lots JOIN categories ON lots.category_id = categories.id;

/*Показ лота по его ID (3) + название категории к которой принадлежит лот*/
SELECT lots.id, lots.date_creation, title, lot_description, img, start_price, date_end, step, name_category
FROM lots JOIN categories ON lots.category_id = categories.id
WHERE lots.id = 3;

/*Обновить название лота по его идентификатору (6)*/
UPDATE lots
SET title = 'Маска Oakley Canopy v2'
WHERE id = 6;

/*Получить список ставок для лота по его идентификатору с сортировкой по дате*/
SELECT bets.date_bet, bets.price_bet, lots.title, users.user_name
FROM bets
JOIN lots ON bets.lot_id = lots.id
JOIN users ON bets.user_id = users.id
WHERE bets.lot_id = 2
ORDER BY bets.date_bet DESC;
