<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meow Gallery - โลกของเหมียว</title>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400&family=Mali:wght@400;600;700&display=swap"
        rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        :root {
            --bg-color: #fff9f5;
            --primary: #ffb7b2;
            --primary-hover: #ff9aa2;
            --text-head: #6d4c41;
            --text-body: #8d6e63;
            --card-bg: #ffffff;
        }

        body {
            background-color: var(--bg-color);
            font-family: 'Kanit', sans-serif;
            color: var(--text-body);
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        h1,
        h2,
        h3,
        h4,
        h5,
        h6,
        .navbar-brand,
        .btn {
            font-family: 'Mali', cursive;
        }

        /* Navbar */
        .navbar {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(10px);
            box-shadow: 0 4px 20px rgba(255, 183, 178, 0.15);
        }

        .navbar-brand {
            color: var(--primary) !important;
            font-weight: 700;
            font-size: 1.5rem;
        }

        .nav-btn-admin {
            border: 2px solid var(--primary);
            color: var(--primary);
            border-radius: 50px;
            padding: 5px 20px;
            transition: 0.3s;
        }

        .nav-btn-admin:hover {
            background: var(--primary);
            color: white;
        }

        /* Form & Inputs */
        .form-control {
            border-radius: 15px;
            border: 2px solid #ffdac1;
            padding: 10px 15px;
        }

        .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 10px rgba(255, 183, 178, 0.3);
            outline: none;
        }

        .btn-pastel {
            background-color: #ffdac1;
            color: var(--text-head);
            border-radius: 50px;
            font-weight: 600;
            transition: 0.3s;
            border: none;
        }

        .btn-pastel:hover {
            background: var(--primary);
            color: white;
        }
    </style>
</head>

<body>

    <nav class="navbar navbar-expand-lg fixed-top">
        <div class="container">
            <a class="navbar-brand" href="home.php">
                <i class="bi bi-balloon-heart-fill"></i> Meow Gallery
            </a>

            <div class="ms-auto d-flex align-items-center">

                <?php if (isset($_SESSION['admin_id'])): ?>
                    <span class="me-3" style="cursor: pointer;"
                        title="สวัสดี, <?= htmlspecialchars($_SESSION['admin_name']) ?>">
                        <i class="bi bi-person-circle" style="font-size: 1.8rem; color: var(--primary);"></i>
                    </span>
                <?php endif; ?>

                <?php if ($current_page == 'home.php'): ?>
                    <a href="cat_system.php" class="nav-btn-admin text-decoration-none">
                        <i class="bi bi-gear-fill"></i> จัดการระบบ
                    </a>
                <?php else: ?>
                    <a href="home.php" class="nav-btn-admin text-decoration-none"
                        style="border-color: #8d6e63; color: #8d6e63;">
                        <i class="bi bi-house-door-fill"></i> กลับหน้าผู้ชม
                    </a>
                <?php endif; ?>
    
            </div>
        </div>
    </nav>