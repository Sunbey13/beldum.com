<?php
session_start();
include('../dbconn.php');

$received_data = json_decode(file_get_contents("php://input"));

$data = array();

if ($received_data->action == 'getLists') {
    $driver_id = $_SESSION['driver_id'];
    $sql = "SELECT `task_list`.`task_list_id`, `task_list`.`date`, `user`.`initials`, `task_list`.`route`, `task_list`.`tasks` FROM `task_list`
            INNER JOIN `car` ON `task_list`.`car_id` = `car`.`car_id`
            INNER JOIN `driver` ON `car`.`driver_id` = `driver`.`driver_id`
            INNER JOIN `user` ON `task_list`.`created_by` = `user`.`user_id`
            WHERE `driver`.`driver_id` = $driver_id AND `task_list`.`task_list_state` = 0";
    $query = $conn->query($sql);
    if ($query) {
        if ($query->num_rows > 0) {
            while ($row = $query->fetch_array()) {
                $row['tasks'] = json_decode($row['tasks']);
                $data[] = $row;
            }
        } else {
            $data['successMessage'] = 'Здесь будут отображаться ваши листы заданий!';
        }
    } else {
        $data['errorMessage'] = 'Ошибка при получении данных!';
    }
}

$conn->close();
echo json_encode($data);
die();
?>