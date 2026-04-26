<div class="tab-pane fade" id="t-sups">
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-custom-card border-0 pt-4 px-4">
            <h5 class="fw-bold mb-0 text-custom-accent"><i class="bi bi-truck me-2"></i>إضافة مورد / تاجر جديد</h5>
            <p class="text-custom-muted small mb-0">تسجيل بيانات الموردين لمتابعة المشتريات الآجلة والديون</p>
        </div>
        <div class="card-body p-4">
            <form method="POST" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label small fw-bold text-custom-muted">اسم المورد / المحل</label>
                    <input name="s_name" class="form-control bg-custom-glass border-0 py-2" placeholder="مثلاً: شركة الوحدة للأسمنت" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-bold text-custom-muted">رقم التواصل</label>
                    <input name="s_info" class="form-control bg-custom-glass border-0 py-2" placeholder="هاتف أو موبايل">
                </div>
                <div class="col-md-5">
                    <label class="form-label small fw-bold text-custom-muted">العنوان</label>
                    <div class="input-group">
                        <input name="s_address" class="form-control bg-custom-glass border-0 py-2" placeholder="المدينة، الشارع...">
                        <button name="add_supplier" class="btn btn-custom-accent px-4"><i class="bi bi-plus-lg me-1"></i>حفظ</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-custom-card border-0 pt-4 px-4 d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <h5 class="fw-bold mb-0 text-custom-main">كشف الموردين والمديونيات</h5>
                <p class="text-custom-muted small mb-0">إجمالي المشتريات الآجلة وما تم سداده لكل مورد</p>
            </div>
            <div class="search-box">
                <i class="bi bi-search"></i>
                <input type="text" class="form-control table-search border-0 bg-custom-glass" data-target="#supsTable" placeholder="بحث عن مورد...">
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="supsTable">
                    <thead>
                        <tr>
                            <th class="ps-4">المورد</th>
                            <th>معلومات التواصل</th>
                            <th>إجمالي الدين</th>
                            <th>المسدد</th>
                            <th>المتبقي</th>
                            <th class="text-center pe-4">إجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sups=$pdo->prepare("SELECT * FROM suppliers WHERE project_id = ?");
                        $sups->execute([$pid]);
                        while($s=$sups->fetch()):
                            $debt = $pdo->query("SELECT SUM(price) FROM materials WHERE supplier_id={$s['id']} AND is_paid=0")->fetchColumn() ?: 0;
                            $paid = $pdo->query("SELECT SUM(amount) FROM supplier_payments WHERE supplier_id={$s['id']}")->fetchColumn() ?: 0;
                            $rem = $debt - $paid;
                        ?>
                        <tr>
                            <td class="ps-4">
                                <span class="fw-bold text-custom-main d-block"><?= htmlspecialchars($s['supplier_name']) ?></span>
                                <small class="text-custom-muted"><?= htmlspecialchars($s['address'] ?: '') ?></small>
                            </td>
                            <td>
                                <span class="small d-block mb-1 text-secondary"><i class="bi bi-phone me-1"></i><?= htmlspecialchars($s['contact_info'] ?: '-') ?></span>
                            </td>
                            <td><span class="fw-bold text-custom-main"><?= number_format($debt) ?></span></td>
                            <td><span class="text-success"><?= number_format($paid) ?></span></td>
                            <td>
                                <span class="badge <?= $rem > 0 ? 'bg-danger-subtle text-danger' : 'bg-success-subtle text-success' ?> px-3">
                                    <?= number_format($rem) ?>
                                </span>
                            </td>
                            <td class="text-center pe-4">
                                <div class="btn-group shadow-sm rounded-3 overflow-hidden">
                                    <button class="btn btn-sm btn-custom-accent" data-bs-toggle="modal" data-bs-target="#sPay<?= $s['id'] ?>" title="تسديد دفعة">
                                        <i class="bi bi-cash-stack"></i>
                                    </button>
                                    <button class="btn btn-sm btn-info text-white" data-bs-toggle="modal" data-bs-target="#sHist<?= $s['id'] ?>" title="سجل الدفعات">
                                        <i class="bi bi-clock-history"></i>
                                    </button>
                                    <a href="?del_sup=<?= $s['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('حذف المورد وسجله نهائياً؟')" title="حذف">
                                        <i class="bi bi-trash3"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
