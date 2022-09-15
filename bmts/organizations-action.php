<?php
session_start();
include('../dbconn.php');

$received_data = json_decode(file_get_contents("php://input"));

$data = array();

if($received_data->action == 'get') {
    $sql = "SELECT * FROM `organization`";
    $query = $conn->query($sql);
    if ($query) {
        if ($query->num_rows > 0) {
            while ($row = $query->fetch_array()) {
                $row['organ_address'] = json_decode($row['organ_address']);
                $data[] = $row;
            }
        } else {
            $data['successMessage'] = 'Добавьте первую организацию, чтобы увидеть ее здесь!';
        }
    } else {
        $data['errorMessage'] = 'Ошибка при получении данных!';
    }
}

if($received_data->action == 'getOrgan') {
    $sql = "SELECT * FROM `organization` WHERE `organ_id` = '$received_data->id'";
    $query = $conn->query($sql);
    if ($query) {
        $row = $query->fetch_array();
        $row['organ_address'] = json_decode($row['organ_address']);
        $data = $row;
    } else {
        $data['errorMessage'] = 'Ошибка при получении данных!';
    }
}

if($received_data->action == 'add') {
    $sql = "INSERT INTO `organization` VALUES (null, '$received_data->spec', '$received_data->organ_name', '$received_data->organ_address')";
echo $sql;
        $query = $conn->query($sql);
    if ($query) {
        $data['successMessage'] = "Организация успешно добавлена!";
    } else {
        $data['errorMessage'] = "При добавлении организации произошла ошибка!";
    }
}

if($received_data->action == 'update') {
    $sql = "UPDATE `organization` SET `spec` = '$received_data->spec', `organ_name` = '$received_data->organ_name', `organ_address` = '$received_data->organ_address'  WHERE `organ_id` = '$received_data->id'";
    $query = $conn->query($sql);
    if ($query) {
        $data['successMessage'] = "Данные организации успешно обновлены!";
    } else {
        $data['errorMessage'] = "При обновлении данных организации произошла ошибка!";
    }
}

if ($received_data->action == "delete") {
    $sql = "DELETE FROM `organization` WHERE `organ_id` = '$received_data->id'";
    $query = $conn->query($sql);
    if ($query) {
        $data['successMessage'] = "Организация успешно удалена!";
    } else {
        $data['errorMessage'] = "При удалении организации произошла ошибка!";
    }
}
$conn->close();
echo json_encode($data);
die();
?>