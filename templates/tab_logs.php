<div class="tab-pane fade" id="t-logs">
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-custom-card border-0 pt-4 px-4 d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <h5 class="fw-bold mb-0 text-custom-accent"><i class="bi bi-journal-text me-2"></i>سجل نشاطات النظام</h5>
                <p class="text-custom-muted small mb-0">تتبع كافة العمليات والإجراءات التي تمت في المشروع</p>
            </div>
            <div class="search-box">
                <i class="bi bi-search"></i>
                <input type="text" class="form-control table-search border-0 bg-custom-glass" data-target="#logsTable" placeholder="بحث في السجل...">
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="logsTable">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">الوقت والتاريخ</th>
                            <th>المستخدم</th>
                            <th>الإجراء</th>
                            <th class="pe-4">التفاصيل</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $logs = $pdo->prepare("SELECT al.*, u.username FROM activity_log al LEFT JOIN users u ON al.user_id = u.id WHERE al.project_id = ? OR al.project_id IS NULL ORDER BY al.created_at DESC LIMIT 100");
                        $logs->execute([$pid]);
                        while($l = $logs->fetch()):
                        ?>
                        <tr>
                            <td class="ps-4">
                                <span class="small fw-bold text-custom-main"><?= date('Y-m-d', strtotime($l['created_at'])) ?></span><br>
                                <small class="text-custom-muted"><?= date('H:i:s', strtotime($l['created_at'])) ?></small>
                            </td>
                            <td><span class="badge bg-secondary bg-opacity-10 text-secondary fw-normal"><?= htmlspecialchars($l['username'] ?: 'النظام') ?></span></td>
                            <td><span class="fw-bold text-custom-main"><?= htmlspecialchars($l['action']) ?></span></td>
                            <td class="pe-4 small text-custom-muted"><?= htmlspecialchars($l['details'] ?: '-') ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
