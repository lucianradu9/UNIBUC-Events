<?php
$url = "https://www.libertatea.ro/entertainment/evenimente-culturale-2025-romania-festival-neratat-5137641";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$html = curl_exec($ch);

curl_close($ch);

if ($html === false) {
    die("Eroare");
}

// Parsare HTML cu DOMDcument
$dom = new DOMDocument();
libxml_use_internal_errors(true);
$dom->loadHTML($html);
libxml_clear_errors();

$xpath = new DOMXPath($dom);

// Titluri evenimente (<h2> cu clasa wp-block-heading)
$titles = $xpath->query('//h2[contains(@class, "wp-block-heading")]');

// Descrieri evenimente (<p>-ul dupa <h2>)
$descriptions = [];
foreach ($titles as $title) {
    $description = $xpath->query('following-sibling::p[1]', $title);
    if ($description->length > 0) {
        $descriptions[] = $description->item(0);
    } else {
        $descriptions[] = null;
    }
}
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Evenimente 2025 | UNIBUC Events</title>
    <link rel="icon" href="../media/favicon.png" type="image/png">
    <link rel="shortcut icon" href="../media/favicon.png" type="image/png">
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
            <p>Nu exista evenimente disponibile.</p>
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
