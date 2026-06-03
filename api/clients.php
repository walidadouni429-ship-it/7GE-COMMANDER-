<?php
require_once '../config/database.php';
requireAuth();
$db = Database::getInstance()->getConnection();
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $stmt = $db->query("SELECT c.*, COUNT(d.id) AS nb_devis FROM clients c LEFT JOIN devis d ON c.id=d.client_id GROUP BY c.id ORDER BY c.nom");
    $clients = $stmt->fetchAll();
    jsonResponse(['success'=>true,'clients'=>$clients]);
} elseif ($method === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $stmt = $db->prepare("INSERT INTO clients (nom, telephone, email, adresse, gouvernorat) VALUES (?,?,?,?,?)");
    $stmt->execute([$data['nom'], $data['telephone'] ?? '', $data['email'] ?? '', $data['adresse'] ?? '', $data['gouvernorat'] ?? '']);
    jsonResponse(['success'=>true,'id'=>$db->lastInsertId()]);
}
?>