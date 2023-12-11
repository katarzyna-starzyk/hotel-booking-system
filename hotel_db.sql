-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Czas generowania: 11 Gru 2023, 09:08
-- Wersja serwera: 10.4.25-MariaDB
-- Wersja PHP: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Baza danych: `hotel_db`
--

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `bookings`
--

CREATE TABLE `bookings` (
  `booking_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `room_id` int(11) NOT NULL,
  `start_date` datetime NOT NULL,
  `end_date` datetime NOT NULL,
  `final_price` double NOT NULL,
  `rating` int(1) DEFAULT NULL,
  `review` text COLLATE utf8mb4_polish_ci DEFAULT NULL,
  `expired` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_polish_ci;

--
-- Zrzut danych tabeli `bookings`
--

INSERT INTO `bookings` (`booking_id`, `user_id`, `room_id`, `start_date`, `end_date`, `final_price`, `rating`, `review`, `expired`) VALUES
(7, 1, 7, '2023-12-13 12:00:00', '2023-12-14 10:00:00', 80, NULL, NULL, 0),
(8, 1, 11, '2023-12-12 12:00:00', '2023-12-14 10:00:00', 200, NULL, NULL, 0),
(9, 1, 11, '2023-12-19 12:00:00', '2023-12-21 10:00:00', 200, NULL, NULL, 0);

--
-- Wyzwalacze `bookings`
--
DELIMITER $$
CREATE TRIGGER `booking_expired` AFTER UPDATE ON `bookings` FOR EACH ROW BEGIN
	IF NEW.expired = 1 THEN
    	INSERT INTO bookings_archive VALUES
        (OLD.booking_id, OLD.user_id, OLD.room_id, OLD.start_date, OLD.end_date, OLD.final_price);
        DELETE FROM bookings WHERE booking_id = OLD.booking_id;
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `booking_update` BEFORE UPDATE ON `bookings` FOR EACH ROW BEGIN
	IF NEW.start_date != OLD.start_date
    OR NEW.end_date != OLD.end_date THEN
    BEGIN
        DECLARE c INT;
        SELECT COUNT(*) INTO c FROM bookings
        WHERE NEW.start_date < end_date
        AND NEW.end_date > start_date
        AND NEW.booking_id != booking_id;

        IF c > 0 THEN
            SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = "Istnieje już rezerwacja w tym terminie.";
         END IF;

         IF NEW.start_date = NEW.end_date THEN
            SIGNAL SQLSTATE '45001'
            SET MESSAGE_TEXT = "Data zameldowania i wymeldowania nie mogą być takie same.";
         END IF;

        IF NEW.start_date > NEW.end_date THEN
            SIGNAL SQLSTATE '45002'
            SET MESSAGE_TEXT = 'Data zameldowania nie może być po dacie wymeldowania';
        END IF;

        IF NEW.start_date < CURDATE()
        OR NEW.end_date < CURDATE() THEN
            SIGNAL SQLSTATE '45003'
            SET MESSAGE_TEXT = 'Data zameldowania lub wymeldowania nie może być datą dzisiejszą lub wcześniejszą';
        END IF;
    END;
	END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `calculate_final_price` BEFORE INSERT ON `bookings` FOR EACH ROW BEGIN
DECLARE room_price DECIMAL(10,2);
DECLARE days INT;

SELECT price INTO room_price FROM rooms WHERE room_id = NEW.room_id;
SET days = DATEDIFF(NEW.end_date, NEW.start_date);
SET NEW.final_price = room_price * days;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `calculate_final_price_update` BEFORE UPDATE ON `bookings` FOR EACH ROW BEGIN
    DECLARE room_price DECIMAL(10,2);
    DECLARE days INT;

    SELECT price INTO room_price FROM rooms WHERE room_id = NEW.room_id;
    
    SET days = DATEDIFF(NEW.end_date, NEW.start_date);

    SET NEW.final_price = room_price * days;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `bookings_archive`
--

CREATE TABLE `bookings_archive` (
  `booking_id` int(11) NOT NULL,
  `room_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `start_date` datetime NOT NULL,
  `end_date` datetime NOT NULL,
  `price` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_polish_ci;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `hotels`
--

CREATE TABLE `hotels` (
  `hotel_id` int(11) NOT NULL,
  `name` varchar(50) COLLATE utf8mb4_polish_ci NOT NULL,
  `street` varchar(40) COLLATE utf8mb4_polish_ci NOT NULL,
  `building_nr` int(3) NOT NULL,
  `apartment_nr` int(3) DEFAULT NULL,
  `zip_code` char(6) COLLATE utf8mb4_polish_ci NOT NULL,
  `city` varchar(50) COLLATE utf8mb4_polish_ci NOT NULL,
  `type_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_polish_ci;

--
-- Zrzut danych tabeli `hotels`
--

INSERT INTO `hotels` (`hotel_id`, `name`, `street`, `building_nr`, `apartment_nr`, `zip_code`, `city`, `type_id`) VALUES
(1, 'Warszawski', 'Aleje Jerozolimskie', 12, NULL, '00-001', 'Warszawa', 4),
(2, 'Krakowski', 'Św. Tomasza', 21, 101, '30-001', 'Kraków', 5),
(3, 'Wrocławski', 'Świdnicka', 33, 202, '50-001', 'Wrocław', 6),
(4, 'Elegancki', 'Elegancka', 45, NULL, '00-001', 'Warszawa', 7),
(5, 'Zielonka', 'Zielona', 54, 303, '50-001', 'Wrocław', 8),
(7, 'Plaza', 'Centrum', 76, 404, '00-001', 'Warszawa', 5),
(8, 'Plaza', 'Łobzowska', 89, NULL, '30-001', 'Kraków', 6),
(9, 'Panoramiczny', 'Panoramiczna', 98, 505, '50-001', 'Wrocław', 4),
(10, 'Luksusowy', 'Krakowska', 10, NULL, '00-001', 'Warszawa', 7),
(11, 'Na Pagórku', 'Krakowska', 22, 606, '30-001', 'Kraków', 8),
(12, 'Nowoczesny', 'Nowoczesna', 31, NULL, '50-001', 'Wrocław', 5),
(13, 'Nowogrodzki', 'Nowogrodzka', 43, 707, '00-001', 'Warszawa', 6),
(14, 'Luksusowy', 'Butikowa', 56, NULL, '50-001', 'Wrocław', 7),
(15, 'Elegancki', 'Elegancka', 65, 808, '30-001', 'Kraków', 8),
(16, 'Widokówka', 'Widokowa', 78, NULL, '00-001', 'Warszawa', 4),
(19, 'Luksusowy', 'Panoramiczna', 14, 101, '00-001', 'Warszawa', 7),
(20, 'Na Parkowej', 'Parkowa', 26, NULL, '30-001', 'Kraków', 8),
(21, 'Miejski', 'Miejska', 35, 202, '50-001', 'Wrocław', 4),
(22, 'Na Parkowej', 'Parkowa', 47, NULL, '00-001', 'Warszawa', 5),
(23, 'Klasyczny', 'Klasyczna', 58, 303, '30-001', 'Kraków', 6),
(24, 'Riverside', 'Krakowska', 67, NULL, '50-001', 'Wrocław', 7),
(25, 'Cisza W Mieście', 'Miejska', 79, 404, '00-001', 'Warszawa', 8),
(26, 'Zamkowy', 'Zamkowa', 88, NULL, '30-001', 'Kraków', 4),
(27, 'Harmonia', 'Harmonijna', 97, 505, '50-001', 'Wrocław', 5),
(28, 'Królewskie Rezydencje', 'Centrum', 11, NULL, '00-001', 'Warszawa', 6),
(29, 'Luksus i komfort', 'Komfortowa', 23, 606, '30-001', 'Kraków', 7),
(30, 'Centralny', 'Centrum', 32, NULL, '50-001', 'Wrocław', 8),
(31, 'Pod Lipami', 'Lipowa', 12, NULL, '00-001', 'Warszawa', 1),
(32, 'Krakowiak', 'Kazimierza Wielkiego', 21, 101, '30-001', 'Kraków', 1),
(33, 'Świdnicki', 'Świdnicka', 33, 202, '50-001', 'Wrocław', 1),
(34, 'Studencki', 'Łazienkowska', 45, NULL, '00-001', 'Warszawa', 2),
(35, 'Studencki', 'Grunwaldzka', 54, 303, '50-001', 'Wrocław', 2),
(36, 'Studencki', 'Browarna', 67, NULL, '30-001', 'Kraków', 2),
(37, 'Centrum', 'Nowogrodzka', 76, 404, '00-001', 'Warszawa', 1),
(38, 'Centrum', 'Św. Tomasza', 89, NULL, '30-001', 'Kraków', 1),
(40, 'Dworzec', 'Aleje Jerozolimskie', 10, NULL, '00-001', 'Warszawa', 3),
(41, 'Dworzec', 'Basztowa', 22, 606, '30-001', 'Kraków', 3),
(42, 'Dworzec', 'Sucha', 31, NULL, '50-001', 'Wrocław', 3),
(43, 'Nowoczesny', 'Świętokrzyska', 43, 707, '00-001', 'Warszawa', 1),
(44, 'Nowoczesny', 'Łobzowska', 56, NULL, '30-001', 'Kraków', 1),
(45, 'Nowoczesny', 'Sukiennice', 65, 808, '50-001', 'Wrocław', 1),
(46, 'Studencki z Widokiem', 'Krakowskie Przedmieście', 78, NULL, '00-001', 'Warszawa', 2),
(47, 'Studencki z Widokiem', 'Starowiślna', 87, 909, '30-001', 'Kraków', 2),
(48, 'Studencki z Widokiem i', 'Rynek', 99, NULL, '50-001', 'Wrocław', 2),
(49, 'Przy Parku Warszawskim', 'Parkowa', 14, 101, '00-001', 'Warszawa', 1),
(50, 'Przy Parku Krakowskim', 'Planty', 26, NULL, '30-001', 'Kraków', 1),
(51, 'Przy Parku Wrocławskim', 'Park Staromiejski', 35, 202, '50-001', 'Wrocław', 1),
(52, 'Studencki Centrum', 'Żurawia', 47, NULL, '00-001', 'Warszawa', 2),
(53, 'Studencki Centrum', 'Podwale', 58, 303, '30-001', 'Kraków', 2),
(54, 'Studencki Centrum', 'Sądowa', 67, NULL, '50-001', 'Wrocław', 2),
(55, 'Harmonijny', 'Targowa', 79, 404, '00-001', 'Warszawa', 1),
(56, 'Harmonijny', 'Westerplatte', 88, NULL, '30-001', 'Kraków', 1),
(57, 'Harmonijny', 'Kazimierza Wielkiego', 97, 505, '50-001', 'Wrocław', 1),
(58, 'Studencki Królewski', 'Królewska', 11, NULL, '00-001', 'Warszawa', 2),
(59, 'Studencki Królewski', 'Franciszkańska', 23, 606, '30-001', 'Kraków', 2),
(60, 'Studencki Królewski', 'Świdnicka', 32, NULL, '50-001', 'Wrocław', 2),
(85, 'Studencki Mieszczański', 'Sezamkowa', 44, NULL, '30-001', 'Kraków', 1);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `hotelstypes`
--

CREATE TABLE `hotelstypes` (
  `type_id` int(11) NOT NULL,
  `type` varchar(50) COLLATE utf8mb4_polish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_polish_ci;

--
-- Zrzut danych tabeli `hotelstypes`
--

INSERT INTO `hotelstypes` (`type_id`, `type`) VALUES
(1, 'Hostel'),
(2, 'Hotel'),
(3, 'Hotel 2*'),
(4, 'Hotel 3*'),
(5, 'Hotel 4*'),
(6, 'Hotel 5*'),
(7, 'Apartament'),
(8, 'Domek letniskowy');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `rooms`
--

CREATE TABLE `rooms` (
  `room_id` int(11) NOT NULL,
  `hotel_id` int(11) NOT NULL,
  `number` int(3) NOT NULL,
  `beds` int(2) NOT NULL,
  `max_guests` int(2) NOT NULL,
  `price` float NOT NULL,
  `available` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_polish_ci;

--
-- Zrzut danych tabeli `rooms`
--

INSERT INTO `rooms` (`room_id`, `hotel_id`, `number`, `beds`, `max_guests`, `price`, `available`) VALUES
(1, 31, 101, 8, 8, 100, 1),
(2, 31, 102, 16, 16, 80, 1),
(3, 31, 103, 4, 4, 150, 1),
(4, 31, 104, 8, 8, 100, 1),
(5, 31, 105, 16, 16, 80, 1),
(6, 32, 101, 8, 8, 100, 1),
(7, 32, 102, 16, 16, 80, 1),
(8, 32, 103, 4, 4, 150, 1),
(9, 32, 104, 8, 8, 100, 1),
(10, 32, 105, 16, 16, 80, 1),
(11, 33, 101, 8, 8, 100, 1),
(12, 33, 102, 16, 16, 80, 1),
(13, 33, 103, 4, 4, 150, 1),
(14, 33, 104, 8, 8, 100, 1),
(15, 33, 105, 16, 16, 80, 1),
(16, 37, 101, 8, 8, 100, 1),
(17, 37, 102, 16, 16, 80, 1),
(18, 37, 103, 4, 4, 150, 1),
(19, 37, 104, 8, 8, 100, 1),
(20, 37, 105, 16, 16, 80, 1),
(21, 38, 101, 8, 8, 100, 1),
(22, 38, 102, 16, 16, 80, 1),
(23, 38, 103, 4, 4, 150, 1),
(24, 38, 104, 8, 8, 100, 1),
(25, 38, 105, 16, 16, 80, 1),
(31, 43, 101, 8, 8, 100, 1),
(32, 43, 102, 16, 16, 80, 1),
(33, 43, 103, 4, 4, 150, 1),
(34, 43, 104, 8, 8, 100, 1),
(35, 43, 105, 16, 16, 80, 1),
(36, 44, 101, 8, 8, 100, 1),
(37, 44, 102, 16, 16, 80, 1),
(38, 44, 103, 4, 4, 150, 1),
(39, 44, 104, 8, 8, 100, 1),
(40, 44, 105, 16, 16, 80, 1),
(41, 45, 101, 8, 8, 100, 1),
(42, 45, 102, 16, 16, 80, 1),
(43, 45, 103, 4, 4, 150, 1),
(44, 45, 104, 8, 8, 100, 1),
(45, 45, 105, 16, 16, 80, 1),
(46, 49, 101, 8, 8, 100, 1),
(47, 49, 102, 16, 16, 80, 1),
(48, 49, 103, 4, 4, 150, 1),
(49, 49, 104, 8, 8, 100, 1),
(50, 49, 105, 16, 16, 80, 1),
(51, 50, 101, 8, 8, 100, 1),
(52, 50, 102, 16, 16, 80, 1),
(53, 50, 103, 4, 4, 150, 1),
(54, 50, 104, 8, 8, 100, 1),
(55, 50, 105, 16, 16, 80, 1),
(56, 51, 101, 8, 8, 100, 1),
(57, 51, 102, 16, 16, 80, 1),
(58, 51, 103, 4, 4, 150, 1),
(59, 51, 104, 8, 8, 100, 1),
(60, 51, 105, 16, 16, 80, 1),
(61, 55, 101, 8, 8, 100, 1),
(62, 55, 102, 16, 16, 80, 1),
(63, 55, 103, 4, 4, 150, 1),
(64, 55, 104, 8, 8, 100, 1),
(65, 55, 105, 16, 16, 80, 1),
(66, 56, 101, 8, 8, 100, 1),
(67, 56, 102, 16, 16, 80, 1),
(68, 56, 103, 4, 4, 150, 1),
(69, 56, 104, 8, 8, 100, 1),
(70, 56, 105, 16, 16, 80, 1),
(71, 57, 101, 8, 8, 100, 1),
(72, 57, 102, 16, 16, 80, 1),
(73, 57, 103, 4, 4, 150, 1),
(74, 57, 104, 8, 8, 100, 1),
(75, 57, 105, 16, 16, 80, 1),
(76, 34, 101, 4, 4, 400, 1),
(77, 34, 102, 2, 2, 250, 1),
(78, 34, 103, 1, 2, 250, 1),
(79, 34, 104, 3, 2, 350, 1),
(80, 34, 105, 4, 3, 400, 1),
(81, 35, 101, 4, 4, 400, 1),
(82, 35, 102, 2, 2, 250, 1),
(83, 35, 103, 1, 2, 250, 1),
(84, 35, 104, 3, 2, 350, 1),
(85, 35, 105, 4, 3, 400, 1),
(86, 36, 101, 4, 4, 400, 1),
(87, 36, 102, 2, 2, 250, 1),
(88, 36, 103, 1, 2, 250, 1),
(89, 36, 104, 3, 2, 350, 1),
(90, 36, 105, 4, 3, 400, 1),
(91, 46, 101, 4, 4, 400, 1),
(92, 46, 102, 2, 2, 250, 1),
(93, 46, 103, 1, 2, 250, 1),
(94, 46, 104, 3, 2, 350, 1),
(95, 46, 105, 4, 3, 400, 1),
(96, 47, 101, 4, 4, 400, 1),
(97, 47, 102, 2, 2, 250, 1),
(98, 47, 103, 1, 2, 250, 1),
(99, 47, 104, 3, 2, 350, 1),
(100, 47, 105, 4, 3, 400, 1),
(101, 48, 101, 4, 4, 400, 1),
(102, 48, 102, 2, 2, 250, 1),
(103, 48, 103, 1, 2, 250, 1),
(104, 48, 104, 3, 2, 350, 1),
(105, 48, 105, 4, 3, 400, 1),
(106, 52, 101, 4, 4, 400, 1),
(107, 52, 102, 2, 2, 250, 1),
(108, 52, 103, 1, 2, 250, 1),
(109, 52, 104, 3, 2, 350, 1),
(110, 52, 105, 4, 3, 400, 1),
(111, 53, 101, 4, 4, 400, 1),
(112, 53, 102, 2, 2, 250, 1),
(113, 53, 103, 1, 2, 250, 1),
(114, 53, 104, 3, 2, 350, 1),
(115, 53, 105, 4, 3, 400, 1),
(116, 54, 101, 4, 4, 400, 1),
(117, 54, 102, 2, 2, 250, 1),
(118, 54, 103, 1, 2, 250, 1),
(119, 54, 104, 3, 2, 350, 1),
(120, 54, 105, 4, 3, 400, 1),
(121, 58, 101, 4, 4, 400, 1),
(122, 58, 102, 2, 2, 250, 1),
(123, 58, 103, 1, 2, 250, 1),
(124, 58, 104, 3, 2, 350, 1),
(125, 58, 105, 4, 3, 400, 1),
(126, 59, 101, 4, 4, 400, 1),
(127, 59, 102, 2, 2, 250, 1),
(128, 59, 103, 1, 2, 250, 1),
(129, 59, 104, 3, 2, 350, 1),
(130, 59, 105, 4, 3, 400, 1),
(131, 60, 101, 4, 4, 400, 1),
(132, 60, 102, 2, 2, 250, 1),
(133, 60, 103, 1, 2, 250, 1),
(134, 60, 104, 3, 2, 350, 1),
(135, 60, 105, 4, 3, 400, 1),
(136, 40, 101, 2, 2, 600, 1),
(137, 40, 102, 1, 2, 600, 1),
(138, 40, 103, 2, 3, 800, 1),
(139, 40, 104, 3, 4, 1000, 1),
(140, 40, 105, 4, 4, 1000, 1),
(141, 41, 101, 2, 2, 600, 1),
(142, 41, 102, 1, 2, 600, 1),
(143, 41, 103, 2, 3, 800, 1),
(144, 41, 104, 3, 4, 1000, 1),
(145, 41, 105, 4, 4, 1000, 1),
(146, 42, 101, 2, 2, 600, 1),
(147, 42, 102, 1, 2, 600, 1),
(148, 42, 103, 2, 3, 800, 1),
(149, 42, 104, 3, 4, 1000, 1),
(150, 42, 105, 4, 4, 1000, 1),
(151, 1, 101, 2, 2, 1200, 1),
(152, 1, 102, 1, 2, 1200, 1),
(153, 1, 103, 2, 3, 1800, 1),
(154, 1, 104, 3, 4, 2000, 1),
(155, 1, 105, 4, 4, 2000, 1),
(161, 9, 101, 2, 2, 1200, 1),
(162, 9, 102, 1, 2, 1200, 1),
(163, 9, 103, 2, 3, 1800, 1),
(164, 9, 104, 3, 4, 2000, 1),
(165, 9, 105, 4, 4, 2000, 1),
(166, 16, 101, 2, 2, 1200, 1),
(167, 16, 102, 1, 2, 1200, 1),
(168, 16, 103, 2, 3, 1800, 1),
(169, 16, 104, 3, 4, 2000, 1),
(170, 16, 105, 4, 4, 2000, 1),
(171, 21, 101, 2, 2, 1200, 1),
(172, 21, 102, 1, 2, 1200, 1),
(173, 21, 103, 2, 3, 1800, 1),
(174, 21, 104, 3, 4, 2000, 1),
(175, 21, 105, 4, 4, 2000, 1),
(176, 26, 101, 2, 2, 1200, 1),
(177, 26, 102, 1, 2, 1200, 1),
(178, 26, 103, 2, 3, 1800, 1),
(179, 26, 104, 3, 4, 2000, 1),
(180, 26, 105, 4, 4, 2000, 1),
(181, 2, 101, 2, 2, 2000, 1),
(182, 2, 102, 1, 2, 2000, 1),
(183, 2, 103, 2, 3, 3000, 1),
(184, 2, 104, 3, 4, 4000, 1),
(185, 2, 105, 4, 4, 4000, 1),
(186, 2, 106, 1, 1, 1700, 1),
(187, 7, 101, 2, 2, 2000, 1),
(188, 7, 102, 1, 2, 2000, 1),
(189, 7, 103, 2, 3, 3000, 1),
(190, 7, 104, 3, 4, 4000, 1),
(191, 7, 105, 4, 4, 4000, 1),
(192, 7, 106, 1, 1, 1700, 1),
(193, 12, 101, 2, 2, 2000, 1),
(194, 12, 102, 1, 2, 2000, 1),
(195, 12, 103, 2, 3, 3000, 1),
(196, 12, 104, 3, 4, 4000, 1),
(197, 12, 105, 4, 4, 4000, 1),
(198, 12, 106, 1, 1, 1700, 1),
(205, 22, 101, 2, 2, 2000, 1),
(206, 22, 102, 1, 2, 2000, 1),
(207, 22, 103, 2, 3, 3000, 1),
(208, 22, 104, 3, 4, 4000, 1),
(209, 22, 105, 4, 4, 4000, 1),
(210, 22, 106, 1, 1, 1700, 1),
(211, 27, 101, 2, 2, 2000, 1),
(212, 27, 102, 1, 2, 2000, 1),
(213, 27, 103, 2, 3, 3000, 1),
(214, 27, 104, 3, 4, 4000, 1),
(215, 27, 105, 4, 4, 4000, 1),
(216, 27, 106, 1, 1, 1700, 1),
(217, 3, 101, 1, 1, 2000, 1),
(218, 3, 102, 1, 2, 3000, 1),
(219, 3, 103, 2, 3, 4000, 1),
(220, 3, 104, 3, 4, 5000, 1),
(221, 3, 105, 4, 4, 5000, 1),
(222, 3, 106, 2, 2, 3000, 1),
(223, 8, 101, 1, 1, 2000, 1),
(224, 8, 102, 1, 2, 3000, 1),
(225, 8, 103, 2, 3, 4000, 1),
(226, 8, 104, 3, 4, 5000, 1),
(227, 8, 105, 4, 4, 5000, 1),
(228, 8, 106, 2, 2, 3000, 1),
(229, 13, 101, 1, 1, 2000, 1),
(230, 13, 102, 1, 2, 3000, 1),
(231, 13, 103, 2, 3, 4000, 1),
(232, 13, 104, 3, 4, 5000, 1),
(233, 13, 105, 4, 4, 5000, 1),
(234, 13, 106, 2, 2, 3000, 1),
(241, 23, 101, 1, 1, 2000, 1),
(242, 23, 102, 1, 2, 3000, 1),
(243, 23, 103, 2, 3, 4000, 1),
(244, 23, 104, 3, 4, 5000, 1),
(245, 23, 105, 4, 4, 5000, 1),
(246, 23, 106, 2, 2, 3000, 1),
(247, 28, 101, 1, 1, 2000, 1),
(248, 28, 102, 1, 2, 3000, 1),
(249, 28, 103, 2, 3, 4000, 1),
(250, 28, 104, 3, 4, 5000, 1),
(251, 28, 105, 4, 4, 5000, 1),
(252, 28, 106, 2, 2, 3000, 1),
(253, 4, 101, 4, 8, 5000, 1),
(254, 4, 102, 4, 4, 4000, 1),
(255, 4, 103, 2, 2, 3000, 1),
(256, 4, 104, 2, 4, 4000, 1),
(257, 10, 101, 4, 8, 5000, 1),
(258, 10, 102, 4, 4, 4000, 1),
(259, 10, 103, 2, 2, 3000, 1),
(260, 10, 104, 2, 4, 4000, 1),
(261, 14, 101, 4, 8, 5000, 1),
(262, 14, 102, 4, 4, 4000, 1),
(263, 14, 103, 2, 2, 3000, 1),
(264, 14, 104, 2, 4, 4000, 1),
(265, 19, 101, 4, 8, 5000, 1),
(266, 19, 102, 4, 4, 4000, 1),
(267, 19, 103, 2, 2, 3000, 1),
(268, 19, 104, 2, 4, 4000, 1),
(269, 24, 101, 4, 8, 5000, 1),
(270, 24, 102, 4, 4, 4000, 1),
(271, 24, 103, 2, 2, 3000, 1),
(272, 24, 104, 2, 4, 4000, 1),
(273, 29, 101, 4, 8, 5000, 1),
(274, 29, 102, 4, 4, 4000, 1),
(275, 29, 103, 2, 2, 3000, 1),
(276, 29, 104, 2, 4, 4000, 1),
(277, 5, 101, 4, 8, 5000, 1),
(278, 5, 102, 4, 4, 4000, 1),
(279, 5, 103, 2, 2, 3000, 1),
(280, 5, 104, 2, 4, 4000, 1),
(281, 11, 101, 4, 8, 5000, 1),
(282, 11, 102, 4, 4, 4000, 1),
(283, 11, 103, 2, 2, 3000, 1),
(284, 11, 104, 2, 4, 4000, 1),
(285, 15, 101, 4, 8, 5000, 1),
(286, 15, 102, 4, 4, 4000, 1),
(287, 15, 103, 2, 2, 3000, 1),
(288, 15, 104, 2, 4, 4000, 1),
(289, 20, 101, 4, 8, 5000, 1),
(290, 20, 102, 4, 4, 4000, 1),
(291, 20, 103, 2, 2, 3000, 1),
(292, 20, 104, 2, 4, 4000, 1),
(293, 25, 101, 4, 8, 5000, 1),
(294, 25, 102, 4, 4, 4000, 1),
(295, 25, 103, 2, 2, 3000, 1),
(296, 25, 104, 2, 4, 4000, 1),
(297, 30, 101, 4, 8, 5000, 1),
(298, 30, 102, 4, 4, 4000, 1),
(299, 30, 103, 2, 2, 3000, 1),
(300, 30, 104, 2, 4, 4000, 1);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(50) COLLATE utf8mb4_polish_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_polish_ci NOT NULL,
  `email` varchar(50) COLLATE utf8mb4_polish_ci NOT NULL,
  `first_name` varchar(50) COLLATE utf8mb4_polish_ci NOT NULL,
  `last_name` varchar(50) COLLATE utf8mb4_polish_ci NOT NULL,
  `birth_date` date NOT NULL DEFAULT current_timestamp(),
  `isAdmin` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_polish_ci;

--
-- Zrzut danych tabeli `users`
--

INSERT INTO `users` (`user_id`, `username`, `password`, `email`, `first_name`, `last_name`, `birth_date`, `isAdmin`) VALUES
(1, 'user1', '$2y$10$um.Itobxe2r/Sc5XsB9N6eOz8bhfa5SKwgoX1z35AGVt38aJMy1Hy', 'user1@gmail.com', 'Katarzyna', 'Starzyk', '2001-09-11', 0),
(2, 'user2', '$2y$10$Y0RLelOYMLv3TvLEJicMC.Yz1NX//uJJLMFBTkstdEFHrD47MWWKa', 'user2@gmail.com', 'Admin', 'Admin', '2000-09-11', 2);

--
-- Indeksy dla zrzutów tabel
--

--
-- Indeksy dla tabeli `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`booking_id`),
  ADD KEY `bookings_ibfk_1` (`user_id`),
  ADD KEY `bookings_ibfk_2` (`room_id`);

--
-- Indeksy dla tabeli `bookings_archive`
--
ALTER TABLE `bookings_archive`
  ADD PRIMARY KEY (`booking_id`);

--
-- Indeksy dla tabeli `hotels`
--
ALTER TABLE `hotels`
  ADD PRIMARY KEY (`hotel_id`),
  ADD KEY `hotels_ibfk_2` (`type_id`);

--
-- Indeksy dla tabeli `hotelstypes`
--
ALTER TABLE `hotelstypes`
  ADD PRIMARY KEY (`type_id`);

--
-- Indeksy dla tabeli `rooms`
--
ALTER TABLE `rooms`
  ADD PRIMARY KEY (`room_id`),
  ADD KEY `rooms_ibfk_1` (`hotel_id`);

--
-- Indeksy dla tabeli `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT dla zrzuconych tabel
--

--
-- AUTO_INCREMENT dla tabeli `bookings`
--
ALTER TABLE `bookings`
  MODIFY `booking_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT dla tabeli `hotels`
--
ALTER TABLE `hotels`
  MODIFY `hotel_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=86;

--
-- AUTO_INCREMENT dla tabeli `hotelstypes`
--
ALTER TABLE `hotelstypes`
  MODIFY `type_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT dla tabeli `rooms`
--
ALTER TABLE `rooms`
  MODIFY `room_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=301;

--
-- AUTO_INCREMENT dla tabeli `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Ograniczenia dla zrzutów tabel
--

--
-- Ograniczenia dla tabeli `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `bookings_ibfk_2` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`room_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Ograniczenia dla tabeli `hotels`
--
ALTER TABLE `hotels`
  ADD CONSTRAINT `hotels_ibfk_2` FOREIGN KEY (`type_id`) REFERENCES `hotelstypes` (`type_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Ograniczenia dla tabeli `rooms`
--
ALTER TABLE `rooms`
  ADD CONSTRAINT `rooms_ibfk_1` FOREIGN KEY (`hotel_id`) REFERENCES `hotels` (`hotel_id`) ON DELETE CASCADE ON UPDATE CASCADE;

DELIMITER $$
--
-- Zdarzenia
--
CREATE DEFINER=`root`@`localhost` EVENT `daily_trigger_event` ON SCHEDULE EVERY 30 SECOND STARTS '2023-12-09 02:02:43' ON COMPLETION NOT PRESERVE ENABLE DO UPDATE bookings SET expired = true WHERE end_date < CURDATE()$$

DELIMITER ;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
