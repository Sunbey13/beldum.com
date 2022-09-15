<?php
session_start();
include('../dbconn.php');

$received_data = json_decode(file_get_contents("php://input"));

$data = array();
$tasks_id = array();

if ($received_data->action == "getList") {
    $task_list_tasks = ($conn->query("SELECT `task_list`.`tasks` FROM `task_list` WHERE `task_list_id` = '$received_data->task_list_id'"))->fetch_array();
    if ($task_list_tasks != '') {
        foreach (json_decode($task_list_tasks['tasks']) as $task) {
            $tasks_id[] = $task->id;
        }
        $tasks_id = implode(",", $tasks_id);
        $tasks_sql = "SELECT `organization`.`organ_name`, `task`.`task_id`, `task`.`task_type`, `task`.`organ_address`, `task`.`curator_organ`, `task`.`curator_organ_tel`, `task`.`cargo_name`,`task`.`curator_remeza`, `user`.`name`, `task`.`state` FROM `task` 
                INNER JOIN `organization` ON `task`.`organ_id` = `organization`.`organ_id` 
                INNER JOIN `user` ON `task`.`customer_id` = `user`.`user_id`
                WHERE `task_id` IN ($tasks_id)";
        $tasks_query = $conn->query($tasks_sql);
        while ($res = $tasks_query->fetch_array()) {
            $data[] = $res;
        }
    } else {
        $data['errorMessage'] = 'Ошибка при получении данных';
    }
}

if ($received_data->action == "setAsDone") {
    $sql = "UPDATE `task` SET `task`.`state` = 2 WHERE `task`.`task_id` = '$received_data->task_id'";
    $query = $conn->query($sql);
    if (!$query) {
        $data['errorMessage'] = "Ошибка при изменении статуса задания!";
    }
}

if ($received_data->action == "returnToWork") {
    $sql = "UPDATE `task` SET `task`.`state` = 1 WHERE `task`.`task_id` = '$received_data->task_id'";
    $query = $conn->query($sql);
    if (!$query) {
        $data['errorMessage'] = "Ошибка при изменении статуса задания!";
    }
}

$conn->close();
echo json_encode($data);
die();
?>