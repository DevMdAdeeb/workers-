-- تحديث هيكل قاعدة البيانات لتطوير المشروع

-- إضافة حقول للعمال
ALTER TABLE workers ADD COLUMN phone VARCHAR(20) DEFAULT NULL;
ALTER TABLE workers ADD COLUMN address TEXT DEFAULT NULL;

-- إضافة حقول للموردين
ALTER TABLE suppliers ADD COLUMN address TEXT DEFAULT NULL;
-- (ملاحظة: حقل contact_info موجود مسبقاً ويمكن استخدامه لرقم الهاتف)

-- إنشاء جدول سجل النشاطات
CREATE TABLE IF NOT EXISTS activity_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    project_id INT DEFAULT NULL,
    action VARCHAR(255) NOT NULL,
    details TEXT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- تحسينات إضافية (اختياري: إضافة فهرسة لتسريع البحث)
CREATE INDEX idx_worker_name ON workers(worker_name);
CREATE INDEX idx_material_name ON materials(item_name);
CREATE INDEX idx_purchase_date ON materials(purchase_date);

-- جدول المرفقات لدعم رفع ملفات متعددة لكل مادة
CREATE TABLE IF NOT EXISTS material_attachments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    material_id INT NOT NULL,
    file_path VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (material_id) REFERENCES materials(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
