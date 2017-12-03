<?php
$db_host = "localhost";
$db_login = "root";
$db_password = "";
$db_name = "schema_db";

$db_link = mysqli_connect($db_host, $db_login, $db_password, $db_name);

if ($db_link == false)
{
    $db_error = mysqli_connect_error();
    
    $page_content = include_template("templates/error.php", [
        "db_error" => $db_error
    ]);
    
    print($page_content);
    exit();
}

?>