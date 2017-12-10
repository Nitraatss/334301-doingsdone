<?php
require_once("init.php");
require_once("vendor/autoload.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // добавление новой задачи
    if (isset($_POST["project"])) {
        // переносим поля в переменную
        $project = $_POST;
        // указываем требуемые поля
        $required_add = ["project"];
        $dict = ["project" => "Название"];
        $project_errors = [];

        foreach ($_POST as $key => $value) {
            // проверяем наличие требуемых и полей и их заполнение
            if (in_array($key, $required_add) && $value=="") {
                $project_errors[$dict[$key]] = "Это поле надо заполнить";
            }
        }

        // если есть ошибки информируем
        if (count($project_errors)) {
            $add_form = include_template("templates/add_project_form.php", ["project_errors" => $project_errors]);
            $overlay = 1;
            $modal_hidden = 1;
        }
        // если нет то добавляем новый проект в базу
        else {
            // формируем запрос
            $sql_request = "INSERT INTO projects (category, user_id) VALUES (?, ?)";

            $stmt = mysqli_prepare($db_link, $sql_request);
            mysqli_stmt_bind_param($stmt, "si", $project["project"], $user_id["id"]);
            mysqli_stmt_execute($stmt);

            header("location: /");
        }
    }
}
?>
