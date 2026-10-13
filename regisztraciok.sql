-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Gép: 127.0.0.1
-- Létrehozás ideje: 2026. Okt 13. 12:01
-- Kiszolgáló verziója: 10.4.32-MariaDB
-- PHP verzió: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Adatbázis: `nadasdy`
--

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `regisztraciok`
--

CREATE TABLE `regisztraciok` (
  `id` int(11) NOT NULL,
  `teljes_nev` varchar(50) NOT NULL,
  `elozo_iskola` varchar(100) NOT NULL,
  `koli` tinyint(1) NOT NULL,
  `vegzos` tinyint(1) NOT NULL,
  `ekkor_regisztralt` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- A tábla adatainak kiíratása `regisztraciok`
--

INSERT INTO `regisztraciok` (`id`, `teljes_nev`, `elozo_iskola`, `koli`, `vegzos`, `ekkor_regisztralt`) VALUES
(1, 'Homor Bálint', 'Répcelak Általános Iskola', 1, 1, '2026-10-13 09:33:13');

--
-- Indexek a kiírt táblákhoz
--

--
-- A tábla indexei `regisztraciok`
--
ALTER TABLE `regisztraciok`
  ADD PRIMARY KEY (`id`);

--
-- A kiírt táblák AUTO_INCREMENT értéke
--

--
-- AUTO_INCREMENT a táblához `regisztraciok`
--
ALTER TABLE `regisztraciok`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
