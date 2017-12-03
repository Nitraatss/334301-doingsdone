<?php
require_once("init.php");

/**
* Функция шаблонизатор
*
* @param string $templateDir путь к файлу шаблона
* @param array $templateData массив с данными для этого шаблона
*
*/
function include_template($template_dir, $template_data)
{
    if (file_exists($template_dir))
    {
        foreach($template_data as $key => $value)
        {
            ${$key} = $value;
        }

        ob_start();
        require_once($template_dir);
        $template = ob_get_contents();
        ob_end_clean();
        
        return $template;
    }

    else
    {
        return("");
    }
}

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

/**
* Сравнения введенного email со значениями в массиве с данными пользователей
*
* @param string $email список задач
* @param array $users название проекта
*
* @return array
*/
function searchUserByEmail($email, $users) 
{
    $result = null;
    foreach ($users as $user) {
        if ($user["email"] == $email) {
            $result = $user;
            break;
        }
    }

    return $result;
}
?>