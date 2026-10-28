-- StudentHub database (Practicals 8-14). Import in phpMyAdmin.
DROP DATABASE IF EXISTS studenthub;
CREATE DATABASE studenthub CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE studenthub;

CREATE TABLE users (
  user_id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(60) NOT NULL,
  email VARCHAR(100) NOT NULL UNIQUE,
  mobile VARCHAR(10) DEFAULT NULL,
  gender ENUM('Male','Female','Other') DEFAULT NULL,
  password_hash VARCHAR(255) NOT NULL,
  role ENUM('admin','student') NOT NULL DEFAULT 'student',
  status ENUM('active','inactive') NOT NULL DEFAULT 'active',
  last_login DATETIME DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE students (
  student_id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT DEFAULT NULL,
  name VARCHAR(60) NOT NULL,
  email VARCHAR(100) NOT NULL UNIQUE,
  course VARCHAR(30) NOT NULL,
  year TINYINT NOT NULL DEFAULT 1,
  status ENUM('active','inactive') NOT NULL DEFAULT 'active',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_students_user FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE events (
  event_id INT AUTO_INCREMENT PRIMARY KEY,
  event_name VARCHAR(100) NOT NULL,
  event_date DATE NOT NULL,
  venue VARCHAR(100) NOT NULL,
  category VARCHAR(40) NOT NULL DEFAULT 'General',
  status ENUM('open','closed') NOT NULL DEFAULT 'open',
  poster VARCHAR(150) DEFAULT NULL,
  description TEXT
) ENGINE=InnoDB;

CREATE TABLE registrations (
  registration_id INT AUTO_INCREMENT PRIMARY KEY,
  student_id INT NOT NULL,
  event_id INT NOT NULL,
  registration_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uq_student_event (student_id, event_id),
  CONSTRAINT fk_reg_student FOREIGN KEY (student_id) REFERENCES students(student_id) ON DELETE CASCADE,
  CONSTRAINT fk_reg_event FOREIGN KEY (event_id) REFERENCES events(event_id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE audit_logs (
  log_id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT DEFAULT NULL,
  action VARCHAR(50) NOT NULL,
  details VARCHAR(255) DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Demo logins: admin@studenthub.local / Admin@123 ; student@studenthub.local / Student@123
INSERT INTO users (name,email,mobile,gender,password_hash,role) VALUES
('Administrator','admin@studenthub.local','9000000001','Other','$6$onwkE7GRMdhdJEgZ$GQaND1tZGwh/hI38FLhEv0IziX6i12sQLrNpqIZu2rP5rf/piFVcCRuIjAWzCo2Jfjys31RLuyYyHmmUZfyef.','admin'),
('Demo Student','student@studenthub.local','9000000002','Other','$6$GA2RH/R4eJwfpUSN$pAHa4CZRIm7wDytV1bGHy212doN/Lh4Y5JvwTM0agAUrczu6LxJfSFALWWQDULQIdoh.SSEGojgBIz9Nj036I/','student');

INSERT INTO students (user_id,name,email,course,year) VALUES
(2,'Demo Student','student@studenthub.local','B.Tech',2),
(NULL,'Aarav Patel','aarav.patel@student.charusat.edu.in','BCA',2),
(NULL,'Diya Shah','diya.shah@student.charusat.edu.in','B.Sc IT',3),
(NULL,'Kabir Mehta','kabir.mehta@student.charusat.edu.in','MCA',4),
(NULL,'Isha Desai','isha.desai@student.charusat.edu.in','B.Tech',1),
(NULL,'Rohan Joshi','rohan.joshi@student.charusat.edu.in','BCA',2),
(NULL,'Meera Trivedi','meera.trivedi@student.charusat.edu.in','B.Sc IT',3),
(NULL,'Vivaan Pandya','vivaan.pandya@student.charusat.edu.in','MCA',4),
(NULL,'Anaya Modi','anaya.modi@student.charusat.edu.in','B.Tech',1),
(NULL,'Krish Vyas','krish.vyas@student.charusat.edu.in','BCA',2),
(NULL,'Nisha Rana','nisha.rana@student.charusat.edu.in','B.Sc IT',3),
(NULL,'Arjun Bhatt','arjun.bhatt@student.charusat.edu.in','MCA',4),
(NULL,'Pooja Solanki','pooja.solanki@student.charusat.edu.in','B.Tech',1),
(NULL,'Dev Parmar','dev.parmar@student.charusat.edu.in','BCA',2),
(NULL,'Riya Thakkar','riya.thakkar@student.charusat.edu.in','B.Sc IT',3),
(NULL,'Harsh Gandhi','harsh.gandhi@student.charusat.edu.in','MCA',4),
(NULL,'Sneha Chauhan','sneha.chauhan@student.charusat.edu.in','B.Tech',1),
(NULL,'Yash Dave','yash.dave@student.charusat.edu.in','BCA',2),
(NULL,'Tanvi Raval','tanvi.raval@student.charusat.edu.in','B.Sc IT',3),
(NULL,'Manav Kadam','manav.kadam@student.charusat.edu.in','MCA',4),
(NULL,'Kavya Nair','kavya.nair@student.charusat.edu.in','B.Tech',1);

INSERT INTO events (event_name,event_date,venue,category,status,description) VALUES
('CHARUSAT RED HAT DAY','2026-10-07','CSPIT, CHARUSAT','Workshop','closed','Red Hat day with Linux and open source activities.'),
('Hackathon 2026','2026-11-20','CSPIT Auditorium','Hackathon','open','24-hour coding hackathon.'),
('Career Fair','2026-12-10','Main Ground','Seminar','open','Meet recruiters and explore opportunities.'),
('Web Development Workshop','2026-11-05','Lab 628B','Workshop','open','Hands-on HTML, CSS, JavaScript and PHP workshop.'),
('Annual Cultural Fest','2026-12-20','Main Ground','Cultural','open','Music, dance and drama performances.');

INSERT INTO registrations (student_id,event_id) VALUES (1,2),(1,4);

INSERT INTO audit_logs (user_id,action,details) VALUES (1,'seed','Database created with sample data');
