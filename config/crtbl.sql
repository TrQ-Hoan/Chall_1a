ALTER TABLE `assignments` DROP FOREIGN KEY fk_teacher_assignment;
ALTER TABLE `submits` DROP FOREIGN KEY fk_assignment;
ALTER TABLE `submits` DROP FOREIGN KEY fk_student_submit;
ALTER TABLE `submits` DROP FOREIGN KEY fk_user_submit;
ALTER TABLE `challenges` DROP FOREIGN KEY fk_teacher_challenge;
ALTER TABLE `messages` DROP FOREIGN KEY fk_sender;
ALTER TABLE `messages` DROP FOREIGN KEY fk_receiver;

DROP TABLE IF EXISTS `users`, `assignments`, `submits`, `challenges`, `messages`;

CREATE TABLE IF NOT EXISTS `users` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `username` VARCHAR(50) NOT NULL UNIQUE,
    `password` VARCHAR(50) NOT NULL,
    `fullname` VARCHAR(128) NOT NULL,
    `email` VARCHAR(128) NOT NULL,
    `phone` VARCHAR(16) NOT NULL,
    `role` VARCHAR(20) NOT NULL
);

CREATE TABLE IF NOT EXISTS `assignments` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `teacherid` INT NOT NULL,
    `title` VARCHAR(100) NOT NULL COMMENT 'Tiêu đề bài tập',
    `description` VARCHAR(255) NOT NULL COMMENT 'Mô tả bài tập',
    `files` VARCHAR(100) NOT NULL COMMENT 'Đường dẫn bài tập',
    `dueto` DATETIME DEFAULT NULL COMMENT 'Hạn nộp',
    `lastupdate` DATETIME NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'Thời gian sửa cuối cùng',
    `createat` DATETIME NOT NULL DEFAULT current_timestamp() COMMENT 'Thời gian tạo'
);

ALTER TABLE `assignments` ADD CONSTRAINT fk_teacher_assignment FOREIGN KEY (`teacherid`) REFERENCES `users`(`id`);

CREATE TABLE IF NOT EXISTS `submits` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `assignmentid` INT NOT NULL COMMENT 'ID của assignment',
    `usersubmitid` INT NOT NULL COMMENT 'ID học sinh submit',
    `ogfilename` TEXT NOT NULL COMMENT 'Tên file submit',
    `files` TEXT NOT NULL COMMENT 'Đường dẫn file submit',
    `lastupdate` DATETIME NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'Thời gian chỉnh sửa cuối cùng',
    `createat` DATETIME NOT NULL DEFAULT current_timestamp() COMMENT 'Thời gian submit'
);

ALTER TABLE `submits` ADD CONSTRAINT fk_assignment FOREIGN KEY (`assignmentid`) REFERENCES `assignments`(`id`);
ALTER TABLE `submits` ADD CONSTRAINT fk_user_submit FOREIGN KEY (`usersubmitid`) REFERENCES `users`(`id`);

CREATE TABLE IF NOT EXISTS `challenges` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `teacherid` INT NOT NULL,
    `title` TEXT NOT NULL COMMENT 'Tên challenge',
    `files` VARCHAR(100) NOT NULL COMMENT 'Đường dẫn file',
    `hints` TEXT DEFAULT NULL,
    `lastupdate` DATETIME NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'Thời gian chỉnh sửa cuối cùng',
    `createat` DATETIME NOT NULL DEFAULT current_timestamp() COMMENT 'Thời gian tạo'
);

ALTER TABLE `challenges` ADD CONSTRAINT fk_teacher_challenge FOREIGN KEY (`teacherid`) REFERENCES `users`(`id`);

CREATE TABLE IF NOT EXISTS `messages` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `idsend` INT NOT NULL COMMENT 'ID người gửi',
    `idrec` INT NOT NULL COMMENT 'ID người nhận',
    `content` TEXT DEFAULT NULL,
    `lastupdate` DATETIME NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'Thời gian chỉnh sửa cuối cùng',
    `createat` DATETIME NOT NULL DEFAULT current_timestamp() COMMENT 'Thời gian nhắn tin'
);

ALTER TABLE `messages` ADD CONSTRAINT fk_sender FOREIGN KEY (`idsend`) REFERENCES `users`(`id`);
ALTER TABLE `messages` ADD CONSTRAINT fk_receiver FOREIGN KEY (`idrec`) REFERENCES `users`(`id`);