<div class="tab-pane fade" id="t-tools">
    <div class="row g-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm glass h-100">
                <div class="card-header bg-transparent border-0 pt-4 px-4">
                    <h5 class="fw-bold mb-0 text-primary"><i class="bi bi-calculator me-2"></i>محول وحدات القياس الهندسية</h5>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="small fw-bold text-muted mb-1">المقدار</label>
                            <input type="number" id="conv_val" class="form-control" placeholder="أدخل الرقم هنا">
                        </div>
                        <div class="col-6">
                            <label class="small fw-bold text-muted mb-1">من</label>
                            <select id="conv_from" class="form-select">
                                <option value="m">متر (طولي)</option>
                                <option value="cm">سنتيمتر</option>
                                <option value="m2">متر مربع (مساحة)</option>
                                <option value="m3">متر مكعب (حجم)</option>
                                <option value="ft">قدم</option>
                                <option value="in">بوصة</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="small fw-bold text-muted mb-1">إلى</label>
                            <select id="conv_to" class="form-select">
                                <option value="m">متر (طولي)</option>
                                <option value="cm">سنتيمتر</option>
                                <option value="m2">متر مربع</option>
                                <option value="m3">متر مكعب</option>
                                <option value="ft">قدم</option>
                                <option value="in">بوصة</option>
                            </select>
                        </div>
                        <div class="col-12 mt-4">
                            <div class="p-3 bg-primary bg-opacity-10 rounded text-center">
                                <h4 class="fw-bold text-primary mb-0" id="conv_res">0.00</h4>
                                <small class="text-muted">النتيجة المحسوبة</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card border-0 shadow-sm glass h-100">
                <div class="card-header bg-transparent border-0 pt-4 px-4">
                    <h5 class="fw-bold mb-0 text-primary"><i class="bi bi-box-seam me-2"></i>حاسبة تقدير الكميات البسيطة</h5>
                </div>
                <div class="card-body p-4 text-center py-5">
                    <i class="bi bi-cone-striped display-1 text-muted opacity-25"></i>
                    <p class="mt-3 text-muted">سيتم إضافة حاسبة كميات الخرسانة والدهانات في التحديث القادم.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('conv_val').addEventListener('input', updateConv);
document.getElementById('conv_from').addEventListener('change', updateConv);
document.getElementById('conv_to').addEventListener('change', updateConv);

function updateConv() {
    const val = parseFloat(document.getElementById('conv_val').value) || 0;
    const from = document.getElementById('conv_from').value;
    const to = document.getElementById('conv_to').value;
    let res = 0;

    // Basic conversion logic (Mock for common units)
    if (from === to) res = val;
    else if (from === 'm' && to === 'cm') res = val * 100;
    else if (from === 'cm' && to === 'm') res = val / 100;
    else if (from === 'm' && to === 'ft') res = val * 3.28084;
    else if (from === 'ft' && to === 'm') res = val / 3.28084;
    else res = "وحدات غير متوافقة";

    document.getElementById('conv_res').textContent = typeof res === 'number' ? res.toFixed(2) : res;
}
</script>
