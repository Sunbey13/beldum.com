<?php
session_start();
include('../dbconn.php');

$received_data = json_decode(file_get_contents("php://input"));

$data = array();

if ($received_data->action == 'getUser') {
    $id = $_SESSION['id'];
    $sql = "SELECT `user`.`role`, `user`.`permission_number` FROM `user` WHERE `user_id` = $id";
    $query = $conn->query($sql);
    if ($query) {
        $data = $query->fetch_array();
    } else {
        $data['errorMessage'] = 'Ошибка при получении данных!';
    }
}

if ($received_data->action == 'getCars') {
    $sql = "SELECT `car`.*, `driver`.`driver_id`, `driver`.`name`, `driver`.`surname`, `driver`.`last_name`, `driver`.`phone` FROM `car` 
            INNER JOIN `driver` ON `car`.`driver_id` = `driver`.`driver_id`";
    $query = $conn->query($sql);
    if ($query) {
        while ($row = $query->fetch_array()) {
            $data[] = $row;
        }
    } else {
        $data['errorMessage'] = 'Ошибка при получении данных!';
    }
}

if ($received_data->action == 'getNewTasks') {
    $sql = "SELECT `task`.*, `organization`.`organ_name`, `user`.`name` AS 'customer_name', `user`.`surname` AS 'customer_surname', `user`.`last_name` AS 'customer_last_name' FROM task 
            INNER JOIN `organization` ON `task`.`organ_id` = `organization`.`organ_id`
            INNER JOIN `user` ON `task`.`customer_id` = `user`.`user_id` WHERE `task`.`state` = 0 ORDER BY `date` ASC";
    $query = $conn->query($sql);
    if ($query) {
        if ($query->num_rows > 0) {
            while ($row = $query->fetch_array()) {
                $row['date'] = date("d-m-Y", strtotime($row['date']));
                $data[] = $row;
            }
        } else {
            $data['successMessage'] = 'Ожидайте новых заявок или создайте их сами!';
        }
    } else {
        $data['errorMessage'] = 'Ошибка при получении данных!';
    }
}

if ($received_data->action == 'getAllTasks') {
    if (sizeof($received_data->task_ids) > 0) {
        $res = getTasksId($received_data->task_ids);
        $ids = implode(',', $res);
        $sql = "SELECT `task`.*, `organization`.`organ_name`, `user`.`name` AS 'customer_name', `user`.`surname` AS 'customer_surname', `user`.`last_name` AS 'customer_last_name' FROM task
            INNER JOIN `organization` ON `task`.`organ_id` = `organization`.`organ_id`
            INNER JOIN `user` ON `task`.`customer_id` = `user`.`user_id` WHERE `state` < 2 OR `task_id` IN ($ids) ORDER BY `state` ASC ";
    } else {
        $sql = "SELECT `task`.*, `organization`.`organ_name`, `user`.`name` AS 'customer_name', `user`.`surname` AS 'customer_surname', `user`.`last_name` AS 'customer_last_name' FROM task
            INNER JOIN `organization` ON `task`.`organ_id` = `organization`.`organ_id`
            INNER JOIN `user` ON `task`.`customer_id` = `user`.`user_id` WHERE `state` < 2 ORDER BY `state` ASC ";
    }
    $query = $conn->query($sql);
    if ($query) {
        if ($query->num_rows > 0) {
            while ($row = $query->fetch_array()) {
                $row['date'] = date("d-m-Y", strtotime($row['date']));
                $data[] = $row;
            }
        } else {
            $data['successMessage'] = "Дождитесь поступления заявок или создайте первую, чтобы увидеть ее здесь!";
        }
    } else {
        $data['errorMessage'] = 'Ошибка при получении данных!';
    }
}

if ($received_data->action == 'add') {
    $created_by = $_SESSION['id'];
    $sql = "INSERT INTO `task_list` VALUES ('$received_data->task_list_id', '$received_data->date', '$received_data->car_id', '$received_data->tasks', '$created_by', '$received_data->route', 0)";
    $query = $conn->query($sql);
    if ($query) {
        $res = getTasksId($received_data->tasks);
        $ids = implode(',', $res);
        $task_query = $conn->query("UPDATE `task` SET `state` = 1, `driver` = '$received_data->driver' WHERE `task_id` IN ($ids)");
        $data['successMessage'] = "Лист заданий успешно создан!";
    } else {
        $data['errorMessage'] = "При создании листа заданий произошла ошибка!";
    }
}

if ($received_data->action == 'getLists') {
    $sql = "SELECT `task_list`.*, `driver`.`initials` FROM `task_list` 
            INNER JOIN `car` ON `task_list`.`car_id` = `car`.`car_id`
            INNER JOIN `driver` ON `car`.`driver_id` = `driver`.`driver_id`
            WHERE `task_list`.`task_list_state` = 0";
    $query = $conn->query($sql);
    if ($query) {
        if ($query->num_rows > 0) {
            while ($row = $query->fetch_array()) {
                $row['tasks'] = json_decode($row['tasks']);
                $data[] = $row;
            }
        } else {
            $data['successMessage'] = 'Создайте первый лист заданий, чтобы увидеть его здесь!';
        }
    } else {
        $data['errorMessage'] = 'Ошибка при получении данных!';
    }
}

if ($received_data->action == 'getList') {
    $sql = "SELECT `task_list`.*, `user`.`initials`, `car`.`mark`,`car`.`model`, `car`.`number`, `driver`.`name`, `driver`.`surname`, `driver`.`last_name` FROM `task_list`
            INNER JOIN `user` ON `task_list`.`created_by` = `user`.`user_id`
            INNER JOIN `car` ON `task_list`.`car_id` = `car`.`car_id`
            INNER JOIN `driver` ON `car`.`driver_id` = `driver`.`driver_id` WHERE `task_list`.`task_list_id` = '$received_data->task_list_id'";
    $query = $conn->query($sql);
    if ($query) {
        $row = $query->fetch_array();
        $row['tasks'] = json_decode($row['tasks']);
        $data = $row;
    } else {
        $data['errorMessage'] = 'Ошибка при получении данных!';
    }
}

if ($received_data->action == 'update') {
    $default_tasks = getTasksId((($conn->query("SELECT `task_list`.tasks FROM `task_list` WHERE `task_list_id` = '$received_data->task_list_id'"))->fetch_array())['tasks']);
    $sql = "UPDATE `task_list` SET `date` = '$received_data->date', `car_id` = '$received_data->car_id', `tasks` = '$received_data->tasks', `route` = '$received_data->route' WHERE `task_list_id` = '$received_data->task_list_id'";
    $query = $conn->query($sql);
    if ($query) {
        $ids = implode(',', array_diff($default_tasks, getTasksId($received_data->tasks)));
        $query_set_0 = $conn->query("UPDATE `task` SET `state` = 0, `driver` = '' WHERE `task_id` IN ('$ids')");
        $ids = implode(',', array_diff(getTasksId($received_data->tasks), $default_tasks));
        $query_set_1 = $conn->query("UPDATE `task` SET `driver` = '$received_data->driver', `state` = 1 WHERE `task_id` IN ('$ids')");
        $data['successMessage'] = "Лист заданий успешно обновлен!";
    } else {
        $data['errorMessage'] = "При обновлении листа заданий произошла ошибка!";
    }
}

if ($received_data->action == 'delete') {
    $tasks_query = $conn->query("SELECT `task_list`.`tasks` FROM `task_list` WHERE `task_list_id` = '$received_data->task_list_id'");
    $sql = "DELETE FROM `task_list` WHERE `task_list_id` = '$received_data->task_list_id'";
    $query = $conn->query($sql);
    if ($query && $tasks_query) {
        $tasks_result = $tasks_query->fetch_array();
        $default_tasks = getTasksId($tasks_result['tasks']);
        $ids = implode(',', $default_tasks);
        $reset_state_query = $conn->query("UPDATE `task` SET `driver` = '', `state` = 0 WHERE `state` = 1 AND `task_id` IN ($ids)");
        $data['successMessage'] = "Лист заданий успешно удален!";
        $default_tasks = array();
    } else {
        $data['errorMessage'] = "При удалении листа заданий произошла ошибка!";
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

if($received_data->action == 'getTask') {
    $sql = "SELECT `task`.*, `organization`.`organ_id` FROM `task` INNER JOIN `organization` ON `task`.`organ_id` = `organization`.`organ_id` INNER JOIN `user` ON `task`.`customer_id` = `user`.`user_id` WHERE `task`.`task_id` = '$received_data->task_id'";
    $query = $conn->query($sql);
    if ($query) {
        $data = $query->fetch_array();
    } else {
        $data['errorMessage'] = "Ошибка при получении данных!";
    }
}

if($received_data->action == 'updateTask') {
    $sql = "UPDATE `task` SET `date` = '$received_data->date', `task_type` = '$received_data->task_type', `organ_id` = '$received_data->organ_id', `organ_address` = '$received_data->organ_address', `curator_organ` = '$received_data->curator_organ', `curator_organ_tel` = '$received_data->curator_organ_tel', `cargo_name` = '$received_data->cargo_name', `cargo_h` = '$received_data->cargo_h', `cargo_w` = '$received_data->cargo_w', `cargo_l` = '$received_data->cargo_l', `cargo_weight` = '$received_data->cargo_weight', `curator_remeza` = '$received_data->curator_remeza', `notes` = '$received_data->notes' WHERE `task_id` = '$received_data->task_id'";
    $query = $conn->query($sql);
    if ($query) {
        $data['successMessage'] = "Данные заявки успешно обновлены!";
    } else {
        $data['errorMessage'] = "При обновлении данных заявки произошла ошибка!";
    }
}

if($received_data->action == 'addTask') {
    $customer_id = $_SESSION['id'];
    $sql = "INSERT INTO `task` VALUES (NULL, '$received_data->date', '$received_data->task_type', '$received_data->organ_id', '$received_data->organ_address', '$received_data->curator_organ', '$received_data->curator_organ_tel', '$customer_id', '$received_data->cargo_name', '$received_data->cargo_w', '$received_data->cargo_h', '$received_data->cargo_l', '$received_data->cargo_weight', '$received_data->curator_remeza', '$received_data->notes', NULL, 0)";
    $query = $conn->query($sql);
    if ($query) {
        $data['successMessage'] = "Заявка успешно создана!";
    } else {
        $data['errorMessage'] = "При создании заявка произошла ошибка!";
    }
}

if ($received_data->action == "deleteTask") {
    $sql = "DELETE FROM `task` WHERE `task_id` = '$received_data->task_id'";
    $query = $conn->query($sql);
    if ($query) {
        $data['successMessage'] = "Заявка успешно удалена!";
    } else {
        $data['errorMessage'] = "При удалении заявки произошла ошибка!";
    }
}

if ($received_data->action == "completeTaskList") {
    $sql = "UPDATE `task_list` SET `task_list_state` = 1 WHERE `task_list_id` = '$received_data->task_list_id'";
    $query = $conn->query($sql);
    if ($query) {
        $tasks = getTasksId($received_data->tasks);
        $ids = implode(',', $tasks);
        $reset_state_query = $conn->query("UPDATE `task` SET `state` = 2 WHERE `task_id` IN ($ids)");
        $data['successMessage'] = "Лист заданий отправлен в историю!";
    } else {
        $data['errorMessage'] = "При завершении произошла ошибка!";
    }
}

if ($received_data->action == "getBankData") {
    $sql = "SELECT `bank_data` FROM `poa_settings`";
    $query = $conn->query($sql);
    if ($query) {
        $data = json_decode($query->fetch_array()['bank_data']);
    } else {
        $data['errorMessage'] = "Ошибка при получении данных!";
    }
}

function getTasksId($jsonString): array
{
    $ids = array();
    foreach (json_decode($jsonString) as $task) {
        $ids[] = $task->id;
    }
    return $ids;
}

$conn->close();
echo json_encode($data);
die();
?>
