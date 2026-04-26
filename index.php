<?php
session_start();
require_once 'config/db.php';
require_once 'includes/functions.php';
require_once 'includes/auth_logic.php';

$uid = $_SESSION['user_id'] ?? null;
if (isset($_GET['set_project'])) {
    $_SESSION['active_project'] = $_GET['set_project'];
    // Redirect but keep other GET params if exist (like att_date)
    $params = $_GET;
    unset($params['set_project']);
    $qs = count($params) > 0 ? "?" . http_build_query($params) : "";
    header("Location: index.php" . $qs);
    exit();
}
$pid = $_SESSION['active_project'] ?? null;

require_once 'includes/project_logic.php';

// Logic for report_data
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <link rel="stylesheet" href="assets/style.css?v=1.2">
</head>
<body class="container-fluid py-4" style="max-width: 1400px;">

<?php if (!$uid): ?>
    <?php include 'templates/login_form.php'; ?>
<?php else: ?>
    <div class="animate__animated animate__fadeIn px-md-4">
        <?php include 'templates/header.php'; ?>

        <?php if ($pid): ?>
            <div class="tab-content">
                <?php
                include 'templates/tab_budget.php';
                include 'templates/tab_materials.php';
                include 'templates/tab_workers.php';
                include 'templates/tab_attendance.php';
                include 'templates/tab_milestones.php';
                include 'templates/tab_journal.php';
                include 'templates/tab_equipment.php';
                include 'templates/tab_gallery.php';
                include 'templates/tab_tools.php';
                include 'templates/tab_tasks.php';
                include 'templates/tab_report.php';
                include 'templates/tab_suppliers.php';
                include 'templates/tab_logs.php';
                ?>
            </div>
        <?php else: ?>
            <div class="card border-0 shadow-sm p-5 text-center bg-white rounded-4 animate__animated animate__pulse glass">
                <div class="bg-light bg-opacity-50 p-4 rounded-circle d-inline-block mx-auto mb-4">
                    <i class="bi bi-building-add display-1 text-primary"></i>
                </div>
                <h3 class="fw-bold text-dark">مرحباً بك في نظام الإعمار</h3>
                <p class="text-muted fs-5 mb-4">ابدأ باختيار مشروع من القائمة أعلاه أو أضف مشروعاً جديداً للبدء في إدارة أعمالك</p>
                <div class="d-flex justify-content-center gap-2">
                    <button class="btn btn-primary px-4 py-2 fw-bold" data-bs-toggle="modal" data-bs-target="#addP">
                        <i class="bi bi-plus-lg me-2"></i>إضافة مشروعك الأول
                    </button>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <?php include 'templates/modals.php'; ?>
<?php endif; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/script.js?v=1.2"></script>
</body>
</html>
