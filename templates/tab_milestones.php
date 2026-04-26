<div class="tab-pane fade" id="t-milestones">
    <div class="card border-0 shadow-sm mb-4 glass">
        <div class="card-header bg-transparent border-0 pt-4 px-4">
            <h5 class="fw-bold mb-0 text-custom-accent"><i class="bi bi-calendar2-week me-2"></i>مراحل إنجاز المشروع (Milestones)</h5>
            <p class="text-custom-muted small mb-0">تحديد المواعيد النهائية والمراحل الكبرى للمشروع</p>
        </div>
        <div class="card-body p-4">
            <form method="POST" class="row g-3 mb-4">
                <div class="col-md-4">
                    <label class="form-label">اسم المرحلة</label>
                    <input name="m_name" class="form-control" placeholder="مثلاً: صب القواعد، التشطيبات..." required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">التاريخ المتوقع</label>
                    <input type="date" name="m_date" class="form-control" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">الحالة</label>
                    <select name="m_status" class="form-select">
                        <option value="pending">قيد الانتظار</option>
                        <option value="in_progress">جاري العمل</option>
                        <option value="completed">تم الإنجاز</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button name="add_milestone" class="btn btn-custom-accent w-100">إضافة مرحلة</button>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>المرحلة</th>
                            <th>التاريخ المستهدف</th>
                            <th>الحالة</th>
                            <th class="text-center">إجراء</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $mls = $pdo->prepare("SELECT * FROM milestones WHERE project_id = ? ORDER BY target_date ASC");
                        $mls->execute([$pid]);
                        while($m = $mls->fetch()): ?>
                        <tr>
                            <td class="fw-bold"><?= h($m['milestone_name']) ?></td>
                            <td><?= $m['target_date'] ?></td>
                            <td>
                                <?php if($m['status'] == 'completed'): ?>
                                    <span class="badge bg-success">تم الإنجاز</span>
                                <?php elseif($m['status'] == 'in_progress'): ?>
                                    <span class="badge bg-primary">جاري العمل</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">قيد الانتظار</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <a href="?del_ms=<?= $m['id'] ?>" class="btn btn-sm btn-outline-danger border-0"><i class="bi bi-trash"></i></a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
