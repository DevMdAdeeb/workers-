<div class="tab-pane fade show active" id="t-budget">
    <div class="row">
        <div class="col-md-7">
            <div class="card p-4 h-100 border-0">
                <?php
                $proj=$pdo->prepare("SELECT * FROM projects WHERE id=?"); $proj->execute([$pid]); $cur_p=$proj->fetch();
                $m_spent=$pdo->query("SELECT SUM(price) FROM materials WHERE project_id=$pid")->fetchColumn() ?: 0;
                $w_spent=($pdo->query("SELECT SUM(paid_amount) FROM workers WHERE project_id=$pid")->fetchColumn() ?: 0) + ($pdo->query("SELECT SUM(amount) FROM worker_payments wp JOIN workers w ON wp.worker_id=w.id WHERE w.project_id=$pid")->fetchColumn() ?: 0);
                $total_spent = $m_spent + $w_spent;
                $rem_budget = $cur_p['budget'] - $total_spent;
                $percent = ($cur_p['budget']>0) ? min(100, ($total_spent/$cur_p['budget'])*100) : 0;
                ?>
                <h5 class="fw-bold mb-4">إدارة ميزانية المشروع</h5>

                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <div class="p-3 bg-light rounded text-center">
                            <small class="text-muted d-block mb-1">الميزانية</small>
                            <span class="h5 fw-bold"><?= formatCurrency($cur_p['budget']) ?></span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 bg-light rounded text-center">
                            <small class="text-muted d-block mb-1 text-danger">المصروف</small>
                            <span class="h5 fw-bold text-danger"><?= formatCurrency($total_spent) ?></span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 bg-light rounded text-center">
                            <small class="text-muted d-block mb-1 text-success">المتبقي</small>
                            <span class="h5 fw-bold text-success"><?= formatCurrency($rem_budget) ?></span>
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <div class="d-flex justify-content-between mb-1">
                        <small>نسبة الاستهلاك</small>
                        <small class="fw-bold"><?= round($percent, 1) ?>%</small>
                    </div>
                    <div class="progress shadow-sm">
                        <div class="progress-bar <?= ($percent > 90 ? 'bg-danger' : ($percent > 75 ? 'bg-warning' : 'bg-primary')) ?>"
                             role="progressbar" style="width: <?= $percent ?>%"></div>
                    </div>
                    <?php if($percent > 90): ?>
                        <div class="alert alert-danger mt-3 p-2 small border-0 shadow-sm">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i> تنبيه: الميزانية شارفت على الانتهاء!
                        </div>
                    <?php endif; ?>
                </div>

                <form method="POST" class="d-flex gap-2">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0"><i class="bi bi-cash-stack"></i></span>
                        <input type="number" name="budget" class="form-control border-start-0" placeholder="تعديل الميزانية" value="<?= $cur_p['budget'] ?>">
                    </div>
                    <button name="update_budget" class="btn btn-dark px-4"><i class="bi bi-save me-1"></i> حفظ</button>
                </form>
            </div>
        </div>
        <div class="col-md-5">
            <div class="card p-4 h-100 border-0 bg-primary text-white">
                <h5 class="fw-bold mb-3">التقارير السريعة</h5>
                <p class="small opacity-75">يمكنك تحميل كشف حساب شامل للمشروع يحتوي على كافة التفاصيل المالية والمهام المنجزة.</p>
                <div class="mt-auto">
                    <a href="report.php?pid=<?= $pid ?>" target="_blank" class="btn btn-light w-100 py-3 fw-bold shadow-sm mb-2">
                        <i class="bi bi-file-earmark-pdf me-2"></i> تصدير PDF شامل
                    </a>
                    <button class="btn btn-outline-light w-100 py-2 small" onclick="window.print()">
                        <i class="bi bi-printer me-1"></i> طباعة الصفحة الحالية
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
