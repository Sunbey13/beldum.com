<?php
use Dompdf\Dompdf;
require_once '../vendor/autoload.php';
session_start();
include('../access/bmtsaccess.php');
include('../dbconn.php');

$id = $_GET['id'];

$data = array();
$tasks_data = array();
$tasks_id = array();

if ($id != "") {
    $sql = "SELECT `task_list`.*,
    `user`.`initials` AS bmts_initials, `user`.`permission_date`, `user`.`permission_number`,
    `car`.`mark`,`car`.`model`, `car`.`number`,
    `driver`.`initials` 'driver_initials', `driver`.`ie_name`, `driver`.`bank_iban`, `driver`.`bank_bik`, `driver`.`contract_number`, `driver`.`contract_date` FROM `task_list`
    INNER JOIN `user` ON `task_list`.`created_by` = `user`.`user_id`
    INNER JOIN `car` ON `task_list`.`car_id` = `car`.`car_id`
    INNER JOIN `driver` ON `car`.`driver_id` = `driver`.`driver_id` WHERE `task_list`.`task_list_id` = '$id'";
    $query = $conn->query($sql);
    if ($query) {
        $row = $query->fetch_array();
        $data = $row;
        foreach (json_decode($data['tasks']) as $task) {
            $tasks_id[] = $task->id;
        }
        $tasks_id = implode(",", $tasks_id);
        $tasks_sql = "SELECT `organization`.`organ_name`, `task`.`organ_address`, `task`.`curator_organ`, `task`.`curator_organ_tel`, `task`.`cargo_name`,`task`.`curator_remeza`, `user`.`name` FROM `task` 
                    INNER JOIN `organization` ON `task`.`organ_id` = `organization`.`organ_id` 
                    INNER JOIN `user` ON `task`.`customer_id` = `user`.`user_id`
                    WHERE `task_id` IN ($tasks_id)";
        $tasks_query = $conn->query($tasks_sql);
        while ($res = $tasks_query->fetch_array()) {
            $tasks_data[] = $res;
        }
        generate($data, $tasks_data);
    } else {
        $data['errorMessage'] = 'Ошибка при получении данных!';
    }
} else {
    echo "Запрос не удался!";
}

function generate($data, $tasks_data) {
    $i = 1;
    $html = '<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Лист заданий</title>
<style>
* {
    font-family: "DejaVu Sans", sans-serif;
}

p {
    margin: 0;
    padding: 0;
    font-size: 12pt;
}

.task-list-number {
    text-align: center;
    margin-bottom: 5px;
}

.task-list-paragraph {
    text-align: justify;
    text-indent: 25px;
}

.task-list-table {
    width: 100%;
    margin: auto;
    border-collapse: collapse;
    margin-bottom: 5px;
    margin-top: 5px;
}

.task-list-table td,
.task-list-table th {
    font-size: 12pt;
    font-weight: normal;
    border: 1px solid #000;
}

.field {
    text-decoration: underline;
}

.customer {
    margin-top: 25px;
    width: 45%;
    display: inline-block;
    text-align: center;
}

.uppercase {
    text-transform: uppercase;
}

.act-title {
    margin-top: 34px;
    text-align: center;
}
</style>
</head>
<body>
<div class="task-list">
<p class="task-list-number">Лист заданий № <span class="field">'.$data['task_list_id'].'</span></p>
<p>г.Рогачев</p>
<p class="task-list-paragraph">
    Является протоколом согласования стоимости работ к договору № <span class="field">'.$data['contract_number'].'</span> от <span class="field">'.date("d.m.Y", strtotime($data['contract_date'])).'</span> г. между <strong>ЗАО "Ремеза"</strong> (далее "Заказчик") в лице
    начальника БМТС <span class="field">'.$data['bmts_initials'].'</span>, действующего на основании доверенности № <span class="field">'.$data['permission_number'].'</span> от <span class="field">'.date("d.m.Y", strtotime($data['permission_date'])).'</span> г. и <span class="field"><strong>'.$data['ie_name'].'</strong></span> (далее "Исполнитель").
</p>
<p class="task-list-paragraph">
    Автомобиль: <span class="field">'.$data['mark'].'</span> <span class="field">'.$data['number'].'</span>. Водитель: <span class="field"><strong>'.$data['driver_initials'].'</strong></span>. Маршрут: <span class="field">'.$data['route'].'</span> Дата <span class="field">'.$data['date'].'</span> № путевого листа __________
</p>
</div>
<table class="task-list-table">
    <thead>
        <tr>
            <th>№ п/п</th>
            <th>Адрес, наименование фирмы</th>
            <th>Контактное лицо на фирме, № тел.</th>
            <th>Получать</th>
            <th>Товар получен по ТТН №</th>
            <th>Кон. лицо "Заказчика"</th>
            <th>Приоритет</th>
            <th>Получатель</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <th>1</th>
            <th>2</th>
            <th>3</th>
            <th>4</th>
            <th>5</th>
            <th>6</th>
            <th>7</th>
            <th>8</th>
        </tr>';
    foreach ($tasks_data as $task) {
        $html .= '
         <tr>
            <td style="text-align: center">'.$i.'</td>
            <td>'.$task["organ_name"].', '.$task["organ_address"].'</td>
            <td>'.$task["curator_organ"].', '.$task["curator_organ_tel"].'</td>
            <td>'.$task["cargo_name"].'</td>
            <td></td>
            <td>'.$task["curator_remeza"].'</td>
            <td></td>
            <td>'.$task["name"].'</td>
        </tr>';
        $i++;
    }
    $html .= '</tbody>
</table>
<p class="task-list-paragraph">
    Внести изменения в порядок выполнения задания, дополнить или отменить его имеет право только "Заказчик".<br>
    Инструктаж провел __________ начальник БМТС <span class="field">'.$data['bmts_initials'].'</span> С заданием ознакомлен, инструктаж получил водитель __________
</p>
<br>
<p><strong>
    Предварительно согласованная стоимость __________ <br>
    Планируемое время выезда __________ факт. время выезда __________ <br>
    Без НДС ст. 91 Особенной части налогового Кодекса РБ
</strong></p>
<div class="customers" style="width: 80%; margin: auto; display: flex; justify-content: space-between">
    <p class="customer">
        Заказчик <br>
        ЗАО "Ремеза" <br>
        <br>
        _______________ <span class="field">'.$data['bmts_initials'].'</span>
    </p>
    <p class="customer">
        Исполнитель <br>
        <span class="field">'.$data['ie_name'].'</span> <br>
        <br>
        _______________ <span class="field">'.$data['driver_initials'].'</span>
    </p>
</div>
<p class="act-title">
    <span class="uppercase">Акт выполненных работ</span> № ________ от ________ 20__г.
    <p>г. Рогачев</p>
    <p class="task-list-paragraph">
        Настоящий акт составлен в том, что согласно договора №<span class="field"><strong>'.$data['contract_number'].'</strong></span> от <span class="field"><strong>'.date("d.m.Y", strtotime($data['contract_date'])).'</strong></span>г. между "Заказчиком" (<strong>ЗАО "Ремеза"</strong>),
        в лице начальника БМТС <span class="field"><strong>'.$data['bmts_initials'].'</strong></span>, действующего на основании доверенности № <span class="field">'.$data['permission_number'].'</span> от <span class="field">'.date("d.m.Y", strtotime($data['permission_date'])).'</span>г. и "Исполнителем" (<span class="field"><strong>'.$data['ie_name'].'</strong></span>)
    </p>
    <p>выполнены работы по доставке груза по маршруту ______________________________ по товаротранспортным накладным:</p>
    <p>
        - приходные: № ________________________________________________________________________________________________ <br>
        - расходные: № ________________________________________________________________________________________________
    </p>
    <p>
        <strong>Стоимость выполненных работ -</strong> _______________________________согласно листа задания № ________ от ________ 20__г.<br>
        Настоящий акт свидетельствует о приемке работ и служит основанием для произведения оплаты "Исполнителю". <br>
        IBAN: <span class="field uppercase">'.$data['bank_iban'].'</span>, BIK: <span class="field uppercase">'.$data['bank_bik'].'</span>
    </p>
</p>
<div class="customers" style="width: 80%; margin: auto">
    <div>
        <p class="customer" style="text-align: center; display: inline-block;">
            Заказчик <br>
            ЗАО "Ремеза" <br>
            <br>
            _______________ <span class="field">'.$data['bmts_initials'].'</span>
        </p>
        <p class="customer">
            Исполнитель <br>
            <span class="field">'.$data['ie_name'].'</span> <br>
            <br>
            _______________ <span class="field">'.$data['driver_initials'].'</span>
        </p>
    </div>
</div>
</body>
</html>';

    $dompdf = new Dompdf;
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'landscape');
    $dompdf->render();
    $dompdf->stream('Лист заданий '.$data['driver_initials'].' '.date("d.m.Y", strtotime($data['date'])).'.pdf', array("Attachment" => $_GET['action']));
}
$conn->close();
die();
?>


