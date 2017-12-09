<?php
require_once("init.php");
require_once("vendor/autoload.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["search"])) {
        mysqli_query($db_link, "CREATE FULLTEXT INDEX title_search ON tasks(title)");

        $search = $_POST["search"];

        if ($search){
            $sql_request = "SELECT title, DATE_FORMAT(deadline_date, '%d.%m.%Y') as deadline_date, project_id, is_done, id FROM tasks WHERE user_id = " . $user_id['id'] . " AND MATCH (title) AGAINST(?)";
            $stmt = db_get_prepare_stmt($db_link, $sql_request, [$search]);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            $specific_tasks = mysqli_fetch_all($result, MYSQLI_ASSOC);
        }
    }
}
?>
