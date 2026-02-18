<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

require_once 'db_connect_pdo.php';

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$params = [];

$sql = "SELECT * FROM CatBreeds WHERE is_visible = 1";

if (!empty($search)) {
    // แยกตัวแปรเป็น 2 ชื่อ ไม่ให้ซ้ำกัน
    $sql .= " AND (name_th LIKE :search_th OR name_en LIKE :search_en)";
    $params[':search_th'] = "%" . $search . "%";
    $params[':search_en'] = "%" . $search . "%";
}

$sql .= " ORDER BY id DESC";

try {
    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
    $cats = $stmt->fetchAll();

    echo json_encode([
        'status' => 'success',
        'data' => $cats
    ], JSON_UNESCAPED_UNICODE);
} catch (PDOException $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'เกิดข้อผิดพลาดในการดึงข้อมูล: ' . $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
?>