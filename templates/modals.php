<!-- مودال مشروع جديد -->
<div class="modal fade" id="addP" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form method="POST" class="modal-content border-0 shadow-lg p-4">
            <div class="modal-header border-0 pb-2">
                <h5 class="fw-bold m-0 text-custom-accent"><i class="bi bi-building-add me-2"></i>إضافة مشروع جديد</h5>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <label class="form-label small fw-bold text-custom-muted">اسم المشروع (عمارة، فيلا، مجمع...)</label>
                <input name="project_name" class="form-control form-control-lg border-0 bg-custom-glass py-3 fs-5" placeholder="مثلاً: مشروع حي الأمل السكني" required autofocus>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button name="add_project" class="btn btn-custom-accent w-100 py-3 fw-bold shadow-sm">حفظ المشروع والبدء بالعمل</button>
            </div>
        </form>
    </div>
</div>

<?php
if ($pid) {
    $ws_modals = $pdo->prepare("SELECT * FROM workers WHERE project_id=?");
    $ws_modals->execute([$pid]);
    while($w = $ws_modals->fetch()):
?>
    <!-- مودال دفع سحبيات للعامل -->
    <div class="modal fade" id="payM<?= $w['id'] ?>" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content border-0 shadow-lg p-4 text-center">
                <div class="bg-primary bg-opacity-10 p-3 rounded-circle mx-auto mb-3" style="width: 70px; height: 70px;">
                    <i class="bi bi-cash-coin text-custom-accent fs-2"></i>
                </div>
                <h6 class="fw-bold mb-1">صرف مبلغ مالي</h6>
                <p class="text-custom-muted small mb-4">للعامل: <?= htmlspecialchars($w['worker_name']) ?></p>
                <form method="POST">
                    <input type="hidden" name="worker_id" value="<?= $w['id'] ?>">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-custom-muted">المبلغ المراد صرفه</label>
                        <input type="number" name="amt" class="form-control form-control-lg text-center fw-bold border-0 bg-custom-glass py-3 fs-4" placeholder="0.00" required autofocus>
                    </div>
                    <div class="mb-4">
                        <label class="form-label small fw-bold text-custom-muted">تاريخ الصرف</label>
                        <input type="date" name="p_date" class="form-control border-0 bg-custom-glass py-2 text-center" value="<?= date('Y-m-d') ?>">
                    </div>
                    <button name="add_pay" class="btn btn-custom-accent w-100 py-2 fw-bold shadow-sm">تأكيد عملية الصرف</button>
                </form>
            </div>
        </div>
    </div>

    <!-- مودال سجل الدفعات للعامل -->
    <div class="modal fade" id="histM<?= $w['id'] ?>" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg p-0 overflow-hidden">
                <div class="modal-header bg-custom-glass border-0 p-4">
                    <h6 class="fw-bold m-0"><i class="bi bi-clock-history me-2"></i>سجل دفعات: <?= htmlspecialchars($w['worker_name']) ?></h6>
                    <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-0">
                    <div class="bg-primary bg-opacity-10 p-3 text-center border-bottom border-white">
                        <span class="text-custom-muted small d-block mb-1">إجمالي المبلغ المتفق عليه</span>
                        <h4 class="fw-bold text-custom-accent mb-0"><?= formatCurrency($w['total_amount']) ?></h4>
                    </div>
                    <div class="p-3 border-bottom bg-custom-glass bg-opacity-50 d-flex justify-content-between align-items-center">
                        <span class="small fw-bold">السلفة الأولى (عند التسجيل)</span>
                        <span class="badge bg-secondary px-3 py-2"><?= formatCurrency($w['paid_amount']) ?></span>
                    </div>
                    <div class="list-group list-group-flush" style="max-height: 400px; overflow-y: auto;">
                        <?php
                        $payments = $pdo->prepare("SELECT * FROM worker_payments WHERE worker_id=? ORDER BY payment_date DESC");
                        $payments->execute([$w['id']]);
                        while($p = $payments->fetch()):
                        ?>
                            <div class="list-group-item p-3 border-light">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="text-custom-muted small"><i class="bi bi-calendar3 me-1"></i> <?= $p['payment_date'] ?></span>
                                    <span class="fw-bold text-custom-accent fs-6"><?= formatCurrency($p['amount']) ?></span>
                                </div>
                                <div class="d-flex gap-2 justify-content-end">
                                    <button class="btn btn-sm btn-light text-info py-1 px-2" data-bs-toggle="collapse" data-bs-target="#editPay<?= $p['id'] ?>"><i class="bi bi-pencil-square"></i> تعديل</button>
                                    <form method="POST" onsubmit="return confirm('حذف هذه الدفعة؟')" class="d-inline">
                                        <input type="hidden" name="pay_id" value="<?= $p['id'] ?>">
                                        <button name="del_pay" class="btn btn-sm btn-light text-danger py-1 px-2"><i class="bi bi-trash"></i> حذف</button>
                                    </form>
                                </div>
                                <div class="collapse mt-3" id="editPay<?= $p['id'] ?>">
                                    <form method="POST" class="bg-custom-glass p-3 rounded-3 shadow-sm border">
                                        <input type="hidden" name="pay_id" value="<?= $p['id'] ?>">
                                        <div class="row g-2">
                                            <div class="col-6">
                                                <label class="small text-custom-muted mb-1">المبلغ</label>
                                                <input type="number" name="amt" class="form-control form-control-sm border-0 bg-custom-card" value="<?= $p['amount'] ?>" required>
                                            </div>
                                            <div class="col-6">
                                                <label class="small text-custom-muted mb-1">التاريخ</label>
                                                <input type="date" name="p_date" class="form-control form-control-sm border-0 bg-custom-card" value="<?= $p['payment_date'] ?>" required>
                                            </div>
                                            <div class="col-12 mt-3">
                                                <button name="edit_pay" class="btn btn-sm btn-custom-accent w-100 fw-bold py-2">حفظ التغييرات</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php
    endwhile;

    // مودالات الموردين
    $sups_modals = $pdo->prepare("SELECT * FROM suppliers WHERE project_id=?");
    $sups_modals->execute([$pid]);
    while($s = $sups_modals->fetch()):
?>
    <!-- مودال دفع قسط للمورد -->
    <div class="modal fade" id="sPay<?= $s['id'] ?>" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content border-0 shadow-lg p-4 text-center">
                <div class="bg-success bg-opacity-10 p-3 rounded-circle mx-auto mb-3" style="width: 70px; height: 70px;">
                    <i class="bi bi-bank2 text-success fs-2"></i>
                </div>
                <h6 class="fw-bold mb-1">تسديد دفعة للمورد</h6>
                <p class="text-custom-muted small mb-4"><?= htmlspecialchars($s['supplier_name']) ?></p>
                <form method="POST">
                    <input type="hidden" name="supplier_id" value="<?= $s['id'] ?>">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-custom-muted">المبلغ المدفوع</label>
                        <input type="number" name="amount" class="form-control form-control-lg text-center fw-bold border-0 bg-custom-glass py-3 fs-4 text-success" placeholder="0.00" required autofocus>
                    </div>
                    <div class="mb-4">
                        <label class="form-label small fw-bold text-custom-muted">تاريخ السداد</label>
                        <input type="date" name="p_date" class="form-control border-0 bg-custom-glass py-2 text-center" value="<?= date('Y-m-d') ?>">
                    </div>
                    <button name="add_s_payment" class="btn btn-dark w-100 py-2 fw-bold shadow-sm">تأكيد عملية السداد</button>
                </form>
            </div>
        </div>
    </div>

    <!-- مودال سجل دفعات المورد -->
    <div class="modal fade" id="sHist<?= $s['id'] ?>" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg p-0 overflow-hidden">
                <div class="modal-header bg-custom-glass border-0 p-4">
                    <h6 class="fw-bold m-0"><i class="bi bi-journal-text me-2"></i>سجل دفعات المورد: <?= htmlspecialchars($s['supplier_name']) ?></h6>
                    <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-0">
                    <div class="list-group list-group-flush">
                        <?php
                        $phs = $pdo->prepare("SELECT * FROM supplier_payments WHERE supplier_id=? ORDER BY payment_date DESC");
                        $phs->execute([$s['id']]);
                        while($ph = $phs->fetch()):
                        ?>
                            <div class="list-group-item d-flex justify-content-between align-items-center p-3 border-light">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-check2-all text-success me-3"></i>
                                    <span class="text-custom-muted small"><?= $ph['payment_date'] ?></span>
                                </div>
                                <span class="fw-bold text-success fs-6"><?= formatCurrency($ph['amount']) ?></span>
                            </div>
                        <?php endwhile; ?>
                        <?php if($phs->rowCount() == 0): ?>
                            <div class="p-5 text-center text-custom-muted">
                                <i class="bi bi-inbox fs-1 opacity-25 d-block mb-2"></i>
                                لا توجد دفعات مسجلة حالياً
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php
    endwhile;
}
?>
