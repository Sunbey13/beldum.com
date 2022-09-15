<?php
session_start();
include('../dbconn.php');

$received_data = json_decode(file_get_contents("php://input"));

$data = array();

if($received_data->action == 'get') {
    $sql = "SELECT * FROM `driver`";
    $driver_query = $conn->query($sql);
    if ($driver_query) {
        if ($driver_query->num_rows > 0) {
            while ($row = $driver_query->fetch_array()) {
                $data[] = $row;
            }
        } else {
            $data['successMessage'] = 'Добавьте первого водителя, чтобы увидеть его здесь!';
        }
    } else {
        $data['errorMessage'] = 'Ошибка при получении данных!';
    }
}

if($received_data->action == 'getDriver') {
    $sql = "SELECT * FROM `driver` WHERE `driver_id` = '$received_data->id'";
    $driver_query = $conn->query($sql);
    if ($driver_query) {
        $data = $driver_query->fetch_array();
    } else {
        $data['errorMessage'] = "Ошибка при получении данных!";
    }
}

if($received_data->action == 'add') {
    $driver_sql = "INSERT INTO `driver` VALUES (null, '$received_data->name', '$received_data->surname', '$received_data->last_name', '$received_data->initials', '$received_data->phone', '$received_data->licenses', '$received_data->date_of_birth', '$received_data->passport_series_number', '$received_data->passport_issue_date', '$received_data->passport_issued_by', '$received_data->contract_number', '$received_data->contract_date', '$received_data->ie_name', '$received_data->bank_iban', '$received_data->bank_bik')";
    $driver_query = $conn->query($driver_sql);
    if ($driver_query) {
        $data['successMessage'] = "Водитель успешно добавлен!";
    } else {
        $data['errorMessage'] = "При добавлении водителя произошла ошибка!";
    }
}

if($received_data->action == 'update') {
    $driver_sql = "UPDATE `driver` SET `name` = '$received_data->name', `surname` = '$received_data->surname', `last_name` = '$received_data->last_name', `initials` = '$received_data->initials', `phone` = '$received_data->phone', `licenses` = '$received_data->licenses', `date_of_birth` = '$received_data->date_of_birth', `passport_series_number` = '$received_data->passport_series_number', `passport_issued_by` = '$received_data->passport_issued_by', `passport_issue_date` = '$received_data->passport_issue_date', `contract_number` = '$received_data->contract_number', `contract_date` = '$received_data->contract_date', `ie_name` = '$received_data->ie_name', `bank_iban` = '$received_data->bank_iban', `bank_bik` = '$received_data->bank_bik' WHERE `driver_id` = '$received_data->driver_id'";
    $driver_query = $conn->query($driver_sql);
    if ($driver_query) {
        $data['successMessage'] = "Данные водителя успешно обновлены!";
    } else {
        $data['errorMessage'] = "При обновлении данных водителя произошла ошибка!";
    }
}

if ($received_data->action == "delete") {
    $driver_sql = "DELETE FROM `driver` WHERE `driver_id` = '$received_data->driver_id'";
    $driver_query = $conn->query($driver_sql);
    if ($driver_query) {
        $data['successMessage'] = "Водитель успешно удален!";
    } else {
        $data['errorMessage'] = "При удалении водителя произошла ошибка!";
    }
}
$conn->close();
echo json_encode($data);
die();
?>