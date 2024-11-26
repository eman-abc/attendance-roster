CREATE SCHEMA ROSTER;

CREATE TABLE IF NOT EXISTS user (
  id INT NOT NULL AUTO_INCREMENT,
  fullname VARCHAR(200) NOT NULL,
  email VARCHAR(200) NOT NULL,
  class VARCHAR(10) NOT NULL,
  role ENUM('teacher', 'student', 'admin') NOT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;

ALTER TABLE user
ADD COLUMN password VARCHAR(255) NOT NULL;

-- semesters table
CREATE TABLE semesters (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    status ENUM('previous', 'current', 'upcoming') NOT NULL
);


-- class table
CREATE TABLE classes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    semester_id INT NOT NULL,  -- Link to the semester
    name VARCHAR(255) NOT NULL,  -- Name of the class, e.g., "Math 101"
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    FOREIGN KEY (semester_id) REFERENCES semesters(id)
);
ALTER TABLE classes
ADD COLUMN teacher_id INT,
ADD CONSTRAINT fk_teacher_id
    FOREIGN KEY (teacher_id)
    REFERENCES user(id);
    

CREATE TABLE student_class (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,  -- Link to the student
    class_id INT NOT NULL,    -- Link to the class
    FOREIGN KEY (student_id) REFERENCES user(id),  -- Assuming 'user' table contains student data
    FOREIGN KEY (class_id) REFERENCES classes(id)  -- Link to the 'classes' table
);
    
CREATE TABLE attendance (
    id INT AUTO_INCREMENT PRIMARY KEY,
    class_id INT NOT NULL,  -- Link to the class
    student_id INT NOT NULL,  -- Link to the student
    date DATE NOT NULL,  -- The date of the class
    status ENUM('present', 'absent') NOT NULL,
    comment TEXT,  -- Optional comment for absentees
    FOREIGN KEY (class_id) REFERENCES classes(id),
    FOREIGN KEY (student_id) REFERENCES user(id)
);




-- Step 1: Disable foreign key checks
SET FOREIGN_KEY_CHECKS = 0;

-- Step 2: Truncate all tables
TRUNCATE TABLE attendance;
TRUNCATE TABLE student_class;
TRUNCATE TABLE classes;
TRUNCATE TABLE semesters;
TRUNCATE TABLE user;

-- Step 3: Re-enable foreign key checks
SET FOREIGN_KEY_CHECKS = 1;

-- Step 4: Populate tables with extended data

-- Insert data into semesters
INSERT INTO semesters (id, name, start_date, end_date, status) VALUES
(1, 'Fall 2024', '2024-09-01', '2024-12-31', 'current'),
(2, 'Spring 2024', '2024-01-01', '2024-05-15', 'previous'),
(3, 'Summer 2024', '2024-06-01', '2024-08-15', 'upcoming'),
(4, 'Fall 2023', '2023-09-01', '2023-12-31', 'previous'),
(5, 'Spring 2023', '2023-01-01', '2023-05-15', 'previous');

-- Insert data into users (teachers, students, and admin)
INSERT INTO user (id, fullname, email, class, role, password) VALUES
(1, 'Alice Johnson', 'alice@example.com', 'CS101', 'teacher', 'password1'),
(2, 'David Lee', 'david@example.com', 'CS102', 'teacher', 'password2'),
(3, 'Eve Taylor', 'eve@example.com', 'CS103', 'teacher', 'password3'),
(4, 'John Doe', 'john.doe@example.com', 'CS104', 'teacher', 'password4'),
(5, 'Jane Smith', 'jane.smith@example.com', 'CS105', 'teacher', 'password5'),
(6, 'Bob Smith', 'bob@example.com', 'CS101', 'student', 'password6'),
(7, 'Charlie Brown', 'charlie@example.com', 'CS101', 'student', 'password7'),
(8, 'Daisy Green', 'daisy@example.com', 'CS102', 'student', 'password8'),
(9, 'Ethan Black', 'ethan@example.com', 'CS102', 'student', 'password9'),
(10, 'Fiona White', 'fiona@example.com', 'CS103', 'student', 'password10'),
(11, 'Grace Adams', 'grace@example.com', 'CS103', 'student', 'password11'),
(12, 'Henry Clark', 'henry@example.com', 'CS104', 'student', 'password12'),
(13, 'Ivy Brown', 'ivy@example.com', 'CS104', 'student', 'password13'),
(14, 'Jack Green', 'jack@example.com', 'CS105', 'student', 'password14'),
(15, 'Kathy Red', 'kathy@example.com', 'CS105', 'student', 'password15'),
(16, 'Admin User', 'admin@example.com', 'N/A', 'admin', 'password16');

-- Insert data into classes (across semesters and linked to teachers)
INSERT INTO classes (id, semester_id, name, start_time, end_time, teacher_id) VALUES
(1, 1, 'Web Engineering', '09:00:00', '10:30:00', 1),
(2, 1, 'Software Construction', '11:00:00', '12:30:00', 1),
(3, 1, 'Data Structures', '13:00:00', '14:30:00', 2),
(4, 2, 'Introduction to Programming', '10:00:00', '11:30:00', 2),
(5, 2, 'Algorithms', '14:00:00', '15:30:00', 3),
(6, 3, 'Artificial Intelligence', '12:00:00', '13:30:00', 3),
(7, 3, 'Machine Learning', '14:00:00', '15:30:00', 4),
(8, 4, 'Database Systems', '10:00:00', '11:30:00', 5),
(9, 5, 'Operating Systems', '14:00:00', '15:30:00', 4);

-- Insert data into student_class (linking students to classes)
INSERT INTO student_class (id, student_id, class_id) VALUES
(1, 6, 1), (2, 7, 1), (3, 6, 2), (4, 7, 2),
(5, 8, 3), (6, 9, 3), (7, 8, 4), (8, 9, 4),
(9, 10, 5), (10, 11, 5), (11, 10, 6), (12, 11, 6),
(13, 12, 7), (14, 13, 7), (15, 14, 8), (16, 15, 8),
(17, 14, 9), (18, 15, 9);

-- Insert data into attendance (adding multiple records for each class)
INSERT INTO attendance (id, class_id, student_id, date, status, comment) VALUES
-- Attendance for class 1
(1, 1, 6, '2024-09-01', 'present', 'Good start'),
(2, 1, 7, '2024-09-01', 'absent', 'Missed the lecture'),
(3, 1, 6, '2024-09-02', 'present', 'Active participation'),
(4, 1, 7, '2024-09-02', 'present', 'Caught up well'),
-- Attendance for class 2
(5, 2, 6, '2024-09-01', 'present', ''),
(6, 2, 7, '2024-09-01', 'absent', ''),
-- Attendance for class 3
(7, 3, 8, '2024-09-01', 'present', ''),
(8, 3, 9, '2024-09-01', 'absent', 'Family emergency'),
(9, 3, 8, '2024-09-02', 'present', ''),
(10, 3, 9, '2024-09-02', 'present', ''),
-- Attendance for class 4
(11, 4, 8, '2024-01-01', 'present', 'Started strong'),
(12, 4, 9, '2024-01-01', 'present', ''),
-- Attendance for class 5
(13, 5, 10, '2024-01-01', 'present', ''),
(14, 5, 11, '2024-01-01', 'absent', 'Medical leave'),
-- Attendance for class 6
(15, 6, 10, '2024-06-01', 'present', ''),
(16, 6, 11, '2024-06-01', 'present', 'Good participation'),
-- Attendance for class 7
(17, 7, 12, '2024-06-02', 'present', 'Excellent'),
(18, 7, 13, '2024-06-02', 'absent', ''),
-- Attendance for class 8
(19, 8, 14, '2023-09-01', 'present', 'Good start'),
(20, 8, 15, '2023-09-01', 'present', ''),
-- Attendance for class 9
(21, 9, 14, '2023-01-01', 'present', ''),
(22, 9, 15, '2023-01-01', 'absent', 'Late registration');

-- Step 5: Verify Data
SELECT * FROM semesters;
SELECT * FROM user;
SELECT * FROM classes;
SELECT * FROM student_class;
SELECT * FROM attendance;


select * from user;


-- Add more students (total 50 students)
INSERT INTO user (id, fullname, email, class, role, password) VALUES
(17, 'Lily White', 'lily.white@example.com', 'CS106', 'student', 'password17'),
(18, 'Mason Clark', 'mason.clark@example.com', 'CS107', 'student', 'password18'),
(19, 'Olivia Blue', 'olivia.blue@example.com', 'CS108', 'student', 'password19'),
(20, 'Lucas Gray', 'lucas.gray@example.com', 'CS109', 'student', 'password20'),
(21, 'Emma Lewis', 'emma.lewis@example.com', 'CS110', 'student', 'password21'),
(22, 'James King', 'james.king@example.com', 'CS111', 'student', 'password22'),
(23, 'Sophia Scott', 'sophia.scott@example.com', 'CS112', 'student', 'password23'),
(24, 'Liam Young', 'liam.young@example.com', 'CS113', 'student', 'password24'),
(25, 'Isabella Martinez', 'isabella.martinez@example.com', 'CS114', 'student', 'password25'),
(26, 'Benjamin Hall', 'benjamin.hall@example.com', 'CS115', 'student', 'password26'),
(27, 'Mia Lee', 'mia.lee@example.com', 'CS116', 'student', 'password27'),
(28, 'Ethan Harris', 'ethan.harris@example.com', 'CS117', 'student', 'password28'),
(29, 'Charlotte Carter', 'charlotte.carter@example.com', 'CS118', 'student', 'password29'),
(30, 'Aiden Adams', 'aiden.adams@example.com', 'CS119', 'student', 'password30'),
(31, 'Amelia Wilson', 'amelia.wilson@example.com', 'CS120', 'student', 'password31'),
(32, 'David Green', 'david.green@example.com', 'CS121', 'student', 'password32'),
(33, 'Harper Brown', 'harper.brown@example.com', 'CS122', 'student', 'password33'),
(34, 'Daniel Rodriguez', 'daniel.rodriguez@example.com', 'CS123', 'student', 'password34'),
(35, 'Ella Walker', 'ella.walker@example.com', 'CS124', 'student', 'password35'),
(36, 'Matthew Perez', 'matthew.perez@example.com', 'CS125', 'student', 'password36'),
(37, 'Abigail Murphy', 'abigail.murphy@example.com', 'CS126', 'student', 'password37'),
(38, 'Jack Wood', 'jack.wood@example.com', 'CS127', 'student', 'password38'),
(39, 'Zoe Nelson', 'zoe.nelson@example.com', 'CS128', 'student', 'password39'),
(40, 'Michael King', 'michael.king@example.com', 'CS129', 'student', 'password40'),
(41, 'Lucas Lee', 'lucas.lee@example.com', 'CS130', 'student', 'password41'),
(42, 'Sofia Brown', 'sofia.brown@example.com', 'CS131', 'student', 'password42'),
(43, 'Henry Lewis', 'henry.lewis@example.com', 'CS132', 'student', 'password43'),
(44, 'Chloe Clark', 'chloe.clark@example.com', 'CS133', 'student', 'password44'),
(45, 'Mason Walker', 'mason.walker@example.com', 'CS134', 'student', 'password45'),
(46, 'Emily Perez', 'emily.perez@example.com', 'CS135', 'student', 'password46'),
(47, 'David Young', 'david.young@example.com', 'CS136', 'student', 'password47'),
(48, 'Scarlett Harris', 'scarlett.harris@example.com', 'CS137', 'student', 'password48'),
(49, 'Samuel Scott', 'samuel.scott@example.com', 'CS138', 'student', 'password49'),
(50, 'Mia King', 'mia.king@example.com', 'CS139', 'student', 'password50');

-- Add more teachers (total 10 teachers)
INSERT INTO user (id, fullname, email, class, role, password) VALUES
(51, 'John White', 'john.white@example.com', 'CS140', 'teacher', 'password51'),
(52, 'Rachel Adams', 'rachel.adams@example.com', 'CS141', 'teacher', 'password52'),
(53, 'George Martin', 'george.martin@example.com', 'CS142', 'teacher', 'password53'),
(54, 'Victoria Brown', 'victoria.brown@example.com', 'CS143', 'teacher', 'password54'),
(55, 'William Gray', 'william.gray@example.com', 'CS144', 'teacher', 'password55');

-- Add more semesters (total 10 semesters)
INSERT INTO semesters (id, name, start_date, end_date, status) VALUES
(6, 'Winter 2024', '2024-12-01', '2025-02-28', 'upcoming'),
(7, 'Fall 2025', '2025-09-01', '2025-12-31', 'upcoming'),
(8, 'Spring 2025', '2025-01-01', '2025-05-15', 'upcoming'),
(9, 'Summer 2025', '2025-06-01', '2025-08-15', 'upcoming'),
(10, 'Fall 2026', '2026-09-01', '2026-12-31', 'upcoming');

-- Add classes with at least 20 students per class
INSERT INTO classes (id, semester_id, name, start_time, end_time, teacher_id) VALUES
(10, 1, 'Web Engineering Advanced', '09:00:00', '10:30:00', 1),
(11, 1, 'Software Construction Advanced', '11:00:00', '12:30:00', 2),
(12, 1, 'Data Structures Advanced', '13:00:00', '14:30:00', 3),
(13, 2, 'Introduction to Programming Advanced', '10:00:00', '11:30:00', 4),
(14, 2, 'Algorithms Advanced', '14:00:00', '15:30:00', 5),
(15, 3, 'Artificial Intelligence Advanced', '12:00:00', '13:30:00', 6),
(16, 3, 'Machine Learning Advanced', '14:00:00', '15:30:00', 7),
(17, 4, 'Database Systems Advanced', '10:00:00', '11:30:00', 8),
(18, 5, 'Operating Systems Advanced', '14:00:00', '15:30:00', 9),
(19, 6, 'Network Security', '10:00:00', '11:30:00', 10);

-- Add students to each class (ensure each class has at least 20 students)
-- Note: For simplicity, we will use the same student IDs repeatedly to fulfill the condition that each class has at least 20 students
INSERT INTO student_class (student_id, class_id) VALUES
(6, 10), (7, 10), (8, 10), (9, 10), (10, 10), (11, 10), (12, 10), (13, 10), (14, 10), (15, 10),
(16, 10), (17, 10), (18, 10), (19, 10), (20, 10), (21, 10), (22, 10), (23, 10), (24, 10), (25, 10),
(6, 11), (7, 11), (8, 11), (9, 11), (10, 11), (11, 11), (12, 11), (13, 11), (14, 11), (15, 11),
(16, 11), (17, 11), (18, 11), (19, 11), (20, 11), (21, 11), (22, 11), (23, 11), (24, 11), (25, 11);