<div class="tab-pane fade" id="t-attendance">
    <div class="card border-0 shadow-sm mb-4 glass">
        <div class="card-header bg-transparent border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
            <div>
                <h5 class="fw-bold mb-0 text-primary"><i class="bi bi-calendar-check me-2"></i>تحضير العمال اليومي</h5>
                <p class="text-muted small mb-0">تسجيل حضور وغياب العمال في الموقع</p>
            </div>
            <form method="GET" class="d-flex gap-2">
                <input type="hidden" name="set_project" value="<?= $pid ?>">
                <input type="date" name="att_date" class="form-control form-control-sm" value="<?= $_GET['att_date'] ?? date('Y-m-d') ?>" onchange="this.form.submit()">
            </form>
        </div>
        <div class="card-body p-4">
            <form method="POST">
                <input type="hidden" name="att_date" value="<?= $_GET['att_date'] ?? date('Y-m-d') ?>">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>اسم العامل</th>
                                <th>الحالة</th>
                                <th>ملاحظات</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $target_date = $_GET['att_date'] ?? date('Y-m-d');
                            $workers = $pdo->prepare("SELECT id, worker_name FROM workers WHERE project_id = ?");
                            $workers->execute([$pid]);
                            while($w = $workers->fetch()):
                                $att = $pdo->prepare("SELECT * FROM attendance WHERE worker_id = ? AND project_id = ? AND attendance_date = ?");
                                $att->execute([$w['id'], $pid, $target_date]);
                                $current = $att->fetch();
                            ?>
                            <tr>
                                <td class="fw-bold"><?= h($w['worker_name']) ?></td>
                                <td>
                                    <div class="btn-group btn-group-sm" role="group">
                                        <input type="radio" class="btn-check" name="status[<?= $w['id'] ?>]" id="p<?= $w['id'] ?>" value="present" <?= (!$current || $current['status'] == 'present') ? 'checked' : '' ?>>
                                        <label class="btn btn-outline-success" for="p<?= $w['id'] ?>">حاضر</label>

                                        <input type="radio" class="btn-check" name="status[<?= $w['id'] ?>]" id="h<?= $w['id'] ?>" value="half_day" <?= ($current && $current['status'] == 'half_day') ? 'checked' : '' ?>>
                                        <label class="btn btn-outline-warning" for="h<?= $w['id'] ?>">نصف يوم</label>

                                        <input type="radio" class="btn-check" name="status[<?= $w['id'] ?>]" id="a<?= $w['id'] ?>" value="absent" <?= ($current && $current['status'] == 'absent') ? 'checked' : '' ?>>
                                        <label class="btn btn-outline-danger" for="a<?= $w['id'] ?>">غائب</label>
                                    </div>
                                </td>
                                <td>
                                    <input name="notes[<?= $w['id'] ?>]" class="form-control form-control-sm" placeholder="ملاحظة..." value="<?= h($current['notes'] ?? '') ?>">
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
                <div class="text-end mt-3">
                    <button name="save_attendance" class="btn btn-primary px-5 shadow-sm">حفظ كشف التحضير</button>
                </div>
            </form>
        </div>
    </div>
</div>
