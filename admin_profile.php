<?php
session_start();
require_once 'db_connect_pdo.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

$admin_id = $_SESSION['admin_id'];
$success_msg = '';
$error_msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $full_name = trim($_POST['full_name']);
    $new_password = trim($_POST['new_password']);

    if (empty($username) || empty($full_name)) {
        $error_msg = 'กรุณากรอก Username และ ชื่อ-นามสกุล ให้ครบถ้วน';
    } else {
        try {
            $check_stmt = $conn->prepare("SELECT id FROM AdminsCat WHERE username = :usr AND id != :id");
            $check_stmt->execute([':usr' => $username, ':id' => $admin_id]);

            if ($check_stmt->fetch()) {
                $error_msg = '❌ ชื่อผู้ใช้นี้ (Username) ถูกใช้งานแล้ว กรุณาเลือกชื่ออื่นครับ';
            } else {
                if (!empty($new_password)) {
                    $stmt = $conn->prepare("UPDATE AdminsCat SET username = :usr, full_name = :name, password = :pass WHERE id = :id");
                    $stmt->execute([':usr' => $username, ':name' => $full_name, ':pass' => $new_password, ':id' => $admin_id]);
                } else {
                    $stmt = $conn->prepare("UPDATE AdminsCat SET username = :usr, full_name = :name WHERE id = :id");
                    $stmt->execute([':usr' => $username, ':name' => $full_name, ':id' => $admin_id]);
                }

                $_SESSION['admin_name'] = $full_name;
                $success_msg = '✅ อัปเดตข้อมูลโปรไฟล์เรียบร้อยแล้ว!';
            }
        } catch (PDOException $e) {
            $error_msg = 'เกิดข้อผิดพลาดในการอัปเดตข้อมูล: ' . $e->getMessage();
        }
    }
}

$stmt = $conn->prepare("SELECT * FROM AdminsCat WHERE id = :id");
$stmt->execute([':id' => $admin_id]);
$admin = $stmt->fetch();
?>

<?php include 'header.php'; ?>

<style>
    .profile-card {
        background: white;
        border-radius: 25px;
        padding: 40px;
        box-shadow: 0 10px 30px rgba(255, 183, 178, 0.2);
        max-width: 500px;
        margin: 120px auto 50px auto;
    }
</style>

<div class="container">
    <div class="profile-card">
        <div class="text-center mb-4">
            <h3>⚙️ แก้ไขข้อมูลโปรไฟล์</h3>
            <p class="text-muted">จัดการบัญชีผู้ดูแลระบบของคุณ</p>
        </div>

        <?php if ($success_msg): ?>
            <div class="alert alert-success rounded-pill text-center py-2"><?= $success_msg ?></div>
        <?php endif; ?>
        <?php if ($error_msg): ?>
            <div class="alert alert-danger rounded-pill text-center py-2"><?= $error_msg ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label class="form-label text-muted small fw-bold">ชื่อผู้ใช้ (Username) - ใช้สำหรับล็อกอิน</label>
                <input type="text" name="username" class="form-control"
                    value="<?= htmlspecialchars($admin['username']) ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label text-muted small fw-bold">ชื่อ-นามสกุล ที่แสดงผลหน้าเว็บ</label>
                <input type="text" name="full_name" class="form-control"
                    value="<?= htmlspecialchars($admin['full_name']) ?>" required>
            </div>
            <div class="mb-4">
                <label class="form-label text-muted small fw-bold">รหัสผ่านใหม่
                    (ปล่อยว่างไว้ถ้าไม่ต้องการเปลี่ยน)</label>
                <input type="password" name="new_password" class="form-control" placeholder="พิมพ์รหัสผ่านใหม่...">
            </div>

            <button type="submit" class="btn btn-pastel w-100 mb-3">บันทึกข้อมูล 💾</button>
            <div class="text-center">
                <a href="cat_system.php" class="text-decoration-none" style="color: #8d6e63;">⬅️
                    กลับไปหน้าระบบจัดการแมว</a>
            </div>
        </form>
    </div>
</div>

<?php include 'footer.php'; ?>