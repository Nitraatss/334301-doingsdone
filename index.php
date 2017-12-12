<?php
// создание сессии
session_start();

require_once("functions.php");
require_once("mysql_helper.php");
require_once("init.php");
require_once("vendor/autoload.php");

// задачи пользователя
$tasks;
// категории задач
$projects = ["Все"];
// текущая дата
$current_date;

// получение данных о пользователях
$sql_request = "SELECT email, username, pass FROM users";
$result = mysqli_query($db_link, $sql_request);
$users = mysqli_fetch_all($result, MYSQLI_ASSOC);

if ($_SESSION["user"]) {
    $sql_request = "SELECT id FROM users WHERE username = '" . $_SESSION["user"]["username"] . "'";
    $result = mysqli_query($db_link, $sql_request);
    $user_id = mysqli_fetch_array($result, MYSQLI_ASSOC);

    // создание массива с категориями проектов
    $sql_request = "SELECT category FROM projects WHERE user_id = " . $user_id['id'];
    $result = mysqli_query($db_link, $sql_request);
    $categories = mysqli_fetch_all($result, MYSQLI_ASSOC);
    foreach($categories as $key => $value) {
        array_push($projects, $value[category]);
    }

    // создание массива с задачами для пользователя
    $sql_request = "SELECT title, DATE_FORMAT(deadline_date, '%d.%m.%Y') as deadline_date, project_id, is_done, id, file_path FROM tasks WHERE user_id = " . $user_id['id'];
    $result = mysqli_query($db_link, $sql_request);
    $tasks = mysqli_fetch_all($result, MYSQLI_ASSOC);

    // получение текущей даты
    $sql_request = "SELECT DATE_FORMAT(CURDATE() , '%d.%m.%Y')";
    $result = mysqli_query($db_link, $sql_request);
    $curent_day = mysqli_fetch_array($result, MYSQLI_ASSOC);

    foreach ($curent_day as $value) {
        $current_date = $value;
    }
}

$overlay;
$modal_hidden;
$login_form;

// проверка наличия параметра запроса login, для показа формы ввода email и пароля
if (isset($_GET["login"])) {
    $login_form = include_template("templates/login_form.php", []);
    $overlay = 1;
    $modal_hidden = 1;
}

// обнуление пользовательской сессии при нажатии на Выход
require_once("templates/logout.php");

$current_project;

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

// отображение отдельных задач
// массив для задач с условиями
$specific_tasks;

if (isset($_GET["task_switch"])) {

    // задачи на сегодня
    if ($_GET["task_switch"] == "today") {
        $sql_request = "SELECT title, DATE_FORMAT(deadline_date, '%d.%m.%Y') as deadline_date, project_id, is_done, file_path, id FROM tasks WHERE user_id = ? AND deadline_date = CURDATE()";
        $specific_tasks = filter_tasks ($db_link, $sql_request, $user_id["id"]);
    }

    // задачи на завтра
    if ($_GET["task_switch"] == "tomorrow") {
        $sql_request = "SELECT title, DATE_FORMAT(deadline_date, '%d.%m.%Y') as deadline_date, project_id, is_done, file_path, id FROM tasks WHERE user_id = ? AND deadline_date = ADDDATE(CURDATE(), INTERVAL 1 DAY);";
        $specific_tasks = filter_tasks ($db_link, $sql_request, $user_id["id"]);
    }

    // просроченные задачи
    if ($_GET["task_switch"]=='wasted') {
        $sql_request = "SELECT title, DATE_FORMAT(deadline_date, '%d.%m.%Y') as deadline_date, project_id, is_done, file_path, id FROM tasks WHERE user_id = ? AND deadline_date < CURDATE()";
        $specific_tasks = filter_tasks ($db_link, $sql_request, $user_id["id"]);
    }
}

$add_form;

// вывод шаблона формы добавления задачи при нажатии на кнопку добавить задачу
if (isset($_GET["add"])) {
    $add_form = include_template("templates/add_from.php", ["projects" => $projects]);
    $overlay = 1;
    $modal_hidden = 1;
}

// вывод шаблона формы добавления проекта при нажатии на кнопку добавить проект
if (isset($_GET["add_project"])) {
    $add_form = include_template("templates/add_project_form.php", []);
    $overlay = 1;
    $modal_hidden = 1;
}

// получение данных из форм
// добавление задачи
require_once("add_task.php");
// добавление проекта
require_once("add_project.php");
// регистрация и авторизация
require_once("reg_auth.php");
// поиск
require_once("search.php");

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
    exit;
}

// смена статуса по клику на задачу
if (isset($_GET["changestatus"]))
{
    $change_stat = $_GET["changestatus"];

    foreach ($tasks as $key => $value)
    {
        if ($change_stat == $value["id"])
        {
            if ($value['is_done'] == 0) {
                $sql_request = "UPDATE tasks SET is_done = 1 WHERE title = '". $value['title'] ."' AND user_id = " . $user_id['id'] . " AND id = " . $value['id'] . "";
                mysqli_query($db_link, $sql_request);

            }
            else {
                $sql_request = "UPDATE tasks SET is_done = 0 WHERE title = '". $value['title'] ."'";
                mysqli_query($db_link, $sql_request);
            }

        }

    }

    header("location: /");
    exit;
}

// если cookie существует, то присваиваем параметру значение отметки в чекбоксе
if (isset($_COOKIE["CheckCookie"]))
{
    $_SESSION["check"] = "checked";
}
// если не существует, то снимаем отметку
else
{
    $_SESSION["check"] = "";
}

// проверка выбранного проекта в меню слева. если проекта не существует возвращаем код ответа 404
if (isset ($_GET["project_id"])) {
    if(!isset($projects[$_GET["project_id"]])) {
        http_response_code(404);
        header("location: /404.php");
    }
}

// вывод поля задач
$page_content = include_template("templates/index.php", [
    "tasks" => isset($current_project)?$current_project:$tasks,
    "check" => $_SESSION["check"],
    "current_date" => $current_date,
    "specific_tasks" => $specific_tasks
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

