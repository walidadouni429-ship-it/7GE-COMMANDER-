<?php
define('DB_HOST', 'localhost');
define('DB_NAME', 'sge_erp');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USER', 'contact@sevengenenergy.tn');
define('SMTP_PASS', 'votre_mot_de_passe_app');
define('SMTP_FROM_NAME', 'Seven Generation Energy');

define('SGE_EMAIL', 'contact@sevengenenergy.tn');

class Database {
    private static $instance = null;
    private $pdo;

    private function __construct() {
        $dsn = "mysql:host=".DB_HOST.";dbname=".DB_NAME.";charset=".DB_CHARSET;
        $options = [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC];
        $this->pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
    }
    public static function getInstance() {
        if (!self::$instance) self::$instance = new self();
        return self::$instance;
    }
    public function getConnection() { return $this->pdo; }
}

session_start();
function requireAuth() {
    if (!isset($_SESSION['user_id'])) {
        http_response_code(401);
        die(json_encode(['success'=>false,'message'=>'Non authentifié']));
    }
}
function jsonResponse($data, $code=200) {
    http_response_code($code);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}
?>