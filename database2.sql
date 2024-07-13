CREATE DATABASE `skop`;
USE `skop`;

CREATE TABLE `discount_clubs` (
    `id` TINYINT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `name` VARCHAR(31) NOT NULL,
    `discount` TINYINT NOT NULL
);

CREATE TABLE `users` (
    `id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `email` VARCHAR(63) NOT NULL,
    `password` VARCHAR(63) NOT NULL,
    `first_name` VARCHAR(31) NOT NULL,
    `last_name` VARCHAR(31) NOT NULL,
    `permissions` TINYINT NOT NULL,
    `discount_club_id` TINYINT UNSIGNED DEFAULT NULL,

    UNIQUE (`email`),
    FOREIGN KEY (`discount_club_id`) REFERENCES `discount_clubs`(`id`)
        ON UPDATE RESTRICT
        ON DELETE RESTRICT
);

CREATE TABLE `genres` (
    `id` SMALLINT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `name` VARCHAR(31) NOT NULL

    UNIQUE (`name`)
);

CREATE TABLE `movies` (
    `id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `title` VARCHAR(127) NOT NULL,
    `rating` ENUM('A', '13', 'R') NOT NULL,
    `original_title` VARCHAR(127) NOT NULL,
    `release_date` DATE NOT NULL,
    `synopsis` TEXT NOT NULL,
    `runtime` SMALLINT UNSIGNED NOT NULL,
    `director` VARCHAR(127) NOT NULL,
    `significant_cast_1` VARCHAR(127) NOT NULL,
    `significant_cast_2` VARCHAR(127) NOT NULL,
    `significant_cast_3` VARCHAR(127) NOT NULL,

    UNIQUE (`title`)
);

CREATE TABLE `movie_genres` (
    `movie_id` INT UNSIGNED NOT NULL,
    `genre_id` SMALLINT UNSIGNED NOT NULL,

    PRIMARY KEY (`movie_id`, `genre_id`),
    FOREIGN KEY (`movie_id`) REFERENCES `movies`(`id`)
        ON UPDATE CASCADE
        ON DELETE CASCADE,
    FOREIGN KEY (`genre_id`) REFERENCES `genres`(`id`)
        ON UPDATE CASCADE
        ON DELETE CASCADE
);

CREATE TABLE `theaters` (
    `id` TINYINT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `name` VARCHAR(63) NOT NULL,
    `active` TINYINT(1) NOT NULL DEFAULT 1,

    UNIQUE (`name`),
    INDEX (`active`)
);

CREATE TABLE `screening_features` (
    `id` TINYINT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `description` VARCHAR(31) NOT NULL,

    UNIQUE (`description`)
);

CREATE TABLE `repertoire` (
    `id` BIGINT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `movie_id` INT UNSIGNED NOT NULL,
    `theater_id` TINYINT UNSIGNED NOT NULL,
    `screening_start` DATETIME NOT NULL,

    INDEX (`movie_id`),
    INDEX (`theater_id`),
    INDEX (`screening_start`),

    FOREIGN KEY (`movie_id`) REFERENCES `movies`(`id`)
        ON UPDATE RESTRICT
        ON DELETE RESTRICT,
    FOREIGN KEY (`theater_id`) REFERENCES `theaters`(`id`)
        ON UPDATE RESTRICT
        ON DELETE RESTRICT
);

CREATE TABLE `repertoire_features` (
    `screening_id` BIGINT UNSIGNED NOT NULL,
    `feature_id` TINYINT UNSIGNED NOT NULL,

    PRIMARY KEY (`screening_id`, `feature_id`),
    FOREIGN KEY (`screening_id`) REFERENCES `repertoire`(`id`)
        ON UPDATE RESTRICT
        ON DELETE RESTRICT,
    FOREIGN KEY (`feature_id`) REFERENCES `screening_features`(`id`)
        ON UPDATE RESTRICT
        ON DELETE RESTRICT
);

CREATE TABLE `theater_seat_types` (
    `id` TINYINT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `name` VARCHAR(31) NOT NULL,
    `price_adult` INT UNSIGNED NOT NULL,
    `price_adult_weekend` INT UNSIGNED DEFAULT NULL,
    `price_child` INT UNSIGNED DEFAULT NULL,
    `price_adult_weekend` INT UNSIGNED NOT NULL,
    `extra` VARCHAR(255) DEFAULT NULL,

    UNIQUE (`name`)
);

CREATE TABLE `theater_seating` (
    `id` BIGINT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `theater_id` TINYINT UNSIGNED NOT NULL,
    `seat_type_id` TINYINT UNSIGNED NOT NULL,
    `active` TINYINT(1) NOT NULL DEFAULT 1,
    `row` SMALLINT NOT NULL,
    `column` SMALLINT NOT NULL,

    INDEX (`active`, `theater_id`),
    FOREIGN KEY (`theater_id`) REFERENCES `theaters`(`id`)
        ON UPDATE RESTRICT
        ON DELETE RESTRICT,
    FOREIGN KEY (`seat_type_id`) REFERENCES `theater_seat_types`(`id`)
        ON UPDATE RESTRICT
        ON DELETE RESTRICT
);

CREATE TABLE `tickets` (
    `id` BIGINT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `user_id` INT UNSIGNED NOT NULL,
    `repertoire_id` BIGINT UNSIGNED NOT NULL,
    `ticket_code` VARCHAR(15) NOT NULL,
    `comment` VARCHAR(63) DEFAULT NULL,
    `discount_club_id` TINYINT UNSIGNED,
    `price` INT UNSIGNED NOT NULL,
    `booked_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `paid_at` DATETIME DEFAULT NULL,

    UNIQUE (`ticket_code`),
    UNIQUE (`user_id`, `repertoire_id`),
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`)
        ON UPDATE RESTRICT
        ON DELETE RESTRICT,
    FOREIGN KEY (`repertoire_id`) REFERENCES `repertoire`(`id`)
        ON UPDATE RESTRICT
        ON DELETE RESTRICT,
    FOREIGN KEY (`discount_club_id`) REFERENCES `discount_clubs`(`id`)
        ON UPDATE RESTRICT
        ON DELETE RESTRICT
);

CREATE TABLE `ticket_seats` (
    `ticket_id` BIGINT UNSIGNED NOT NULL,
    `seat_id` BIGINT UNSIGNED NOT NULL,

    PRIMARY KEY (`ticket_id`, `seat_id`),
    FOREIGN KEY (`ticket_id`) REFERENCES `tickets`(`id`)
        ON UPDATE CASCADE
        ON DELETE CASCADE,
    FOREIGN KEY (`seat_id`) REFERENCES `theater_seating`(`id`)
        ON UPDATE RESTRICT
        ON DELETE RESTRICT
);
