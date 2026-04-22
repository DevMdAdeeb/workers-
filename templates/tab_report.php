<div class="tab-pane fade" id="t-report">
    <div class="card p-4 border-0 shadow-sm mb-4">
        <h6 class="fw-bold mb-3"><i class="bi bi-search me-1"></i> استعلام عن رصيد عامل لفترة محددة</h6>
        <form method="POST" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label small">اختر العامل</label>
                <select name="worker_name" class="form-select" required>
                    <option value="">اختر العامل من القائمة</option>
                    <?php
                    $ws_rep = $pdo->prepare("SELECT DISTINCT worker_name FROM workers WHERE project_id = ?");
                    $ws_rep->execute([$pid]);
                    while($w = $ws_rep->fetch()) echo "<option value='{$w['worker_name']}'>{$w['worker_name']}</option>";
                    ?>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small">من تاريخ</label>
                <input type="date" name="date_from" class="form-control" required>
            </div>
            <div class="col-md-3">
                <label class="form-label small">إلى تاريخ</label>
                <input type="date" name="date_to" class="form-control" value="<?= date('Y-m-d') ?>" required>
            </div>
            <div class="col-md-2">
                <button name="get_worker_report" class="btn btn-dark w-100 py-2">عرض التقرير</button>
            </div>
        </form>
    </div>

    <?php if ($report_data): ?>
        <div class="card p-4 border-0 shadow-lg" id="printableReport">
            <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
                <div>
                    <h5 class="m-0 text-primary fw-bold">كشف حساب: <?= htmlspecialchars($report_data['name']) ?></h5>
                    <small class="text-muted">نطاق البحث: من <?= $report_data['from'] ?> إلى <?= $report_data['to'] ?></small>
                </div>
                <div class="text-end d-print-none">
                    <button onclick="window.print()" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-printer me-1"></i> طباعة الكشف
                    </button>
                </div>
            </div>
            <div class="row text-center g-4">
                <div class="col-md-4">
                    <div class="bg-light p-4 rounded-4 shadow-sm border-top border-primary border-4">
                        <small class="d-block text-muted mb-2 fw-bold text-uppercase">إجمالي المستحق (الاتفاق)</small>
                        <h3 class="m-0 fw-bold"><?= formatCurrency($report_data['total']) ?></h3>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="bg-light p-4 rounded-4 shadow-sm border-top border-success border-4">
                        <small class="d-block text-muted mb-2 fw-bold text-uppercase">إجمالي ما تم صرفه له</small>
                        <h3 class="m-0 text-success fw-bold"><?= formatCurrency($report_data['paid']) ?></h3>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="bg-primary text-white p-4 rounded-4 shadow-lg border-top border-white border-4">
                        <small class="d-block opacity-75 mb-2 fw-bold text-uppercase">الرصيد المتبقي له في ذمتك</small>
                        <h3 class="m-0 fw-bold"><?= formatCurrency($report_data['rem']) ?></h3>
                    </div>
                </div>
            </div>
        </div>
    <?php elseif(isset($error_msg)): ?>
        <div class="alert alert-warning border-0 shadow-sm"><i class="bi bi-exclamation-circle me-2"></i><?= $error_msg ?></div>
    <?php endif; ?>
</div>
