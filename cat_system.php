<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}
?>

<?php include 'header.php'; ?>

<style>
    .cat-img {
        width: 80px;
        height: 80px;
        object-fit: cover;
        border-radius: 8px;
        border: 1px solid #ddd;
        background: #eee;
    }

    .table-responsive {
        background: white;
        border-radius: 10px;
        padding: 15px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    }
</style>

<div class="container" style="margin-top: 120px; margin-bottom: 50px;">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <span class="me-3 fw-bold" style="color: #6d4c41;">👤 สวัสดี,
                <?= htmlspecialchars($_SESSION['admin_name']) ?></span>
            <a href="admin_profile.php" class="btn btn-info me-2 shadow-sm text-white" style="border-radius: 50px;">⚙️
                โปรไฟล์</a>
            <a href="logout.php" class="btn btn-danger me-2 shadow-sm" style="border-radius: 50px;">🚪 ออกจากระบบ</a>
            <button class="btn btn-success px-4 shadow-sm" onclick="openModal()" style="border-radius: 50px;">+
                เพิ่มข้อมูล</button>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table align-middle table-hover">
            <thead class="table-dark">
                <tr>
                    <th width="100">รูปภาพ</th>
                    <th>ชื่อสายพันธุ์</th>
                    <th width="150">สถานะ</th>
                    <th width="180" class="text-center">จัดการ</th>
                </tr>
            </thead>
            <tbody id="cat-table-body">
                <tr>
                    <td colspan="4" class="text-center py-4">กำลังโหลดข้อมูล...</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="catModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form id="catForm" enctype="multipart/form-data" class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalTitle">เพิ่มข้อมูลแมว</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="action" value="save">
                <input type="hidden" name="id" id="cat_id">
                <input type="hidden" name="old_image" id="old_image">

                <div class="row g-3">
                    <div class="col-md-6"><label>ชื่อไทย</label><input type="text" name="name_th" id="name_th"
                            class="form-control" required></div>
                    <div class="col-md-6"><label>ชื่ออังกฤษ</label><input type="text" name="name_en" id="name_en"
                            class="form-control" required></div>
                    <div class="col-12">
                        <label class="fw-bold text-danger">รูปภาพหน้าปกหลัก (1 รูป)</label>
                        <input type="file" name="image_file" class="form-control" accept="image/*">
                    </div>
                    <div class="col-12 mt-3">
                        <label class="fw-bold text-primary">รูปภาพเพิ่มเติม (เลือกได้หลายรูปพร้อมกัน)</label>
                        <input type="file" name="gallery_files[]" class="form-control" accept="image/*" multiple>
                        <small class="text-muted">เคล็ดลับ: กด Ctrl (หรือ Command บน Mac)
                            ค้างไว้เพื่อเลือกหลายรูป(*ห้ามเลือกรูปซ้ำกับรูปหน้าปก*)</small>
                    </div>
                    <div class="col-12"><label>คำอธิบาย</label><textarea name="description" id="description"
                            class="form-control" rows="2"></textarea></div>
                    <div class="col-md-6"><label>ลักษณะ</label><textarea name="characteristics" id="characteristics"
                            class="form-control" rows="3"></textarea></div>
                    <div class="col-md-6"><label>วิธีดูแล</label><textarea name="care_instructions"
                            id="care_instructions" class="form-control" rows="3"></textarea></div>
                    <div class="col-12">
                        <label>สถานะ</label>
                        <select name="is_visible" id="is_visible" class="form-select">
                            <option value="1">แสดงผล</option>
                            <option value="0">ซ่อน</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary rounded-pill" data-bs-dismiss="modal">ยกเลิก</button>
                <button type="submit" class="btn btn-success rounded-pill">บันทึก</button>
            </div>
        </form>
    </div>
</div>

<script>
    let catModal; // ประกาศตัวแปรไว้รอก่อน

    function loadAdminCats() {
        fetch('api_admin_cats.php')
            .then(res => res.json())
            .then(data => {
                let tbody = $('#cat-table-body');
                tbody.empty();

                if (data.status === 'success' && data.data.length > 0) {
                    data.data.forEach(cat => {
                        let statusBadge = cat.is_visible == 1
                            ? '<span class="badge rounded-pill bg-success">✅ แสดงผล</span>'
                            : '<span class="badge rounded-pill bg-secondary">🙈 ซ่อน</span>';

                        let catJson = JSON.stringify(cat).replace(/"/g, '&quot;');

                        tbody.append(`
                            <tr>
                                <td><img src="${cat.image_url}" class="cat-img" onerror="this.src='https://via.placeholder.com/80?text=No+Img'"></td>
                                <td>
                                    <div class="fw-bold">${cat.name_th}</div>
                                    <small class="text-muted">${cat.name_en}</small>
                                </td>
                                <td>${statusBadge}</td>
                                <td class="text-center">
                                    <button class="btn btn-sm btn-warning rounded-pill px-3" onclick="editCat(${catJson})">แก้ไข</button>
                                    <button class="btn btn-sm btn-danger rounded-pill px-3" onclick="deleteCat(${cat.id})">ลบ</button>
                                </td>
                            </tr>
                        `);
                    });
                } else {
                    tbody.append('<tr><td colspan="4" class="text-center py-3">ไม่มีข้อมูลสายพันธุ์แมว</td></tr>');
                }
            })
            .catch(err => {
                console.error('Error:', err);
                $('#cat-table-body').html('<tr><td colspan="4" class="text-center py-3 text-danger">เกิดข้อผิดพลาดในการโหลดข้อมูล</td></tr>');
            });
    }

    function openModal() {
        $('#modalTitle').text('เพิ่มข้อมูลแมว');
        $('#cat_id, #old_image').val('');
        $('#catForm')[0].reset();
        catModal.show();
    }

    function editCat(data) {
        $('#modalTitle').text('แก้ไข: ' + data.name_th);
        $('#cat_id').val(data.id);
        $('#old_image').val(data.image_url);
        $('#name_th').val(data.name_th);
        $('#name_en').val(data.name_en);
        $('#description').val(data.description);
        $('#characteristics').val(data.characteristics);
        $('#care_instructions').val(data.care_instructions);
        $('#is_visible').val(data.is_visible);
        catModal.show();
    }

    function deleteCat(id) {
        if (confirm('ยืนยันลบข้อมูลแมว? (รูปภาพทั้งหมดจะถูกลบไปด้วย)')) {
            let formData = new FormData();
            formData.append('action', 'delete');
            formData.append('id', id);

            fetch('api_admin_cats.php', { method: 'POST', body: formData })
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'success') {
                        loadAdminCats();
                    } else {
                        alert('Error: ' + data.message);
                    }
                });
        }
    }

    // ฟังก์ชัน $(document).ready จะทำงานเมื่อโค้ดทุกอย่าง (รวมถึง Footer) โหลดเสร็จแล้ว
    $(document).ready(function () {
        // เตรียมระบบป๊อปอัป
        catModal = new bootstrap.Modal(document.getElementById('catModal'));

        // เตรียมระบบบันทึกข้อมูล
        $('#catForm').on('submit', function (e) {
            e.preventDefault();
            let formData = new FormData(this);

            fetch('api_admin_cats.php', { method: 'POST', body: formData })
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'success') {
                        catModal.hide();
                        loadAdminCats();
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(err => console.error('Error:', err));
        });

        // ดึงข้อมูลแมวมาแสดง
        loadAdminCats();
    });
</script>

<?php include 'footer.php'; ?>