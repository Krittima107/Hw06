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
        $target_dir = "Cat/";
        if (!file_exists($target_dir)) {
            @mkdir($target_dir, 0777, true);
        }

        // 1. จัดการรูปภาพหลัก (รูปหน้าปก)
        if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] == 0) {
            $file_ext = strtolower(pathinfo($_FILES["image_file"]["name"], PATHINFO_EXTENSION));
            if (in_array($file_ext, ['jpg', 'jpeg', 'png', 'gif'])) {
                $new_filename = "cat_main_" . time() . "_" . uniqid() . "." . $file_ext;
                $target_file = $target_dir . $new_filename;
                if (move_uploaded_file($_FILES['image_file']['tmp_name'], $target_file)) {
                    $image_path = $target_file;
                }
            }
        }

        try {
            // บันทึกข้อมูลแมว
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

            // ดึง ID ของแมวที่เพิ่งบันทึก (เพื่อเอาไปผูกกับรูปภาพเพิ่มเติม)
            $cat_id = empty($_POST['id']) ? $conn->lastInsertId() : $_POST['id'];

            // 2. จัดการรูปภาพเพิ่มเติม (อัปโหลดได้หลายรูป)
            if (isset($_FILES['gallery_files']) && !empty($_FILES['gallery_files']['name'][0])) {
                $file_count = count($_FILES['gallery_files']['name']);
                for ($i = 0; $i < $file_count; $i++) {
                    if ($_FILES['gallery_files']['error'][$i] == 0) {
                        $ext = strtolower(pathinfo($_FILES['gallery_files']['name'][$i], PATHINFO_EXTENSION));
                        if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif'])) {
                            $g_filename = "gallery_" . time() . "_" . uniqid() . "." . $ext;
                            $g_target = $target_dir . $g_filename;

                            if (move_uploaded_file($_FILES['gallery_files']['tmp_name'][$i], $g_target)) {
                                // บันทึกลงตาราง CatGallery
                                $g_stmt = $conn->prepare("INSERT INTO CatGallery (cat_id, image_url) VALUES (?, ?)");
                                $g_stmt->execute([$cat_id, $g_target]);
                            }
                        }
                    }
                }
            }

            echo json_encode(['status' => 'success', 'message' => 'บันทึกข้อมูลเรียบร้อย']);
        } catch (PDOException $e) {
            echo json_encode(['status' => 'error', 'message' => 'Database Error: ' . $e->getMessage()]);
        }
        exit;
    }

    // ลบข้อมูล (ลบทั้งแมวและรูปภาพในแกลลอรี่)
    if ($_POST['action'] === 'delete') {
        try {
            $id = $_POST['id'];

            // ลบรูปภาพเพิ่มเติมจากแฟ้มและตาราง CatGallery
            $g_stmt = $conn->prepare("SELECT image_url FROM CatGallery WHERE cat_id = ?");
            $g_stmt->execute([$id]);
            $galleries = $g_stmt->fetchAll();
            foreach ($galleries as $g) {
                if (file_exists($g['image_url']))
                    @unlink($g['image_url']);
            }
            $conn->prepare("DELETE FROM CatGallery WHERE cat_id = ?")->execute([$id]);

            // ลบรูปหลักและตัวแมว
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