<?php
use Dompdf\Dompdf;
require_once '../vendor/autoload.php';
session_start();
include('../access/bmtsaccess.php');
include('../dbconn.php');

$received_data = json_decode(file_get_contents("php://input"));

$driver_id = $_GET['driver_id'];
$bank_data = $_GET['bank_data'];
$to_date = date("d.m.Y", strtotime( $_GET['date']));
$number = $_GET['number'];
$organ_name = $_GET['organ_name'];
$cargo_name = $_GET['cargo_name'];

$driver_query = $conn->query("SELECT `name`, `surname`, `last_name`, `passport_series_number`, `passport_issue_date`, `passport_issued_by` FROM `driver` WHERE `driver_id` = $driver_id");
$driver = $driver_query->fetch_array();
$poa_query = $conn->query("SELECT `poa_eng`, `poa_acc` FROM `poa_settings`");
$poa = $poa_query->fetch_array();

date_default_timezone_set('UTC +3');

if ($driver_query && $poa_query) {
    $html = '
<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Доверенность №</title>
</head>
<style>
* {
    font-family: "DejaVu Sans", sans-serif;
    font-size: 8pt;
}

p {
    margin: 0;
    padding: 0;
}

.table {
    width: 100%;
    margin: auto;
    border-collapse: collapse;
    margin-bottom: 5px;
    margin-top: 5px;
}

.table td,
.table th {
    border: 1px solid #000;
    text-align: center;
}

.bold {
    font-size: 10pt;
    font-weight: bold;
    margin-left: 10px;
}
</style>
<body>
Предприятие <span style="text-decoration: underline"><strong>ЗАО "Ремеза", УНН 400046213 ОКПО 14443043</strong></span>
<div style="text-align: center"><h2 style="font-size: 14pt">Доверенность № '.$number.'</h2></div>
<p>Дата выдачи <span class="bold">'.date("d.m.Y").' г.</span></p>
<br>
<p>Доверенность действительна по <span class="bold">'.$to_date.' г.</span></p>
<div>
    <p style="border-bottom: 1px solid #000">ЗАО "Ремеза", ул. Александра Пушкина 65, г.Рогачев, 247672, Гомельской обл., Республика Беларусь</p>
    <p style="text-align: center">наименование потребителя и его адрес</p>
</div>
<div>
    <p style="border-bottom: 1px solid #000">ЗАО "Ремеза", ул. Александра Пушкина 65, г.Рогачев, 247672, Гомельской обл., Республика Беларусь</p>
    <p style="text-align: center">наименование плательщика и его адрес</p>
</div>
<p>'.$bank_data.'</p>
<br>
<p>Доверенность выдана: <span class="bold">водитель '.$driver['surname'].' '.$driver['name'].' '.$driver['last_name'].'</span></p>
<p>Паспорт: '.$driver['passport_series_number'].'</p>
<p>Выдан '.$driver['passport_issued_by'].'</p>
<p>Дата выдачи: '.date("d.m.Y", strtotime($driver['passport_issue_date'])).' г.</p>
<br>
<div style="">
<p>На получение от<span class="bold">'.$organ_name.'</span></p>
<p>материальных ценностей по № от ..</p>
</div>
<br>
<p style="text-align: center; text-transform: uppercase; font-weight: bold">
   Перечень товарно-материальных ценностей, подлежащих получению
</p>
<br>
<div>
    <table class="table">
        <thead>
        <tr>
            <th style="max-width: 20px;">№ п/п</th>
            <th>Наименование</th>
            <th>Ед. изм.</th>
            <th>Количество (прописью)</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td>1</td>
            <td>'.$cargo_name.'</td>
            <td></td>
            <td></td>
        </tr>
        </tbody>
    </table>
</div>
<br>
<p>Подпись лица, получившего доверенность _________________________ удостоверяем.</p>
<br>
<p>М.П.</p>
<br>
<div style="display: flex; justify-content: space-between">
    <p style="display: inline-block">Главный инженер __________ '.$poa['poa_eng'].'/</p>
    <p style="display: inline-block; margin-left: 70px;">Главный бухгалтер __________ '.$poa['poa_acc'].'/</p>
</div>
</body>
</html>
';

    $dompdf = new Dompdf;
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();
    $dompdf->stream('Доверенность.pdf', array("Attachment" => 0));
} else {
    echo "При экспорте произошла ошибка!";
}

$conn->close();
die();
?>


