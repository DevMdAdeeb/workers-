<!-- مودال مشروع جديد -->
<div class="modal fade" id="addP" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form method="POST" class="modal-content border-0 shadow-lg p-3">
            <div class="modal-header border-0 pb-0">
                <h6 class="fw-bold m-0">إضافة مشروع جديد (عمارة/فيلا)</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input name="project_name" class="form-control form-control-lg border-0 bg-light" placeholder="اسم المشروع" required autofocus>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button name="add_project" class="btn btn-primary w-100 py-2 fw-bold">حفظ المشروع</button>
            </div>
        </form>
    </div>
</div>

<?php
// جلب العمال للمودالات
if ($pid) {
    $ws_modals = $pdo->query("SELECT * FROM workers WHERE project_id=$pid");
    while($w = $ws_modals->fetch()):
?>
    <!-- مودال دفع سحبيات للعامل -->
    <div class="modal fade" id="payM<?= $w['id'] ?>" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content border-0 shadow-lg p-3 text-center">
                <h6 class="fw-bold mb-3">صرف مبلغ لـ <?= htmlspecialchars($w['worker_name']) ?></h6>
                <form method="POST">
                    <input type="hidden" name="worker_id" value="<?= $w['id'] ?>">
                    <div class="mb-3">
                        <input type="number" name="amt" class="form-control form-control-lg text-center fw-bold border-0 bg-light" placeholder="المبلغ" required autofocus>
                    </div>
                    <div class="mb-3">
                        <input type="date" name="p_date" class="form-control border-0 bg-light" value="<?= date('Y-m-d') ?>">
                    </div>
                    <button name="add_pay" class="btn btn-primary w-100 py-2">تأكيد عملية الصرف</button>
                </form>
            </div>
        </div>
    </div>

    <!-- مودال سجل الدفعات للعامل -->
    <div class="modal fade" id="histM<?= $w['id'] ?>" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg p-3">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="fw-bold m-0">سجل دفعات: <?= htmlspecialchars($w['worker_name']) ?></h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="bg-light p-2 rounded mb-3 small d-flex justify-content-between align-items-center">
                    <span>السلفة الأولى عند التسجيل</span>
                    <span class="fw-bold"><?= formatCurrency($w['paid_amount']) ?></span>
                </div>
                <div class="list-group list-group-flush">
                    <?php
                    $payments = $pdo->query("SELECT * FROM worker_payments WHERE worker_id={$w['id']} ORDER BY payment_date DESC");
                    while($p = $payments->fetch()):
                    ?>
                        <div class="list-group-item py-2 border-light small">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="text-muted"><?= $p['payment_date'] ?></span>
                                <span class="fw-bold text-primary"><?= formatCurrency($p['amount']) ?></span>
                            </div>
                            <div class="d-flex gap-2 justify-content-end mt-1">
                                <button class="btn btn-link btn-sm text-info p-0" data-bs-toggle="collapse" data-bs-target="#editPay<?= $p['id'] ?>"><i class="bi bi-pencil-square"></i></button>
                                <form method="POST" onsubmit="return confirm('حذف هذه الدفعة؟')" class="d-inline">
                                    <input type="hidden" name="pay_id" value="<?= $p['id'] ?>">
                                    <button name="del_pay" class="btn btn-link btn-sm text-danger p-0"><i class="bi bi-trash"></i></button>
                                </form>
                            </div>
                            <div class="collapse mt-2" id="editPay<?= $p['id'] ?>">
                                <form method="POST" class="bg-light p-2 rounded">
                                    <input type="hidden" name="pay_id" value="<?= $p['id'] ?>">
                                    <div class="row g-2">
                                        <div class="col-6"><input type="number" name="amt" class="form-control form-control-sm" value="<?= $p['amount'] ?>" required></div>
                                        <div class="col-6"><input type="date" name="p_date" class="form-control form-control-sm" value="<?= $p['payment_date'] ?>" required></div>
                                        <div class="col-12"><button name="edit_pay" class="btn btn-sm btn-primary w-100">حفظ التعديل</button></div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>
        </div>
    </div>
<?php
    endwhile;

    // مودالات الموردين
    $sups_modals = $pdo->query("SELECT * FROM suppliers WHERE project_id=$pid");
    while($s = $sups_modals->fetch()):
?>
    <!-- مودال دفع قسط للمورد -->
    <div class="modal fade" id="sPay<?= $s['id'] ?>" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content border-0 shadow-lg p-3 text-center">
                <h6 class="fw-bold mb-3">دفع دفعة لـ <?= htmlspecialchars($s['supplier_name']) ?></h6>
                <form method="POST">
                    <input type="hidden" name="supplier_id" value="<?= $s['id'] ?>">
                    <div class="mb-2">
                        <input type="number" name="amount" class="form-control form-control-lg text-center fw-bold border-0 bg-light" placeholder="المبلغ" required autofocus>
                    </div>
                    <div class="mb-3 small text-muted">
                        <input type="date" name="p_date" class="form-control form-control-sm border-0 bg-light" value="<?= date('Y-m-d') ?>">
                    </div>
                    <button name="add_s_payment" class="btn btn-dark w-100 py-2">تأكيد الدفع</button>
                </form>
            </div>
        </div>
    </div>

    <!-- مودال سجل دفعات المورد -->
    <div class="modal fade" id="sHist<?= $s['id'] ?>" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg p-3">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="fw-bold m-0">سجل أقساط المورد: <?= htmlspecialchars($s['supplier_name']) ?></h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="list-group list-group-flush">
                    <?php
                    $phs = $pdo->query("SELECT * FROM supplier_payments WHERE supplier_id={$s['id']} ORDER BY payment_date DESC");
                    while($ph = $phs->fetch()):
                    ?>
                        <div class="list-group-item d-flex justify-content-between py-2 border-light small">
                            <span class="text-muted"><?= $ph['payment_date'] ?></span>
                            <span class="fw-bold text-success"><?= formatCurrency($ph['amount']) ?></span>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>
        </div>
    </div>
<?php
    endwhile;
}
?>
