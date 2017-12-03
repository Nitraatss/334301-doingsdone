<?php
require_once("init.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    //проверка и заполнение формы добавления новой задачи
    if (!isset($_POST["email"])) {
        // переносим поля в переменную
        $task = $_POST;
        // указываем требуемые поля
        $required_add = ["title", "category", "deadline_date"];
        $dict = ["title" => "Название", "category" => "Проект", "deadline_date" => "Дата выполнения"];
        $task_errors = [];

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
            move_uploaded_file($tmp_name, "./" . $path);
        }

        // преобразование даты к единому формату
        foreach ($task as $key => $arg) {
            if($key == "deadline_date") {
                $task[$key] = date("d.m.Y", strtotime($task[$key]));
            }
        }

        // если есть ошибки возвращаем форму с сохраненными параметрами
        if (count($task_errors)) {
            $add_form = include_template("templates/add_from.php", ["$task" => $task, "task_errors" => $task_errors]);
            $overlay = 1;
            $modal_hidden = 1;
        }
        // если нет то добавляем новую задачу в базу
        else {
            // преобразуем формат даты
            $date = date("Y-m-d", strtotime($task['deadline_date']));
            // получаем id категории проекта для БД
            foreach($projects as $key => $value)
            {if ($task['category'] == $value) {
                    $pr_id = $key;
                }
            }
            // формируем запрос
            $sql_request = "INSERT INTO tasks (title, project_id, deadline_date, user_id) VALUES (?, ?, ?, ?)";

            $lol = mysqli_prepare($db_link, $sql_request);
            mysqli_stmt_bind_param($lol, 'sisi', $task["title"], $pr_id, $date, $user_id['id']);
            mysqli_stmt_execute($lol);

            header('location: /');
        }
    }
    // проверка формы регистрации пользователя на сайте
    elseif (isset($_POST[email]) && isset($_POST[password]) && isset($_POST[name])) {
        $registration_data = $_POST;
        // указываем требуемые поля
        $required_registration = ["email", "password", "name"];
        $dict = ["email" => "Email", "password" => "Пароль", "name" => "Имя"];
        $registration_errors = [];

        foreach ($_POST as $key => $value) {
            // проверяем наличие требуемых и полей и их заполнение
            if (in_array($key, $required_registration) && $value=="") {
                $registration_errors[$dict[$key]] = "Это поле надо заполнить";
            }
        }

        // проверяем существование email схожего email
        if ($user = searchUserByEmail($registration_data["email"], $users)) {
            $registration_errors[$dict["email"]] = "Пользователь с таким email уже существует";
        }

        // провереяем формат email
        if ( filter_var($registration_data["email"], FILTER_VALIDATE_EMAIL) == false) {
            $registration_errors[$dict["email"]] = "Не верный формат";
        }

        // если есть ошибки возвращаем форму с сохраненными параметрами
        if (count($registration_errors)) {
            $_SESSION['reg_er'] = $registration_errors;
            $_SESSION['reg_dat'] = $_POST;
            header("location: /index.php?register=false");
        }
        // если ошибок нет формируем запрос на добавление пользователя в БД
        else {
            $sql_request = "INSERT INTO users (username, email, pass) VALUES (?, ?, ?)";
            $us_name = $registration_data['name'];
            $em = $registration_data['email'];            
            $pasw = password_hash($registration_data['password'], PASSWORD_DEFAULT);

            $stmt = mysqli_prepare($db_link, $sql_request);
            mysqli_stmt_bind_param($stmt, 'sss', $us_name, $em, $pasw);            
            mysqli_stmt_execute($stmt);
        }
    }// авторизация пользователя
    else {
        // переносим поля в переменную
        $login_data = $_POST;
        // указываем требуемые поля
        $required_login = ["email", "password"];
        $dict = ["email" => "Email", "password" => "Пароль"];
        $login_errors = [];

        foreach ($_POST as $key => $value) {
            // проверяем наличие требуемых и полей и их заполнение
            if (in_array($key, $required_login) && $value=="") {
                $login_errors[$dict[$key]] = "Это поле надо заполнить";
            }
        }

        // проверяем существование email и совпадение пароля
        if ($user = searchUserByEmail($login_data["email"], $users)) {
            if (password_verify($login_data["password"], $user["pass"])) {
                $_SESSION["user"] = $user;
                
                //если пользователь авторизован формируем список его задач из БД
                if ($_SESSION["user"]) {
                    // создание массива с задачами для пользователя
                    $sql_request = "SELECT id FROM users WHERE username = '" . $_SESSION["user"]["username"] . "'";
                    $result = mysqli_query($db_link, $sql_request);
                    $user_id = mysqli_fetch_array($result, MYSQLI_ASSOC);

                    $sql_request = "SELECT title, deadline_date, project_id, is_done FROM tasks WHERE user_id = " . $user_id['id'];
                    $result = mysqli_query($db_link, $sql_request);
                    $tasks = mysqli_fetch_all($result, MYSQLI_ASSOC);
                    }
            }
            else {
                $login_errors[$dict["password"]] = "Неверный пароль";
            }
        }
        else {
            if ($login_data["email"]=="") {
                $login_errors[$dict[$key]] = "Это поле надо заполнить";}
            else {
                $login_errors[$dict["email"]] = "Такой пользователь не найден";
            }
        }

        // если есть ошибки возвращаем форму с сохраненными параметрами
        if (count($login_errors)) {
            $login_form = include_template("templates/login_form.php", ["login_data" => $_POST, "login_errors" => $login_errors]);
            $overlay = 1;
            $modal_hidden = 1;
        }
    }
}
?>