<?php
// создание сессии
session_start();

require_once("functions.php");
require_once("init.php");

//задачи пользователя
$tasks;
//категории задач
$projects;

// получение данных о пользователях
$sql_request = "SELECT email, username, pass FROM users";
$result = mysqli_query($db_link, $sql_request);
$users = mysqli_fetch_all($result, MYSQLI_ASSOC);

// создание массива с категориями проектов
$sql_request = "SELECT category FROM projects";
$result = mysqli_query($db_link, $sql_request);
$categories = mysqli_fetch_all($result, MYSQLI_ASSOC);
foreach($categories as $key => $value) {
    $projects[$key] = $value[category];
}

if ($_SESSION["user"]) {
    // создание массива с задачами для пользователя
    $sql_request = "SELECT id FROM users WHERE username = '" . $_SESSION["user"]["username"] . "'";
    $result = mysqli_query($db_link, $sql_request);
    $user_id = mysqli_fetch_array($result, MYSQLI_ASSOC);

    $sql_request = "SELECT title, deadline_date, project_id, is_done, id FROM tasks WHERE user_id = " . $user_id['id'];
    $result = mysqli_query($db_link, $sql_request);
    $tasks = mysqli_fetch_all($result, MYSQLI_ASSOC);
}

// устанавливаем часовой пояс в Московское время
date_default_timezone_set("Europe/Moscow");
$days = rand(-3, 3);
$task_deadline_ts = strtotime("+" . $days . " day midnight"); // метка времени даты выполнения задачи
$current_ts = strtotime("now midnight"); // текущая метка времени
$date_deadline = date("d.m.Y",$task_deadline_ts);//дата выполнения задачи
$days_until_deadline = floor(($task_deadline_ts - $current_ts) / 86400); //кол-во дней до даты задачи

// проверка наличия параметра запроса login, для показа формы ввода email и пароля
if (isset($_GET["login"])) {
    $login_form = include_template("templates/login_form.php", []);
    $overlay = 1;
    $modal_hidden = 1;
}

// обнуление пользовательской сессии при нажатии на Выход
require_once("templates/logout.php");

// отображение задач одной категории
// проверка существования идентификатора
if (isset($_GET["project_id"])) {
    $current_project = [];
    $project_id = $_GET["project_id"];
    
    foreach ($tasks as $value) {
        // если идентификатор совпал, то переносим все данные категории в отдельный массив
        if (($projects[$value["project_id"]] == $projects[$project_id]) || ($project_id == 0)) {
            array_push($current_project, $value);
        }
    }
}

// если идентификатора не существует возвращаем код ответа 404
foreach ($projects as $key=>$value) {
    if ($_GET["project_id"]!=$key) {
        http_response_code(404);
    }
}

// вывод шаблона формы добавления задачи при нажатии на кнопку добавить задачу
if (isset($_GET["add"])) {
    $add_form = include_template("templates/add_from.php", []);
    $overlay = 1;
    $modal_hidden = 1;
}

// проверка форм отправки данных
require_once("form_post.php");

// параметр по умолчанию для чекбокса 
$_SESSION["check"] = "";

// при налаичии параметра в запросе по нажатию на чекбокс выполняем
if (isset($_GET["show_completed"])) {
    // параметры cookie
    $name_cookie = "CheckCookie";
    $expire_cookie = strtotime("+30 days");
    $path_cookie = "/";
    $cookie_value = $_GET["show_completed"];

    // если cookie не существует, создаем
    if (!isset($_COOKIE["CheckCookie"])) {
        setcookie($name_cookie, json_encode($cookie_value), $expire_cookie, $path_cookie);
    }
    // если существует, то удаляем, чтобы снять отметку
    else {
        setcookie($name_cookie, json_encode($cookie_value), time()-3600, $path_cookie);
    }

    header("location: /");
}

// смена статуса по клику на задачу
if (isset($_GET["changestatus"]))
{
    $a =  $_GET["changestatus"];

    foreach ($tasks as $key => $value)
    {
        if ($a == $key)
        {
            if ($value['is_done'] == 0)
            {
            $sql_request = "UPDATE tasks SET is_done = 1 WHERE title = '". $value['title'] ."' AND user_id = " . $user_id['id'] . " AND id = " . $value['id'] . "";
            mysqli_query($db_link, $sql_request);
            
            }
            else
            {
             $sql_request = "UPDATE tasks SET is_done = 0 WHERE title = '". $value['title'] ."'";
             mysqli_query($db_link, $sql_request);
            }
            
        }
        
    }

    header("location: /");
}

// если cookie сущетсует, то присваиваем параметру значение отметки в чекбоксе
if (isset($_COOKIE["CheckCookie"]))
{
    $_SESSION["check"] = "checked";
}
// если не существует, то снимаем отметку
else
{
    $_SESSION["check"] = "";
}

// вывод поля задач
$page_content = include_template("templates/index.php", [
"tasks" => isset($current_project)?$current_project:$tasks,
"check" => $_SESSION["check"]
]);

$registration_form;
// отображение формы регистрации
if ($_GET["register"] == "true") {
    $registration_form = include_template("templates/register_form.php", []);
}
else {
    $registration_form = include_template("templates/register_form.php", ["registration_data" => $_SESSION['reg_dat'], "registration_errors" => $_SESSION['reg_er']]);
}

$layout_content_template;
// отображение страницы регистрации
if (isset($_GET["register"])) {
    $layout_content_template = "templates/register.php";
}
else {
    // при отсутсвии сессии с данными пользователя отображается стартовая страница
    $layout_content_template = isset($_SESSION["user"])?"templates/layout.php":"templates/guest.php";
}

$layout_content = include_template($layout_content_template, [
"content_main" => $page_content,
"add_form" => isset($add_form)?$add_form:"",
"login_form" => isset($login_form)?$login_form:"",
"registration_form" => isset($registration_form)?$registration_form:"",
"projects" => $projects,
"tasks" => $tasks,
// отображение имени пользователя с на основании данных из сессии
"username" => $_SESSION["user"]["username"],
"title" => "Дела в порядке",
// отображение оверлея body при открытой форме
"overlay" => ($overlay == 1) ?"overlay":"",
// отображение формы
"modal_hidden" => ($modal_hidden == 1)?"":"hidden"
]);

print($layout_content);
?>

