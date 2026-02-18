<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

require_once 'db_connect_pdo.php';

$search = isset($_GET['search']) ? $_GET['search'] : '';
$params = [];

$sql = "SELECT * FROM CatBreeds WHERE is_visible = 1";

if (!empty($search)) {
    $sql .= " AND (name_th LIKE :search OR name_en LIKE :search)";
    $params[':search'] = "%$search%";
}

$sql .= " ORDER BY id DESC";

try {
    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
    $cats = $stmt->fetchAll();

    echo json_encode([
        'status' => 'success',
        'data' => $cats
    ]);
} catch (PDOException $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'เกิดข้อผิดพลาดในการดึงข้อมูล'
    ]);
}
?>