<div class="tab-pane fade" id="t-works">
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-transparent border-0 pt-4 px-4">
            <h5 class="fw-bold mb-0 text-primary"><i class="bi bi-person-plus me-2"></i>تسجيل عامل أو فني جديد</h5>
        </div>
        <div class="card-body p-4">
            <form method="POST" class="row g-3">
                <div class="col-md-4 col-lg-3">
                    <label class="form-label small fw-bold text-muted">اسم العامل</label>
                    <input name="w_name" class="form-control bg-light border-0 py-2" placeholder="الاسم الرباعي" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-bold text-muted">رقم الهاتف</label>
                    <input name="phone" class="form-control bg-light border-0 py-2" placeholder="7xxxxxxxx">
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-bold text-muted">نوع العمل / الاختصاص</label>
                    <input name="task" class="form-control bg-light border-0 py-2" placeholder="مثلاً: بناء، مليس...">
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-bold text-muted">تاريخ البدء</label>
                    <input type="date" name="w_date" class="form-control bg-light border-0 py-2" value="<?= date('Y-m-d') ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-bold text-muted">العنوان</label>
                    <input name="address" class="form-control bg-light border-0 py-2" placeholder="الحي / الشارع">
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold text-muted">فترة الدوام</label>
                    <select name="work_period" class="form-select bg-light border-0 py-2">
                        <option value="full">يوم كامل</option>
                        <option value="half">نصف يوم</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold text-muted">مبلغ الاتفاق (ر.ي)</label>
                    <input type="number" name="total" class="form-control bg-light border-0 py-2" placeholder="0">
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold text-muted">السلفة الأولى</label>
                    <input type="number" name="sufa" class="form-control bg-light border-0 py-2" placeholder="0">
                </div>
                <div class="col-md-12 mt-2 text-end">
                    <button name="add_worker" class="btn btn-primary px-5 py-2 shadow-sm"><i class="bi bi-plus-circle me-1"></i>إضافة العامل</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-transparent border-0 pt-4 px-4 d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <h5 class="fw-bold mb-0 text-primary">كشف العمال والمدفوعات</h5>
            </div>
            <div class="search-box">
                <i class="bi bi-search"></i>
                <input type="text" class="form-control table-search border-0 bg-light" data-target="#workersTable" placeholder="بحث عن عامل...">
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="workersTable">
                    <thead>
                        <tr>
                            <th class="ps-4">العامل</th>
                            <th>التواصل</th>
                            <th>العمل والفترة</th>
                            <th>المتبقي</th>
                            <th>التاريخ</th>
                            <th class="text-center pe-4">إجراءات</th>
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
                            <td class="ps-4">
                                <span class="fw-bold text-dark d-block"><?= htmlspecialchars($w['worker_name']) ?></span>
                                <small class="text-muted"><?= htmlspecialchars($w['address'] ?: '') ?></small>
                            </td>
                            <td>
                                <a href="tel:<?= $w['phone'] ?>" class="text-decoration-none text-secondary small d-block mb-1"><i class="bi bi-telephone me-1"></i><?= htmlspecialchars($w['phone'] ?: '-') ?></a>
                                <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $w['phone']) ?>?text=<?= urlencode($wa_msg) ?>" target="_blank" class="badge bg-success-subtle text-success text-decoration-none fw-normal"><i class="bi bi-whatsapp me-1"></i>واتساب</a>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border fw-normal mb-1"><?= htmlspecialchars($w['task_desc']) ?></span>
                                <small class="text-muted d-block"><?= ($w['work_period'] == 'half' ? 'نصف يوم' : 'يوم كامل') ?></small>
                            </td>
                            <td>
                                <span class="text-danger fw-bold fs-6"><?= number_format($rem) ?></span>
                                <small class="text-muted d-block" style="font-size: 0.7rem;">من أصل <?= number_format($w['total_amount']) ?></small>
                            </td>
                            <td><span class="small text-muted"><?= $w['entry_date'] ?></span></td>
                            <td class="text-nowrap text-center pe-4">
                                <div class="btn-group shadow-sm rounded-3 overflow-hidden">
                                    <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#payM<?= $w['id'] ?>" title="صرف دفعة">
                                        <i class="bi bi-cash-stack"></i>
                                    </button>
                                    <button class="btn btn-sm btn-info text-white" data-bs-toggle="modal" data-bs-target="#histM<?= $w['id'] ?>" title="السجل">
                                        <i class="bi bi-clock-history"></i>
                                    </button>
                                    <a href="?del_work=<?= $w['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('حذف بيانات العامل نهائياً؟')" title="حذف">
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
