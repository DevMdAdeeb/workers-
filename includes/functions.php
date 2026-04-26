<?php
function logActivity($pdo, $user_id, $project_id, $action, $details = null) {
    try {
        $stmt = $pdo->prepare("INSERT INTO activity_log (user_id, project_id, action, details) VALUES (?, ?, ?, ?)");
        $stmt->execute([$user_id, $project_id, $action, $details]);
    } catch (PDOException $e) {
        // Logging failure should not interrupt the main application
    }
}

function formatCurrency($amount) {
    return number_format((float)$amount, 0, '.', ',') . " ر.ي";
}

/**
 * Escapes output for HTML context
 */
function h($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

function uploadFiles($files, $dest = 'uploads/') {
    $uploadedNames = [];
    if (!is_dir($dest)) {
        mkdir($dest, 0755, true);
    }

    if (!isset($files['name']) || !is_array($files['name'])) {
        return [];
    }

    foreach ($files['name'] as $key => $val) {
        if (!empty($val) && $files['error'][$key] == 0) {
            $ext = strtolower(pathinfo($val, PATHINFO_EXTENSION));
            $allowed = ['jpg', 'jpeg', 'png', 'pdf', 'webp'];

            if (in_array($ext, $allowed)) {
                $newName = time() . "_" . bin2hex(random_bytes(8)) . "." . $ext;
                if (move_uploaded_file($files['tmp_name'][$key], $dest . $newName)) {
                    $uploadedNames[] = $newName;
                }
            }
        }
    }
    return $uploadedNames;
}
?>
