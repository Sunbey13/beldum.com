<?php
session_start();
include('../dbconn.php');

$received_data = json_decode(file_get_contents("php://input"));

$data = array();
$data['errorMessage'] = '';

if ($received_data->action == 'getListPerm') {
    $id = $_SESSION['id'];
    $sql = "SELECT `user`.`permission_number`, `user`.`permission_date` FROM `user` WHERE `user_id` = $id";
    $query = $conn->query($sql);
    if ($query) {
        $res = $query->fetch_array();
        if ($res['permission_number'] != null) {
            $data = $res;
        } else {
            $data['noPermissionDataMessage'] = "Данные доверенности не указаны!";
        }
    } else {
        $data['errorMessage'] = "Ошибка при получении данных о номере и дате доверенности!";
    }
}

if ($received_data->action == 'updatePermission') {
    $id = $_SESSION['id'];
    $sql = "UPDATE `user` SET `user`.`permission_number` = '$received_data->permission_number', `user`.`permission_date` = '$received_data->permission_date' WHERE `user_id` = $id";
    $query = $conn->query($sql);
    if ($query) {
        $data['successMessage'] = "Данные доверенности успешно обновлены!";
    } else {
        $data['errorMessage'] = "Ошибка при попытке изменения данных!";
    }
}

if ($received_data->action == 'getPoaSettings') {
    $sql = "SELECT * FROM `poa_settings`";
    $query = $conn->query($sql);
    if ($query) {
        $data = $query->fetch_array();
        $data['bank_data'] = json_decode($data['bank_data']);
    } else {
        $data['errorMessage'] = "Ошибка при получении настроек экспорта доверенностей!";
    }
}

if ($received_data->action == 'updatePoa') {
    $sql = "UPDATE `poa_settings` SET `poa_acc` = '$received_data->poa_acc', `poa_eng` = '$received_data->poa_eng'";
    $query = $conn->query($sql);
    if ($query) {
        $data['successMessage'] = "Данные экспорта успешно обновлены!";
    } else {
        $data['errorMessage'] = "Ошибка при попытке изменения данных!";
    }
}

if ($received_data->action == 'saveBankData') {
    $sql = "UPDATE `poa_settings` SET `bank_data` = '$received_data->bank_data'";
    $query = $conn->query($sql);
    if ($query) {
        $data['successMessage'] = "Банковские реквизиты успешно обновлены!";
    } else {
        $data['errorMessage'] = "Ошибка при попытке изменения данных!";
    }
}

$conn->close();
echo json_encode($data);
die();
?>