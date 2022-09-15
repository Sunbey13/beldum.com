<?php
session_start();
include('../dbconn.php');

$received_data = json_decode(file_get_contents("php://input"));

$data = array();

if ($received_data->action == 'getLists') {
    $sql = "SELECT `task_list`.*, `driver`.`initials` FROM `task_list` 
            INNER JOIN `car` ON `task_list`.`car_id` = `car`.`car_id`
            INNER JOIN `driver` ON `car`.`driver_id` = `driver`.`driver_id`
            WHERE `task_list`.`task_list_state` = 1";
    $query = $conn->query($sql);
    if ($query) {
        if ($query->num_rows > 0) {
            while ($row = $query->fetch_array()) {
                $row['tasks'] = json_decode($row['tasks']);
                $data[] = $row;
            }
        } else {
            $data['successMessage'] = 'Завершите лист заданий, чтобы увидеть его здесь!';
        }
    } else {
        $data['errorMessage'] = 'Ошибка при получении данных!';
    }
}

$conn->close();
echo json_encode($data);
die();
?>