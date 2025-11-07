<?php
// db_connect.php
$DB_HOST = "localhost";
$DB_USER = "root";
$DB_PASS = "";
$DB_NAME = "registration_db";

$mysqli = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
if ($mysqli->connect_error) {
    http_response_code(500);
    echo json_encode(["status"=>false,"error"=>"DB connection failed: ".$mysqli->connect_error]);
    exit;
}
?>
