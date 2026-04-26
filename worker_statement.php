<?php
session_start();
require_once 'config/db.php';
require_once 'includes/functions.php';

if (!isset($_SESSION['user_id']) || !isset($_GET['wid'])) { die("غير مسموح بالدخول"); }

$wid = $_GET['wid'];
$uid = $_SESSION['user_id'];

// جلب بيانات العامل
$stmt = $pdo->prepare("SELECT w.*, p.project_name FROM workers w JOIN projects p ON w.project_id = p.id WHERE w.id = ? AND p.user_id = ?");
$stmt->execute([$wid, $uid]);
$worker = $stmt->fetch();

if (!$worker) { die("بيانات العامل غير موجودة"); }

$pid = $worker['project_id'];

// جلب كافة الدفعات
$payments = $pdo->prepare("SELECT * FROM worker_payments WHERE worker_id = ? ORDER BY payment_date ASC");
$payments->execute([$wid]);
$all_payments = $payments->fetchAll();

$total_payments = 0;
foreach($all_payments as $p) $total_payments += $p['amount'];
$total_paid = $worker['paid_amount'] + $total_payments;
$remaining = $worker['total_amount'] - $total_paid;

// إعدادات التاريخ العربي (اختياري)
$days = ["الأحد", "الاثنين", "الثلاثاء", "الأربعاء", "الخميس", "الجمعة", "السبت"];
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>كشف حساب: <?= htmlspecialchars($worker['worker_name']) ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css">
    <style>
        body { background: white; font-family: 'Segoe UI', Tahoma, sans-serif; padding: 40px; }
        .report-header { border-bottom: 2px solid #0d6efd; margin-bottom: 30px; padding-bottom: 15px; }
        .info-box { background: #f8f9fa; border-radius: 10px; padding: 20px; margin-bottom: 30px; }
        .table { border: 1px solid #dee2e6; }
        .table thead { background: #0d6efd; color: white; }
        .summary-card { border: 2px solid #0d6efd; border-radius: 10px; padding: 20px; text-align: center; }
        @media print {
            .no-print { display: none; }
            body { padding: 0; }
            .info-box { background: #f8f9fa !important; -webkit-print-color-adjust: exact; }
        }
    </style>
</head>
<body>

    <div class="text-center no-print mb-4">
        <button onclick="window.print()" class="btn btn-primary btn-lg"><i class="bi bi-printer"></i> طباعة كشف الحساب / حفظ PDF</button>
        <a href="index.php" class="btn btn-outline-secondary btn-lg">العودة للنظام</a>
    </div>

    <div class="report-header d-flex justify-content-between align-items-center">
        <div>
            <h1 class="text-primary fw-bold">كشف حساب مالي</h1>
            <p class="text-muted mb-0">مشروع: <?= htmlspecialchars($worker['project_name']) ?></p>
        </div>
        <div class="text-start">
            <h5 class="fw-bold"><?= $_SESSION['username'] ?></h5>
            <p class="small text-muted mb-0">تاريخ الاستخراج: <?= date('Y-m-d') ?></p>
        </div>
    </div>

    <div class="row info-box g-3">
        <div class="col-md-6">
            <h5 class="fw-bold mb-3">بيانات العامل:</h5>
            <p class="mb-1"><strong>الاسم:</strong> <?= htmlspecialchars($worker['worker_name']) ?></p>
            <p class="mb-1"><strong>نوع العمل:</strong> <?= htmlspecialchars($worker['task_desc']) ?></p>
            <p class="mb-1"><strong>رقم الهاتف:</strong> <?= htmlspecialchars($worker['phone'] ?: '-') ?></p>
            <p class="mb-1"><strong>فترة العمل:</strong> <?= ($worker['work_period'] == 'half' ? 'نصف يوم' : 'يوم كامل') ?></p>
        </div>
        <div class="col-md-6">
            <h5 class="fw-bold mb-3">ملخص الحساب:</h5>
            <p class="mb-1"><strong>إجمالي مبلغ الاتفاق:</strong> <?= formatCurrency($worker['total_amount']) ?></p>
            <p class="mb-1 text-success"><strong>إجمالي ما تم صرفه:</strong> <?= formatCurrency($total_paid) ?></p>
            <p class="mb-1 text-danger h5 mt-2"><strong>المتبقي (له):</strong> <?= formatCurrency($remaining) ?></p>
        </div>
    </div>

    <h5 class="fw-bold mb-3 text-primary">تفاصيل السحبيات والدفعات:</h5>
    <table class="table table-striped table-bordered align-middle">
        <thead>
            <tr>
                <th style="width: 20%">التاريخ</th>
                <th style="width: 20%">اليوم</th>
                <th>التفاصيل</th>
                <th style="width: 25%">المبلغ</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><?= $worker['entry_date'] ?></td>
                <td><?= $days[date('w', strtotime($worker['entry_date']))] ?></td>
                <td>السلفة الأولى عند تسجيل العامل</td>
                <td class="fw-bold"><?= formatCurrency($worker['paid_amount']) ?></td>
            </tr>
            <?php foreach($all_payments as $p): ?>
            <tr>
                <td><?= $p['payment_date'] ?></td>
                <td><?= $days[date('w', strtotime($p['payment_date']))] ?></td>
                <td>سحبة نقدية</td>
                <td class="fw-bold text-primary"><?= formatCurrency($p['amount']) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr class="table-dark">
                <td colspan="3" class="text-start fw-bold">الإجمالي الكلي للمصروف له</td>
                <td class="fw-bold"><?= formatCurrency($total_paid) ?></td>
            </tr>
        </tfoot>
    </table>

    <div class="mt-5 row">
        <div class="col-4 border-top pt-2 text-center">
            <p class="small text-muted">توقيع المستلم</p>
            <br><br>
            ________________
        </div>
        <div class="col-4 offset-4 border-top pt-2 text-center">
            <p class="small text-muted">توقيع الإدارة</p>
            <br><br>
            ________________
        </div>
    </div>

    <div class="mt-5 pt-5 text-center small text-muted">
        تم استخراج هذا التقرير آلياً بواسطة نظام إدارة الإعمار الذكي
    </div>

</body>
</html>
