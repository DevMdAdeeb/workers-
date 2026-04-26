<div class="tab-pane fade" id="t-works">
    <div class="card p-3 border-0 mb-4 shadow-sm">
        <h6 class="fw-bold mb-3"><i class="bi bi-person-plus me-1"></i> تسجيل عامل أو فني</h6>
        <form method="POST" class="row g-3">
            <div class="col-md-3"><input name="w_name" class="form-control" placeholder="اسم العامل" required></div>
            <div class="col-md-3"><input name="phone" class="form-control" placeholder="رقم الهاتف"></div>
            <div class="col-md-3"><input name="task" class="form-control" placeholder="نوع العمل (مثلاً: سباك)"></div>
            <div class="col-md-3"><input name="address" class="form-control" placeholder="العنوان"></div>
            <div class="col-md-3">
                <select name="work_period" class="form-select">
                    <option value="full">يوم كامل</option>
                    <option value="half">نصف يوم</option>
                </select>
            </div>
            <div class="col-md-3">
                <div class="input-group">
                    <span class="input-group-text small">مبلغ الاتفاق</span>
                    <input type="number" name="total" class="form-control" placeholder="0">
                </div>
            </div>
            <div class="col-md-3">
                <div class="input-group">
                    <span class="input-group-text small">السلفة الأولى</span>
                    <input type="number" name="sufa" class="form-control" placeholder="0">
                </div>
            </div>
            <div class="col-md-3"><input type="date" name="w_date" class="form-control" value="<?= date('Y-m-d') ?>"></div>
            <div class="col-md-3"><button name="add_worker" class="btn btn-primary w-100"><i class="bi bi-save me-1"></i> حفظ البيانات</button></div>
        </form>
    </div>

    <div class="card border-0 p-3 shadow-sm">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="fw-bold m-0">قائمة العمال والمستحقات</h6>
            <div class="search-box">
                <i class="bi bi-search"></i>
                <input type="text" class="form-control form-control-sm table-search" data-target="#workersTable" placeholder="بحث عن عامل...">
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle" id="workersTable">
                <thead class="table-light">
                    <tr>
                        <th>العامل</th>
                        <th>التواصل</th>
                        <th>نوع العمل</th>
                        <th>المتبقي</th>
                        <th>التاريخ</th>
                        <th class="text-center">إجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $ws=$pdo->query("SELECT * FROM workers WHERE project_id=$pid ORDER BY id DESC");
                    while($w=$ws->fetch()):
                        $inst=$pdo->query("SELECT SUM(amount) FROM worker_payments WHERE worker_id={$w['id']}")->fetchColumn() ?: 0;
                        $rem = $w['total_amount'] - ($w['paid_amount'] + $inst);
                        $wa_msg = "كشف حساب {$w['worker_name']}: الاتفاق ".number_format($w['total_amount'])." | استلم ".number_format($w['paid_amount']+$inst)." | المتبقي ".number_format($rem);
                    ?>
                    <tr>
                        <td><span class="fw-bold"><?= htmlspecialchars($w['worker_name']) ?></span></td>
                        <td>
                            <small class="d-block"><?= htmlspecialchars($w['phone'] ?: '-') ?></small>
                            <small class="text-muted"><?= htmlspecialchars($w['address'] ?: '') ?></small>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border"><?= htmlspecialchars($w['task_desc']) ?></span>
                            <br>
                            <small class="text-muted"><?= ($w['work_period'] == 'half' ? 'نصف يوم' : 'يوم كامل') ?></small>
                        </td>
                        <td><span class="text-danger fw-bold"><?= number_format($rem) ?></span></td>
                        <td><small><?= $w['entry_date'] ?></small></td>
                        <td class="text-nowrap text-center">
                            <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#payM<?= $w['id'] ?>" title="دفع سلفة">
                                <i class="bi bi-cash-coin"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-info" data-bs-toggle="modal" data-bs-target="#histM<?= $w['id'] ?>" title="سجل الدفعات">
                                <i class="bi bi-journal-text"></i>
                            </button>
                            <a href="worker_statement.php?wid=<?= $w['id'] ?>" target="_blank" class="btn btn-sm btn-outline-secondary" title="كشف حساب (طباعة)">
                                <i class="bi bi-printer"></i>
                            </a>
                            <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $w['phone']) ?>?text=<?= urlencode($wa_msg) ?>" target="_blank" class="btn btn-sm btn-outline-success">
                                <i class="bi bi-whatsapp"></i>
                            </a>
                            <a href="?del_work=<?= $w['id'] ?>" class="btn btn-sm btn-outline-danger border-0 ms-1" onclick="return confirm('حذف بيانات العامل؟')">
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
