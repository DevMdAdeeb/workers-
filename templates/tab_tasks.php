<div class="tab-pane fade" id="t-tasks">
    <div class="row g-4">
        <div class="col-md-4">
            <div class="card p-4 border-0 shadow-sm h-100">
                <h6 class="fw-bold mb-3"><i class="bi bi-plus-square me-1"></i> إضافة مهمة موقع</h6>
                <form method="POST">
                    <textarea name="task_text" class="form-control mb-3" rows="3" placeholder="اكتب وصف المهمة هنا (مثلاً: تركيب شبابيك الدور الثاني)" required></textarea>
                    <button name="add_task" class="btn btn-primary w-100 py-2"><i class="bi bi-plus-lg me-1"></i> إضافة لقائمة المهام</button>
                </form>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card border-0 shadow-sm h-100 overflow-hidden">
                <div class="card-header bg-white border-0 py-3">
                    <h6 class="fw-bold m-0"><i class="bi bi-list-check me-1"></i> قائمة المهام والمتابعة</h6>
                </div>
                <div class="list-group list-group-flush">
                    <?php
                    $tasks = $pdo->prepare("SELECT * FROM tasks WHERE project_id = ? ORDER BY is_done ASC, id DESC");
                    $tasks->execute([$pid]);
                    if($tasks->rowCount() > 0):
                        while($t = $tasks->fetch()):
                    ?>
                    <div class="list-group-item d-flex justify-content-between align-items-center py-3 border-light">
                        <div class="d-flex align-items-center">
                            <a href="?toggle_task=<?= $t['id'] ?>" class="text-decoration-none me-3">
                                <?php if($t['is_done']): ?>
                                    <i class="bi bi-check-circle-fill text-success fs-4"></i>
                                <?php else: ?>
                                    <i class="bi bi-circle text-muted fs-4"></i>
                                <?php endif; ?>
                            </a>
                            <span class="<?= $t['is_done'] ? 'text-decoration-line-through text-muted' : 'fw-500' ?>">
                                <?= htmlspecialchars($t['task_text']) ?>
                            </span>
                        </div>
                        <a href="?del_task=<?= $t['id'] ?>" class="btn btn-sm btn-outline-danger border-0" onclick="return confirm('حذف المهمة؟')">
                            <i class="bi bi-trash"></i>
                        </a>
                    </div>
                    <?php endwhile; else: ?>
                        <div class="p-5 text-center text-muted">
                            <i class="bi bi-clipboard-check display-4 mb-2 d-block opacity-25"></i>
                            لا توجد مهام مضافة حالياً
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
