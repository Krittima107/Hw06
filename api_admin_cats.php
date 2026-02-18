<?php
header('Content-Type: application/json; charset=utf-8');
require_once 'db_connect_pdo.php';

$response = ['status' => 'error', 'message' => 'เกิดข้อผิดพลาดไม่ทราบสาเหตุ'];

// 1. ดึงข้อมูล (GET)
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        $cats = $conn->query("SELECT * FROM CatBreeds ORDER BY id DESC")->fetchAll();
        echo json_encode(['status' => 'success', 'data' => $cats]);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'เกิดข้อผิดพลาดในการดึงข้อมูล']);
    }
    exit;
}

// 2. จัดการข้อมูล (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {

    // บันทึกข้อมูล (เพิ่ม/แก้ไข)
    if ($_POST['action'] === 'save') {
        $image_path = $_POST['old_image'] ?? '';

        // จัดการอัปโหลดรูป
        if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] == 0) {
            $target_dir = "Cat/";
            if (!file_exists($target_dir)) {
                @mkdir($target_dir, 0777, true);
            }

            $file_ext = strtolower(pathinfo($_FILES["image_file"]["name"], PATHINFO_EXTENSION));
            $allowed = ['jpg', 'jpeg', 'png', 'gif'];

            if (in_array($file_ext, $allowed)) {
                $new_filename = "cat_" . time() . "_" . uniqid() . "." . $file_ext;
                $target_file = $target_dir . $new_filename;

                if (move_uploaded_file($_FILES['image_file']['tmp_name'], $target_file)) {
                    $image_path = $target_file;
                    if (!empty($_POST['old_image']) && file_exists($_POST['old_image'])) {
                        if (strpos($_POST['old_image'], 'Cat/') !== false) {
                            @unlink($_POST['old_image']);
                        }
                    }
                } else {
                    echo json_encode(['status' => 'error', 'message' => 'ย้ายไฟล์ไม่ได้! กรุณาเช็ค Permission']);
                    exit;
                }
            } else {
                echo json_encode(['status' => 'error', 'message' => 'รองรับเฉพาะรูปภาพ (JPG, PNG, GIF)']);
                exit;
            }
        }

        try {
            if (empty($_POST['id'])) {
                $sql = "INSERT INTO CatBreeds (name_th, name_en, description, characteristics, care_instructions, image_url, is_visible) 
                        VALUES (:name_th, :name_en, :description, :characteristics, :care_instructions, :image_url, :is_visible)";
                $stmt = $conn->prepare($sql);
            } else {
                $sql = "UPDATE CatBreeds SET name_th=:name_th, name_en=:name_en, description=:description, 
                        characteristics=:characteristics, care_instructions=:care_instructions, 
                        image_url=:image_url, is_visible=:is_visible WHERE id=:id";
                $stmt = $conn->prepare($sql);
                $stmt->bindValue(':id', $_POST['id']);
            }

            $stmt->bindValue(':name_th', $_POST['name_th']);
            $stmt->bindValue(':name_en', $_POST['name_en']);
            $stmt->bindValue(':description', $_POST['description']);
            $stmt->bindValue(':characteristics', $_POST['characteristics']);
            $stmt->bindValue(':care_instructions', $_POST['care_instructions']);
            $stmt->bindValue(':image_url', $image_path);
            $stmt->bindValue(':is_visible', $_POST['is_visible']);
            $stmt->execute();

            echo json_encode(['status' => 'success', 'message' => 'บันทึกข้อมูลเรียบร้อย']);
        } catch (PDOException $e) {
            echo json_encode(['status' => 'error', 'message' => 'Database Error']);
        }
        exit;
    }

    // ลบข้อมูล
    if ($_POST['action'] === 'delete') {
        try {
            $id = $_POST['id'];
            $stmt = $conn->prepare("SELECT image_url FROM CatBreeds WHERE id = ?");
            $stmt->execute([$id]);
            $img = $stmt->fetchColumn();

            if ($img && file_exists($img)) {
                @unlink($img);
            }

            $conn->prepare("DELETE FROM CatBreeds WHERE id = ?")->execute([$id]);
            echo json_encode(['status' => 'success', 'message' => 'ลบข้อมูลเรียบร้อย']);
        } catch (PDOException $e) {
            echo json_encode(['status' => 'error', 'message' => 'Database Error']);
        }
        exit;
    }
}
?>