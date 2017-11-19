<?php
// показывать или нет выполненные задачи
$show_complete_tasks = rand(0, 1);
// устанавливаем часовой пояс в Московское время
date_default_timezone_set('Europe/Moscow');
$days = rand(-3, 3);
$task_deadline_ts = strtotime("+" . $days . " day midnight"); // метка времени даты выполнения задачи
$current_ts = strtotime('now midnight'); // текущая метка времени
$date_deadline = date("d.m.Y",$task_deadline_ts);//дата выполнения задачи
$days_until_deadline = floor(($task_deadline_ts - $current_ts) / 86400); //кол-во дней до даты задачи
$projects = ["Все", "Входящие", "Учеба", "Работа", "Домашние дела", "Авто"]; //массив проектов
//массив задач
$tasks = [
    [
        "title" => "Собеседование в IT компании",
        "deadline_date" => "01.06.2018",
        "category" => $projects[3],
        "is_done" => false
    ],
    [
        "title" => "Выполнить тестовое задание",
        "deadline_date" => "25.05.2018",
        "category" => $projects[3],
        "is_done" => false
    ],
    [
        "title" => "Сделать задание первого раздела",
        "deadline_date" => "21.04.2018",
        "category" => $projects[2],
        "is_done" => "Да"
    ],
    [
        "title" => "Встреча с другом",
        "deadline_date" => "22.04.2018",
        "category" => $projects[1],
        "is_done" => false
    ],
    [
        "title" => "Купить корм для кота",
        "deadline_date" => "Нет",
        "category" => $projects[4],
        "is_done" => false
    ],
    [
        "title" => "Заказать пиццу",
        "deadline_date" => "Нет",
        "category" => $projects[4],
        "is_done" => false
    ]
];

/**
* Расчет колличества задач по названи проекта.
*
* @param array $tasks список задач
* @param array $project_name название проекта
*
* @return int
*/

function category_count($tasks, $project_name)
{
    $count = 0;//счетчик задач
    
    foreach($tasks as $task)
    {
        if($project_name === "Все" || $task["category"] === $project_name)
        {
            $count++;
        }
    }

    return $count;
}

require_once("functions.php");

$page_content = include_template("templates\index.php", ["tasks" => $tasks]);
$layout_content = include_template("templates\layout.php", [
    "content_main" => $page_content,
    "projects" => $projects,
    "tasks" => $tasks,
    "username" => "Эрик",
    "title" => "Дела в порядке"
]);

print($layout_content);
?>

