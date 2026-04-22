<div class="row g-3 mb-4">
    <div class="col-md-8">
        <div class="card p-2 h-100 flex-row align-items-center justify-content-between px-3 border-0">
            <div class="d-flex align-items-center">
                <i class="bi bi-building text-primary fs-4 me-3"></i>
                <select onchange="location.href='?set_project='+this.value" class="form-select border-0 fw-bold fs-5 shadow-none" style="cursor: pointer;">
                    <option value="">-- اختر المشروع الحالي --</option>
                    <?php
                    $ps=$pdo->prepare("SELECT * FROM projects WHERE user_id = ?");
                    $ps->execute([$uid]);
                    while($p=$ps->fetch()) echo "<option value='{$p['id']}' ".($pid==$p['id']?'selected':'').">{$p['project_name']}</option>";
                    ?>
                </select>
            </div>
            <button class="btn btn-primary btn-sm rounded-circle shadow-sm" data-bs-toggle="modal" data-bs-target="#addP" title="إضافة مشروع جديد">
                <i class="bi bi-plus-lg"></i>
            </button>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card p-2 h-100 px-3 flex-row justify-content-between align-items-center border-0">
            <div class="d-flex align-items-center">
                <div class="bg-light rounded-circle p-2 me-2">
                    <i class="bi bi-person text-primary"></i>
                </div>
                <span class="fw-bold"><?= htmlspecialchars($_SESSION['username']) ?></span>
            </div>
            <a href="?logout=1" class="btn btn-outline-danger btn-sm border-0" title="تسجيل الخروج">
                <i class="bi bi-box-arrow-right fs-5"></i>
            </a>
        </div>
    </div>
</div>
