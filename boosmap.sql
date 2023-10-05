-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 14-10-2022 a las 14:32:44
-- Versión del servidor: 10.4.22-MariaDB
-- Versión de PHP: 8.1.2

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `boosmap`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `idusuario` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `correo` varchar(50) NOT NULL,
  `clave` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



--
-- Estructura de tabla para la tabla `viajes`
--

CREATE TABLE `viajes` (
  `idviaje` int(11) NOT NULL,
  `idusuario` int(11) NOT NULL,
  `destino` varchar(25) NOT NULL,
  `fecha` datetime NOT NULL,
  `monto` int(6) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `viajes`
--

INSERT INTO `viajes` VALUES(1, 4, 'quilpue', '2022-10-08 21:38:30', 3200);
INSERT INTO `viajes` VALUES(2, 5, 'Viña del Mar', '2022-10-08 22:12:46', 3400);
INSERT INTO `viajes` VALUES(3, 5, 'Valparaiso', '2022-10-08 22:13:34', 3800);
INSERT INTO `viajes` VALUES(4, 5, 'Concon', '2022-10-08 22:13:36', 4100);
INSERT INTO `viajes` VALUES(5, 5, 'Concon', '2022-10-08 22:13:38', 4100);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`idusuario`),
  ADD UNIQUE KEY `correo` (`correo`);

--
-- Indices de la tabla `viajes`
--
ALTER TABLE `viajes`
  ADD PRIMARY KEY (`idviaje`),
  ADD KEY `idusuario` (`idusuario`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `idusuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT de la tabla `viajes`
--
ALTER TABLE `viajes`
  MODIFY `idviaje` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=57;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `viajes`
--
ALTER TABLE `viajes`
  ADD CONSTRAINT `fk_idusuario` FOREIGN KEY (`idusuario`) REFERENCES `usuarios` (`idusuario`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

DELIMITER $$
CREATE PROCEDURE `detalles_viajes`(IN `_idusuario` INT, IN `_periodo` VARCHAR(10), OUT `viajes` INT, OUT `total` INT)
CASE _periodo
WHEN 'mes' THEN
SELECT COUNT(*) , SUM(monto) into viajes, total FROM viajes WHERE idusuario = _idusuario and extract(month from fecha) = extract(month from now());
WHEN 'semana' THEN
      SELECT COUNT(*), SUM(monto) INTO viajes, total
      FROM viajes
      WHERE idusuario = _idusuario 
      AND fecha >= DATE_SUB(NOW(), 
      INTERVAL DAYOFWEEK(NOW()) - 1 DAY)
      AND fecha < DATE_ADD(DATE_SUB(NOW(), 
      INTERVAL DAYOFWEEK(NOW()) - 1 DAY), 
      INTERVAL 7 DAY);
WHEN 'hoy' THEN
SELECT COUNT(*) , SUM(monto) into viajes, total FROM viajes WHERE idusuario = _idusuario and DATE_FORMAT(fecha, '%Y-%m-%d') = DATE_FORMAT(now(), '%Y-%m-%d');
END CASE$$
DELIMITER ;