<div class="tab-pane fade" id="t-tasks">
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white border-0 pt-4 px-4">
            <h5 class="fw-bold mb-0 text-primary"><i class="bi bi-list-check me-2"></i>قائمة المهام والمتابعة</h5>
            <p class="text-muted small mb-0">سجل المهام المطلوب إنجازها في الموقع</p>
        </div>
        <div class="card-body p-4">
            <form method="POST" class="d-flex gap-2 mb-4">
                <input name="task_text" class="form-control bg-light border-0 py-2" placeholder="أضف مهمة جديدة هنا..." required>
                <button name="add_task" class="btn btn-primary px-4 shadow-sm"><i class="bi bi-plus-lg me-1"></i>إضافة</button>
            </form>

            <div class="row g-3">
                <?php
                $tasks = $pdo->prepare("SELECT * FROM tasks WHERE project_id = ? ORDER BY id DESC");
                $tasks->execute([$pid]);
                while($t = $tasks->fetch()): ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="card h-100 border-0 shadow-sm bg-light bg-opacity-50 transition-all hover-shadow">
                            <div class="card-body p-3 d-flex align-items-center">
                                <div class="me-3">
                                    <a href="?toggle_task=<?= $t['id'] ?>" class="btn btn-lg p-0 text-<?= $t['is_done'] ? 'success' : 'secondary opacity-25' ?>">
                                        <i class="bi bi-check-circle-fill fs-3"></i>
                                    </a>
                                </div>
                                <div class="flex-grow-1">
                                    <span class="fw-bold text-dark <?= $t['is_done'] ? 'text-decoration-line-through text-muted' : '' ?>">
                                        <?= htmlspecialchars($t['task_text']) ?>
                                    </span>
                                </div>
                                <div>
                                    <a href="?del_task=<?= $t['id'] ?>" class="btn btn-sm btn-outline-danger border-0 rounded-circle" onclick="return confirm('حذف المهمة؟')">
                                        <i class="bi bi-trash3"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
                <?php if($tasks->rowCount() == 0): ?>
                    <div class="col-12 text-center py-5">
                        <i class="bi bi-clipboard-check text-muted display-1 opacity-25"></i>
                        <p class="text-muted mt-3">لا توجد مهام حالياً. ابدأ بإضافة المهام لمتابعة سير العمل.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
