<?php
session_start();
include 'db_connection.php';

require '../vendor/autoload.php';

use Smalot\PdfParser\Parser;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpSpreadsheet\IOFactory as ExcelIO;

if (!isset($_SESSION['user_id'])) {
    die("Eroare: Nu esti autentificat.");
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['event_file'])) {
    $file_tmp = $_FILES['event_file']['tmp_name'];
    $file_name = $_FILES['event_file']['name'];
    $file_ext = pathinfo($file_name, PATHINFO_EXTENSION);

    $events = [];

    if ($file_ext === 'pdf') {
        $parser = new Parser();
        $pdf = $parser->parseFile($file_tmp);
        $text = $pdf->getText();

        preg_match_all('/(.+?)\s+(\d{4}-\d{2}-\d{2})(\d{2}:\d{2}:\d{2})/', $text, $matches, PREG_SET_ORDER);

        foreach ($matches as $event) {
            $events[] = [
                'name' => trim($event[1]),
                'date' => $event[2],
                'time' => $event[3]
            ];
        }

    } elseif (in_array($file_ext, ['doc', 'docx'])) {
        $phpWord = IOFactory::load($file_tmp);
        $text = '';

        foreach ($phpWord->getSections() as $section) {
            foreach ($section->getElements() as $element) {
                if (method_exists($element, 'getText')) {
                    $text .= $element->getText() . "\n";
                }
            }
        }

        preg_match_all('/<td>(.*?)<\/td>\s*<td>(\d{4}-\d{2}-\d{2})<\/td>\s*<td>(\d{2}:\d{2}:\d{2})<\/td>/', $text, $matches, PREG_SET_ORDER);

        foreach ($matches as $event) {
            $events[] = [
                'name' => trim(strip_tags($event[1])),
                'date' => $event[2],
                'time' => $event[3]
            ];
        }

    } elseif (in_array($file_ext, ['xls', 'xlsx'])) {
        $spreadsheet = ExcelIO::load($file_tmp);
        $sheet = $spreadsheet->getActiveSheet();

        foreach ($sheet->getRowIterator(2) as $row) {
            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(true);

            $data = [];
            foreach ($cellIterator as $cell) {
                $data[] = $cell->getValue();
            }

            if (count($data) >= 3) {
                $events[] = [
                    'name' => trim($data[0]),
                    'date' => trim($data[1]),
                    'time' => trim($data[2])
                ];
            }
        }
    } else {
        die("Eroare: Format de fișier invalid.");
    }

    // Introducere evenimente
    if (!empty($events)) {
        $stmt = $conn->prepare("INSERT INTO events (user_id, event_name, event_description, event_date, event_time) VALUES (?, ?, 'Eveniment importat', ?, ?)");

        foreach ($events as $event) {
            $stmt->bind_param("isss", $user_id, $event['name'], $event['date'], $event['time']);
            $stmt->execute();
        }

        header("Location: manage_events.php?success=imported");
        exit;
    } else {
        die("Eroare: Nu s-au gasit evenimente valide în fisier.");
    }
}
?>
