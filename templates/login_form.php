<div class="row justify-content-center align-items-center" style="min-height: 80vh;">
    <div class="col-md-5">
        <div class="card border-0 shadow-lg overflow-hidden animate__animated animate__zoomIn">
            <div class="card-header bg-primary text-white p-4 text-center border-0">
                <i class="bi bi-shield-lock display-4 d-block mb-2"></i>
                <h4 class="fw-bold mb-0">نظام إدارة الإعمار</h4>
                <p class="small text-white-50 mb-0">يرجى تسجيل الدخول للمتابعة</p>
            </div>
            <div class="card-body p-4 p-md-5">
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger border-0 small py-2 mb-4"><?= $error ?></div>
                <?php endif; ?>
                <?php if (isset($success)): ?>
                    <div class="alert alert-success border-0 small py-2 mb-4"><?= $success ?></div>
                <?php endif; ?>

                <form method="POST" id="authForm">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">اسم المستخدم</label>
                        <div class="input-group">
                            <span class="input-group-text border-0 bg-light"><i class="bi bi-person text-muted"></i></span>
                            <input name="username" class="form-control border-0 bg-light py-2" placeholder="أدخل اسم المستخدم" required autofocus>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="form-label small fw-bold text-muted">كلمة المرور</label>
                        <div class="input-group">
                            <span class="input-group-text border-0 bg-light"><i class="bi bi-key text-muted"></i></span>
                            <input type="password" name="password" class="form-control border-0 bg-light py-2" placeholder="********" required>
                        </div>
                    </div>

                    <button type="submit" name="login" id="submitBtn" class="btn btn-primary w-100 py-2 fw-bold shadow-sm mb-3">دخول للنظام</button>

                    <div class="text-center mt-3">
                        <span class="small text-muted" id="toggleText">ليس لديك حساب؟</span>
                        <button type="button" class="btn btn-link btn-sm text-primary p-0 fw-bold" id="toggleBtn" onclick="toggleAuth()">إنشاء حساب جديد</button>
                    </div>
                </form>
            </div>
            <div class="card-footer bg-light p-3 text-center border-0">
                <p class="small text-muted mb-0">&copy; <?= date('Y') ?> نظام إدارة الإعمار الاحترافي</p>
            </div>
        </div>
    </div>
</div>

<script>
function toggleAuth() {
    const btn = document.getElementById('submitBtn');
    const text = document.getElementById('toggleText');
    const toggleBtn = document.getElementById('toggleBtn');

    if (btn.name === 'login') {
        btn.name = 'register';
        btn.textContent = 'تسجيل حساب جديد';
        text.textContent = 'لديك حساب بالفعل؟';
        toggleBtn.textContent = 'تسجيل الدخول';
    } else {
        btn.name = 'login';
        btn.textContent = 'دخول للنظام';
        text.textContent = 'ليس لديك حساب؟';
        toggleBtn.textContent = 'إنشاء حساب جديد';
    }
}
</script>
