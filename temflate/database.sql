CREATE DATABASE IF NOT EXISTS clinic_db;
USE clinic_db;

CREATE TABLE IF NOT EXISTS doctor(
  docID INT AUTO_INCREMENT PRIMARY KEY,
  docFName VARCHAR(50) NOT NULL,
  docLName VARCHAR(50) NOT NULL,
  docAddress VARCHAR(50) NOT NULL,
  docSpecial VARCHAR(50) NOT NULL
);

CREATE TABLE IF NOT EXISTS patient(
  patID INT AUTO_INCREMENT PRIMARY KEY,
  patFName VARCHAR(50) NOT NULL,
  patLName VARCHAR(50) NOT NULL,
  patBDate DATE NOT NULL,
  patTelNo VARCHAR(15) NOT NULL
);

CREATE TABLE IF NOT EXISTS consultation(
  consulID INT AUTO_INCREMENT PRIMARY KEY,
  patID INT NOT NULL,
  docID INT NOT NULL,
  consulDate DATETIME NOT NULL,
  diagnosis INT NOT NULL,
  prescription VARCHAR(30) NOT NULL,

  FOREIGN KEY (patID) REFERENCES patient (patID),
  FOREIGN KEY (docID) REFERENCES doctor (docID)
);

INSERT INTO doctor (docFName, docLName, docAddress, docSpecial)
VALUES
('John', 'Santos', 'Cebu City', 'Cardiology'),
('Maria', 'Reyes', 'Mandaue City', 'Pediatrics'),
('Mark', 'Cruz', 'Lapu-Lapu City', 'Dentistry'),
('Anna', 'Garcia', 'Talisay City', 'Dermatology'),
('James', 'Lopez', 'Consolacion', 'General Medicine');

INSERT INTO patient (patFName, patLName, patBDate, patTelNo)
VALUES
('Peter', 'Dela Cruz', '2002-05-15', '09123456789'),
('Sophia', 'Ramos', '2000-08-20', '09234567890'),
('Daniel', 'Mendoza', '1998-12-10', '09345678901'),
('Angela', 'Torres', '2005-03-25', '09456789012'),
('Michael', 'Flores', '1995-07-18', '09567890123');

INSERT INTO consultation (patID, docID, consulDate, diagnosis, prescription)
VALUES
(1, 1, '2026-11-10 08:30:00', 5, 'Medicine A'),
(2, 2, '2026-11-10 09:00:00', 10, 'Medicine B'),
(3, 3, '2026-11-10 10:30:00', 15, 'Medicine C'),
(4, 4, '2026-11-10 13:00:00', 20, 'Medicine D'),
(5, 5, '2026-11-10 14:30:00', 25, 'Medicine E');
