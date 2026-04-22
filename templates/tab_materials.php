<div class="tab-pane fade" id="t-mats">
    <div class="card p-3 border-0 mb-4">
        <h6 class="fw-bold mb-3"><i class="bi bi-plus-circle me-1"></i> إضافة مادة جديدة</h6>
        <form method="POST" enctype="multipart/form-data" class="row g-3">
            <div class="col-md-3"><input name="name" class="form-control" placeholder="اسم المادة" required></div>
            <div class="col-md-2"><input type="number" name="price" class="form-control" placeholder="السعر" required></div>
            <div class="col-md-2">
                <select name="cat" class="form-select">
                    <option value="">التصنيف</option>
                    <?php $cs=$pdo->query("SELECT * FROM categories WHERE project_id=$pid"); while($c=$cs->fetch()) echo "<option>{$c['cat_name']}</option>"; ?>
                </select>
            </div>
            <div class="col-md-2">
                <select name="paid" class="form-select"><option value="1">كاش</option><option value="0">دين (على الحساب)</option></select>
            </div>
            <div class="col-md-3">
                <select name="supplier_id" class="form-select shadow-sm">
                    <option value="">اختر التاجر (في حال الدين)</option>
                    <?php
                    $sups_list = $pdo->prepare("SELECT id, supplier_name FROM suppliers WHERE project_id = ?");
                    $sups_list->execute([$pid]);
                    while($sl = $sups_list->fetch()) echo "<option value='{$sl['id']}'>{$sl['supplier_name']}</option>";
                    ?>
                </select>
            </div>
            <div class="col-md-3"><input type="date" name="date" class="form-control" value="<?= date('Y-m-d') ?>"></div>
            <div class="col-md-3"><input type="file" name="inv[]" class="form-control" multiple title="رفع فواتير (يمكنك اختيار أكثر من ملف)"></div>
            <div class="col-md-6"><button name="add_mat" class="btn btn-success w-100"><i class="bi bi-plus-lg me-1"></i> إضافة المشتريات</button></div>
        </form>
        <hr>
        <form method="POST" class="d-flex gap-2">
            <input name="cat_name" class="form-control form-control-sm" style="max-width: 200px;" placeholder="إضافة تصنيف جديد">
            <button name="add_cat" class="btn btn-sm btn-secondary"><i class="bi bi-folder-plus"></i></button>
        </form>
    </div>

    <div class="card border-0 p-3">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="fw-bold m-0">سجل المواد المشراة</h6>
            <div class="search-box">
                <i class="bi bi-search"></i>
                <input type="text" class="form-control form-control-sm table-search" data-target="#materialsTable" placeholder="بحث في المواد...">
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle" id="materialsTable">
                <thead class="table-light">
                    <tr>
                        <th>المادة</th>
                        <th>التصنيف</th>
                        <th>السعر</th>
                        <th>التاريخ</th>
                        <th>الحالة</th>
                        <th class="text-center">إجراء</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $ms=$pdo->query("SELECT * FROM materials WHERE project_id=$pid ORDER BY id DESC");
                    while($m=$ms->fetch()): ?>
                    <tr>
                        <td>
                            <span class="fw-bold"><?= htmlspecialchars($m['item_name']) ?></span>
                            <?php if($m['invoice_image']): ?>
                                <a href="uploads/<?= $m['invoice_image'] ?>" target="_blank" class="ms-1 text-primary" title="عرض الفاتورة"><i class="bi bi-file-earmark-image"></i></a>
                            <?php endif; ?>
                            <?php
                            $atts = $pdo->prepare("SELECT file_path FROM material_attachments WHERE material_id = ?");
                            $atts->execute([$m['id']]);
                            while($att = $atts->fetch()):
                            ?>
                                <a href="uploads/<?= $att['file_path'] ?>" target="_blank" class="ms-1 text-secondary" title="مرفق إضافي"><i class="bi bi-paperclip"></i></a>
                            <?php endwhile; ?>
                        </td>
                        <td><span class="badge badge-category"><?= htmlspecialchars($m['category']) ?></span></td>
                        <td><span class="fw-bold"><?= number_format($m['price']) ?></span></td>
                        <td><small><?= $m['purchase_date'] ?></small></td>
                        <td>
                            <?php if($m['is_paid']): ?>
                                <span class="badge bg-success-subtle text-success border-success-subtle px-2">كاش</span>
                            <?php else: ?>
                                <span class="badge bg-danger-subtle text-danger border-danger-subtle px-2">دين</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-center">
                            <a href="?del_mat=<?= $m['id'] ?>" class="btn btn-sm btn-outline-danger border-0" onclick="return confirm('هل أنت متأكد من حذف هذه المادة؟')"><i class="bi bi-trash3"></i></a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
