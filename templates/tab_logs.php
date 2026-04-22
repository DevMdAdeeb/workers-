<div class="tab-pane fade" id="t-logs">
    <div class="card border-0 shadow-sm p-3">
        <h6 class="fw-bold mb-3"><i class="bi bi-clock-history me-1"></i> سجل نشاطات النظام</h6>
        <div class="table-responsive">
            <table class="table table-sm table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>التاريخ والوقت</th>
                        <th>المستخدم</th>
                        <th>الإجراء</th>
                        <th>التفاصيل</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $logs = $pdo->prepare("
                        SELECT l.*, u.username
                        FROM activity_log l
                        JOIN users u ON l.user_id = u.id
                        WHERE l.project_id = ? OR l.project_id IS NULL
                        ORDER BY l.id DESC LIMIT 50
                    ");
                    $logs->execute([$pid]);
                    while($log = $logs->fetch()):
                    ?>
                    <tr>
                        <td><small class="text-muted"><?= $log['created_at'] ?></small></td>
                        <td><span class="badge bg-secondary-subtle text-secondary"><?= htmlspecialchars($log['username']) ?></span></td>
                        <td><span class="fw-bold small"><?= htmlspecialchars($log['action']) ?></span></td>
                        <td><small><?= htmlspecialchars($log['details']) ?></small></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
