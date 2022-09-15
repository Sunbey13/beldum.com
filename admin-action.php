<?php
session_start();
include ('dbconn.php');
include ('access/roles.php');

$received_data = $received_data = json_decode(file_get_contents("php://input"));

$data = array();

if ($received_data->action == "get") {
    $sql = "SELECT `user`.`user_id`, `user`.`name`, `user`.`surname`, `user`.`last_name`, `user`.`initials`, `user`.`login`, `user`.`role` FROM `user`";
    $query = $conn->query($sql);
    if ($query) {
        if ($query->num_rows > 1) {
            while ($row = $query->fetch_array()) {
                $row['role'] = $roles[$row['role']];
                $data[] = $row;
            }
        } else {
            $data['successMessage'] = 'Добавьте первого пользователя, чтобы увидеть его здесь!';
        }
    } else {
        $data['errorMessage'] = "Ошибка при получении данных!";
    }
}

if ($received_data->action == "getUser") {
    $sql = "SELECT `user`.`user_id`, `user`.`name`, `user`.`surname`, `user`.`last_name`, `user`.`login`, `user`.`role`, `user`.`driver_id` FROM `user` WHERE `user`.`user_id` = '$received_data->id'";
    $query = $conn->query($sql);
    if ($query) {
        $data = $query->fetch_array();
    } else {
        $data['errorMessage'] = "Ошибка при получении данных!";
    }
}

if ($received_data->action == "add") {
    $password = password_hash($received_data->password, PASSWORD_BCRYPT);
    if ($received_data->driver_id == '') {
        $sql = "INSERT INTO `user` VALUES (null,'$received_data->login', '$password', '$received_data->name','$received_data->surname', '$received_data->last_name', '$received_data->initials', '$received_data->role', null, null, null)";
    } else {
        $sql = "INSERT INTO `user` VALUES (null,'$received_data->login', '$password', '$received_data->name','$received_data->surname', '$received_data->last_name', '$received_data->initials', '$received_data->role', null, null, '$received_data->driver_id')";
    }
    $query = $conn->query($sql);
    if ($query) {
        $data['successMessage'] = "Пользователь успешно добавлен!";
    } else {
        $data['errorMessage'] = "При добавлении пользователя произошла ошибка!";
    }
}

if ($received_data->action == "update") {
    if ($received_data->driver_id == '') {
        $sql = "UPDATE `user` SET `name` = '$received_data->name', `surname` = '$received_data->surname', `last_name` = '$received_data->last_name', `initials` = '$received_data->initials', `login` = '$received_data->login', `role` = '$received_data->role', `driver_id` = null WHERE `user_id` = '$received_data->id'";
    } else {
        $sql = "UPDATE `user` SET `name` = '$received_data->name', `surname` = '$received_data->surname', `last_name` = '$received_data->last_name', `initials` = '$received_data->initials', `login` = '$received_data->login', `role` = '$received_data->role', `driver_id` = '$received_data->driver_id' WHERE `user_id` = '$received_data->id'";
    }
    $query = $conn->query($sql);
    if ($query) {
        $data['successMessage'] = "Данные пользователя успешно обновлены!";
    } else {
        $data['errorMessage'] = "При обновлении данных пользователя произошла ошибка!";
    }
}

if ($received_data->action == "delete") {
    $sql = "DELETE FROM `user` WHERE `user_id` = '$received_data->id'";
    $query = $conn->query($sql);
    if ($query) {
        $data['successMessage'] = "Пользователь успешно удален!";
    } else {
        $data['errorMessage'] = "При удалении пользователя произошла ошибка!";
    }
}

if ($received_data->action == "getDrivers") {
    $sql = "SELECT driver.driver_id, driver.name, driver.surname, driver.last_name FROM `driver`";
    $query = $conn->query($sql);
    if ($query) {
        while ($row = $query->fetch_array()) {
            $data[] = $row;
        }
    } else {
        $data['errorMessage'] = "Ошибка при получении данных водителей!";
    }
}
$conn->close();
echo json_encode($data);
die();
?>