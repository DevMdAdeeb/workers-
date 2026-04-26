<div class="tab-pane fade" id="t-mats">
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white border-0 pt-4 px-4">
            <h5 class="fw-bold mb-0 text-primary"><i class="bi bi-cart-plus me-2"></i>إضافة مشتريات جديدة</h5>
            <p class="text-muted small mb-0">قم بتسجيل المواد والمعدات التي تم شراؤها للمشروع</p>
        </div>
        <div class="card-body p-4">
            <form method="POST" enctype="multipart/form-data" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label small fw-bold text-muted">اسم المادة</label>
                    <input name="name" class="form-control bg-light border-0 py-2" placeholder="مثلاً: حديد، أسمنت..." required>
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold text-muted">السعر (ر.ي)</label>
                    <input type="number" name="price" class="form-control bg-light border-0 py-2" placeholder="0" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-bold text-muted">التصنيف</label>
                    <div class="input-group">
                        <select name="cat" class="form-select bg-light border-0 py-2">
                            <option value="">اختر التصنيف</option>
                            <?php $cs=$pdo->query("SELECT * FROM categories WHERE project_id=$pid"); while($c=$cs->fetch()) echo "<option>{$c['cat_name']}</option>"; ?>
                        </select>
                        <button type="button" class="btn btn-outline-secondary border-0 bg-light" data-bs-toggle="collapse" data-bs-target="#newCatForm" title="إضافة تصنيف جديد">
                            <i class="bi bi-plus-circle"></i>
                        </button>
                    </div>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-bold text-muted">تاريخ الشراء</label>
                    <input type="date" name="date" class="form-control bg-light border-0 py-2" value="<?= date('Y-m-d') ?>">
                </div>

                <div class="collapse col-12" id="newCatForm">
                    <div class="bg-light p-3 rounded-3 d-flex gap-2 align-items-center">
                        <input name="cat_name" class="form-control form-control-sm border-0" placeholder="اسم التصنيف الجديد">
                        <button name="add_cat" class="btn btn-sm btn-secondary px-3">إضافة</button>
                    </div>
                </div>

                <div class="col-md-3">
                    <label class="form-label small fw-bold text-muted">طريقة الدفع</label>
                    <select name="paid" class="form-select bg-light border-0 py-2" onchange="document.getElementById('supSelect').style.display = (this.value == '0' ? 'block' : 'none')">
                        <option value="1">نقداً (كاش)</option>
                        <option value="0">آجل (دين)</option>
                    </select>
                </div>
                <div class="col-md-5" id="supSelect" style="display: none;">
                    <label class="form-label small fw-bold text-muted">المورد / التاجر</label>
                    <select name="supplier_id" class="form-select bg-light border-0 py-2">
                        <option value="">-- اختر التاجر --</option>
                        <?php
                        $sups_list = $pdo->prepare("SELECT id, supplier_name FROM suppliers WHERE project_id = ?");
                        $sups_list->execute([$pid]);
                        while($sl = $sups_list->fetch()) echo "<option value='{$sl['id']}'>{$sl['supplier_name']}</option>";
                        ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-bold text-muted">المرفقات (فواتير/صور)</label>
                    <input type="file" name="inv[]" class="form-control bg-light border-0 py-2" multiple>
                </div>

                <div class="col-12 mt-4 text-end">
                    <button name="add_mat" class="btn btn-primary px-5 py-2 shadow-sm"><i class="bi bi-check2-circle me-1"></i>حفظ المشتريات</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-0 pt-4 px-4 d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <h5 class="fw-bold mb-0 text-dark">سجل المشتريات</h5>
                <p class="text-muted small mb-0">عرض كافة المواد التي تم توريدها للموقع</p>
            </div>
            <div class="search-box">
                <i class="bi bi-search"></i>
                <input type="text" class="form-control table-search border-0 bg-light" data-target="#materialsTable" placeholder="بحث سريع...">
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="materialsTable">
                    <thead>
                        <tr>
                            <th class="ps-4">المادة</th>
                            <th>التصنيف</th>
                            <th>السعر</th>
                            <th>التاريخ</th>
                            <th>الحالة</th>
                            <th class="text-center pe-4">إجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $ms=$pdo->query("SELECT * FROM materials WHERE project_id=$pid ORDER BY id DESC");
                        while($m=$ms->fetch()): ?>
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center">
                                    <span class="fw-bold text-dark"><?= htmlspecialchars($m['item_name']) ?></span>
                                    <div class="ms-2">
                                        <?php if($m['invoice_image']): ?>
                                            <a href="uploads/<?= $m['invoice_image'] ?>" target="_blank" class="btn btn-sm btn-light text-primary py-0 px-1" title="عرض الفاتورة"><i class="bi bi-image"></i></a>
                                        <?php endif; ?>
                                        <?php
                                        $atts = $pdo->prepare("SELECT file_path FROM material_attachments WHERE material_id = ?");
                                        $atts->execute([$m['id']]);
                                        while($att = $atts->fetch()):
                                        ?>
                                            <a href="uploads/<?= $att['file_path'] ?>" target="_blank" class="btn btn-sm btn-light text-secondary py-0 px-1" title="مرفق"><i class="bi bi-paperclip"></i></a>
                                        <?php endwhile; ?>
                                    </div>
                                </div>
                            </td>
                            <td><span class="badge bg-secondary bg-opacity-10 text-secondary"><?= htmlspecialchars($m['category'] ?: 'عام') ?></span></td>
                            <td><span class="fw-bold text-primary"><?= number_format($m['price']) ?></span> <small class="text-muted">ر.ي</small></td>
                            <td><span class="small text-muted"><?= $m['purchase_date'] ?></span></td>
                            <td>
                                <?php if($m['is_paid']): ?>
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-3">نقداً</span>
                                <?php else: ?>
                                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-3">آجل</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center pe-4">
                                <a href="?del_mat=<?= $m['id'] ?>" class="btn btn-sm btn-outline-danger border-0 rounded-circle" onclick="return confirm('هل أنت متأكد من حذف هذه المادة؟')"><i class="bi bi-trash3"></i></a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
