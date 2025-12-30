<?php
require dirname(__DIR__).'/vendor/autoload.php';

use Symfony\Component\Dotenv\Dotenv;

// Charger les variables .env
(new Dotenv())->bootEnv(dirname(__DIR__).'/.env');

$databaseUrl = $_ENV['DATABASE_URL'];
echo "URL de base de données : " . $databaseUrl . "<br>";

// Extraire les infos de l'URL
$parts = parse_url($databaseUrl);
$host = $parts['host'] ?? 'localhost';
$port = $parts['port'] ?? 3306;
$user = $parts['user'] ?? 'root';
$pass = $parts['pass'] ?? '';
$dbname = trim($parts['path'] ?? '', '/');

try {
    $dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4";
    $pdo = new PDO($dsn, $user, $pass);
    echo "Connexion PDO réussie !";
} catch (PDOException $e) {
    echo " Erreur PDO : " . $e->getMessage() . "<br>";
    echo "DSN essayé : $dsn";
}
?>