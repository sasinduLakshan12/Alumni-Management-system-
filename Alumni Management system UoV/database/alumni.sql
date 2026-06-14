-- Create database
CREATE DATABASE alumni_management;
USE alumni_management;

-- Users table with roles
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    reg_no VARCHAR(20) UNIQUE,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    graduation_year INT,
    faculty ENUM('Technological Studies', 'Applied Science', 'Business Studies'),
    current_job VARCHAR(100),
    company VARCHAR(100),
    phone VARCHAR(15),
    role ENUM('public', 'alumni', 'admin') DEFAULT 'public',
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Events table
CREATE TABLE events (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(200) NOT NULL,
    description TEXT,
    event_date DATE,
    venue VARCHAR(200),
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Announcements table
CREATE TABLE announcements (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(200) NOT NULL,
    content TEXT,
    posted_by INT,
    posted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert default admin (password: admin123)
-- Note: Generate password hash with: password_hash('admin123', PASSWORD_DEFAULT)
INSERT INTO users (reg_no, name, email, password, role, status, graduation_year, faculty) 
VALUES ('ADMIN01', 'System Admin', 'admin@alumni.edu', 
        'Admin@123', 'admin', 'approved', 2020, 'Technological Studies');

-- Insert sample alumni (approved)
INSERT INTO users (reg_no, name, email, password, role, status, graduation_year, faculty, current_job, company, phone) VALUES
('2021/ICTS/42', 'J.C. Deshan', 'deshan@email.com', 'deshan@123', 'alumni', 'approved', 2023, 'Technological Studies', 'Software Engineer', 'Tech Corp', '0112223333'),
('2021/ICTS/78', 'Bashry H.M.', 'bashry@email.com', 'bashry@123', 'alumni', 'approved', 2024, 'Applied Science', 'ML Engineer', 'Data Inc', '0114445555'),
('2021/ICTS/116', 'Wandana D.M.O.', 'wandana@email.com', 'wandana@123', 'alumni', 'approved', 2023, 'Business Studies', 'Digital Marketing Manager', 'Market Pros', '0116667777'),
('2021/ICTS/148', 'R. Jathugulan', 'jathu@email.com', 'jathu@123', 'alumni', 'approved', 2020, 'Technological Studies', 'Fullstack Developer', 'Net Solutions', '0779362339'),
('2021/ICTS/86', 'S.S.S.L.S. Shantha', 'shantha@email.com', 'shantha@123', 'alumni', 'approved', 2022, 'Applied Science', 'Research Scientist', 'Science Lab', '0110001111');

-- Insert sample pending registrations
INSERT INTO users (reg_no, name, email, password, role, status, graduation_year, faculty) VALUES
('2022/ICTS/50', 'New User 1', 'new1@email.com', 'new1@123', 'alumni', 'pending', 2024, 'Business Studies'),
('2022/ICTS/51', 'New User 2', 'new2@email.com', 'new2@123', 'alumni', 'pending', 2024, 'Technological Studies');

-- Insert sample events
INSERT INTO events (title, description, event_date, venue, created_by) VALUES
('Annual Alumni Meet 2024', 'Join us for the annual alumni gathering and networking event', '2026-01-15', 'University Main Hall', 1),
('Tech Workshop: AI & ML', 'Learn about the latest trends in Artificial Intelligence and Machine Learning', '2026-01-19', 'Computer Science Building', 1),
('Career Fair 2024', 'Connect with top employers and explore job opportunities', '2026-01-10', 'Business Faculty Auditorium', 1),
('Sports Day 2024', 'Annual sports competition for alumni', '2025-15-12', 'University Stadium', 1);

-- Insert sample announcements
INSERT INTO announcements (title, content, posted_by) VALUES
('Welcome New Alumni!', 'We welcome our 2023 graduates to the alumni community. Stay connected!', 1),
('Scholarship Opportunities', 'Several scholarship opportunities are available for further studies. Contact admin for details.', 1),
('Upcoming Reunion', 'Class of 2020 reunion is scheduled for next month. Check events page for details.', 1);