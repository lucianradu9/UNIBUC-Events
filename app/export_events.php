<?php
session_start();
include 'db_connection.php';

if (!isset($_SESSION['user_id'])) {
    die("Acces neautorizat.");
}

$user_id = $_SESSION['user_id'];
$format = isset($_GET['format']) ? $_GET['format'] : 'xlsx';

$query = "SELECT event_name, event_date, event_time FROM events WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$events = [];
while ($row = $result->fetch_assoc()) {
    $events[] = $row;
}

if ($format == "xlsx") {
    exportToExcel($events);
} elseif ($format == "doc") {
    exportToWord($events);
} elseif ($format == "pdf") {
    exportToPDF($events);
} else {
    die("Format invalid.");
}

function exportToExcel($events) {
    require '../vendor/autoload.php';
    $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setCellValue('A1', 'Nume Eveniment')->setCellValue('B1', 'Data')->setCellValue('C1', 'Ora');

    $rowNum = 2;
    foreach ($events as $event) {
        $sheet->setCellValue("A$rowNum", $event['event_name'])
              ->setCellValue("B$rowNum", $event['event_date'])
              ->setCellValue("C$rowNum", $event['event_time']);
        $rowNum++;
    }

    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="evenimente.xlsx"');
    $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
    $writer->save('php://output');
    exit;
}

function exportToWord($events) {
    header("Content-type: application/vnd.ms-word");
    header("Content-Disposition: attachment;filename=evenimente.doc");

    echo "<h2>Evenimentele tale</h2>";
    echo "<table border='1'><tr><th>Nume Eveniment</th><th>Data</th><th>Ora</th></tr>";
    foreach ($events as $event) {
        echo "<tr><td>{$event['event_name']}</td><td>{$event['event_date']}</td><td>{$event['event_time']}</td></tr>";
    }
    echo "</table>";
    exit;
}

function exportToPDF($events) {
    require '../vendor/autoload.php';
    $dompdf = new Dompdf\Dompdf();
    $html = "<h2>Evenimentele tale</h2><table border='1'><tr><th>Nume Eveniment</th><th>Data</th><th>Ora</th></tr>";

    foreach ($events as $event) {
        $html .= "<tr><td>{$event['event_name']}</td><td>{$event['event_date']}</td><td>{$event['event_time']}</td></tr>";
    }
    $html .= "</table>";

    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();
    $dompdf->stream("evenimente.pdf", ["Attachment" => 1]);
    exit;
}
?>
