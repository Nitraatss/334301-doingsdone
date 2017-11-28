INSERT INTO users 
SET email = 'ignat.v@gmail.com',
username = 'Игнат',
pass = '$2y$10$OqvsKHQwr0Wk6FMZDoHo1uHoXd4UdxJG/5UDtUiie00XaxMHrW8ka';

INSERT INTO users 
SET email = 'kitty_93@li.ru',
username = 'Леночка',
pass = '$2y$10$bWtSjUhwgggtxrnJ7rxmIe63ABubHQs0AS0hgnOo41IEdMHkYoSVa';

INSERT INTO users 
SET email = 'warrior07@mail.ru',
username = 'Руслан',
pass = '$2y$10$2OxpEH7narYpkOT1H5cApezuzh10tZEEQ2axgFOaKW.55LxIJBgWW';

INSERT INTO projects 
SET category = 'Все';

INSERT INTO projects 
SET category = 'Входящие';

INSERT INTO projects 
SET category = 'Учеба';

INSERT INTO projects 
SET category = 'Работа';

INSERT INTO projects 
SET category = 'Домашние дела';

INSERT INTO projects 
SET category = 'Авто';

INSERT INTO tasks 
SET title = 'Собеседование в IT компании',
deadline_date = '2018-06-01',
is_done = 0,
project_id = 4;

INSERT INTO tasks 
SET title = 'Выполнить тестовое задание',
deadline_date = '2018-05-25',
is_done = 0,
project_id = 4,
user_id  = 3;

INSERT INTO tasks 
SET title = 'Сделать задание первого раздела',
deadline_date = '2018-04-21',
is_done = 1,
project_id = 3,
user_id = 3;

INSERT INTO tasks 
SET title = 'Встреча с другом',
deadline_date = '2018-04-22',
is_done = 0,
project_id = 2;
user_id = 2;

INSERT INTO tasks 
SET title = 'Купить корм для кота',
is_done = 0,
project_id = 5;

INSERT INTO tasks 
SET title = 'Заказать пиццу',
is_done = 0,
project_id = 5;

SELECT title, username
FROM tasks t
JOIN users u
ON user_id = u.id
where u.id = 3;

SELECT title, category 
FROM tasks t 
JOIN projects p 
ON project_id = p.id 
where p.id = 4;

UPDATE tasks 
SET is_done = 1
WHERE title = 'Купить корм для кота';

SELECT title, deadline_date
FROM tasks
where deadline_date = ADDDATE(
    UTC_DATE(), 
    INTERVAL 1 DAY);

UPDATE tasks 
SET title = 'Новое название'
WHERE id = 1;