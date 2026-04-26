<div class="top-header mb-4">
    <div class="card p-2 border-0 glass shadow-sm">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
            <!-- Right: Brand & Project Selection -->
            <div class="d-flex align-items-center gap-3 p-2">
                <div class="bg-primary bg-opacity-10 p-2 rounded-2 d-none d-md-block">
                    <i class="bi bi-building text-custom-accent fs-5"></i>
                </div>
                <div style="min-width: 200px;" class="project-select-container">
                    <select onchange="location.href='index.php?set_project='+this.value" class="form-select border-0 fw-bold shadow-none bg-transparent text-custom-accent p-0 small" style="cursor: pointer; font-size: 0.9rem;">
                        <option value="">-- اختر المشروع --</option>
                        <?php
                        $ps=$pdo->prepare("SELECT * FROM projects WHERE user_id = ?");
                        $ps->execute([$uid]);
                        while($p=$ps->fetch()) echo "<option value='{$p['id']}' ".($pid==$p['id']?'selected':'').">".h($p['project_name'])."</option>";
                        ?>
                    </select>
                </div>
            </div>

            <!-- Left: Actions & Profile -->
            <div class="d-flex align-items-center gap-2">
                <!-- Theme Toggle -->
                <button id="theme-toggle" class="header-icon-btn" title="تبديل الوضع">
                    <i class="bi bi-moon-stars-fill"></i>
                </button>

                <!-- Sidebar Toggle (Only if project selected) -->
                <?php if($pid): ?>
                <button id="sidebar-toggle" class="header-icon-btn" title="القائمة">
                    <i class="bi bi-list fs-4"></i>
                </button>
                <?php endif; ?>

                <!-- Add Project -->
                <button class="header-icon-btn" data-bs-toggle="modal" data-bs-target="#addP" title="مشروع جديد">
                    <i class="bi bi-plus-lg"></i>
                </button>

                <!-- User Profile & Logout -->
                <div class="d-flex align-items-center bg-custom-glass bg-opacity-50 rounded-2 px-3 py-1 gap-2 border" style="height: 40px;">
                    <span class="small fw-bold text-custom-main d-none d-md-inline"><?= h($_SESSION['username']) ?></span>
                    <a href="?logout=1" class="text-danger d-flex align-items-center" title="تسجيل الخروج">
                        <i class="bi bi-box-arrow-right fs-5" style="line-height: 1;"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Sidebar Navigation -->
<div id="sidebar-overlay" class="sidebar-overlay"></div>
<div id="sidebar" class="sidebar shadow-lg">
    <div class="sidebar-header">
        <h5 class="fw-bold mb-0 text-custom-accent">القائمة الرئيسية</h5>
        <button id="sidebar-close" class="btn btn-sm btn-light border-0">
            <i class="bi bi-x-lg"></i>
        </button>
    </div>
    <?php if($pid) include 'templates/navigation.php'; ?>
</div>
