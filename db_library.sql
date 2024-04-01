/*
 Navicat Premium Data Transfer

 Source Server         : db_library
 Source Server Type    : MySQL
 Source Server Version : 50744
 Source Host           : localhost:3308
 Source Schema         : db_library_2

 Target Server Type    : MySQL
 Target Server Version : 50744
 File Encoding         : 65001

 Date: 23/03/2024 09:49:58
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for tbl_archive
-- ----------------------------
DROP TABLE IF EXISTS `tbl_archive`;
CREATE TABLE `tbl_archive`  (
  `Archive_ID` int(11) NOT NULL,
  `User_ID` int(11) NOT NULL,
  `Accession_Code` varchar(17) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `Book_Title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `Quantity` int(11) NOT NULL,
  `Date` date NOT NULL,
  PRIMARY KEY (`Archive_ID`) USING BTREE,
  INDEX `Accession_Code`(`Accession_Code`) USING BTREE,
  INDEX `User_ID`(`User_ID`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of tbl_archive
-- ----------------------------

-- ----------------------------
-- Table structure for tbl_authors
-- ----------------------------
DROP TABLE IF EXISTS `tbl_authors`;
CREATE TABLE `tbl_authors`  (
  `Authors_ID` varchar(11) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `Authors_Name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `Nationality` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`Authors_ID`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of tbl_authors
-- ----------------------------
INSERT INTO `tbl_authors` VALUES ('415819', 'Deped ', 'N/A');
INSERT INTO `tbl_authors` VALUES ('427895', 'Blancia ', 'N/A');
INSERT INTO `tbl_authors` VALUES ('881421', 'Deped ', 'N/A');
INSERT INTO `tbl_authors` VALUES ('901960', 'Deped ', 'N/A');
INSERT INTO `tbl_authors` VALUES ('A65', 'Jose P. Abletez', 'Filipino');
INSERT INTO `tbl_authors` VALUES ('B45', 'Jerry H. Bentley', 'Filipino');
INSERT INTO `tbl_authors` VALUES ('B937', 'James MacGregor Burns', '');
INSERT INTO `tbl_authors` VALUES ('J15', 'Helen Pierce Jacob', '');
INSERT INTO `tbl_authors` VALUES ('M178', 'Sample', 'Sample');
INSERT INTO `tbl_authors` VALUES ('T177', 'Lorie Tarslis', '');

-- ----------------------------
-- Table structure for tbl_books
-- ----------------------------
DROP TABLE IF EXISTS `tbl_books`;
CREATE TABLE `tbl_books`  (
  `Accession_Code` int(17) NOT NULL AUTO_INCREMENT,
  `Book_Title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `Authors_ID` varchar(11) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `Publisher_ID` varchar(11) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `Section_Code` int(11) NOT NULL,
  `Shelf_Number` int(11) NOT NULL,
  `tb_edition` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `Year_Published` year NOT NULL,
  `ISBN` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `Bibliography` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `Quantity` int(11) NOT NULL,
  `Price` float NOT NULL,
  `tb_status` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`Accession_Code`) USING BTREE,
  INDEX `Authors_ID`(`Authors_ID`) USING BTREE,
  INDEX `Publisher_ID`(`Publisher_ID`) USING BTREE,
  INDEX `Section_Code`(`Section_Code`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of tbl_books
-- ----------------------------
INSERT INTO `tbl_books` VALUES (1, 'World Of data Structure', 'A65', '1', 1, 1, 'First Edition', 2019, '1', 'NA', 55, 20, 'Available');

-- ----------------------------
-- Table structure for tbl_borrow
-- ----------------------------
DROP TABLE IF EXISTS `tbl_borrow`;
CREATE TABLE `tbl_borrow`  (
  `Borrow_ID` int(11) NOT NULL AUTO_INCREMENT,
  `User_ID` int(11) NOT NULL,
  `Borrower_ID` int(11) NOT NULL,
  `Accession_Code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `Date_Borrowed` date NOT NULL,
  `Due_Date` date NOT NULL,
  `tb_status` varchar(11) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`Borrow_ID`) USING BTREE,
  INDEX `User_ID`(`User_ID`) USING BTREE,
  INDEX `Borrower_ID`(`Borrower_ID`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 3 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of tbl_borrow
-- ----------------------------
INSERT INTO `tbl_borrow` VALUES (1, 1016, 21, '001', '2024-03-18', '2024-03-19', 'Pending');
INSERT INTO `tbl_borrow` VALUES (2, 1016, 1, '001', '2024-03-18', '2024-03-19', 'Pending');

-- ----------------------------
-- Table structure for tbl_borrowdetails
-- ----------------------------
DROP TABLE IF EXISTS `tbl_borrowdetails`;
CREATE TABLE `tbl_borrowdetails`  (
  `BorrowDetails_ID` int(11) NOT NULL AUTO_INCREMENT,
  `Borrower_ID` int(11) NOT NULL,
  `Accession_Code` varchar(17) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `Quantity` int(11) NOT NULL,
  `tb_status` varchar(11) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`BorrowDetails_ID`) USING BTREE,
  INDEX `Borrow_ID`(`Borrower_ID`) USING BTREE,
  INDEX `Accession_Code`(`Accession_Code`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 3 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of tbl_borrowdetails
-- ----------------------------
INSERT INTO `tbl_borrowdetails` VALUES (1, 21, '001', 1, 'Paid');
INSERT INTO `tbl_borrowdetails` VALUES (2, 1, '001', 1, 'Pending');

-- ----------------------------
-- Table structure for tbl_borrower
-- ----------------------------
DROP TABLE IF EXISTS `tbl_borrower`;
CREATE TABLE `tbl_borrower`  (
  `Borrower_ID` int(11) NOT NULL AUTO_INCREMENT,
  `First_Name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `Middle_Name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `Last_Name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `Contact_Number` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `Email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `affiliation` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`Borrower_ID`) USING BTREE,
  UNIQUE INDEX `Email`(`Email`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 22 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of tbl_borrower
-- ----------------------------
INSERT INTO `tbl_borrower` VALUES (1, 'Kurt', 'Parlocha', 'Blancia', '09511980549', 'blncia@gmail.com', 'DEPED');
INSERT INTO `tbl_borrower` VALUES (2, 'Kent', 'Eq', 'Santos', '09511980549', 'dawd@gmail.com', 'ANHS');
INSERT INTO `tbl_borrower` VALUES (21, 'Angela ', 'Mae', 'Salvana', '094509240592', 'gela@gmail.com', 'softDev@gmail.com');

-- ----------------------------
-- Table structure for tbl_contributor
-- ----------------------------
DROP TABLE IF EXISTS `tbl_contributor`;
CREATE TABLE `tbl_contributor`  (
  `Contributor_ID` int(11) NOT NULL,
  `Name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `Address` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `Email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `Contact_Number` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`Contributor_ID`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of tbl_contributor
-- ----------------------------

-- ----------------------------
-- Table structure for tbl_employee
-- ----------------------------
DROP TABLE IF EXISTS `tbl_employee`;
CREATE TABLE `tbl_employee`  (
  `User_ID` int(11) NOT NULL AUTO_INCREMENT,
  `tb_password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `First_Name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `Middle_Name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `Last_Name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `tb_role` enum('Admin','Staff') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `Contact_Number` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `E_mail` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `tb_address` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `token` int(6) NOT NULL,
  PRIMARY KEY (`User_ID`, `token`) USING BTREE,
  UNIQUE INDEX `E-mail`(`E_mail`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1017 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of tbl_employee
-- ----------------------------
INSERT INTO `tbl_employee` VALUES (1001, 'L!br@rian', 'Elizabeth', 'P.', 'Nahial', 'Admin', '0', 'admin@mail.com', 'Villanueva', 0);
INSERT INTO `tbl_employee` VALUES (1004, '$2y$10$RP..bgHybw16gbX5UB/eS.nl/xgEIrvlddIg4iS/iLAd5Ns7vvKVe', 'Kurt Bernstein', 'Blancia', 'p', 'Admin', '93501305135', '1002@gmail.com', 'awdawd', 0);
INSERT INTO `tbl_employee` VALUES (1006, '$2y$10$U0L5rbEtji/4W0FrGet1OusOf6on/3HxDeqfQrVoq.CFpzjGS.4tS', 'Mayor', 'Blancia', 'p', 'Staff', '0000', '100awd2@gmail.com', 'awdawd', 0);
INSERT INTO `tbl_employee` VALUES (1012, '$2y$10$o0UgmL0isjyvXPZL/ONEveGLsZ9aRxehhhGHJKIJzDi7g6u0yUFcO', 'awdawdawd', 'awdawd', 'p', 'Staff', '0000', '123123@gmail.com', 'awdawd', 0);
INSERT INTO `tbl_employee` VALUES (1013, '$2y$10$U88Ku2PXx/Yukh1H/X6qEu4yzzufkVvksqD.ItC9J2QPjwh3xDWhq', 'test', 'Wdawd', 'dawda', 'Staff', '12312312', '12312s3@gmail.com', 'awdawd', 0);
INSERT INTO `tbl_employee` VALUES (1014, '$2y$10$wZMBNPgDHhKNPuY.HP6ky.PJ.rbC6I5LeHjpsotTt7VuHoEfeO93e', 'Kurt Blancia', 'e', 'P', 'Admin', '01515', 'blancia@gmail.com', 'dawdawd', 0);
INSERT INTO `tbl_employee` VALUES (1015, '1234', 'Kurt Blancia 2', 'awdawdawd', 'P', 'Admin', '01515232', 'blanciakurtbernstein@gmail.com', 'dawdawd', 386777);
INSERT INTO `tbl_employee` VALUES (1016, '12345', 'testemp', 'ep', 'p', 'Staff', 'awdawd', 'k.blancia.dev@gmail.com', 'awdwd', 482755);

-- ----------------------------
-- Table structure for tbl_fines
-- ----------------------------
DROP TABLE IF EXISTS `tbl_fines`;
CREATE TABLE `tbl_fines`  (
  `Fine_ID` int(11) NOT NULL AUTO_INCREMENT,
  `Borrower_ID` int(11) NOT NULL,
  `ReturnDetails_ID` int(11) NOT NULL,
  `Amount` int(10) NOT NULL,
  `Reason` enum('Late Return','Lost','Damage Book') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `Payment_Status` enum('Outstanding','Paid') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `Date_Created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `Payment_Date` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`Fine_ID`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 6 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of tbl_fines
-- ----------------------------
INSERT INTO `tbl_fines` VALUES (1, 1, 1, 50, NULL, 'Paid', '2024-03-13 00:00:00', '2024-03-13 12:41:05');
INSERT INTO `tbl_fines` VALUES (2, 1, 2, 50, NULL, 'Paid', '2024-03-13 00:00:00', '2024-03-13 12:45:52');
INSERT INTO `tbl_fines` VALUES (3, 1, 3, 50, NULL, 'Paid', '2024-03-13 00:00:00', '2024-03-13 12:46:42');
INSERT INTO `tbl_fines` VALUES (4, 1, 4, 50, NULL, 'Paid', '2024-03-13 00:00:00', '2024-03-13 12:48:07');
INSERT INTO `tbl_fines` VALUES (5, 1, 1, 50, NULL, 'Paid', '2024-03-19 00:00:00', '2024-03-19 01:34:37');

-- ----------------------------
-- Table structure for tbl_log
-- ----------------------------
DROP TABLE IF EXISTS `tbl_log`;
CREATE TABLE `tbl_log`  (
  `Log_ID` int(11) NOT NULL,
  `User_ID` int(11) NOT NULL,
  `Borrower_ID` int(11) NOT NULL,
  `Date&Time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of tbl_log
-- ----------------------------

-- ----------------------------
-- Table structure for tbl_publisher
-- ----------------------------
DROP TABLE IF EXISTS `tbl_publisher`;
CREATE TABLE `tbl_publisher`  (
  `Publisher_ID` varchar(11) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `Publisher_Name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `Address` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`Publisher_ID`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of tbl_publisher
-- ----------------------------
INSERT INTO `tbl_publisher` VALUES ('206975', 'deped 10', 'NA');
INSERT INTO `tbl_publisher` VALUES ('435796', '2', 'NA');
INSERT INTO `tbl_publisher` VALUES ('920301', 'deped 10', 'NA');
INSERT INTO `tbl_publisher` VALUES ('982720', 'deped 10', 'NA');
INSERT INTO `tbl_publisher` VALUES ('HMC', 'Houghton Mifflin Company', '');
INSERT INTO `tbl_publisher` VALUES ('MCS', 'McClelland & Stewart, Ltd.', '');
INSERT INTO `tbl_publisher` VALUES ('MGHC', 'McGraw Hill Company', '');
INSERT INTO `tbl_publisher` VALUES ('MJPHI', 'Mary Jo Publishing House, Inc.', '');
INSERT INTO `tbl_publisher` VALUES ('PHI', 'Prentice Hall, Inc.', '');

-- ----------------------------
-- Table structure for tbl_receiving
-- ----------------------------
DROP TABLE IF EXISTS `tbl_receiving`;
CREATE TABLE `tbl_receiving`  (
  `Receive_ID` int(11) NOT NULL,
  `User_ID` int(11) NOT NULL,
  `Contributor_ID` int(11) NOT NULL,
  `Type` enum('Donor','Supplier') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `Date` date NOT NULL
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of tbl_receiving
-- ----------------------------

-- ----------------------------
-- Table structure for tbl_receivingdetails
-- ----------------------------
DROP TABLE IF EXISTS `tbl_receivingdetails`;
CREATE TABLE `tbl_receivingdetails`  (
  `RD_ID` int(11) NOT NULL,
  `Receive_ID` int(11) NOT NULL,
  `Accession Code` varchar(17) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `Book Title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `Quantity` int(255) NOT NULL,
  `Price` float NOT NULL
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of tbl_receivingdetails
-- ----------------------------

-- ----------------------------
-- Table structure for tbl_requestbooks
-- ----------------------------
DROP TABLE IF EXISTS `tbl_requestbooks`;
CREATE TABLE `tbl_requestbooks`  (
  `Request_ID` int(11) NOT NULL AUTO_INCREMENT,
  `User_ID` int(11) NOT NULL,
  `Book_Title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `Authors_ID` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `Publisher_ID` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `price` double NULL DEFAULT NULL,
  `tb_edition` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `Year_Published` year NOT NULL,
  `Quantity` int(11) NOT NULL,
  `tb_status` enum('Pending','Approved') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`Request_ID`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of tbl_requestbooks
-- ----------------------------
INSERT INTO `tbl_requestbooks` VALUES (1, 1016, 'Data Mining', 'Deped', 'deped 10', NULL, 'First Edition', 2019, 20, 'Pending');

-- ----------------------------
-- Table structure for tbl_returned
-- ----------------------------
DROP TABLE IF EXISTS `tbl_returned`;
CREATE TABLE `tbl_returned`  (
  `Borrow_ID` int(11) NOT NULL AUTO_INCREMENT,
  `User_ID` int(11) NOT NULL,
  `Borrower_ID` int(11) NOT NULL,
  `Date_Returned` date NULL DEFAULT NULL,
  `tb_status` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`Borrow_ID`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 12 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of tbl_returned
-- ----------------------------
INSERT INTO `tbl_returned` VALUES (1, 1002, 1, '2024-03-19', 'Resolved');
INSERT INTO `tbl_returned` VALUES (2, 1002, 1, '2024-03-13', 'Resolved');
INSERT INTO `tbl_returned` VALUES (3, 1002, 1, '2024-03-13', 'Resolved');
INSERT INTO `tbl_returned` VALUES (4, 1002, 1, '2024-03-13', 'Resolved');
INSERT INTO `tbl_returned` VALUES (5, 1016, 16, NULL, 'Pending');
INSERT INTO `tbl_returned` VALUES (6, 1016, 16, NULL, 'Pending');
INSERT INTO `tbl_returned` VALUES (7, 1016, 2, NULL, 'Pending');
INSERT INTO `tbl_returned` VALUES (8, 1016, 21, NULL, 'Pending');
INSERT INTO `tbl_returned` VALUES (9, 1016, 21, NULL, 'Pending');
INSERT INTO `tbl_returned` VALUES (10, 1016, 21, NULL, 'Pending');
INSERT INTO `tbl_returned` VALUES (11, 1016, 1, NULL, 'Pending');

-- ----------------------------
-- Table structure for tbl_returningdetails
-- ----------------------------
DROP TABLE IF EXISTS `tbl_returningdetails`;
CREATE TABLE `tbl_returningdetails`  (
  `Return_ID` int(11) NOT NULL AUTO_INCREMENT,
  `BorrowDetails_ID` int(11) NOT NULL,
  `tb_status` enum('Returned','Borrowed') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`Return_ID`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 12 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of tbl_returningdetails
-- ----------------------------
INSERT INTO `tbl_returningdetails` VALUES (1, 1, 'Returned');
INSERT INTO `tbl_returningdetails` VALUES (2, 1, 'Returned');
INSERT INTO `tbl_returningdetails` VALUES (3, 1, 'Returned');
INSERT INTO `tbl_returningdetails` VALUES (4, 1, 'Returned');
INSERT INTO `tbl_returningdetails` VALUES (5, 16, 'Borrowed');
INSERT INTO `tbl_returningdetails` VALUES (6, 16, 'Borrowed');
INSERT INTO `tbl_returningdetails` VALUES (7, 2, 'Borrowed');
INSERT INTO `tbl_returningdetails` VALUES (8, 21, 'Borrowed');
INSERT INTO `tbl_returningdetails` VALUES (9, 21, 'Borrowed');
INSERT INTO `tbl_returningdetails` VALUES (10, 21, 'Borrowed');
INSERT INTO `tbl_returningdetails` VALUES (11, 1, 'Returned');

-- ----------------------------
-- Table structure for tbl_section
-- ----------------------------
DROP TABLE IF EXISTS `tbl_section`;
CREATE TABLE `tbl_section`  (
  `Section_uid` int(11) NOT NULL,
  `Section_Name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `Section_Code` varchar(11) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of tbl_section
-- ----------------------------
INSERT INTO `tbl_section` VALUES (1, 'Assorted', 'ASRTD');
INSERT INTO `tbl_section` VALUES (2, 'Circulation', 'CIR');
INSERT INTO `tbl_section` VALUES (3, 'Fiction', 'FIC');
INSERT INTO `tbl_section` VALUES (4, 'Filipiniana', 'FIL');
INSERT INTO `tbl_section` VALUES (5, 'Reference', 'REF');

SET FOREIGN_KEY_CHECKS = 1;
