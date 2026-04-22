<?php
function logActivity($pdo, $user_id, $project_id, $action, $details = null) {
    try {
        $stmt = $pdo->prepare("INSERT INTO activity_log (user_id, project_id, action, details) VALUES (?, ?, ?, ?)");
        $stmt->execute([$user_id, $project_id, $action, $details]);
    } catch (PDOException $e) {
        // فشل تسجيل النشاط لا يجب أن يعطل البرنامج الأساسي
    }
}

function formatCurrency($amount) {
    return number_format($amount, 0, '.', ',') . " ر.ي";
}

function uploadFiles($files, $dest = 'uploads/') {
    $uploadedNames = [];
    if (!is_dir($dest)) mkdir($dest, 0777, true);

    foreach ($files['name'] as $key => $val) {
        if (!empty($val)) {
            $ext = pathinfo($val, PATHINFO_EXTENSION);
            $newName = time() . "_" . uniqid() . "." . $ext;
            if (move_uploaded_file($files['tmp_name'][$key], $dest . $newName)) {
                $uploadedNames[] = $newName;
            }
        }
    }
    return $uploadedNames;
}
?>
