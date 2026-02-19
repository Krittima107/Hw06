<?php include 'header.php'; ?>

<style>
    .search-container {
        max-width: 600px;
        margin: 0 auto;
        position: relative;
    }

    .search-input {
        border-radius: 50px;
        padding: 15px 30px;
        padding-right: 60px;
        box-shadow: 0 5px 15px rgba(255, 218, 193, 0.2);
        font-size: 1.1rem;
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
        border-radius: 25px;
        overflow: hidden;
        transition: all 0.4s ease;
        box-shadow: 0 10px 20px rgba(109, 76, 65, 0.05);
        height: 100%;
        display: flex;
        flex-direction: column;
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

    .modal-content {
        border-radius: 25px;
        border: none;
    }

    .modal-header {
        background-color: var(--primary);
        color: white;
        border-bottom: none;
    }

    .thumb-img {
        width: 70px;
        height: 70px;
        object-fit: cover;
        cursor: pointer;
        border: 2px solid transparent;
        transition: 0.3s;
    }

    .thumb-img:hover {
        border-color: var(--primary-hover) !important;
    }

    .thumb-img.active {
        border-color: var(--primary) !important;
    }
</style>

<div class="container" style="margin-top: 120px;">
    <div class="text-center mb-5">
        <h1 class="display-5 fw-bold" style="color: var(--text-head);">ค้นหาเจ้านายที่คุณรัก 🐾</h1>
        <p class="lead">รวมสายพันธุ์แมวน่ารักๆ ข้อมูลนิสัย และวิธีดูแล</p>

        <div class="search-container mt-4">
            <form id="search-form">
                <input type="text" id="search-input" class="form-control search-input"
                    placeholder="พิมพ์ชื่อสายพันธุ์... (เช่น เปอร์เซีย)">
                <button type="submit" class="search-btn"><i class="bi bi-search"></i></button>
            </form>
            <div id="clear-search-area" class="mt-3 d-none">
                <button class="badge rounded-pill bg-secondary text-decoration-none px-3 py-2 border-0"
                    onclick="clearSearch()">ล้างคำค้นหา ✖</button>
            </div>
        </div>
    </div>

    <div id="cat-container" class="row g-4 pb-5">
        <div class="text-center w-100 py-5">
            <div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>
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
                            style="border-radius: 20px; object-fit: cover; height: 350px; transition: 0.3s;">
                        <div id="m_gallery" class="d-flex flex-wrap gap-2 mt-3 justify-content-center"></div>
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

<script>
    function loadCats(searchQuery = '') {
        const container = document.getElementById('cat-container');
        const clearArea = document.getElementById('clear-search-area');

        if (searchQuery) clearArea.classList.remove('d-none');
        else clearArea.classList.add('d-none');

        fetch(`api_cats.php?search=${encodeURIComponent(searchQuery)}`)
            .then(res => res.json())
            .then(data => {
                container.innerHTML = '';
                if (data.status === 'success' && data.data.length > 0) {
                    data.data.forEach(cat => {
                        const catJson = JSON.stringify(cat).replace(/"/g, '&quot;');
                        const desc = cat.description ? cat.description.substring(0, 70) + '...' : '-';
                        container.innerHTML += `
                            <div class="col-12 col-md-6 col-lg-4">
                                <div class="cat-card">
                                    <img src="${cat.image_url}" class="card-img-top" onerror="this.src='https://via.placeholder.com/400x250/ffdac1/6d4c41?text=Meow'">
                                    <div class="card-body p-4 d-flex flex-column">
                                        <h4 class="card-title">${cat.name_th}</h4>
                                        <h6 class="text-muted small mb-3 text-uppercase">${cat.name_en}</h6>
                                        <p class="small flex-grow-1">${desc}</p>
                                        <button class="btn btn-pastel w-100 py-2 mt-3" onclick="showDetails(${catJson})">ดูน้องตัวนี้ ✨</button>
                                    </div>
                                </div>
                            </div>`;
                    });
                } else {
                    container.innerHTML = `<div class="col-12 text-center py-5"><h3>🐾 ไม่พบน้องแมวที่ค้นหา</h3></div>`;
                }
            })
            .catch(err => {
                container.innerHTML = `<div class="col-12 text-center py-5 text-danger">ขออภัย เกิดข้อผิดพลาดในการเชื่อมต่อ</div>`;
            });
    }

    document.getElementById('search-form').addEventListener('submit', function (e) {
        e.preventDefault();
        loadCats(document.getElementById('search-input').value);
    });

    function clearSearch() {
        document.getElementById('search-input').value = '';
        loadCats('');
    }

    function showDetails(cat) {
        document.getElementById('m_name').innerHTML = `<i class="bi bi-stars"></i> ${cat.name_th} <small class="fs-6">(${cat.name_en})</small>`;
        document.getElementById('m_image').src = cat.image_url;
        document.getElementById('m_desc').innerText = cat.description || '-';
        document.getElementById('m_char').innerText = cat.characteristics || '-';
        document.getElementById('m_care').innerText = cat.care_instructions || '-';

        const galleryContainer = document.getElementById('m_gallery');
        galleryContainer.innerHTML = '';

        // 1. นำรูปภาพทั้งหมด (รูปหลัก + รูปเพิ่มเติม) มารวมกันใน Array เดียว
        let allImages = [cat.image_url];
        if (cat.gallery && cat.gallery.length > 0) {
            allImages = allImages.concat(cat.gallery);
        }

        // 2. ใช้ Set เพื่อกรอง URL ของรูปที่ซ้ำกันออกไป ให้เหลือแค่รูปที่ไม่ซ้ำ
        let uniqueImages = [...new Set(allImages)];

        // 3. วนลูปสร้างรูปขนาดย่อจาก List ที่ไม่ซ้ำแล้ว
        uniqueImages.forEach((imgUrl, index) => {
            // รูปแรก (index 0) ให้มีคลาส active เพื่อแสดงขอบสีแดง
            let activeClass = (index === 0) ? 'active' : '';

            galleryContainer.innerHTML += `
                <img src="${imgUrl}" class="rounded shadow-sm thumb-img ${activeClass}" onclick="changeImage(this, this.src)">
            `;
        });

        new bootstrap.Modal(document.getElementById('detailModal')).show();
    }

    // ฟังก์ชันสำหรับสลับรูปและเลื่อนขอบ
    function changeImage(element, src) {
        document.getElementById('m_image').src = src;
        document.querySelectorAll('.thumb-img').forEach(img => img.classList.remove('active'));
        element.classList.add('active');
    }

    document.addEventListener('DOMContentLoaded', () => loadCats());
</script>

<?php include 'footer.php'; ?>