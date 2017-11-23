<?php
require_once("functions.php");
require_once("userdata.php");

// показывать или нет выполненные задачи
$show_complete_tasks = rand(0, 1);
// устанавливаем часовой пояс в Московское время
date_default_timezone_set("Europe/Moscow");
$days = rand(-3, 3);
$task_deadline_ts = strtotime("+" . $days . " day midnight"); // метка времени даты выполнения задачи
$current_ts = strtotime("now midnight"); // текущая метка времени
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

//создание сессии
session_start();

//проверка наличия параметра запроса login, для показа форму ввода email и пароля
if(isset($_GET["login"]))
{
    $login_form = include_template("templates/login_form.php", []);
    $overlay = 1;
    $modal_hidden = 1;
}

//обнуление пользовательской сессии
require_once("templates/logout.php");

if($_SERVER["REQUEST_METHOD"] == "POST")
{
    //переносим поля в переменную
    $login_data = $_POST;
    //указываем требуемые поля
    $required_login = ["email", "password"];
    $dict = ["email" => "Email", "password" => "Пароль"];
    $login_errors = [];

    foreach ($_POST as $key => $value) {
        //проверяем наличие требуемых и полей и их заполнение
        if (in_array($key, $required_login) && $value=="") {
            $login_errors[$dict[$key]] = "Это поле надо заполнить";
        }
    }

    //проверяем существование email и совпадение пароля
    if ($user = searchUserByEmail($login_data["email"], $users)) {
        if (password_verify($login_data["password"], $user["password"])) {
            $_SESSION["user"] = $user;
        }
        else {
            $login_errors[$dict["password"]] = "Неверный пароль";
        }
    }
    else {
        if ($login_data["email"]=="")
        {$login_errors[$dict[$key]] = "Это поле надо заполнить";}
        else
        {$login_errors[$dict["email"]] = "Такой пользователь не найден";}
    }

    //если есть ошибки возвращаем форму с сохраненными параметрами
    if (count($login_errors)) {
        $login_form = include_template("templates/login_form.php", ["login_data" => $_POST, "login_errors" => $login_errors]);
        $overlay = 1;
        $modal_hidden = 1;
    }
}

//проверка существования идентификатора
if(isset($_GET["project_id"]))
{
    $current_project = [];
    $project_id = $_GET["project_id"];
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
    if($_GET["project_id"]!=$key){
        http_response_code(404);
    }
}

//вывод шаблона формы
if (isset($_GET["add"]))
{
    $add_form = include_template("templates/add_from.php", []);
    $overlay = 1;
    $modal_hidden = 1;
}

if ($_SERVER["REQUEST_METHOD"] == "POST")
{
    if (!isset($_POST["email"]))
    {
        //переносим поля в переменную
        $task = $_POST;
        //указываем требуемые поля
        $required_add = ["title", "category", "deadline_date"];
        $dict = ["title" => "Название", "category" => "Проект", "deadline_date" => "Дата выполнения"];
        $task_errors = [];
        foreach ($_POST as $key => $value) {
            //проверяем наличие требуемых и полей и их заполнение
            if (in_array($key, $required_add) && $value=="") {
                $task_errors[$dict[$key]] = "Это поле надо заполнить";
            }
        }

        // Проверка существования файла и его загрузка
        if (isset($_FILES["preview"]["name"]))
        {
            $tmp_name = $_FILES["preview"]["tmp_name"];
            $path = $_FILES["preview"]["name"];
            move_uploaded_file($tmp_name, "./" . $path);
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
        if (count($task_errors)) {
            $add_form = include_template("templates/add_from.php", ["$task" => $task, "task_errors" => $task_errors]);
            $overlay = 1;
            $modal_hidden = 1;
        }
        //если нет то в форму задач добавляем новую задачу в начало массива tasks
        else {
            array_unshift($tasks, $task);
            $overlay = 0;
            $modal_hidden = 0;
        }
    }
}

//параметр поумолчанию для чекбокса 
$_SESSION["check"] = "";

//при налаичии параметра в запросе по нажатию на чекбокс выполняем
if (isset($_GET["show_completed"]))
{
    //параметры cookie
    $name_cookie = "CheckCookie";
    $expire_cookie = strtotime("+30 days");
    $path_cookie = "/";
    $cookie_value = $_GET["show_completed"];

    //если cookie не существует, создаем
    if (!isset($_COOKIE["CheckCookie"]))
    {
        setcookie($name_cookie, json_encode($cookie_value), $expire_cookie, $path_cookie);
    }
    //если существует, то удаляем, чтобы снять отметку
    else
    {
        setcookie($name_cookie, json_encode($cookie_value), time()-3600, $path_cookie);
    }
    //
    header("location: /");
}

//если cookie сущетсует, то присваиваем параметру значение отметки в чекбоксе
if (isset($_COOKIE["CheckCookie"]))
{
    $_SESSION["check"] = "checked";
}
//если не существует, то снимаем отметку
else
{
    $_SESSION["check"] = "";
}

//вывод поля задач
$page_content = include_template("templates/index.php", [
"tasks" => isset($current_project)?$current_project:$tasks,
"check" => $_SESSION["check"]
]);
//вывод страница
//при отсутсвии сессии с данными пользователя отображается стартовая страница
$layout_content = include_template(isset($_SESSION["user"])?"templates/layout.php":"templates/guest.php", [
"content_main" => $page_content,
"add_form" => isset($add_form)?$add_form:"",
"login_form" => isset($login_form)?$login_form:"",
"projects" => $projects,
"tasks" => $tasks,
//отображение имени пользователя с на основании данных из сессии
"username" => $_SESSION["user"]["name"],
"title" => "Дела в порядке",
//отображение оверлея body при открытой форме
"overlay" => ($overlay == 1) ?"overlay":"",
//отображение формы
"modal_hidden" => ($modal_hidden == 1)?"":"hidden"
]);

print($layout_content);
?>

