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

$days = ["الأحد", "الاثنين", "الثلاثاء", "الأربعاء", "الخميس", "الجمعة", "السبت"];
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>كشف حساب: <?= htmlspecialchars($worker['worker_name']) ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        :root { --print-primary: #1e293b; --print-accent: #0284c7; }
        body { background: #f8fafc; font-family: 'Segoe UI', Tahoma, sans-serif; padding: 40px 0; }
        .statement-card { background: white; width: 210mm; min-height: 297mm; margin: 0 auto; padding: 20mm; box-shadow: 0 10px 25px rgba(0,0,0,0.05); border-radius: 12px; border-top: 8px solid var(--print-accent); }
        .header-section { border-bottom: 2px solid #f1f5f9; padding-bottom: 30px; margin-bottom: 40px; }
        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 30px; margin-bottom: 40px; }
        .info-box { background: #f8fafc; padding: 20px; border-radius: 8px; border: 1px solid #e2e8f0; }
        .table thead { background: var(--print-primary); color: white; }
        .table th, .table td { padding: 12px 15px; border-color: #f1f5f9; }
        .total-row { background: #f0f9ff !important; font-weight: 800; color: var(--print-accent); }
        @media print {
            body { background: white; padding: 0; }
            .statement-card { box-shadow: none; border-radius: 0; width: 100%; margin: 0; padding: 10mm; }
            .no-print { display: none; }
            .info-box { background: #f8fafc !important; -webkit-print-color-adjust: exact; }
            .table thead { -webkit-print-color-adjust: exact; background-color: #1e293b !important; }
            .total-row { -webkit-print-color-adjust: exact; background-color: #f0f9ff !important; }
        }
    </style>
</head>
<body>

    <div class="text-center no-print mb-4">
        <button onclick="window.print()" class="btn btn-dark btn-lg px-5 shadow"><i class="bi bi-printer me-2"></i>طباعة السند / حفظ PDF</button>
        <a href="index.php" class="btn btn-outline-secondary btn-lg px-4 ms-2">العودة</a>
    </div>

    <div class="statement-card">
        <div class="header-section d-flex justify-content-between align-items-center">
            <div>
                <h1 class="fw-bold mb-0" style="color: var(--print-primary);">كشف حساب مالي</h1>
                <p class="text-muted mb-0">سند استلام مستحقات فنية</p>
            </div>
            <div class="text-start">
                <h5 class="fw-bold mb-1"><?= htmlspecialchars($_SESSION['username']) ?></h5>
                <p class="small text-muted mb-0">المشروع: <?= htmlspecialchars($worker['project_name']) ?></p>
                <p class="small text-muted mb-0">تاريخ الإصدار: <?= date('Y-m-d') ?></p>
            </div>
        </div>

        <div class="info-grid">
            <div class="info-box">
                <h6 class="fw-bold text-muted text-uppercase small mb-3 border-bottom pb-2">بيانات المستلم (العامل/الفني)</h6>
                <p class="mb-2"><strong>الاسم:</strong> <span class="fs-5 fw-bold"><?= htmlspecialchars($worker['worker_name']) ?></span></p>
                <p class="mb-2"><strong>الاختصاص:</strong> <?= htmlspecialchars($worker['task_desc']) ?></p>
                <p class="mb-2"><strong>الهاتف:</strong> <?= htmlspecialchars($worker['phone'] ?: '-') ?></p>
                <p class="mb-0"><strong>نوع الدوام:</strong> <?= ($worker['work_period'] == 'half' ? 'نصف يوم' : 'يوم كامل') ?></p>
            </div>
            <div class="info-box">
                <h6 class="fw-bold text-muted text-uppercase small mb-3 border-bottom pb-2">ملخص الوضع المالي</h6>
                <p class="mb-2 d-flex justify-content-between"><span>إجمالي قيمة الاتفاق:</span> <span class="fw-bold"><?= formatCurrency($worker['total_amount']) ?></span></p>
                <p class="mb-2 d-flex justify-content-between text-success"><span>إجمالي المدفوعات:</span> <span class="fw-bold"><?= formatCurrency($total_paid) ?></span></p>
                <hr>
                <p class="mb-0 d-flex justify-content-between text-danger fs-5 fw-bold"><span>المبلغ المتبقي:</span> <span><?= formatCurrency($remaining) ?></span></p>
            </div>
        </div>

        <h6 class="fw-bold mb-3"><i class="bi bi-list-ul me-2"></i>تفاصيل الدفعات والسحبيات المسجلة:</h6>
        <table class="table table-bordered align-middle">
            <thead>
                <tr>
                    <th style="width: 15%">التاريخ</th>
                    <th style="width: 15%">اليوم</th>
                    <th>بيان العملية</th>
                    <th style="width: 20%" class="text-start">المبلغ</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><?= $worker['entry_date'] ?></td>
                    <td class="small text-muted"><?= $days[date('w', strtotime($worker['entry_date']))] ?></td>
                    <td>دفعة أولى (سلفة عند التسجيل في الموقع)</td>
                    <td class="fw-bold text-start"><?= formatCurrency($worker['paid_amount']) ?></td>
                </tr>
                <?php foreach($all_payments as $p): ?>
                <tr>
                    <td><?= $p['payment_date'] ?></td>
                    <td class="small text-muted"><?= $days[date('w', strtotime($p['payment_date']))] ?></td>
                    <td>سحبة نقدية مرحلية</td>
                    <td class="fw-bold text-primary text-start"><?= formatCurrency($p['amount']) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr class="total-row">
                    <td colspan="3" class="text-start">إجمالي المبالغ المستلمة حتى تاريخه</td>
                    <td class="text-start fs-5"><?= formatCurrency($total_paid) ?></td>
                </tr>
            </tfoot>
        </table>

        <div class="mt-5 pt-5 row">
            <div class="col-4 border-top pt-3 text-center">
                <p class="small text-muted mb-4">توقيع المستلم (إقرار بالاستلام)</p>
                <div class="mt-5" style="border-bottom: 1px dashed #ccc; width: 150px; margin: 0 auto;"></div>
            </div>
            <div class="col-4 offset-4 border-top pt-3 text-center">
                <p class="small text-muted mb-4">توقيع الإدارة / المحاسب</p>
                <div class="mt-5" style="border-bottom: 1px dashed #ccc; width: 150px; margin: 0 auto;"></div>
            </div>
        </div>

        <div class="mt-auto pt-5 text-center small text-muted opacity-50">
            هذه الوثيقة صادرة عن النظام الإلكتروني وتعتبر مرجعاً للمحاسبة
        </div>
    </div>

</body>
</html>
