<?php
require_once("functions.php");
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

//проверка существования идентификатора
if(isset($_GET['project_id']))
{    $current_project = [];
    $project_id = $_GET['project_id'];
    foreach ($tasks as $value) {
        //если идентификатор совпал, то переносим все данные категории в отдельный массив
        if (($value["category"] == $projects[$project_id]) || ($project_id == 0)){
            array_push($current_project, $value);
        }
    }
    
}

//если идентификатора не существует возвращаем код ответа 404
foreach ($projects as $key=>$value)
{
    if($_GET['project_id']!=$key){
        http_response_code(404);
    }
}

//вывод шаблона формы
if (isset($_GET['add']))
{
    $form = include_template("templates/template_form.php", []);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
    //переносим поля в переменную
    $task = $_POST;
    //указываем требуемые поля
    $required = ['title', 'category', 'deadline_date'];
    $dict = ['title' => 'Название', 'category' => 'Проект', 'deadline_date' => 'Дата выполнения'];
    $errors = [];
    foreach ($_POST as $key => $value) {
        //проверяем наличие требуемых и полей и их заполнение
        if (in_array($key, $required) && $value=="") {
            $errors[$dict[$key]] = 'Это поле надо заполнить';
        }
    }

    // Проверка существования файла и его загрузка
    if (isset($_FILES['preview']['name']))
    {
        $tmp_name = $_FILES['preview']['tmp_name'];
        $path = $_FILES['preview']['name'];
        move_uploaded_file($tmp_name, './' . $path);
    }

    //преобразование даты к единому формату
    foreach($task as $key => $arg)
    {
        if($key == "deadline_date")
        {
            $task[$key] = date("d.m.Y", strtotime($task[$key]));
        }
    }

    //если есть ошибки возвращаем форму с сохраненными параметрами
    if (count($errors)) {
        $form = include_template('templates/template_form.php', ['$task' => $task, 'errors' => $errors]);
        $overlay = 1;
    }
    //если нет то в форму задач добавляем новую задачу в начало массива tasks
    else {
        array_unshift($tasks, $task);
    }
}


$page_content = include_template("templates/index.php", ["tasks" => isset($current_project)?$current_project:$tasks]);
$layout_content = include_template("templates/layout.php", [
"content_main" => $page_content,
"form" => isset($form)?$form:"",
"projects" => $projects,
"tasks" => $tasks,
"username" => "Эрик",
"title" => "Дела в порядке",
"overlay" => (isset($_GET['add']) || ($overlay == 1)) ?"overlay":""
]);

print($layout_content);
?>

