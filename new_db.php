<?php
$url = parse_url(getenv("DATABASE_URL"));

$host = $url["ccaml3dimis7eh.cluster-czz5s0kz4scl.eu-west-1.rds.amazonaws.com"];
$port = $url["5432"];
$dbname = ltrim($url["d6t9htl8h0njfq"], '/');
$user = $url["urr4pjbg269ik"];
$password = $url["p2f55c759e14baa16382ccda6104b32193069014bf30ef0e0a3d29350386ae967"];

try {
    $pdo = new PDO("pgsql:host=$host;port=$port;dbname=$dbname", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>
