<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

require_once 'db_connect_pdo.php';

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$params = [];

$sql = "SELECT * FROM CatBreeds WHERE is_visible = 1";

if (!empty($search)) {
    $sql .= " AND (name_th LIKE :search_th OR name_en LIKE :search_en)";
    $params[':search_th'] = "%" . $search . "%";
    $params[':search_en'] = "%" . $search . "%";
}

$sql .= " ORDER BY id DESC";

try {
    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
    $cats = $stmt->fetchAll();

    // ลูปดึงรูปภาพจากแกลลอรี่มาใส่ให้แมวแต่ละตัว
    foreach ($cats as $key => $cat) {
        $g_stmt = $conn->prepare("SELECT image_url FROM CatGallery WHERE cat_id = :cat_id");
        $g_stmt->execute([':cat_id' => $cat['id']]);
        $galleries = $g_stmt->fetchAll(PDO::FETCH_COLUMN); // ดึงมาแค่ URL

        $cats[$key]['gallery'] = $galleries; // นำรูปเพิ่มเติมไปเก็บเป็น Array ในชื่อ gallery
    }

    echo json_encode([
        'status' => 'success',
        'data' => $cats
    ], JSON_UNESCAPED_UNICODE);
} catch (PDOException $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'เกิดข้อผิดพลาดในการดึงข้อมูล'
    ], JSON_UNESCAPED_UNICODE);
}
?>