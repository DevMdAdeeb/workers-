-- تحديث قاعدة البيانات للميزات الجديدة

-- إضافة حقل فترة العمل للعمال
ALTER TABLE workers ADD COLUMN work_period ENUM('full', 'half') DEFAULT 'full';

-- (ملاحظة: الجداول الأخرى وسجل النشاطات تم إنشاؤها سابقاً)
