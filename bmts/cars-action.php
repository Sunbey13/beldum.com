<?php
session_start();
include('../dbconn.php');

$received_data = json_decode(file_get_contents("php://input"));

$data = array();

if($received_data->action == 'getCars') {
    $sql = "SELECT * FROM `car` INNER JOIN `driver` on `car`.driver_id = `driver`.`driver_id`";
    $query = $conn->query($sql);
    if ($query) {
        if ($query->num_rows > 0) {
            while ($row = $query->fetch_array()) {
                $data[] = $row;
            }
        } else {
            $data['successMessage'] = 'Добавьте первое авто, чтобы увидеть его здесь!';
        }
    } else {
        $data['errorMessage'] = 'Ошибка при получении данных!';
    }
}

if($received_data->action == 'getCar') {
    $sql = "SELECT * FROM `car` WHERE `car_id` = '$received_data->id'";
    $query = $conn->query($sql);
    if ($query) {
        $data = $query->fetch_array();
    } else {
        $data['errorMessage'] = "Ошибка при получении данных!";
    }
}

if($received_data->action == 'getDrivers') {
    $sql = "SELECT `driver_id`, `name`, `surname`, `last_name` FROM `driver`";
    $query = $conn->query($sql);
    if ($query) {
        while ($row = $query->fetch_array()) {
            $data[] = $row;
        }
    } else {
        $data['errorMessage'] = 'Ошибка при получении данных!';
    }
}

if($received_data->action == 'add') {
    $sql = "INSERT INTO `car` VALUES (null, '$received_data->driverID', '$received_data->mark', '$received_data->model', '$received_data->number', '$received_data->carcassW', '$received_data->carcassH', '$received_data->carcassL', '$received_data->carcassWeight')";
    $query = $conn->query($sql);
    if ($query) {
        $data['successMessage'] = "Авто успешно добавлено!";
    } else {
        $data['errorMessage'] = "При добавлении авто произошла ошибка!";
    }
}

if($received_data->action == 'update') {
    $sql = "UPDATE `car` SET `driver_id` = '$received_data->driverID', `mark` = '$received_data->mark', `model` = '$received_data->model', `number` = '$received_data->number', `carcass_w` = '$received_data->carcassW', `carcass_h` = '$received_data->carcassH', `carcass_l` = '$received_data->carcassL', `carcass_weight` = '$received_data->carcassWeight' WHERE `car_id` = '$received_data->id'";
    $query = $conn->query($sql);
    if ($query) {
        $data['successMessage'] = "Данные авто успешно обновлены!";
    } else {
        $data['errorMessage'] = "При обновлении данных авто произошла ошибка!";
    }
}

if ($received_data->action == "delete") {
    $sql = "DELETE FROM `car` WHERE `car_id` = '$received_data->id'";
    $query = $conn->query($sql);
    if ($query) {
        $data['successMessage'] = "Авто успешно удалено!";
    } else {
        $data['errorMessage'] = "При удалении авто произошла ошибка!";
    }
}
$conn->close();
echo json_encode($data);
die();
?>