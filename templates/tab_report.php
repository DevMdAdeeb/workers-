<div class="tab-pane fade" id="t-rep">
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-custom-card border-0 pt-4 px-4">
            <h5 class="fw-bold mb-0 text-custom-accent"><i class="bi bi-file-earmark-bar-graph me-2"></i>مركز التقارير والاستعلامات</h5>
            <p class="text-custom-muted small mb-0">استخراج تقارير تفصيلية شاملة للمشروع أو لعمال محددين</p>
        </div>
        <div class="card-body p-4">
            <div class="row g-4">
                <!-- تقرير المشروع الشامل -->
                <div class="col-md-6">
                    <div class="card h-100 border-0 shadow-sm bg-primary bg-opacity-10 p-4 border-start border-custom-accent border-4">
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-primary p-3 rounded-3 text-white me-3">
                                <i class="bi bi-building fs-3"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold mb-0">التقرير الختامي للمشروع</h5>
                                <p class="text-custom-muted small mb-0">كشف حساب شامل لكافة المصروفات والديون</p>
                            </div>
                        </div>
                        <a href="report.php?pid=<?= $pid ?>" target="_blank" class="btn btn-custom-accent w-100 py-3 fw-bold mt-auto">
                            <i class="bi bi-printer me-2"></i>استعراض وطباعة التقرير الشامل
                        </a>
                    </div>
                </div>

                <!-- استعلام عن عامل محدد -->
                <div class="col-md-6">
                    <div class="card h-100 border-0 shadow-sm p-4 border-start border-info border-4">
                        <h5 class="fw-bold mb-3"><i class="bi bi-person-search me-2"></i>استعلام عن مستحقات عامل</h5>
                        <form method="POST" class="row g-2">
                            <div class="col-12">
                                <label class="small fw-bold text-custom-muted mb-1">اسم العامل</label>
                                <select name="worker_name" class="form-select border-0 bg-custom-glass py-2" required>
                                    <option value="">-- اختر العامل --</option>
                                    <?php
                                    $wns = $pdo->prepare("SELECT DISTINCT worker_name FROM workers WHERE project_id = ?");
                                    $wns->execute([$pid]);
                                    while($wn = $wns->fetch()) echo "<option>{$wn['worker_name']}</option>";
                                    ?>
                                </select>
                            </div>
                            <div class="col-6">
                                <label class="small fw-bold text-custom-muted mb-1">من تاريخ</label>
                                <input type="date" name="date_from" class="form-control border-0 bg-custom-glass" value="<?= date('Y-m-01') ?>">
                            </div>
                            <div class="col-6">
                                <label class="small fw-bold text-custom-muted mb-1">إلى تاريخ</label>
                                <input type="date" name="date_to" class="form-control border-0 bg-custom-glass" value="<?= date('Y-m-d') ?>">
                            </div>
                            <div class="col-12 mt-3">
                                <button name="get_worker_report" class="btn btn-info text-white w-100 py-2 fw-bold">
                                    <i class="bi bi-search me-2"></i>بحث واستخراج
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <?php if (isset($report_data)): ?>
            <div class="mt-5 card border-0 shadow-lg overflow-hidden animate__animated animate__fadeIn">
                <div class="card-header bg-dark text-white p-4 border-0 d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-0 fw-bold">نتيجة الاستعلام: <?= htmlspecialchars($report_data['name']) ?></h5>
                        <span class="small opacity-75">للفترة من <?= $report_data['from'] ?> إلى <?= $report_data['to'] ?></span>
                    </div>
                    <i class="bi bi-person-check display-6 opacity-25"></i>
                </div>
                <div class="card-body p-0">
                    <div class="row g-0">
                        <div class="col-md-4 border-end">
                            <div class="p-4 text-center">
                                <span class="text-custom-muted small d-block mb-1">إجمالي المستحقات</span>
                                <h3 class="fw-bold text-custom-main"><?= number_format($report_data['total']) ?></h3>
                            </div>
                        </div>
                        <div class="col-md-4 border-end">
                            <div class="p-4 text-center">
                                <span class="text-custom-muted small d-block mb-1">إجمالي المدفوع</span>
                                <h3 class="fw-bold text-success"><?= number_format($report_data['paid']) ?></h3>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-4 text-center">
                                <span class="text-custom-muted small d-block mb-1">المبلغ المتبقي</span>
                                <h3 class="fw-bold text-danger"><?= number_format($report_data['rem']) ?></h3>
                            </div>
                        </div>
                    </div>
                    <div class="p-3 bg-custom-glass text-center border-top">
                        <p class="text-custom-muted small mb-0">هذا الملخص بناءً على العمليات المسجلة ضمن التواريخ المحددة أعلاه فقط.</p>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <?php if (isset($error_msg)): ?>
                <div class="alert alert-danger border-0 mt-4 shadow-sm"><?= $error_msg ?></div>
            <?php endif; ?>
        </div>
    </div>
</div>
