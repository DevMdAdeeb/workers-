<div class="row g-3 mb-4 align-items-stretch">
    <div class="col-md-7 col-lg-8">
        <div class="card p-3 h-100 flex-row align-items-center justify-content-between border-0 shadow-sm">
            <div class="d-flex align-items-center flex-grow-1">
                <div class="bg-primary bg-opacity-10 p-3 rounded-3 me-3 d-none d-sm-block">
                    <i class="bi bi-building text-primary fs-3"></i>
                </div>
                <div class="flex-grow-1">
                    <label class="small text-muted mb-1 d-block">المشروع النشط حالياً</label>
                    <select onchange="location.href='?set_project='+this.value" class="form-select border-0 fw-bold fs-5 shadow-none p-0 bg-transparent" style="cursor: pointer; min-width: 200px;">
                        <option value="">-- اختر المشروع --</option>
                        <?php
                        $ps=$pdo->prepare("SELECT * FROM projects WHERE user_id = ?");
                        $ps->execute([$uid]);
                        while($p=$ps->fetch()) echo "<option value='{$p['id']}' ".($pid==$p['id']?'selected':'').">{$p['project_name']}</option>";
                        ?>
                    </select>
                </div>
            </div>
            <button class="btn btn-primary rounded-3 shadow-sm d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#addP">
                <i class="bi bi-plus-lg"></i>
                <span class="d-none d-md-inline">مشروع جديد</span>
            </button>
        </div>
    </div>
    <div class="col-md-5 col-lg-4">
        <div class="card p-3 h-100 flex-row justify-content-between align-items-center border-0 shadow-sm">
            <div class="d-flex align-items-center">
                <div class="bg-secondary bg-opacity-10 rounded-circle p-2 me-3">
                    <i class="bi bi-person-circle text-secondary fs-4"></i>
                </div>
                <div>
                    <span class="fw-bold d-block"><?= htmlspecialchars($_SESSION['username']) ?></span>
                    <span class="badge bg-success-subtle text-success small fw-normal">مسؤول النظام</span>
                </div>
            </div>
            <a href="?logout=1" class="btn btn-outline-danger border-0 p-2" title="تسجيل الخروج">
                <i class="bi bi-box-arrow-right fs-4"></i>
            </a>
        </div>
    </div>
</div>
