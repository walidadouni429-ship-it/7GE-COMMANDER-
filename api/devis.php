<?php
require_once '../config/database.php';
requireAuth();
$db = Database::getInstance()->getConnection();
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    if (!empty($_GET['id'])) {
        $stmt = $db->prepare("SELECT d.*, c.nom AS client_nom FROM devis d JOIN clients c ON d.client_id=c.id WHERE d.id=?");
        $stmt->execute([$_GET['id']]);
        $devis = $stmt->fetch();
        if (!$devis) jsonResponse(['success'=>false,'message'=>'Devis introuvable'],404);
        $stmt2 = $db->prepare("SELECT * FROM devis_lignes WHERE devis_id=?");
        $stmt2->execute([$_GET['id']]);
        $devis['lignes'] = $stmt2->fetchAll();
        jsonResponse(['success'=>true,'devis'=>$devis]);
    } else {
        $sql = "SELECT d.*, c.nom AS client_nom FROM devis d JOIN clients c ON d.client_id=c.id WHERE 1=1";
        $params = [];
        if (!empty($_GET['search'])) { $sql .= " AND d.numero LIKE ?"; $params[] = '%'.$_GET['search'].'%'; }
        if (!empty($_GET['statut'])) { $sql .= " AND d.statut=?"; $params[] = $_GET['statut']; }
        $sql .= " ORDER BY d.date_creation DESC";
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        jsonResponse(['success'=>true,'devis'=>$stmt->fetchAll()]);
    }
} elseif ($method === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $db->beginTransaction();
    try {
        $client = $data['client'];
        $stmt = $db->prepare("SELECT id FROM clients WHERE nom=? LIMIT 1");
        $stmt->execute([$client['nom']]);
        $clientRow = $stmt->fetch();
        if ($clientRow) $clientId = $clientRow['id'];
        else {
            $stmt = $db->prepare("INSERT INTO clients (nom, telephone, email) VALUES (?,?,?)");
            $stmt->execute([$client['nom'], $client['tel']??'', $client['email']??'']);
            $clientId = $db->lastInsertId();
        }
        $annee = date('Y');
        $cnt = $db->query("SELECT COUNT(*) AS cnt FROM devis WHERE YEAR(date_creation)=$annee")->fetch()['cnt'] + 1;
        $numero = 'SGE-'.$annee.'-'.str_pad($cnt,4,'0',STR_PAD_LEFT);
        $totalHT = array_reduce($data['lignes'], fn($s,$l)=>$s + $l['prix_ht']*$l['quantite'], 0);
        $tva = $totalHT * 0.19;
        $totalTTC = $totalHT + $tva + 1.000;
        $stmt = $db->prepare("INSERT INTO devis (numero, client_id, utilisateur_id, total_ht, total_tva, timbre, total_ttc) VALUES (?,?,?,?,?,?,?)");
        $stmt->execute([$numero, $clientId, $_SESSION['user_id'], $totalHT, $tva, 1.000, $totalTTC]);
        $devisId = $db->lastInsertId();
        $stmtL = $db->prepare("INSERT INTO devis_lignes (devis_id, designation, prix_unitaire_ht, quantite, total_ligne_ht) VALUES (?,?,?,?,?)");
        foreach ($data['lignes'] as $l) {
            $stmtL->execute([$devisId, $l['designation'], $l['prix_ht'], $l['quantite'], $l['prix_ht']*$l['quantite']]);
        }
        $db->commit();
        jsonResponse(['success'=>true,'devis_id'=>$devisId,'numero'=>$numero]);
    } catch (Exception $e) { $db->rollBack(); jsonResponse(['success'=>false,'message'=>$e->getMessage()],500); }
} elseif ($method === 'PATCH') {
    $data = json_decode(file_get_contents('php://input'), true);
    $db->prepare("UPDATE devis SET statut=? WHERE id=?")->execute([$data['statut'], $data['id']]);
    jsonResponse(['success'=>true]);
}
?>