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
    `name` VARCHAR(31) NOT NULL,
    `description` TINYTEXT NOT NULL,

    UNIQUE (`name`)
);

CREATE TABLE `movies` (
    `id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `title` VARCHAR(127) NOT NULL,
    `rating` ENUM('A', '13', 'R') NOT NULL,
    `original_title` VARCHAR(127) NOT NULL,
    `producer_studio` VARCHAR(127) NOT NULL,
    `release_date` DATE NOT NULL,
    `synopsis` TEXT NOT NULL,
    `runtime` SMALLINT UNSIGNED NOT NULL,
    `director` VARCHAR(127) NOT NULL,
    `significant_cast_1` VARCHAR(127) NOT NULL,
    `significant_cast_2` VARCHAR(127) NOT NULL,
    `significant_cast_3` VARCHAR(127) NOT NULL
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

CREATE TABLE `locations` (
    `id` TINYINT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `city` VARCHAR(31) NOT NULL,
    `address` VARCHAR(127) NOT NULL
);

CREATE TABLE `theaters` (
    `id` SMALLINT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `location_id` TINYINT UNSIGNED NOT NULL,
    `name` VARCHAR(63) NOT NULL,

    FOREIGN KEY (`location_id`) REFERENCES `locations`(`id`)
        ON UPDATE RESTRICT
        ON DELETE RESTRICT
);

CREATE TABLE `screening_features` (
    `id` TINYINT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `description` VARCHAR(31) NOT NULL
);

CREATE TABLE `repertoire` (
    `id` BIGINT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `movie_id` INT UNSIGNED NOT NULL,
    `theater_id` SMALLINT UNSIGNED NOT NULL,
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
    `price_child` INT UNSIGNED DEFAULT NULL
);

CREATE TABLE `theater_seating` (
    `id` BIGINT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `theater_id` SMALLINT UNSIGNED NOT NULL,
    `seat_type_id` TINYINT UNSIGNED NOT NULL,
    `active` TINYINT NOT NULL DEFAULT 1,
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
    `ticket_code` VARCHAR(15) NOT NULL,
    `user_id` INT UNSIGNED NOT NULL,
    `comment` VARCHAR(63) NOT NULL,
    `discount_club_id` TINYINT UNSIGNED,
    `price` INT UNSIGNED NOT NULL,
    `booked_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `paid_at` DATETIME DEFAULT NULL,

    UNIQUE (`ticket_code`),
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`)
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
