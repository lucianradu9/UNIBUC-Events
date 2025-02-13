<?php
// URL-ul paginii de pe care vrei să extragi datele
$url = "https://www.libertatea.ro/entertainment/evenimente-culturale-2025-romania-festival-neratat-5137641";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

// Execută cererea cURL și salvează răspunsul
$html = curl_exec($ch);

// Închide sesiunea cURL
curl_close($ch);

// Verifică dacă cererea a fost realizată cu succes
if ($html === false) {
    die("Eroare la preluarea conținutului paginii.");
}

// Inițializează DOMDocument pentru a parsa HTML-ul
$dom = new DOMDocument();
libxml_use_internal_errors(true); // Ignoră erorile de parsare HTML
$dom->loadHTML($html);
libxml_clear_errors();

// Inițializează DOMXPath pentru a naviga prin DOM
$xpath = new DOMXPath($dom);

// Extrage titlurile evenimentelor (elemente <h2> cu clasa wp-block-heading)
$titles = $xpath->query('//h2[contains(@class, "wp-block-heading")]');

// Extrage descrierile evenimentelor (elemente <p> care urmează imediat după <h2>)
$descriptions = [];
foreach ($titles as $title) {
    $description = $xpath->query('following-sibling::p[1]', $title);
    if ($description->length > 0) {
        $descriptions[] = $description->item(0);
    } else {
        $descriptions[] = null; // Dacă nu există descriere, adăugăm null
    }
}
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Evenimente 2025</title>
    <link rel="stylesheet" href="../css/parsed.css">
</head>
<body>
    <div class="container">
        <h2>Evenimente 2025</h2>

        <?php if ($titles->length > 0): ?>
            <div class="event-list">
                <?php foreach ($titles as $index => $title): ?>
                    <div class="event-item">
                        <h3><?php echo htmlspecialchars($title->nodeValue); ?></h3>
                        <?php if (isset($descriptions[$index]) && $descriptions[$index] !== null): ?>
                            <p><strong>Descriere:</strong> <?php echo htmlspecialchars($descriptions[$index]->nodeValue); ?></p>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p>Nu există evenimente disponibile în acest moment.</p>
        <?php endif; ?>

        <div class="back-link">
            <a href="https://www.libertatea.ro/entertainment/evenimente-culturale-2025-romania-festival-neratat-5137641" target="blank">Sursa: libertatea.ro</a>
        </div>

        <div class="back-link">
            <a href="javascript:history. back()">Înapoi</a>
        </div>
    </div>
</body>
</html>
