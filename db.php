<?
$con = mysqli_connect($define['db']['adress'],$define['db']['name'],$define['db']['password'],$define['db']['tab']);
if ($con->connect_error) {
die(json_encode(array('status' => 400, 'text' => "Невозможно подключиться к БД")));
}
if (!mysqli_set_charset($con, "utf8"));

