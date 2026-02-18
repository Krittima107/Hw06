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

        .search-container {
            max-width: 600px;
            margin: 0 auto;
            position: relative;
        }

        .search-input {
            border: 2px solid #ffdac1;
            border-radius: 50px;
            padding: 15px 30px;
            padding-right: 60px;
            box-shadow: 0 5px 15px rgba(255, 218, 193, 0.3);
            color: var(--text-head);
            font-size: 1.1rem;
        }

        .search-input:focus {
            border-color: var(--primary);
            box-shadow: 0 5px 20px rgba(255, 183, 178, 0.4);
            outline: none;
        }

        .search-btn {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            background: var(--primary);
            color: white;
            border: none;
            width: 45px;
            height: 45px;
            border-radius: 50%;
            transition: 0.3s;
        }

        .search-btn:hover {
            background: var(--primary-hover);
            transform: translateY(-50%) scale(1.1);
        }

        .cat-card {
            background: var(--card-bg);
            border: none;
            border-radius: 25px;
            overflow: hidden;
            transition: all 0.4s ease;
            box-shadow: 0 10px 20px rgba(109, 76, 65, 0.05);
            height: 100%;
            position: relative;
        }

        .cat-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(255, 183, 178, 0.3);
        }

        .card-img-top {
            height: 240px;
            object-fit: cover;
            border-bottom: 5px solid #fff0f5;
        }

        .card-title {
            color: var(--text-head);
            font-weight: 700;
            font-size: 1.4rem;
        }

        .card-subtitle {
            color: #ffb7b2;
            font-weight: 600;
        }

        .btn-pastel {
            background-color: #ffdac1;
            color: var(--text-head);
            border-radius: 50px;
            width: 100%;
            padding: 10px;
            font-weight: 600;
            transition: 0.3s;
        }

        .btn-pastel:hover {
            background-color: var(--primary);
            color: white;
        }

        .modal-content {
            border-radius: 25px;
            border: none;
            overflow: hidden;
        }

        .modal-header {
            background-color: var(--primary);
            color: white;
            border-bottom: none;
        }
    </style>
</head>

<body>

    <nav class="navbar navbar-expand-lg fixed-top">
        <div class="container">
            <a class="navbar-brand" href="home.php">
                <i class="bi bi-balloon-heart-fill"></i> Meow Gallery
            </a>
            <div class="ms-auto">
                <a href="cat_system.php" class="nav-btn-admin text-decoration-none">
                    <i class="bi bi-gear-fill"></i> หลังบ้าน
                </a>
            </div>
        </div>
    </nav>

    <div class="container" style="margin-top: 100px;">
        <div class="text-center mb-5">
            <h1 class="display-5 fw-bold" style="color: var(--text-head);">ค้นหาเจ้านายที่คุณรัก 🐾</h1>
            <p class="lead" style="color: var(--text-body);">รวมสายพันธุ์แมวน่ารักๆ ข้อมูลนิสัย และวิธีดูแล</p>

            <div class="search-container mt-4">
                <form id="search-form">
                    <input type="text" id="search-input" class="form-control search-input"
                        placeholder="พิมพ์ชื่อสายพันธุ์... (เช่น เปอร์เซีย)">
                    <button type="submit" class="search-btn"><i class="bi bi-search"></i></button>
                </form>
                <div id="clear-search-btn" class="mt-3 d-none">
                    <button class="badge rounded-pill bg-secondary text-decoration-none px-3 py-2 border-0"
                        onclick="clearSearch()">
                        ล้างคำค้นหา ✖
                    </button>
                </div>
            </div>
        </div>

        <div id="cat-container" class="row g-4 pb-5">
            <div class="text-center w-100 py-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">กำลังโหลด...</span>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="detailModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="m_name">ชื่อแมว</h4>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <img id="m_image" src="" class="img-fluid w-100 shadow-sm"
                                style="border-radius: 20px; object-fit: cover; max-height:350px;">
                        </div>
                        <div class="col-md-6">
                            <h5 class="fw-bold" style="color: var(--primary);">📝 ข้อมูลทั่วไป</h5>
                            <p id="m_desc" class="text-muted"></p>

                            <div class="mt-4 p-3" style="background: #fff9f5; border-radius: 15px;">
                                <h6 class="fw-bold" style="color: var(--text-head);">✨ นิสัยใจคอ</h6>
                                <p id="m_char" class="small mb-0"></p>
                            </div>

                            <div class="mt-3 p-3" style="background: #f0fbf7; border-radius: 15px;">
                                <h6 class="fw-bold text-success">🩺 การดูแลรักษา</h6>
                                <p id="m_care" class="small mb-0"></p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-secondary rounded-pill px-4"
                        data-bs-dismiss="modal">ปิดหน้าต่าง</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function loadCats(searchQuery = '') {
            const container = document.getElementById('cat-container');
            const clearBtn = document.getElementById('clear-search-btn');

            if (searchQuery) clearBtn.classList.remove('d-none');
            else clearBtn.classList.add('d-none');

            fetch(`api_cats.php?search=${encodeURIComponent(searchQuery)}`)
                .then(response => response.json())
                .then(data => {
                    container.innerHTML = '';

                    if (data.status === 'success' && data.data.length > 0) {
                        data.data.forEach(cat => {
                            const catJson = JSON.stringify(cat).replace(/"/g, '&quot;');
                            const description = cat.description ? cat.description.substring(0, 80) + '...' : '-';

                            const html = `
                                <div class="col-12 col-md-6 col-lg-4">
                                    <div class="cat-card">
                                        <img src="${cat.image_url}" class="card-img-top" onerror="this.src='https://via.placeholder.com/400x250/ffdac1/6d4c41?text=Meow'">
                                        <div class="card-body p-4 d-flex flex-column">
                                            <h4 class="card-title">${cat.name_th}</h4>
                                            <h6 class="card-subtitle mb-3 text-uppercase">${cat.name_en}</h6>
                                            <p class="card-text flex-grow-1" style="font-size: 0.95rem;">
                                                ${description}
                                            </p>
                                            <button class="btn btn-pastel mt-3" onclick="showDetails(${catJson})">
                                                ดูน้องตัวนี้ ✨
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            `;
                            container.innerHTML += html;
                        });
                    } else {
                        container.innerHTML = `
                            <div class="col-12 text-center py-5">
                                <i class="bi bi-emoji-dizzy" style="font-size: 3rem; color: var(--primary);"></i>
                                <h3 class="mt-3" style="color: var(--text-head);">ไม่พบน้องแมวที่ค้นหา</h3>
                                <p>ลองพิมพ์คำอื่นดูนะ</p>
                                <button onclick="clearSearch()" class="btn btn-pastel" style="max-width: 200px;">ดูแมวทั้งหมด</button>
                            </div>
                        `;
                    }
                })
                .catch(error => {
                    container.innerHTML = `<div class="col-12 text-center text-danger py-5">เกิดข้อผิดพลาดในการโหลดข้อมูล</div>`;
                });
        }

        document.getElementById('search-form').addEventListener('submit', function (e) {
            e.preventDefault();
            const searchVal = document.getElementById('search-input').value;
            loadCats(searchVal);
        });

        function clearSearch() {
            document.getElementById('search-input').value = '';
            loadCats('');
        }

        function showDetails(cat) {
            document.getElementById('m_name').innerHTML = '<i class="bi bi-stars"></i> ' + cat.name_th + ' <small class="fs-6">(' + cat.name_en + ')</small>';
            document.getElementById('m_image').src = cat.image_url;
            document.getElementById('m_desc').innerText = cat.description || '-';
            document.getElementById('m_char').innerText = cat.characteristics || '-';
            document.getElementById('m_care').innerText = cat.care_instructions || '-';

            var myModal = new bootstrap.Modal(document.getElementById('detailModal'));
            myModal.show();
        }

        document.addEventListener('DOMContentLoaded', () => {
            loadCats();
        });
    </script>
</body>

</html>