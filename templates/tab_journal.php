<div class="tab-pane fade" id="t-journal">
    <div class="card border-0 shadow-sm mb-4 glass">
        <div class="card-header bg-transparent border-0 pt-4 px-4">
            <h5 class="fw-bold mb-0 text-primary"><i class="bi bi-journal-check me-2"></i>سجل اليوميات الميدانية</h5>
            <p class="text-muted small mb-0">توثيق سير العمل اليومي، حالة الطقس، والملاحظات الفنية</p>
        </div>
        <div class="card-body p-4">
            <form method="POST" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label small fw-bold text-muted">التاريخ</label>
                    <input type="date" name="j_date" class="form-control" value="<?= date('Y-m-d') ?>" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-bold text-muted">حالة الطقس</label>
                    <select name="weather" class="form-select">
                        <option value="مشمس">مشمس</option>
                        <option value="غائم">غائم</option>
                        <option value="ممطر">ممطر</option>
                        <option value="رياح شديدة">رياح شديدة</option>
                        <option value="حار جداً">حار جداً</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-bold text-muted">المواد المستلمة اليوم</label>
                    <input name="deliveries" class="form-control" placeholder="مثلاً: وصول حمولة رمل...">
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-bold text-muted">ملاحظات الإنجاز</label>
                    <textarea name="progress" class="form-control" rows="2" placeholder="ماذا تم إنجازه اليوم؟"></textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-bold text-muted">عقبات أو مشاكل</label>
                    <textarea name="issues" class="form-control" rows="2" placeholder="أي معوقات واجهت العمل..."></textarea>
                </div>
                <div class="col-12 text-end">
                    <button name="add_journal" class="btn btn-primary px-5"><i class="bi bi-save me-2"></i>حفظ سجل اليوم</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm glass">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th class="ps-4">التاريخ</th>
                            <th>الطقس</th>
                            <th>الإنجاز</th>
                            <th>المشاكل</th>
                            <th class="text-center pe-4">إجراء</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $js = $pdo->prepare("SELECT * FROM daily_journal WHERE project_id = ? ORDER BY entry_date DESC");
                        $js->execute([$pid]);
                        while($j = $js->fetch()): ?>
                        <tr>
                            <td class="ps-4 fw-bold"><?= $j['entry_date'] ?></td>
                            <td><span class="badge bg-light text-dark border fw-normal"><?= h($j['weather']) ?></span></td>
                            <td><small class="text-muted"><?= mb_strimwidth(h($j['progress_notes']), 0, 50, "...") ?></small></td>
                            <td><small class="text-danger"><?= mb_strimwidth(h($j['issues_encountered']), 0, 50, "...") ?></small></td>
                            <td class="text-center pe-4">
                                <button class="btn btn-sm btn-outline-info border-0" data-bs-toggle="modal" data-bs-target="#viewJ<?= $j['id'] ?>"><i class="bi bi-eye"></i></button>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
