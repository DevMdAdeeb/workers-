<?php
// includes/project_logic.php
if ($uid) {
    if (isset($_POST['add_project'])) {
        $pdo->prepare("INSERT INTO projects (user_id, project_name) VALUES (?, ?)")->execute([$uid, $_POST['project_name']]);
        logActivity($pdo, $uid, $pdo->lastInsertId(), "إضافة مشروع", "المشروع: " . $_POST['project_name']);
    }

    if ($pid) {
        if (isset($_POST['update_budget'])) {
            $pdo->prepare("UPDATE projects SET budget=? WHERE id=?")->execute([$_POST['budget'], $pid]);
            logActivity($pdo, $uid, $pid, "تحديث ميزانية", "الميزانية الجديدة: " . $_POST['budget']);
        }

        if (isset($_POST['add_cat'])) {
            $pdo->prepare("INSERT INTO categories (project_id, cat_name) VALUES (?, ?)")->execute([$pid, $_POST['cat_name']]);
        }

        if (isset($_POST['add_mat'])) {
            $uploadedFiles = uploadFiles($_FILES['inv']);
            $img = !empty($uploadedFiles) ? $uploadedFiles[0] : null;

            $is_paid = $_POST['paid'];
            $supplier_id = ($is_paid == '0' && !empty($_POST['supplier_id'])) ? $_POST['supplier_id'] : null;

            $stmt = $pdo->prepare("INSERT INTO materials (project_id, item_name, price, purchase_date, category, is_paid, invoice_image, supplier_id) VALUES (?,?,?,?,?,?,?,?)");
            $stmt->execute([$pid, $_POST['name'], $_POST['price'], $_POST['date'], $_POST['cat'], $is_paid, $img, $supplier_id]);
            $material_id = $pdo->lastInsertId();

            // حفظ باقي المرفقات في الجدول الجديد
            if (count($uploadedFiles) > 1) {
                $stmt_att = $pdo->prepare("INSERT INTO material_attachments (material_id, file_path) VALUES (?, ?)");
                for ($i = 1; $i < count($uploadedFiles); $i++) {
                    $stmt_att->execute([$material_id, $uploadedFiles[$i]]);
                }
            }

            logActivity($pdo, $uid, $pid, "إضافة مادة", "المادة: " . $_POST['name'] . " - السعر: " . $_POST['price']);
        }

        if (isset($_POST['add_task']) && $pid) {
            $pdo->prepare("INSERT INTO tasks (project_id, task_text) VALUES (?, ?)")->execute([$pid, $_POST['task_text']]);
        }

        if (isset($_GET['toggle_task'])) {
            $stmt = $pdo->prepare("UPDATE tasks SET is_done = NOT is_done WHERE id = ? AND project_id = ?");
            $stmt->execute([$_GET['toggle_task'], $pid]);
            header("Location: index.php"); exit();
        }

        if (isset($_GET['del_task'])) {
            $pdo->prepare("DELETE FROM tasks WHERE id = ? AND project_id = ?")->execute([$_GET['del_task'], $pid]);
            header("Location: index.php"); exit();
        }

        if (isset($_POST['add_worker'])) {
            $pdo->prepare("INSERT INTO workers (project_id, worker_name, task_desc, total_amount, paid_amount, entry_date, phone, address) VALUES (?,?,?,?,?,?,?,?)")
                ->execute([$pid, $_POST['w_name'], $_POST['task'], $_POST['total'], $_POST['sufa'], $_POST['w_date'], $_POST['phone'], $_POST['address']]);
            logActivity($pdo, $uid, $pid, "إضافة عامل", "العامل: " . $_POST['w_name']);
        }

        if (isset($_POST['add_pay'])) {
            $pdo->prepare("INSERT INTO worker_payments (worker_id, amount, payment_date) VALUES (?,?,?)")
                ->execute([$_POST['worker_id'], $_POST['amt'], date('Y-m-d')]);
            logActivity($pdo, $uid, $pid, "صرف سلفة لعامل", "المبلغ: " . $_POST['amt']);
        }

        if (isset($_POST['add_supplier'])) {
            $pdo->prepare("INSERT INTO suppliers (project_id, supplier_name, contact_info, address) VALUES (?, ?, ?, ?)")
                ->execute([$pid, $_POST['s_name'], $_POST['s_info'], $_POST['s_address']]);
            logActivity($pdo, $uid, $pid, "إضافة مورد", "المورد: " . $_POST['s_name']);
        }

        if (isset($_POST['add_s_payment'])) {
            $pdo->prepare("INSERT INTO supplier_payments (supplier_id, amount, payment_date) VALUES (?, ?, ?)")
                ->execute([$_POST['supplier_id'], $_POST['amount'], $_POST['p_date']]);
            logActivity($pdo, $uid, $pid, "دفع دفعة لمورد", "المبلغ: " . $_POST['amount']);
        }
    }

    if (isset($_GET['del_sup']) && $pid) {
        $pdo->prepare("DELETE FROM suppliers WHERE id = ? AND project_id = ?")->execute([$_GET['del_sup'], $pid]);
        header("Location: index.php"); exit();
    }
    if (isset($_GET['del_mat']) && $pid) {
        $pdo->prepare("DELETE FROM materials WHERE id=? AND project_id=?")->execute([$_GET['del_mat'], $pid]);
        header("Location: index.php"); exit();
    }
    if (isset($_GET['del_work']) && $pid) {
        $pdo->prepare("DELETE FROM workers WHERE id=? AND project_id=?")->execute([$_GET['del_work'], $pid]);
        header("Location: index.php"); exit();
    }
}
?>
