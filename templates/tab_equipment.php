<div class="tab-pane fade" id="t-equipment">
    <div class="card border-0 shadow-sm mb-4 glass">
        <div class="card-header bg-transparent border-0 pt-4 px-4">
            <h5 class="fw-bold mb-0 text-custom-accent"><i class="bi bi-truck-flatbed me-2"></i>سجل العهد والمعدات</h5>
            <p class="text-custom-muted small mb-0">تتبع الأدوات والمعدات الموجودة في موقع العمل</p>
        </div>
        <div class="card-body p-4">
            <form method="POST" class="row g-3 mb-4">
                <div class="col-md-4">
                    <label class="form-label">المعدة / الأداة</label>
                    <input name="e_name" class="form-control" placeholder="مثلاً: خلاطة خرسانة، منشار كهربائي..." required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">المسؤول عنها (العامل)</label>
                    <select name="worker_id" class="form-select">
                        <option value="">-- عهدة عامة --</option>
                        <?php
                        $ws = $pdo->prepare("SELECT id, worker_name FROM workers WHERE project_id = ?");
                        $ws->execute([$pid]);
                        while($w = $ws->fetch()) echo "<option value='{$w['id']}'>".h($w['worker_name'])."</option>";
                        ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">تاريخ الاستلام</label>
                    <input type="date" name="e_date" class="form-control" value="<?= date('Y-m-d') ?>">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button name="add_equipment" class="btn btn-custom-accent w-100">تسجيل</button>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>المعدة</th>
                            <th>المسؤول</th>
                            <th>تاريخ الاستلام</th>
                            <th>الحالة</th>
                            <th class="text-center">إجراء</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $eqs = $pdo->prepare("SELECT e.*, w.worker_name FROM equipment e LEFT JOIN workers w ON e.worker_id = w.id WHERE e.project_id = ?");
                        $eqs->execute([$pid]);
                        while($e = $eqs->fetch()): ?>
                        <tr>
                            <td class="fw-bold"><?= h($e['equipment_name']) ?></td>
                            <td><?= h($e['worker_name'] ?: 'عهدة عامة') ?></td>
                            <td><?= $e['received_date'] ?></td>
                            <td><span class="badge bg-info text-custom-main">في الموقع</span></td>
                            <td class="text-center">
                                <a href="?del_eq=<?= $e['id'] ?>" class="btn btn-sm btn-outline-danger border-0"><i class="bi bi-trash"></i></a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
