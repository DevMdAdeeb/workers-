<?php
session_start();
if (!isset($_SESSION['user_id']) || !isset($_GET['pid'])) { die("غير مسموح بالدخول"); }

require_once 'config/db.php';
require_once 'includes/functions.php';

$pid = $_GET['pid'];
$uid = $_SESSION['user_id'];

// جلب بيانات المشروع
$proj = $pdo->prepare("SELECT * FROM projects WHERE id=? AND user_id=?");
$proj->execute([$pid, $uid]);
$project = $proj->fetch();
if (!$project) { die("المشروع غير موجود"); }

// --- الحسابات المالية الدقيقة ---
$m_total_stmt = $pdo->prepare("SELECT SUM(price) FROM materials WHERE project_id=?");
$m_total_stmt->execute([$pid]);
$m_total = $m_total_stmt->fetchColumn() ?: 0;

$m_cash_stmt = $pdo->prepare("SELECT SUM(price) FROM materials WHERE project_id=? AND is_paid=1");
$m_cash_stmt->execute([$pid]);
$m_cash = $m_cash_stmt->fetchColumn() ?: 0;

$w_initial_stmt = $pdo->prepare("SELECT SUM(paid_amount) FROM workers WHERE project_id=?");
$w_initial_stmt->execute([$pid]);
$w_initial = $w_initial_stmt->fetchColumn() ?: 0;

$w_extra_stmt = $pdo->prepare("SELECT SUM(wp.amount) FROM worker_payments wp JOIN workers w ON wp.worker_id=w.id WHERE w.project_id=?");
$w_extra_stmt->execute([$pid]);
$w_extra = $w_extra_stmt->fetchColumn() ?: 0;
$w_paid_total = $w_initial + $w_extra;

$s_paid_total_stmt = $pdo->prepare("SELECT SUM(amount) FROM supplier_payments sp JOIN suppliers s ON sp.supplier_id = s.id WHERE s.project_id = ?");
$s_paid_total_stmt->execute([$pid]);
$s_paid_total = $s_paid_total_stmt->fetchColumn() ?: 0;

$total_spent = $m_cash + $s_paid_total + $w_paid_total;
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>تقرير مشروع - <?= h($project['project_name']) ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        :root { --print-primary: #0f172a; --print-accent: #3b82f6; }
        body { background: #f1f5f9; font-family: 'Segoe UI', Tahoma, sans-serif; padding: 40px 0; }
        .report-paper { background: white; width: 210mm; min-height: 297mm; margin: 0 auto; padding: 25mm; box-shadow: 0 0 20px rgba(0,0,0,0.1); border-radius: 5px; }
        .report-header { border-bottom: 3px solid var(--print-primary); padding-bottom: 20px; margin-bottom: 30px; }
        .section-title { background: #f8fafc; border-right: 5px solid var(--print-accent); padding: 10px 15px; margin: 35px 0 15px 0; font-weight: 700; color: var(--print-primary); text-transform: uppercase; font-size: 1.1rem; }
        .table { font-size: 0.9rem; border-color: #e2e8f0; }
        .table thead { background: var(--print-primary); color: white; }
        .table-secondary { background-color: #f1f5f9 !important; }
        .summary-box { border: 2px solid var(--print-primary); border-radius: 10px; padding: 20px; }
        .watermark { position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%) rotate(-45deg); font-size: 8rem; color: rgba(0,0,0,0.03); z-index: 0; pointer-events: none; }
        @media print {
            body { background: white; padding: 0; }
            .report-paper { box-shadow: none; width: 100%; margin: 0; padding: 15mm; }
            .no-print { display: none; }
            .section-title { -webkit-print-color-adjust: exact; background-color: #f8fafc !important; }
            .table thead { -webkit-print-color-adjust: exact; background-color: #0f172a !important; color: white !important; }
        }
    </style>
</head>
<body>

    <div class="text-center no-print mb-4">
        <button onclick="window.print()" class="btn btn-primary btn-lg px-5 shadow"><i class="bi bi-printer me-2"></i>طباعة التقرير / حفظ كـ PDF</button>
        <a href="index.php" class="btn btn-outline-secondary btn-lg px-4 ms-2">إغلاق</a>
    </div>

    <div class="report-paper position-relative overflow-hidden">
        <div class="watermark fw-bold text-uppercase">Construction</div>

        <div class="report-header d-flex justify-content-between align-items-end">
            <div>
                <h1 class="fw-bold mb-1" style="color: var(--print-primary);">كشف حساب مشروع</h1>
                <h4 class="text-muted mb-0"><?= h($project['project_name']) ?></h4>
            </div>
            <div class="text-start">
                <p class="mb-1 fw-bold">الجهة المصدرة: <?= h($_SESSION['username']) ?></p>
                <p class="mb-0 small text-muted">تاريخ التقرير: <?= date('Y-m-d') ?></p>
            </div>
        </div>

        <!-- 1. ملخص الميزانية -->
        <div class="section-title">أولاً: الملخص المالي والسيولة</div>
        <div class="row g-0 summary-box mb-4">
            <div class="col-4 border-end text-center">
                <span class="text-muted small d-block mb-1">الميزانية المرصودة</span>
                <h4 class="fw-bold mb-0 text-dark"><?= number_format((float)$project['budget']) ?></h4>
            </div>
            <div class="col-4 border-end text-center">
                <span class="text-muted small d-block mb-1">المصروفات الحقيقية</span>
                <h4 class="fw-bold mb-0 text-danger"><?= number_format((float)$total_spent) ?></h4>
            </div>
            <div class="col-4 text-center">
                <span class="text-muted small d-block mb-1">الرصيد المتبقي</span>
                <h4 class="fw-bold mb-0 text-success"><?= number_format((float)($project['budget'] - $total_spent)) ?></h4>
            </div>
        </div>

        <!-- 2. كشف المواد -->
        <div class="section-title">ثانياً: سجل المشتريات والتوريدات</div>
        <table class="table table-bordered align-middle">
            <thead>
                <tr>
                    <th style="width: 15%">التاريخ</th>
                    <th>المادة</th>
                    <th>التصنيف</th>
                    <th style="width: 15%">الحالة</th>
                    <th style="width: 20%">السعر</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $mats_stmt = $pdo->prepare("SELECT * FROM materials WHERE project_id=? ORDER BY purchase_date ASC");
                $mats_stmt->execute([$pid]);
                while($m = $mats_stmt->fetch()): ?>
                <tr>
                    <td><?= h($m['purchase_date']) ?></td>
                    <td class="fw-bold"><?= h($m['item_name']) ?></td>
                    <td><?= h($m['category'] ?: 'عام') ?></td>
                    <td class="text-center small"><?= $m['is_paid'] ? 'نقداً' : '<span class="text-danger">آجل</span>' ?></td>
                    <td class="text-start fw-bold"><?= number_format((float)$m['price']) ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
            <tfoot class="table-secondary">
                <tr class="fw-bold">
                    <td colspan="4" class="text-start">إجمالي قيمة التوريدات (نقداً + آجل)</td>
                    <td class="text-start border-top border-dark border-2"><?= number_format((float)$m_total) ?></td>
                </tr>
            </tfoot>
        </table>

        <!-- 3. كشف العمال -->
        <div class="section-title">ثالثاً: مستحقات العمال والفنيين</div>
        <table class="table table-bordered align-middle">
            <thead>
                <tr>
                    <th>اسم العامل</th>
                    <th>نوع العمل</th>
                    <th>الاتفاق الكلي</th>
                    <th>إجمالي المصروف</th>
                    <th>المتبقي له</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $workers_stmt = $pdo->prepare("SELECT * FROM workers WHERE project_id=? ORDER BY id ASC");
                $workers_stmt->execute([$pid]);
                while($w = $workers_stmt->fetch()):
                    $extra_stmt = $pdo->prepare("SELECT SUM(amount) FROM worker_payments WHERE worker_id=?");
                    $extra_stmt->execute([$w['id']]);
                    $extra = $extra_stmt->fetchColumn() ?: 0;
                    $paid = $w['paid_amount'] + $extra;
                    $rem = $w['total_amount'] - $paid;
                ?>
                <tr>
                    <td class="fw-bold"><?= h($w['worker_name']) ?></td>
                    <td><?= h($w['task_desc']) ?></td>
                    <td><?= number_format((float)$w['total_amount']) ?></td>
                    <td><?= number_format((float)$paid) ?></td>
                    <td class="text-danger fw-bold"><?= number_format((float)$rem) ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
            <tfoot class="table-secondary">
                <tr class="fw-bold">
                    <td colspan="3" class="text-start">إجمالي المبالغ المصروفة للعمال فعلياً</td>
                    <td class="text-start border-top border-dark border-2"><?= number_format((float)$w_paid_total) ?></td>
                    <td>-</td>
                </tr>
            </tfoot>
        </table>

        <!-- 4. كشف الموردين -->
        <div class="section-title">رابعاً: حسابات الموردين والديون</div>
        <table class="table table-bordered align-middle">
            <thead>
                <tr>
                    <th>اسم المورد / التاجر</th>
                    <th>إجمالي المشتريات (آجل)</th>
                    <th>إجمالي المسدد</th>
                    <th>الرصيد المتبقي له</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $sups = $pdo->prepare("SELECT * FROM suppliers WHERE project_id = ?");
                $sups->execute([$pid]);
                $total_debt_all = 0; $total_paid_all = 0;
                while($s = $sups->fetch()):
                    $debt_stmt = $pdo->prepare("SELECT SUM(price) FROM materials WHERE supplier_id=? AND is_paid=0");
                    $debt_stmt->execute([$s['id']]);
                    $debt = $debt_stmt->fetchColumn() ?: 0;

                    $paid_stmt = $pdo->prepare("SELECT SUM(amount) FROM supplier_payments WHERE supplier_id=?");
                    $paid_stmt->execute([$s['id']]);
                    $paid = $paid_stmt->fetchColumn() ?: 0;

                    $rem = $debt - $paid;
                    $total_debt_all += $debt; $total_paid_all += $paid;
                ?>
                <tr>
                    <td class="fw-bold"><?= h($s['supplier_name']) ?></td>
                    <td><?= number_format((float)$debt) ?></td>
                    <td><?= number_format((float)$paid) ?></td>
                    <td class="fw-bold text-danger"><?= number_format((float)$rem) ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
            <tfoot class="table-secondary">
                <tr class="fw-bold text-dark">
                    <td class="text-start">الإجمالي العام للمديونيات</td>
                    <td><?= number_format((float)$total_debt_all) ?></td>
                    <td><?= number_format((float)$total_paid_all) ?></td>
                    <td class="text-danger"><?= number_format((float)($total_debt_all - $total_paid_all)) ?></td>
                </tr>
            </tfoot>
        </table>

        <div class="mt-5 pt-5 row">
            <div class="col-6 text-center">
                <div class="mb-4 small text-muted">توقيع المسؤول المالي</div>
                <div style="border-bottom: 1px solid #ccc; width: 200px; margin: 0 auto;"></div>
            </div>
            <div class="col-6 text-center">
                <div class="mb-4 small text-muted">ختم المنشأة / الإدارة</div>
                <div style="border-bottom: 1px solid #ccc; width: 200px; margin: 0 auto;"></div>
            </div>
        </div>

        <div class="mt-5 text-center small text-muted border-top pt-3">
            تم استخراج هذا التقرير آلياً عبر نظام إدارة الإعمار | صفحة (1) من (1)
        </div>
    </div>

</body>
</html>
