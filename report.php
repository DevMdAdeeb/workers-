<?php
session_start();
if (!isset($_SESSION['user_id']) || !isset($_GET['pid'])) { die("غير مسموح بالدخول"); }

$host = 'localhost';
$db   = 'sams_workers'; 
$user = 'sams_workers';
$pass = 'Mohammed7134';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->exec("set names utf8mb4");
} catch (PDOException $e) { die("خطأ اتصال"); }

$pid = $_GET['pid'];
$uid = $_SESSION['user_id'];

// جلب بيانات المشروع
$proj = $pdo->prepare("SELECT * FROM projects WHERE id=? AND user_id=?");
$proj->execute([$pid, $uid]);
$project = $proj->fetch();
if (!$project) { die("المشروع غير موجود"); }

// --- الحسابات المالية الدقيقة ---

// 1. المواد (إجمالي قيمة المواد المسجلة)
$m_total = $pdo->query("SELECT SUM(price) FROM materials WHERE project_id=$pid")->fetchColumn() ?: 0;
// 2. المواد الكاش (التي دفعت ثمنها فوراً)
$m_cash = $pdo->query("SELECT SUM(price) FROM materials WHERE project_id=$pid AND is_paid=1")->fetchColumn() ?: 0;

// 3. العمال (سلف أولية + دفعات إضافية)
$w_initial = $pdo->query("SELECT SUM(paid_amount) FROM workers WHERE project_id=$pid")->fetchColumn() ?: 0;
$w_extra = $pdo->query("SELECT SUM(wp.amount) FROM worker_payments wp JOIN workers w ON wp.worker_id=w.id WHERE w.project_id=$pid")->fetchColumn() ?: 0;
$w_paid_total = $w_initial + $w_extra;

// 4. الموردين (إجمالي ما دفعته للتجار كأقساط)
$s_paid_total = $pdo->query("SELECT SUM(amount) FROM supplier_payments sp JOIN suppliers s ON sp.supplier_id = s.id WHERE s.project_id = $pid")->fetchColumn() ?: 0;

// المعادلة الذهبية للمصروفات الحقيقية (السيولة الخارجة فعلياً)
$total_spent = $m_cash + $s_paid_total + $w_paid_total;
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>تقرير مشروع - <?= $project['project_name'] ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css">
    <style>
        body { background: white; font-family: 'Segoe UI', Tahoma, sans-serif; padding: 30px; }
        .report-header { border-bottom: 2px solid #333; margin-bottom: 30px; padding-bottom: 10px; }
        .section-title { background: #f8f9fa; padding: 10px; border-right: 5px solid #0d6efd; margin-top: 30px; margin-bottom: 15px; font-weight: bold; }
        .table { font-size: 0.85rem; }
        @media print {
            .no-print { display: none; }
            body { padding: 0; }
        }
    </style>
</head>
<body>

    <div class="text-center no-print mb-4">
        <button onclick="window.print()" class="btn btn-primary btn-lg">حفظ كـ PDF / طباعة الكشف</button>
    </div>

    <div class="report-header d-flex justify-content-between align-items-center">
        <div>
            <h2>كشف حساب مشروع: <?= $project['project_name'] ?></h2>
            <p class="text-muted small">تاريخ التقرير: <?= date('Y-m-d H:i') ?></p>
        </div>
        <div class="text-start">
            <h5>إدارة: <?= $_SESSION['username'] ?></h5>
        </div>
    </div>

    <!-- 1. ملخص الميزانية -->
    <div class="section-title">أولاً: الملخص المالي (السيولة النقدية)</div>
    <table class="table table-bordered text-center">
        <thead class="table-dark">
            <tr>
                <th>الميزانية المرصودة</th>
                <th>إجمالي المصروفات الحقيقية</th>
                <th>المتبقي من الميزانية</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><?= number_format($project['budget'], 2) ?></td>
                <td class="text-danger fw-bold"><?= number_format($total_spent, 2) ?></td>
                <td class="text-success fw-bold"><?= number_format($project['budget'] - $total_spent, 2) ?></td>
            </tr>
        </tbody>
    </table>

    <!-- 2. كشف المواد -->
    <div class="section-title">ثانياً: سجل المواد والمشتريات</div>
    <table class="table table-striped table-sm">
        <thead class="table-secondary">
            <tr>
                <th>التاريخ</th>
                <th>المادة</th>
                <th>التصنيف</th>
                <th>الحالة</th>
                <th>السعر</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $mats = $pdo->query("SELECT * FROM materials WHERE project_id=$pid ORDER BY purchase_date ASC");
            while($m = $mats->fetch()): ?>
            <tr>
                <td><?= $m['purchase_date'] ?></td>
                <td><?= $m['item_name'] ?></td>
                <td><?= $m['category'] ?></td>
                <td><?= $m['is_paid'] ? 'كاش' : '<span class="text-danger">دين</span>' ?></td>
                <td><?= number_format($m['price'], 2) ?></td>
            </tr>
            <?php endwhile; ?>
            <tr class="table-light fw-bold">
                <td colspan="4 text-start">إجمالي قيمة المواد (كاش + دين)</td>
                <td><?= number_format($m_total, 2) ?></td>
            </tr>
        </tbody>
    </table>

    <!-- 3. كشف العمال -->
    <div class="section-title">ثالثاً: سجل العمال والأجور</div>
    <table class="table table-striped table-sm">
        <thead class="table-secondary">
            <tr>
                <th>اسم العامل</th>
                <th>نوع العمل</th>
                <th>الاتفاق الكلي</th>
                <th>إجمالي المدفوع له</th>
                <th>المتبقي في ذمتك</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $workers = $pdo->query("SELECT * FROM workers WHERE project_id=$pid ORDER BY id ASC");
            while($w = $workers->fetch()): 
                $extra = $pdo->query("SELECT SUM(amount) FROM worker_payments WHERE worker_id={$w['id']}")->fetchColumn() ?: 0;
                $paid = $w['paid_amount'] + $extra;
                $rem = $w['total_amount'] - $paid;
            ?>
            <tr>
                <td><?= $w['worker_name'] ?></td>
                <td><?= $w['task_desc'] ?></td>
                <td><?= number_format($w['total_amount'], 2) ?></td>
                <td><?= number_format($paid, 2) ?></td>
                <td class="text-danger fw-bold"><?= number_format($rem, 2) ?></td>
            </tr>
            <?php endwhile; ?>
            <tr class="table-light fw-bold">
                <td colspan="3 text-start">إجمالي المبالغ المدفوعة للعمال فعلياً</td>
                <td><?= number_format($w_paid_total, 2) ?></td>
                <td>-</td>
            </tr>
        </tbody>
    </table>

    <!-- 4. كشف الموردين -->
    <div class="section-title">رابعاً: حسابات الموردين (التجار والديون)</div>
    <table class="table table-bordered table-sm">
        <thead class="table-secondary">
            <tr>
                <th>اسم التاجر</th>
                <th>إجمالي المشتريات (دين)</th>
                <th>إجمالي ما تم سداده</th>
                <th>المتبقي للتاجر</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $sups = $pdo->prepare("SELECT * FROM suppliers WHERE project_id = ?");
            $sups->execute([$pid]);
            $total_debt_all = 0;
            $total_paid_all = 0;

            while($s = $sups->fetch()):
                $debt = $pdo->query("SELECT SUM(price) FROM materials WHERE supplier_id={$s['id']} AND is_paid=0")->fetchColumn() ?: 0;
                $paid = $pdo->query("SELECT SUM(amount) FROM supplier_payments WHERE supplier_id={$s['id']}")->fetchColumn() ?: 0;
                $rem = $debt - $paid;

                $total_debt_all += $debt;
                $total_paid_all += $paid;
            ?>
            <tr>
                <td><?= $s['supplier_name'] ?></td>
                <td><?= number_format($debt, 2) ?></td>
                <td><?= number_format($paid, 2) ?></td>
                <td class="fw-bold text-danger"><?= number_format($rem, 2) ?></td>
            </tr>
            <?php endwhile; ?>
            <tr class="table-dark">
                <td>الإجمالي العام للمديونيات</td>
                <td><?= number_format($total_debt_all, 2) ?></td>
                <td><?= number_format($total_paid_all, 2) ?></td>
                <td><?= number_format($total_debt_all - $total_paid_all, 2) ?></td>
            </tr>
        </tbody>
    </table>

    <!-- 5. المهام المنجزة -->
    <div class="section-title">خامساً: قائمة المهام والمتابعة</div>
    <div class="row px-3">
        <?php 
        $tasks = $pdo->query("SELECT * FROM tasks WHERE project_id=$pid");
        if ($tasks->rowCount() > 0) {
            while($t = $tasks->fetch()): ?>
                <div class="col-6 mb-1">
                    <span class="<?= $t['is_done'] ? 'text-success' : 'text-muted' ?>">
                        <?= $t['is_done'] ? '✔' : '⭕' ?> <?= htmlspecialchars($t['task_text']) ?>
                    </span>
                </div>
            <?php endwhile;
        } else { echo "<p class='text-muted'>لا توجد مهام مسجلة لهذا المشروع.</p>"; }
        ?>
    </div>

    <div class="mt-5 text-center small text-muted border-top pt-3">
        هذا التقرير مخصص لمشروع "<?= $project['project_name'] ?>" | تم الاستخراج بواسطة نظام إدارة الإعمار الذكي
    </div>

</body>
</html> 