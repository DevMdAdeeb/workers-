<div class="tab-pane fade" id="t-gallery">
    <div class="card border-0 shadow-sm mb-4 glass">
        <div class="card-header bg-transparent border-0 pt-4 px-4">
            <h5 class="fw-bold mb-0 text-primary"><i class="bi bi-images me-2"></i>معرض صور ومرفقات المشروع</h5>
            <p class="text-muted small mb-0">كافة الصور والفواتير والمرفقات التي تم رفعها للنظام</p>
        </div>
        <div class="card-body p-4">
            <div class="row g-3">
                <?php
                // Get all materials images
                $imgs = $pdo->prepare("SELECT invoice_image as file, item_name as name, purchase_date as date FROM materials WHERE project_id = ? AND invoice_image IS NOT NULL AND invoice_image != ''");
                $imgs->execute([$pid]);
                $all_files = $imgs->fetchAll();

                // Get all material attachments
                $atts = $pdo->prepare("SELECT ma.file_path as file, m.item_name as name, m.purchase_date as date FROM material_attachments ma JOIN materials m ON ma.material_id = m.id WHERE m.project_id = ?");
                $atts->execute([$pid]);
                $all_files = array_merge($all_files, $atts->fetchAll());

                foreach($all_files as $f):
                    $ext = strtolower(pathinfo($f['file'], PATHINFO_EXTENSION));
                ?>
                <div class="col-6 col-md-3 col-lg-2">
                    <div class="card h-100 border-0 shadow-sm overflow-hidden">
                        <?php if(in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])): ?>
                            <a href="uploads/<?= $f['file'] ?>" target="_blank">
                                <img src="uploads/<?= $f['file'] ?>" class="card-img-top" style="height: 150px; object-fit: cover;" alt="<?= h($f['name']) ?>">
                            </a>
                        <?php else: ?>
                            <div class="bg-light d-flex align-items-center justify-content-center" style="height: 150px;">
                                <i class="bi bi-file-earmark-pdf text-danger display-4"></i>
                            </div>
                        <?php endif; ?>
                        <div class="card-body p-2 text-center">
                            <small class="fw-bold d-block text-truncate"><?= h($f['name']) ?></small>
                            <small class="text-muted" style="font-size: 0.7rem;"><?= $f['date'] ?></small>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>

                <?php if(empty($all_files)): ?>
                    <div class="col-12 text-center py-5">
                        <i class="bi bi-image text-muted display-1 opacity-25"></i>
                        <p class="text-muted mt-3">لا توجد صور أو مرفقات مرفوعة لهذا المشروع بعد.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
