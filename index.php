<?php
session_start();
require_once 'config/db.php';
require_once 'includes/functions.php';
require_once 'includes/auth_logic.php';

$uid = $_SESSION['user_id'] ?? null;
if (isset($_GET['set_project'])) { $_SESSION['active_project'] = $_GET['set_project']; }
$pid = $_SESSION['active_project'] ?? null;

require_once 'includes/project_logic.php';

// Logic for report_data (kept in index for now as it's specific to the report tab)
$report_data = null;
if ($pid && isset($_POST['get_worker_report'])) {
    $w_name = $_POST['worker_name'];
    $from = $_POST['date_from'];
    $to = $_POST['date_to'];

    $stmt = $pdo->prepare("SELECT SUM(total_amount) as grand_total, SUM(paid_amount) as grand_initial_paid FROM workers WHERE worker_name = ? AND project_id = ? AND entry_date BETWEEN ? AND ?");
    $stmt->execute([$w_name, $pid, $from, $to]);
    $worker_totals = $stmt->fetch();

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
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>نظام إدارة الإعمار الاحترافي</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/style.css">
</head>
<body class="container py-3">

<?php if (!$uid): ?>
    <?php include 'templates/login_form.php'; ?>
<?php else: ?>
    <?php include 'templates/header.php'; ?>

    <?php if ($pid): ?>
        <?php include 'templates/navigation.php'; ?>

        <div class="tab-content">
            <?php
            include 'templates/tab_budget.php';
            include 'templates/tab_materials.php';
            include 'templates/tab_workers.php';
            include 'templates/tab_tasks.php';
            include 'templates/tab_report.php';
            include 'templates/tab_suppliers.php';
            include 'templates/tab_logs.php';
            ?>
        </div>
    <?php else: ?>
        <div class='alert alert-info card p-5 text-center shadow-sm'>
            <i class="bi bi-building-add display-1 mb-3 text-primary"></i>
            <h5>مرحباً بك، اختر مشروعاً من القائمة أو أضف مشروعاً جديداً للبدء</h5>
        </div>
    <?php endif; ?>

    <?php include 'templates/modals.php'; ?>
<?php endif; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/script.js"></script>
</body>
</html>
