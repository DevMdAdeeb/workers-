<?php
// includes/project_logic.php
if ($uid) {
    if (isset($_POST['add_project'])) {
        $pdo->prepare("INSERT INTO projects (user_id, project_name) VALUES (?, ?)")->execute([$uid, $_POST['project_name']]);
        logActivity($pdo, $uid, $pdo->lastInsertId(), "إضافة مشروع", "المشروع: " . $_POST['project_name']);
        header("Location: index.php"); exit();
    }

    if ($pid) {
        // Milestones
        if (isset($_POST['add_milestone'])) {
            $pdo->prepare("INSERT INTO milestones (project_id, milestone_name, target_date, status) VALUES (?, ?, ?, ?)")
                ->execute([$pid, $_POST['m_name'], $_POST['m_date'], $_POST['m_status']]);
            header("Location: index.php"); exit();
        }
        if (isset($_GET['del_ms'])) {
            $pdo->prepare("DELETE FROM milestones WHERE id=? AND project_id=?")->execute([$_GET['del_ms'], $pid]);
            header("Location: index.php"); exit();
        }

        // Equipment
        if (isset($_POST['add_equipment'])) {
            $pdo->prepare("INSERT INTO equipment (project_id, equipment_name, worker_id, received_date) VALUES (?, ?, ?, ?)")
                ->execute([$pid, $_POST['e_name'], $_POST['worker_id'] ?: null, $_POST['e_date']]);
            header("Location: index.php"); exit();
        }
        if (isset($_GET['del_eq'])) {
            $pdo->prepare("DELETE FROM equipment WHERE id=? AND project_id=?")->execute([$_GET['del_eq'], $pid]);
            header("Location: index.php"); exit();
        }

        // New Features Logic
        if (isset($_POST['add_journal'])) {
            $stmt = $pdo->prepare("INSERT INTO daily_journal (project_id, entry_date, weather, progress_notes, issues_encountered, material_deliveries) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$pid, $_POST['j_date'], $_POST['weather'], $_POST['progress'], $_POST['issues'], $_POST['deliveries']]);
            logActivity($pdo, $uid, $pid, "إضافة سجل يومي", "التاريخ: " . $_POST['j_date']);
            header("Location: index.php"); exit();
        }

        if (isset($_POST['save_attendance'])) {
            $att_date = $_POST['att_date'];
            foreach ($_POST['status'] as $worker_id => $status) {
                $notes = $_POST['notes'][$worker_id] ?? null;
                $stmt = $pdo->prepare("INSERT INTO attendance (worker_id, project_id, attendance_date, status, notes) VALUES (?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE status = VALUES(status), notes = VALUES(notes)");
                $stmt->execute([$worker_id, $pid, $att_date, $status, $notes]);
            }
            logActivity($pdo, $uid, $pid, "تحديث كشف التحضير", "التاريخ: " . $att_date);
            header("Location: index.php?att_date=" . $att_date); exit();
        }

        // Existing Logic
        if (isset($_POST['update_budget'])) {
            $pdo->prepare("UPDATE projects SET budget=? WHERE id=?")->execute([$_POST['budget'], $pid]);
            logActivity($pdo, $uid, $pid, "تحديث ميزانية", "الميزانية الجديدة: " . $_POST['budget']);
            header("Location: index.php"); exit();
        }

        if (isset($_POST['add_cat'])) {
            $pdo->prepare("INSERT INTO categories (project_id, cat_name) VALUES (?, ?)")->execute([$pid, $_POST['cat_name']]);
            header("Location: index.php"); exit();
        }

        if (isset($_POST['add_mat'])) {
            $uploadedFiles = uploadFiles($_FILES['inv']);
            $img = !empty($uploadedFiles) ? $uploadedFiles[0] : null;

            $is_paid = $_POST['paid'];
            $supplier_id = ($is_paid == '0' && !empty($_POST['supplier_id'])) ? $_POST['supplier_id'] : null;

            $stmt = $pdo->prepare("INSERT INTO materials (project_id, item_name, price, purchase_date, category, is_paid, invoice_image, supplier_id) VALUES (?,?,?,?,?,?,?,?)");
            $stmt->execute([$pid, $_POST['name'], $_POST['price'], $_POST['date'], $_POST['cat'], $is_paid, $img, $supplier_id]);
            $material_id = $pdo->lastInsertId();

            if (count($uploadedFiles) > 1) {
                $stmt_att = $pdo->prepare("INSERT INTO material_attachments (material_id, file_path) VALUES (?, ?)");
                for ($i = 1; $i < count($uploadedFiles); $i++) {
                    $stmt_att->execute([$material_id, $uploadedFiles[$i]]);
                }
            }

            logActivity($pdo, $uid, $pid, "إضافة مادة", "المادة: " . $_POST['name'] . " - السعر: " . $_POST['price']);
            header("Location: index.php"); exit();
        }

        if (isset($_POST['add_task']) && $pid) {
            $pdo->prepare("INSERT INTO tasks (project_id, task_text) VALUES (?, ?)")->execute([$pid, $_POST['task_text']]);
            header("Location: index.php"); exit();
        }

        if (isset($_POST['add_worker'])) {
            $pdo->prepare("INSERT INTO workers (project_id, worker_name, task_desc, total_amount, paid_amount, entry_date, phone, address, work_period) VALUES (?,?,?,?,?,?,?,?,?)")
                ->execute([$pid, $_POST['w_name'], $_POST['task'], $_POST['total'], $_POST['sufa'], $_POST['w_date'], $_POST['phone'], $_POST['address'], $_POST['work_period'] ?? 'full']);
            logActivity($pdo, $uid, $pid, "إضافة عامل", "العامل: " . $_POST['w_name']);
            header("Location: index.php"); exit();
        }

        if (isset($_POST['add_pay'])) {
            $pdo->prepare("INSERT INTO worker_payments (worker_id, amount, payment_date) VALUES (?,?,?)")
                ->execute([$_POST['worker_id'], $_POST['amt'], $_POST['p_date'] ?? date('Y-m-d')]);
            logActivity($pdo, $uid, $pid, "صرف سلفة لعامل", "المبلغ: " . $_POST['amt']);
            header("Location: index.php"); exit();
        }

        if (isset($_POST['del_pay'])) {
            $pdo->prepare("DELETE FROM worker_payments WHERE id=?")->execute([$_POST['pay_id']]);
            header("Location: index.php"); exit();
        }

        if (isset($_POST['edit_pay'])) {
            $pdo->prepare("UPDATE worker_payments SET amount=?, payment_date=? WHERE id=?")
                ->execute([$_POST['amt'], $_POST['p_date'], $_POST['pay_id']]);
            header("Location: index.php"); exit();
        }

        if (isset($_POST['add_supplier'])) {
            $pdo->prepare("INSERT INTO suppliers (project_id, supplier_name, contact_info, address) VALUES (?, ?, ?, ?)")
                ->execute([$pid, $_POST['s_name'], $_POST['s_info'], $_POST['s_address']]);
            logActivity($pdo, $uid, $pid, "إضافة مورد", "المورد: " . $_POST['s_name']);
            header("Location: index.php"); exit();
        }

        if (isset($_POST['add_s_payment'])) {
            $pdo->prepare("INSERT INTO supplier_payments (supplier_id, amount, payment_date) VALUES (?, ?, ?)")
                ->execute([$_POST['supplier_id'], $_POST['amount'], $_POST['p_date']]);
            logActivity($pdo, $uid, $pid, "دفع دفعة لمورد", "المبلغ: " . $_POST['amount']);
            header("Location: index.php"); exit();
        }

        // GET actions
        if (isset($_GET['del_mat'])) {
            $pdo->prepare("DELETE FROM materials WHERE id=? AND project_id=?")->execute([$_GET['del_mat'], $pid]);
            header("Location: index.php"); exit();
        }

        if (isset($_GET['del_work'])) {
            $pdo->prepare("DELETE FROM workers WHERE id=? AND project_id=?")->execute([$_GET['del_work'], $pid]);
            header("Location: index.php"); exit();
        }

        if (isset($_GET['del_sup'])) {
            $pdo->prepare("DELETE FROM suppliers WHERE id=? AND project_id=?")->execute([$_GET['del_sup'], $pid]);
            header("Location: index.php"); exit();
        }

        if (isset($_GET['del_task'])) {
            $pdo->prepare("DELETE FROM tasks WHERE id=? AND project_id=?")->execute([$_GET['del_task'], $pid]);
            header("Location: index.php"); exit();
        }

        if (isset($_GET['toggle_task'])) {
            $pdo->prepare("UPDATE tasks SET is_done = NOT is_done WHERE id=? AND project_id=?")->execute([$_GET['toggle_task'], $pid]);
            header("Location: index.php"); exit();
        }
    }
}
?>
