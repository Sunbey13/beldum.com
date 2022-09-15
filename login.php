<?php
session_start();
include ('dbconn.php');

$received_data = json_decode(file_get_contents("php://input"));

$out = array('error' => false);

$sql = "SELECT * FROM user WHERE login = '$received_data->login'";
$query = $conn->query($sql);

if ($query) {
    if($query->num_rows > 0) {
        $row = $query->fetch_array();
        if (password_verify($received_data->password, $row['password'])) {
            $_SESSION['id'] = $row['user_id'];
            $_SESSION['driver_id'] = $row['driver_id'];
            $_SESSION['login'] = $row['login'];
            $_SESSION['first_name'] = $row['first_name'];
            $_SESSION['last_name'] = $row['last_name'];
            $_SESSION['role'] = $row['role'];
            $out['role'] = $row['role'];
            $out['successMessage'] = "Успешно! Вход...";
        } else {
            $out['error'] = true;
            $out['errorMessage'] = "Ошибка. Неверный пароль!";
        }
    } else {
        $out['error'] = true;
        $out['errorMessage'] = "Ошибка. Пользователь не найден!";
    }
} else {
    $out['errorMessage'] = "Ошибка при выполнении запроса!";
}

$conn->close();
header("Content-type: application/json");
echo json_encode($out);
die();
?>
