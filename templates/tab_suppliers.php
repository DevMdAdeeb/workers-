<div class="tab-pane fade" id="t-suppliers">
    <div class="card p-3 mb-4 border-0 shadow-sm">
        <h6 class="fw-bold mb-3"><i class="bi bi-person-plus me-1"></i> إضافة تاجر/مورد جديد</h6>
        <form method="POST" class="row g-3">
            <div class="col-md-3"><input name="s_name" class="form-control" placeholder="اسم التاجر" required></div>
            <div class="col-md-3"><input name="s_info" class="form-control" placeholder="رقم الهاتف"></div>
            <div class="col-md-4"><input name="s_address" class="form-control" placeholder="عنوان المحل/الشركة"></div>
            <div class="col-md-2"><button name="add_supplier" class="btn btn-dark w-100">حفظ المورد</button></div>
        </form>
    </div>

    <div class="card border-0 p-3 shadow-sm">
        <h6 class="fw-bold mb-3">سجل الموردين والحسابات الدائنة</h6>
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-dark border-0">
                    <tr>
                        <th>التاجر</th>
                        <th>إجمالي السحب (دين)</th>
                        <th>المدفوع (أقساط)</th>
                        <th>المتبقي له</th>
                        <th class="text-center">إجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sups = $pdo->prepare("SELECT * FROM suppliers WHERE project_id = ?");
                    $sups->execute([$pid]);
                    while($s = $sups->fetch()):
                        $debt = $pdo->query("SELECT SUM(price) FROM materials WHERE supplier_id={$s['id']} AND is_paid=0")->fetchColumn() ?: 0;
                        $paid = $pdo->query("SELECT SUM(amount) FROM supplier_payments WHERE supplier_id={$s['id']}")->fetchColumn() ?: 0;
                        $rem = $debt - $paid;
                    ?>
                    <tr>
                        <td>
                            <span class="fw-bold d-block"><?= htmlspecialchars($s['supplier_name']) ?></span>
                            <small class="text-muted"><?= htmlspecialchars($s['contact_info']) ?></small>
                        </td>
                        <td><?= number_format($debt) ?></td>
                        <td class="text-success"><?= number_format($paid) ?></td>
                        <td class="text-danger fw-bold"><?= number_format($rem) ?></td>
                        <td class="text-center">
                            <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#sPay<?= $s['id'] ?>" title="دفع قسط">
                                <i class="bi bi-cash-stack"></i>
                            </button>
                            <button class="btn btn-sm btn-info text-white" data-bs-toggle="modal" data-bs-target="#sHist<?= $s['id'] ?>" title="سجل الأقساط">
                                <i class="bi bi-list-check"></i>
                            </button>
                            <a href="?del_sup=<?= $s['id'] ?>" class="btn btn-sm btn-outline-danger border-0 ms-1" onclick="return confirm('هل أنت متأكد من حذف هذا التاجر؟ سيتم مسح سجل دفعاته أيضاً.')">
                                <i class="bi bi-trash3"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
