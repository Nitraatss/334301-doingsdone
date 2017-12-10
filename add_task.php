<?php
require_once("init.php");
require_once("vendor/autoload.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // добавление новой задачи
    if (isset($_POST["title"]) || isset($_POST["category"]) || isset($_POST["deadline_date"]) || isset($_POST["file"])) {
        // переносим поля в переменную
        $task = $_POST;
        // указываем требуемые поля
        $required_add = ["title", "category"];
        $dict = ["title" => "Название", "category" => "Проект"];
        $task_errors = [];
        $destination;
        $date;

        foreach ($_POST as $key => $value) {
            // проверяем наличие требуемых и полей и их заполнение
            if (in_array($key, $required_add) && $value=="") {
                $task_errors[$dict[$key]] = "Это поле надо заполнить";
            }
        }

        // Проверка существования файла и его загрузка
        if (isset($_FILES["preview"]["name"])) {
            $tmp_name = $_FILES["preview"]["tmp_name"];
            $path = $_FILES["preview"]["name"];
            $destination = "./" . $path;
            move_uploaded_file($tmp_name, $destination);
        }

        if (count($projects) < 2)
        {
            $task_errors[$dict["category"]] = "Сначала создайте проект";
        }

        // если есть ошибки возвращаем форму с сохраненными параметрами
        if (count($task_errors)) {
            $add_form = include_template("templates/add_from.php", ["task" => $task, "task_errors" => $task_errors, "projects" => $projects]);
            $overlay = 1;
            $modal_hidden = 1;
        }
        // если нет то добавляем новую задачу в базу
        else {
            if ($task["deadline_date"] == null) {
                $date = null;
            }
            else {
                $date = $task["deadline_date"];
            }

            if ($destination == "./") {
                $destination = null;
            }

            // получаем id категории проекта для БД
            foreach($projects as $key => $value)
            {if ($task["category"] == $value) {
                    $pr_id = $key;
                }
            }
            // формируем запрос
            $sql_request = "INSERT INTO tasks (title, project_id, deadline_date, user_id, file_path, creation_date, is_done) VALUES (?, ?, ?, ?, ?, CURDATE(), 0)";
            $stmt = mysqli_prepare($db_link, $sql_request);
            mysqli_stmt_bind_param($stmt, "sisis", $task["title"], $pr_id, $date, $user_id["id"], $destination);
            mysqli_stmt_execute($stmt);

            header("location: /");
        }
    }
}
?>
