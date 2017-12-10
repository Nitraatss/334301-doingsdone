<?php
require_once("init.php");
require_once("vendor/autoload.php");

/**
* Функция шаблонизатор
*
* @param string $templateDir путь к файлу шаблона
* @param array $templateData массив с данными для этого шаблона
*
*/
function include_template($template_dir, $template_data) {
    if (file_exists($template_dir)) {
        foreach($template_data as $key => $value) {
            ${$key} = $value;
        }

        ob_start();
        require_once($template_dir);
        $template = ob_get_contents();
        ob_end_clean();

        return $template;
    }

    return("");
}

/**
* Расчет колличества задач по названи проекта.
*
* @param array $tasks список задач
* @param array $project_name название проекта
*
* @return int
*/
function category_count($tasks, $project_name, $projects) {
    // счетчик задач
    $count = 0;

    foreach($tasks as $key => $task) {
        if($project_name === "Все" || $projects[$task["project_id"]] === $project_name)
        {
            $count++;
        }
    }

    return $count;
}

/**
* Сравнения введенного email со значениями в массиве с данными пользователей
*
* @param string $email вводимый email
* @param array $users название проекта
*
* @return array
*/
function searchUserByEmail($email, $users) {
    $result = null;

    foreach ($users as $user) {
        if ($user["email"] == $email) {
            $result = $user;
            break;
        }
    }

    return $result;
}

/**
* Получение задач в зависимости от выбранного фильтра
*
* @param $db_link ресурс соединения
* @param string $sql_request запрос в БД
* @param integer $user_id id пользователя
*
* @return array
*/
function filter_tasks ($db_link, $sql_request, $user_id)
{
    $fliter_tasks = [];

    $stmt = mysqli_prepare($db_link, $sql_request);
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $t_title, $t_date, $pr_id, $is_done, $file_path);

    while (mysqli_stmt_fetch($stmt)) {
        $single_task_data = [
            "title" => $t_title,
            "deadline_date" => $t_date,
            "project_id" => $pr_id,
            "is_done" => $is_done,
            "file_path" => $file_path
        ];

        array_push($fliter_tasks, $single_task_data);
    }
    return ($fliter_tasks);
}

?>
