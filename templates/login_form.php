<div class="row justify-content-center mt-5">
    <div class="col-md-5 card p-4 shadow-lg border-0">
        <div class="text-center mb-4">
            <i class="bi bi-building text-primary display-4"></i>
            <h3 class="mt-2 text-primary fw-bold">نظام إدارة الإعمار</h3>
            <p class="text-muted">أهلاً بك، يرجى تسجيل الدخول للمتابعة</p>
        </div>

        <?php if(isset($error)) echo "<div class='alert alert-danger p-2 border-0 small'><i class='bi bi-exclamation-triangle-fill me-2'></i>$error</div>"; ?>
        <?php if(isset($success)) echo "<div class='alert alert-success p-2 border-0 small'><i class='bi bi-check-circle-fill me-2'></i>$success</div>"; ?>

        <ul class="nav nav-pills nav-justified mb-4" id="pills-tab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="pills-login-tab" data-bs-toggle="pill" data-bs-target="#lForm" type="button" role="tab">دخول</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="pills-register-tab" data-bs-toggle="pill" data-bs-target="#rForm" type="button" role="tab">تسجيل</button>
            </li>
        </ul>

        <div class="tab-content" id="pills-tabContent">
            <div class="tab-pane fade show active" id="lForm" role="tabpanel">
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label small">اسم المستخدم</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-0"><i class="bi bi-person"></i></span>
                            <input name="username" class="form-control bg-light border-0" placeholder="أدخل اسم المستخدم" required>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="form-label small">كلمة المرور</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-0"><i class="bi bi-lock"></i></span>
                            <input type="password" name="password" class="form-control bg-light border-0" placeholder="أدخل كلمة المرور" required>
                        </div>
                    </div>
                    <button name="login" class="btn btn-primary w-100 py-2 fw-bold">تسجيل الدخول</button>
                </form>
            </div>
            <div class="tab-pane fade" id="rForm" role="tabpanel">
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label small">اسم المستخدم الجديد</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-0"><i class="bi bi-person-plus"></i></span>
                            <input name="username" class="form-control bg-light border-0" placeholder="اختر اسم مستخدم" required>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="form-label small">كلمة المرور</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-0"><i class="bi bi-key"></i></span>
                            <input type="password" name="password" class="form-control bg-light border-0" placeholder="اختر كلمة مرور قوية" required>
                        </div>
                    </div>
                    <button name="register" class="btn btn-success w-100 py-2 fw-bold">إنشاء حساب جديد</button>
                </form>
            </div>
        </div>
    </div>
</div>
