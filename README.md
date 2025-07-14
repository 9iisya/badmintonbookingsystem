# badmintonbookingsystem
functional booking website connected to phpMyAdmin for court reservation and payment records.

do copy this and paste on PHPMYSQL first

-- Create the Badminton Court Booking Database
CREATE DATABASE IF NOT EXISTS badmintondb;
USE badmintondb;

-- ==============================
-- Table: ADMIN
-- Stores administrator account details
-- ==============================
CREATE TABLE ADMIN (
    Admin_ID CHAR(12) NOT NULL PRIMARY KEY,
    Admin_Name VARCHAR(50) NOT NULL,
    Admin_PhoneNum VARCHAR(15) NOT NULL,
    Admin_Email VARCHAR(50) NOT NULL,
    Admin_Password VARCHAR(100) NOT NULL
);

-- ==============================
-- Table: EMPLOYEE
-- Stores employee details and links to admin
-- ==============================
CREATE TABLE EMPLOYEE (
    Employee_ID CHAR(12) NOT NULL PRIMARY KEY,
    Emp_Name VARCHAR(50) NOT NULL,
    Emp_PhoneNum VARCHAR(15) NOT NULL,
    Emp_Email VARCHAR(50) NOT NULL,
    Emp_Password VARCHAR(100) NOT NULL,
    Emp_Image VARCHAR(255), -- Optional: employee profile image
    Admin_ID CHAR(12),
    FOREIGN KEY (Admin_ID) REFERENCES ADMIN(Admin_ID)
);

-- ==============================
-- Table: CUSTOMER
-- Stores registered customer information
-- ==============================
CREATE TABLE CUSTOMER (
    Cust_ID CHAR(12) NOT NULL PRIMARY KEY,
    Cust_Name VARCHAR(25) NOT NULL,
    Cust_Email VARCHAR(25) NOT NULL,
    Cust_PhoneNum VARCHAR(11) NOT NULL,
    Cust_Password VARCHAR(100) NOT NULL
);

-- ==============================
-- Table: COURT
-- Stores badminton court details and rates
-- ==============================
CREATE TABLE COURT (
    Court_ID CHAR(12) NOT NULL PRIMARY KEY,
    Court_RatePerSlot DECIMAL(10,2) NOT NULL,
    Court_Desc VARCHAR(100) NOT NULL
);

-- ==============================
-- Table: COURT_STATUS
-- Tracks each court's availability by date and time
-- ==============================
CREATE TABLE COURT_STATUS (
    Status_ID INT AUTO_INCREMENT PRIMARY KEY,
    Court_ID CHAR(12),
    Status_Date DATE,
    Time_Slot VARCHAR(20),          -- Example: '8AM - 10AM'
    Court_Status VARCHAR(20),       -- Example: 'Available', 'Unavailable'
    FOREIGN KEY (Court_ID) REFERENCES COURT(Court_ID)
);

-- ==============================
-- Table: BOOKING
-- Stores all customer court bookings
-- Cascades delete when a customer is deleted
-- ==============================
CREATE TABLE BOOKING (
    Book_ID CHAR(12) NOT NULL PRIMARY KEY,
    Cust_ID CHAR(12),
    Court_ID CHAR(12),
    Employee_ID CHAR(12),
    Court_CheckInTime TIME NOT NULL,
    Court_CheckOutTime TIME NOT NULL,
    Court_UseDate DATE NOT NULL,
    FOREIGN KEY (Cust_ID) REFERENCES CUSTOMER(Cust_ID) ON DELETE CASCADE,
    FOREIGN KEY (Court_ID) REFERENCES COURT(Court_ID),
    FOREIGN KEY (Employee_ID) REFERENCES EMPLOYEE(Employee_ID)
);

-- ==============================
-- Table: PAYMENT
-- Stores customer payment records
-- Cascades delete when a customer is deleted
-- ==============================
CREATE TABLE PAYMENT (
    Payment_ID CHAR(12) NOT NULL PRIMARY KEY,
    Cust_ID CHAR(12),
    Payment_Date DATE NOT NULL,
    Payment_Method VARCHAR(50) NOT NULL,
    FOREIGN KEY (Cust_ID) REFERENCES CUSTOMER(Cust_ID) ON DELETE CASCADE
);

-- ==============================
-- Table: FEEDBACK
-- Stores customer feedback and admin replies
-- Cascades delete when a customer is deleted
-- ==============================
CREATE TABLE FEEDBACK (
    Feedback_ID CHAR(12) NOT NULL PRIMARY KEY,
    Cust_ID CHAR(12),
    Feedback_Rating INT NOT NULL,
    Feedback_Date DATE NOT NULL,
    Feedback_Comment VARCHAR(255),
    Feedback_Reply VARCHAR(255),
    Admin_ID CHAR(12),
    FOREIGN KEY (Cust_ID) REFERENCES CUSTOMER(Cust_ID) ON DELETE CASCADE,
    FOREIGN KEY (Admin_ID) REFERENCES ADMIN(Admin_ID)
);
-- ==============================
-- Table: TASK
-- Manages tasks assigned to employees
-- Cascades delete when an employee is deleted
-- ==============================
CREATE TABLE TASK (
    Task_ID INT AUTO_INCREMENT PRIMARY KEY,
    Task_Name VARCHAR(255) NOT NULL,
    Task_Assigned_To CHAR(12) NOT NULL,
    Task_Status VARCHAR(50) NOT NULL DEFAULT 'Pending',
    FOREIGN KEY (Task_Assigned_To) REFERENCES EMPLOYEE(Employee_ID) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ==============================
-- Default Data Insertion
-- ==============================

-- Insert default admin account
INSERT INTO ADMIN (Admin_ID, Admin_Name, Admin_PhoneNum, Admin_Email, Admin_Password)
VALUES ('AD01', 'Sarah Admin', '0131234567', 'sarahadmin@gmail.com', 'admin123');

-- Insert default employee account
INSERT INTO EMPLOYEE (Employee_ID, Emp_Name, Emp_PhoneNum, Emp_Email, Emp_Password, Admin_ID)
VALUES ('EMP01', 'Aiman', '0123456789', 'aiman@gmail.com', '123', 'AD01');

-- Insert 3 available courts
INSERT INTO COURT (Court_ID, Court_RatePerSlot, Court_Desc)
VALUES 
('C01', 30.00, 'Court A'),
('C02', 30.00, 'Court B'),
('C03', 30.00, 'Court C');
