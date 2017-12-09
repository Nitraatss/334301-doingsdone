<?php
require_once("init.php");
require_once("vendor/autoload.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // регистрация пользователя
    if (isset($_POST["email"]) && isset($_POST["password"]) && isset($_POST["name"])) {
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
        if (filter_var($registration_data["email"], FILTER_VALIDATE_EMAIL) == false) {
            $registration_errors[$dict["email"]] = "Не верный формат";
        }

        // если есть ошибки возвращаем форму с сохраненными параметрами
        if (count($registration_errors)) {
            $_SESSION["reg_er"] = $registration_errors;
            $_SESSION["reg_dat"] = $_POST;
            header("location: /?register=false");
        }
        // если ошибок нет формируем запрос на добавление пользователя в БД
        else {
            $sql_request = "INSERT INTO users (username, email, pass, registration_date) VALUES (?, ?, ?, CURDATE())";
            $us_name = $registration_data["name"];
            $em = $registration_data["email"];
            $pasw = password_hash($registration_data["password"], PASSWORD_DEFAULT);

            $stmt = mysqli_prepare($db_link, $sql_request);
            mysqli_stmt_bind_param($stmt, "sss", $us_name, $em, $pasw);
            mysqli_stmt_bind_param($stmt, "sss", $us_name, $em, $pasw);
            mysqli_stmt_execute($stmt);
        }
    }
    // авторизация пользователя
    elseif (isset($_POST["email"]) && isset($_POST["password"])) {
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
                        header("location: /");
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
