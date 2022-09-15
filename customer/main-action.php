<?php
session_start();
include('../dbconn.php');

$received_data = json_decode(file_get_contents("php://input"));

$data = array();

if($received_data->action == 'getTasks') {
    $customer_id = $_SESSION['id'];
    $sql = "SELECT `task`.*, `user`.`name`, `organization`.`organ_name` FROM `task` INNER JOIN `organization` ON `task`.`organ_id` = `organization`.`organ_id` INNER JOIN `user` ON `task`.`customer_id` = `user`.`user_id` WHERE `task`.`customer_id` = '$customer_id'";
    $query = $conn->query($sql);
    if ($query) {
        if ($query->num_rows > 0) {
            while ($row = $query->fetch_array()) {
                $data[] = $row;
            }
        } else {
            $data['successMessage'] = 'Создайте певую заявку, чтобы увидеть ее здесь!';
        }
    } else {
        $data['errorMessage'] = 'Ошибка при получении данных!';
    }
}

if($received_data->action == 'getTask') {
    $sql = "SELECT `task`.*, `organization`.`organ_id` FROM `task` INNER JOIN `organization` ON `task`.`organ_id` = `organization`.`organ_id` INNER JOIN `user` ON `task`.`customer_id` = `user`.`user_id` WHERE `task`.`task_id` = '$received_data->task_id'";
    $query = $conn->query($sql);
    if ($query) {
        $data = $query->fetch_array();
    } else {
        $data['errorMessage'] = "Ошибка при получении данных!";
    }
}

if($received_data->action == 'add') {
    $customer_id = $_SESSION['id'];
    $sql = "INSERT INTO `task` VALUES (NULL, '$received_data->date', '$received_data->task_type', '$received_data->organ_id', '$received_data->organ_address', '$received_data->curator_organ', '$received_data->curator_organ_tel', '$customer_id', '$received_data->cargo_name', '$received_data->cargo_w', '$received_data->cargo_h', '$received_data->cargo_l', '$received_data->cargo_weight', '$received_data->curator_remeza', '$received_data->notes', NULL, 0)";
    $query = $conn->query($sql);
    if ($query) {
        $data['successMessage'] = "Заявка успешно создана!";
    } else {
        $data['errorMessage'] = "При создании заявка произошла ошибка!";
    }
}

if($received_data->action == 'update') {
    $sql = "UPDATE `task` SET `date` = '$received_data->date', `task_type` = '$received_data->task_type', `organ_id` = '$received_data->organ_id', `organ_address` = '$received_data->organ_address', `curator_organ` = '$received_data->curator_organ', `curator_organ_tel` = '$received_data->curator_organ_tel', `cargo_name` = '$received_data->cargo_name', `cargo_h` = '$received_data->cargo_h', `cargo_w` = '$received_data->cargo_w', `cargo_l` = '$received_data->cargo_l', `cargo_weight` = '$received_data->cargo_weight', `curator_remeza` = '$received_data->curator_remeza', `notes` = '$received_data->notes' WHERE `task_id` = '$received_data->task_id'";
    $query = $conn->query($sql);
    if ($query) {
        $data['successMessage'] = "Данные заявки успешно обновлены!";
    } else {
        $data['errorMessage'] = "При обновлении данных заявки произошла ошибка!";
    }
}

if ($received_data->action == "delete") {
    $sql = "DELETE FROM `task` WHERE `task_id` = '$received_data->task_id'";
    $query = $conn->query($sql);
    if ($query) {
        $data['successMessage'] = "Заявка успешно удалена!";
    } else {
        $data['errorMessage'] = "При удалении заявки произошла ошибка!";
    }
}

if ($received_data->action == "getOrgans") {
    $sql = "SELECT `organ_id`, `spec`, `organ_name`, `organ_address` FROM `organization`";
    $query = $conn->query($sql);
    if ($query) {
        while ($row = $query->fetch_array()) {
            $row['organ_address'] = json_decode($row['organ_address']);
            $data[] = $row;
        }
    } else {
        $data['errorMessage'] = "Ошибка при получении данных организаций!";
    }
}
$conn->close();
echo json_encode($data);
die();
?>