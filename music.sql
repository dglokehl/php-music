-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:8889
-- Generation Time: Oct 31, 2025 at 08:42 AM
-- Server version: 8.0.40
-- PHP Version: 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `music`
--

-- --------------------------------------------------------

--
-- Table structure for table `artists`
--

CREATE TABLE `artists` (
  `id` int NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `artists`
--

INSERT INTO `artists` (`id`, `name`) VALUES
(1, 'Deafheaven'),
(2, 'Young Thug'),
(3, 'Bladee'),
(5, 'Yung Lean'),
(6, 'Playboi Carti'),
(7, 'Fakemink'),
(8, 'Chief Keef'),
(9, 'Future'),
(10, 'Lil Uzi Vert'),
(11, 'Orchid'),
(12, 'Nails'),
(13, 'SpaceGhostPurrp'),
(14, 'Lil Ugly Mane'),
(15, 'Iglooghost'),
(16, 'Gulch'),
(17, 'Charli XCX'),
(18, 'ECCO2K'),
(19, 'Thaiboy Digital'),
(20, 'yeule'),
(21, 'xaviersobased'),
(22, 'SALEM'),
(23, 'Sunami');

-- --------------------------------------------------------

--
-- Table structure for table `genres`
--

CREATE TABLE `genres` (
  `id` int NOT NULL,
  `name` varchar(75) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `genres`
--

INSERT INTO `genres` (`id`, `name`) VALUES
(1, 'Hip-Hop'),
(2, 'Trap'),
(3, 'Rage'),
(4, 'Black Metal'),
(5, 'Cloud Rap'),
(6, 'Metalcore'),
(7, 'Deathcore'),
(8, 'Blackgaze'),
(9, 'Shoegaze'),
(10, 'Chicago Drill'),
(11, 'Experimental Hip-Hop'),
(12, 'Dream Pop'),
(13, 'Screamo'),
(14, 'Grindcore'),
(15, 'Powerviolence'),
(16, 'Pop'),
(17, 'Jerk'),
(18, 'Hyperpop'),
(19, 'Hardcore'),
(20, 'UK Bass'),
(21, 'Electronic');

-- --------------------------------------------------------

--
-- Table structure for table `releases`
--

CREATE TABLE `releases` (
  `id` int NOT NULL,
  `title` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `release_year` int NOT NULL,
  `release_type` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `artwork_url` varchar(300) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `releases`
--

INSERT INTO `releases` (`id`, `title`, `release_year`, `release_type`, `artwork_url`) VALUES
(1, 'Black Brick', 2019, 'single', 'blackbrick.com'),
(3, 'Warlord', 2016, 'album', 'warlord.com'),
(4, 'Barter 6', 2015, 'album', 'barter6.com'),
(5, 'Red Light', 2018, 'album', 'redlight.com'),
(6, 'Stranger', 2017, 'album', 'stranger.com'),
(7, 'Psykos', 2024, 'album', 'psykos.com'),
(8, 'London\'s Saviour', 2023, 'album', 'londonssaviour.com'),
(9, 'Lei Line Eon', 2021, 'album', 'leilineeon.com'),
(10, 'brat', 2024, 'album', 'brat.com'),
(11, 'Whole Lotta Red', 2020, 'album', 'wholelottared.com'),
(12, 'Almighty So', 2013, 'mixtape', 'almightyso.com'),
(13, 'Chaos Is Me', 1999, 'album', 'chaosisme.com'),
(14, 'D&G', 2017, 'album', 'd&g.com'),
(15, 'Super Slimey', 2017, 'album', 'superslimey.com'),
(16, 'Softscars', 2023, 'album', 'softscars.com'),
(17, 'Terror Gang', 2015, 'single', 'terrorgang.com'),
(18, 'Oblivion Access', 2015, 'album', 'oblivionaccess.com'),
(19, 'Sunami/Gulch Split', 2021, 'ep', 'sunamigulch.com'),
(20, 'e', 2019, 'album', 'e.com'),
(21, 'Sunbather', 2013, 'album', 'sunbather.com'),
(22, 'Legendary Member', 2019, 'album', 'legendarymember.com'),
(23, 'Unsilent Death', 2010, 'album', 'unsilentdeath.com'),
(24, 'Keep It Goin Xav', 2024, 'album', 'keepitgoinxav.com');

-- --------------------------------------------------------

--
-- Table structure for table `release_artists`
--

CREATE TABLE `release_artists` (
  `id` int NOT NULL,
  `release_id` int NOT NULL,
  `artist_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `release_artists`
--

INSERT INTO `release_artists` (`id`, `release_id`, `artist_id`) VALUES
(1, 4, 2),
(2, 1, 1),
(3, 5, 3),
(4, 3, 5),
(5, 6, 5),
(6, 7, 5),
(7, 7, 3),
(8, 12, 8),
(9, 10, 17),
(10, 13, 11),
(11, 20, 18),
(12, 14, 3),
(13, 14, 18),
(14, 14, 19),
(15, 24, 21),
(16, 22, 19),
(17, 9, 15),
(18, 8, 7),
(19, 18, 14),
(20, 16, 20),
(21, 19, 23),
(22, 19, 16),
(23, 21, 1),
(24, 17, 13),
(25, 23, 12),
(26, 11, 6),
(27, 15, 9),
(28, 15, 2);

-- --------------------------------------------------------

--
-- Table structure for table `songs`
--

CREATE TABLE `songs` (
  `id` int NOT NULL,
  `title` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `track_number` int NOT NULL,
  `release_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `songs`
--

INSERT INTO `songs` (`id`, `title`, `track_number`, `release_id`) VALUES
(1, 'With That', 2, 4),
(2, 'Halftime', 8, 4),
(3, 'Immortal', 1, 3),
(4, 'Highway Patrol', 2, 3),
(5, 'Black Brick', 1, 1),
(6, 'Obedient', 6, 5),
(7, 'Just Kitten', 3, 8),
(8, 'Blew My High', 7, 12),
(9, 'Young Rambos', 6, 12),
(10, 'Stop Breathing', 3, 11),
(11, 'Terror Gang', 1, 17),
(14, 'Step Up', 1, 19),
(15, 'Die Slow', 2, 19),
(16, 'Bolt Swallower', 3, 19),
(17, 'Accelerator', 4, 19),
(19, 'ghosts', 5, 16),
(20, 'Teen X', 10, 11),
(21, 'Sky', 19, 11),
(22, 'No Life Left', 1, 14),
(23, 'Cinderella', 5, 14),
(24, 'Nosebleed', 12, 14),
(25, 'Weekend at the Fire Academy', 5, 13),
(26, 'Sylph Fossil', 3, 9),
(27, '360', 1, 10),
(28, 'Need Me', 2, 24),
(29, 'Unsilent Death', 5, 23),
(30, 'Dream House', 1, 21);

-- --------------------------------------------------------

--
-- Table structure for table `song_artists`
--

CREATE TABLE `song_artists` (
  `id` int NOT NULL,
  `song_id` int NOT NULL,
  `artist_id` int NOT NULL,
  `featured_artist` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `song_artists`
--

INSERT INTO `song_artists` (`id`, `song_id`, `artist_id`, `featured_artist`) VALUES
(1, 2, 2, 0),
(2, 3, 5, 0),
(3, 4, 5, 0),
(4, 4, 3, 1),
(6, 1, 2, 0),
(7, 5, 1, 0),
(8, 17, 16, 0),
(9, 16, 16, 0),
(10, 14, 23, 0),
(11, 15, 23, 0),
(12, 23, 3, 0),
(13, 23, 18, 0),
(14, 19, 20, 0),
(15, 8, 8, 0),
(16, 7, 7, 0),
(17, 22, 3, 0),
(18, 24, 19, 0),
(19, 24, 18, 0),
(20, 6, 3, 0),
(21, 6, 18, 1),
(22, 21, 6, 0),
(23, 10, 6, 0),
(24, 20, 6, 0),
(25, 20, 9, 1),
(26, 11, 13, 0),
(27, 9, 8, 0),
(28, 30, 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `song_genres`
--

CREATE TABLE `song_genres` (
  `id` int NOT NULL,
  `song_id` int NOT NULL,
  `genre_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `song_genres`
--

INSERT INTO `song_genres` (`id`, `song_id`, `genre_id`) VALUES
(3, 5, 4),
(4, 2, 1),
(5, 1, 1),
(6, 4, 5),
(7, 3, 5),
(8, 4, 1),
(9, 3, 1),
(10, 27, 16),
(11, 27, 18),
(12, 17, 6),
(13, 16, 6),
(14, 15, 6),
(15, 14, 6),
(16, 8, 2),
(17, 8, 10),
(18, 23, 2),
(19, 23, 5),
(20, 19, 9),
(21, 19, 12),
(22, 7, 5),
(23, 10, 3),
(24, 28, 17),
(25, 28, 11),
(26, 22, 5),
(27, 22, 2),
(28, 24, 5),
(29, 24, 2),
(30, 9, 10),
(31, 9, 2),
(32, 6, 5),
(33, 21, 2),
(34, 26, 20),
(35, 26, 21),
(36, 11, 11),
(37, 25, 13),
(38, 25, 15),
(39, 29, 14),
(40, 29, 15),
(41, 20, 3),
(42, 20, 11),
(43, 30, 8),
(44, 17, 7),
(45, 16, 7),
(46, 17, 19),
(47, 16, 19),
(48, 15, 19),
(49, 14, 19);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `artists`
--
ALTER TABLE `artists`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `genres`
--
ALTER TABLE `genres`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `releases`
--
ALTER TABLE `releases`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `release_artists`
--
ALTER TABLE `release_artists`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_release_artists_release_id` (`release_id`),
  ADD KEY `fk_release_artists_artist_id` (`artist_id`);

--
-- Indexes for table `songs`
--
ALTER TABLE `songs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_song_release_id` (`release_id`);

--
-- Indexes for table `song_artists`
--
ALTER TABLE `song_artists`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_song_artists_song_id` (`song_id`),
  ADD KEY `fk_song_artists_artist_id` (`artist_id`);

--
-- Indexes for table `song_genres`
--
ALTER TABLE `song_genres`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_song_genres_song_id` (`song_id`),
  ADD KEY `fk_song_genres_genre_id` (`genre_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `artists`
--
ALTER TABLE `artists`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `genres`
--
ALTER TABLE `genres`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `releases`
--
ALTER TABLE `releases`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `release_artists`
--
ALTER TABLE `release_artists`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `songs`
--
ALTER TABLE `songs`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `song_artists`
--
ALTER TABLE `song_artists`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `song_genres`
--
ALTER TABLE `song_genres`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `release_artists`
--
ALTER TABLE `release_artists`
  ADD CONSTRAINT `fk_release_artists_artist_id` FOREIGN KEY (`artist_id`) REFERENCES `artists` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `fk_release_artists_release_id` FOREIGN KEY (`release_id`) REFERENCES `releases` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Constraints for table `songs`
--
ALTER TABLE `songs`
  ADD CONSTRAINT `fk_song_release_id` FOREIGN KEY (`release_id`) REFERENCES `releases` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Constraints for table `song_artists`
--
ALTER TABLE `song_artists`
  ADD CONSTRAINT `fk_song_artists_artist_id` FOREIGN KEY (`artist_id`) REFERENCES `artists` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `fk_song_artists_song_id` FOREIGN KEY (`song_id`) REFERENCES `songs` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Constraints for table `song_genres`
--
ALTER TABLE `song_genres`
  ADD CONSTRAINT `fk_song_genres_genre_id` FOREIGN KEY (`genre_id`) REFERENCES `genres` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `fk_song_genres_song_id` FOREIGN KEY (`song_id`) REFERENCES `songs` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
