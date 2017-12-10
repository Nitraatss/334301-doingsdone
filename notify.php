<?php
require_once("vendor/autoload.php");
require_once("init.php");

// SQL запрос на получение всех невыполненных задач у которых дата выполнения больше или равна текущей дате/времени минус один час
$sql_request = "SELECT u.username, u.email, COUNT(t.title) AS count_t 
FROM tasks t 
JOIN users u ON t.user_id = u.id 
WHERE t.is_done = 0 AND t.deadline_date >= DATE_SUB(CURDATE(), INTERVAL 1 HOUR) 
GROUP BY u.username, u.email
ORDER BY username";
$result = mysqli_query($db_link, $sql_request);
$users_with_tasks = mysqli_fetch_all($result, MYSQLI_ASSOC);

foreach ($users_with_tasks as $key => $value)
{
    $u_name = $value["username"];
    $u_mail = $value["email"];

    // получаем названия задач
    $sql_request = "SELECT t.title, t.deadline_date
    FROM tasks t 
    JOIN users u ON t.user_id = u.id 
    WHERE t.is_done = 0 AND t.deadline_date >= DATE_SUB(CURDATE(), INTERVAL 1 HOUR) AND u.username = '". $u_name ."'";
    $result = mysqli_query($db_link, $sql_request);
    $waiting_tasks_data = mysqli_fetch_all($result, MYSQLI_ASSOC);
    $email_tasks = [];
    $deadline_dates = [];

    foreach ($waiting_tasks_data as $value1)
    {
        array_push($email_tasks, $value1["title"]);
        array_push($deadline_dates, $value1["deadline_date"]);
    }

    // конфигурация транспорта
    $transport = new Swift_SmtpTransport("smtp.mail.ru", 465, "ssl");
    $transport->setUsername("doingsdone@mail.ru");
    $transport->setPassword("rds7BgcL");

    // сообщение
    $message = new Swift_Message("Уведомление от сервиса «Дела в порядке»");
    $message -> setTo(array($u_mail => $u_name));
    $message -> setFrom(array("doingsdone@mail.ru" => "Дела в порядке"));

    // для одной зачаи или нескольких
    if (count($email_tasks)==1) {
        $message->setBody("
        <html>
        <body>
            <h2>Уважаемый(ая), " . $u_name . ".</h2><br><br>
            <p>
            У вас запланирована задача " . $email_tasks[0] . " на " . $deadline_dates[0] . "
            </p>
        </body>
        </html>");
    }
    else
    {
        for ($i = 0; $i < count($email_tasks); $i++) {
            $message->setBody("
            <html>
            <body>
                <h2>Уважаемый(ая), " . $u_name . ".</h2><br><br>
                <p>
                У вас запланирована задача " . $email_tasks[$i] . " на ". $deadline_dates[$i] . "
                </p>
            </body>
            </html>");
        }
    }

    // отсправка сообщения
    $mailer = new Swift_Mailer($transport);
    $mailer -> send($message);
}
?>