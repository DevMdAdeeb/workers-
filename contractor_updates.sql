-- New tables for Contractor-Specific Features

-- 1. Daily Site Journal (سجل اليوميات)
CREATE TABLE IF NOT EXISTS daily_journal (
    id INT AUTO_INCREMENT PRIMARY KEY,
    project_id INT NOT NULL,
    entry_date DATE NOT NULL,
    weather VARCHAR(100) DEFAULT NULL,
    progress_notes TEXT DEFAULT NULL,
    issues_encountered TEXT DEFAULT NULL,
    material_deliveries TEXT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. Attendance Tracking (تحضير العمال)
CREATE TABLE IF NOT EXISTS attendance (
    id INT AUTO_INCREMENT PRIMARY KEY,
    worker_id INT NOT NULL,
    project_id INT NOT NULL,
    attendance_date DATE NOT NULL,
    status ENUM('present', 'absent', 'half_day', 'on_leave') DEFAULT 'present',
    notes TEXT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (worker_id) REFERENCES workers(id) ON DELETE CASCADE,
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE,
    UNIQUE KEY unique_attendance (worker_id, project_id, attendance_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
