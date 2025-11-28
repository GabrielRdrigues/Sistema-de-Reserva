<?php
// db.php
$host = 'localhost';
$dbname = 'na_chapa_db';
$username = 'root'; // Seu usuário do banco
$password = ''; // Sua senha do banco

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erro na conexão: " . $e->getMessage());
}
?>