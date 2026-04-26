<div class="tab-pane fade show active" id="t-budget">
    <?php
    $stmt = $pdo->prepare("SELECT budget FROM projects WHERE id = ?");
    $stmt->execute([$pid]);
    $current_budget = $stmt->fetchColumn() ?: 0;

    // الحسابات
    $mats_total_stmt = $pdo->prepare("SELECT SUM(price) FROM materials WHERE project_id=?");
    $mats_total_stmt->execute([$pid]);
    $mats_total = $mats_total_stmt->fetchColumn() ?: 0;

    $workers_paid_stmt = $pdo->prepare("SELECT SUM(paid_amount) FROM workers WHERE project_id=?");
    $workers_paid_stmt->execute([$pid]);
    $workers_paid = $workers_paid_stmt->fetchColumn() ?: 0;

    $w_extra_stmt = $pdo->prepare("SELECT SUM(wp.amount) FROM worker_payments wp JOIN workers w ON wp.worker_id=w.id WHERE w.project_id=?");
    $w_extra_stmt->execute([$pid]);
    $w_extra = $w_extra_stmt->fetchColumn() ?: 0;

    $total_spent = $mats_total + $workers_paid + $w_extra;
    $remaining = $current_budget - $total_spent;
    $percent = $current_budget > 0 ? min(round(($total_spent / $current_budget) * 100), 100) : 0;
    ?>

    <div class="row g-3 mb-4">
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm bg-primary text-white overflow-hidden position-relative mb-0 h-100">
                <div class="card-body p-3 position-relative" style="z-index: 2;">
                    <h6 class="text-white-50 fw-bold mb-1 small text-uppercase">الميزانية المرصودة</h6>
                    <h3 class="fw-bold mb-0"><?= number_format((float)$current_budget) ?> <small class="fs-6 fw-normal">ر.ي</small></h3>
                    <button class="btn btn-sm btn-light bg-opacity-25 border-0 text-white mt-2 px-3 py-1" data-bs-toggle="collapse" data-bs-target="#editBudget">
                        <i class="bi bi-pencil-square me-1"></i> تعديل
                    </button>
                    <div class="collapse mt-2" id="editBudget">
                        <form method="POST" class="d-flex gap-2 p-2 bg-custom-card bg-opacity-10 rounded-2">
                            <input type="number" name="budget" class="form-control form-control-sm border-0" value="<?= $current_budget ?>" required style="background: white !important; color: black !important;">
                            <button name="update_budget" class="btn btn-sm btn-warning fw-bold px-3">حفظ</button>
                        </form>
                    </div>
                </div>
                <i class="bi bi-bank position-absolute end-0 bottom-0 text-white opacity-10" style="font-size: 2.5rem; margin-right: 10px; margin-bottom: 10px;"></i>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm bg-custom-card mb-0 h-100">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <h6 class="text-custom-muted fw-bold mb-1 small text-uppercase">إجمالي المصروفات</h6>
                            <h3 class="fw-bold text-custom-main mb-0"><?= number_format((float)$total_spent) ?> <small class="fs-6 fw-normal text-custom-muted">ر.ي</small></h3>
                        </div>
                        <div class="bg-danger bg-opacity-10 p-2 rounded-2">
                            <i class="bi bi-cart-dash text-danger fs-5"></i>
                        </div>
                    </div>
                    <div class="progress bg-custom-glass" style="height: 6px;">
                        <div class="progress-bar <?= $percent > 85 ? 'bg-danger' : 'bg-primary' ?>" style="width: <?= $percent ?>%"></div>
                    </div>
                    <div class="d-flex justify-content-between mt-1 small">
                        <span class="text-custom-muted">نسبة الاستهلاك</span>
                        <span class="fw-bold <?= $percent > 85 ? 'text-danger' : 'text-custom-accent' ?>"><?= $percent ?>%</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm bg-custom-card mb-0 h-100">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <h6 class="text-custom-muted fw-bold mb-1 small text-uppercase">المتبقي المتوفر</h6>
                            <h3 class="fw-bold <?= $remaining >= 0 ? 'text-success' : 'text-danger' ?> mb-0"><?= number_format((float)$remaining) ?> <small class="fs-6 fw-normal text-custom-muted">ر.ي</small></h3>
                        </div>
                        <div class="bg-success bg-opacity-10 p-2 rounded-2">
                            <i class="bi bi-shield-check text-success fs-5"></i>
                        </div>
                    </div>
                    <p class="small text-custom-muted mb-0" style="font-size: 0.75rem;">
                        <i class="bi bi-info-circle me-1"></i>
                        <?= $remaining >= 0 ? 'أداء مالي جيد' : 'تجاوز للميزانية' ?>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <?php if($percent > 85): ?>
    <div class="alert alert-warning border-0 shadow-sm d-flex align-items-center mb-4">
        <i class="bi bi-exclamation-triangle-fill fs-4 me-3"></i>
        <div>
            <strong class="d-block">تنبيه الميزانية!</strong>
            لقد تم استهلاك <?= $percent ?>% من الميزانية المرصودة. يرجى مراجعة المصروفات القادمة بعناية.
        </div>
    </div>
    <?php endif; ?>
</div>
