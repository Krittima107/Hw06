<?php
session_start();
require_once 'db_connect_pdo.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    try {
        $stmt = $conn->prepare("SELECT * FROM AdminsCat WHERE username = :user AND password = :pass");
        $stmt->execute([':user' => $username, ':pass' => $password]);
        $admin = $stmt->fetch();

        if ($admin) {
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_name'] = $admin['full_name'];
            header("Location: cat_system.php");
            exit;
        } else {
            $error = 'ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง!';
        }
    } catch (PDOException $e) {
        $error = 'เกิดข้อผิดพลาดในการเชื่อมต่อฐานข้อมูล';
    }
}
?>

<?php include 'header.php'; ?>

<style>
    /* CSS เฉพาะจัดระเบียบหน้า Login */
    .login-container {
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 80vh;
        padding-top: 50px;
    }

    .login-card {
        background: white;
        border-radius: 25px;
        padding: 40px;
        box-shadow: 0 10px 30px rgba(255, 183, 178, 0.3);
        max-width: 400px;
        width: 100%;
    }
</style>

<div class="login-container">
    <div class="login-card text-center">
        <h3>🐾 เข้าสู่ระบบหลังบ้าน</h3>
        <p class="text-muted mb-4">Meow Gallery Admin</p>

        <?php if ($error): ?>
            <div class="alert alert-danger rounded-pill py-2"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <input type="text" name="username" class="form-control" placeholder="ชื่อผู้ใช้ (admin)" required>
            </div>
            <div class="mb-4">
                <input type="password" name="password" class="form-control" placeholder="รหัสผ่าน (123456)" required>
            </div>
            <button type="submit" class="btn btn-pastel w-100 mb-3">เข้าสู่ระบบ ✨</button>
        </form>
    </div>
</div>

<?php include 'footer.php'; ?>