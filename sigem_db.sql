-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 28-10-2026 a las 03:56:13
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `sigem_db`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `alumnos`
--

CREATE TABLE `alumnos` (
  `ID_ALUMNOS` int(11) NOT NULL,
  `NyA` varchar(100) NOT NULL,
  `DNI` varchar(20) NOT NULL,
  `DIRECCION` varchar(150) DEFAULT NULL,
  `CORREO` varchar(100) DEFAULT NULL,
  `TELEFONO` varchar(30) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `correlativas`
--

CREATE TABLE `correlativas` (
  `ID_CORRELATIVA` int(11) NOT NULL,
  `ID_MATERIA` int(11) DEFAULT NULL,
  `ID_MATERIA_CORRELATIVA` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `docentes`
--

CREATE TABLE `docentes` (
  `ID_DOCENTES` int(11) NOT NULL,
  `NyA` varchar(100) NOT NULL,
  `DNI` varchar(20) NOT NULL,
  `DIRECCION` varchar(150) DEFAULT NULL,
  `CORREO` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `historial_academico`
--

CREATE TABLE `historial_academico` (
  `ID_HISTORIAL` int(11) NOT NULL,
  `ID_ALUMNO` int(11) DEFAULT NULL,
  `ID_MATERIA` int(11) DEFAULT NULL,
  `NOTA` decimal(4,2) DEFAULT NULL,
  `FECHA` date DEFAULT NULL,
  `ESTADO` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `inscripciones`
--

CREATE TABLE `inscripciones` (
  `ID_INSCRIPCIONES` int(11) NOT NULL,
  `N_PERMISO` varchar(30) DEFAULT NULL,
  `FECHA` date DEFAULT NULL,
  `CALIFICACIONES` decimal(4,2) DEFAULT NULL,
  `ID_ALUMNOS` int(11) DEFAULT NULL,
  `ID_MATERIA` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `materias`
--

CREATE TABLE `materias` (
  `ID_MATERIAS` int(11) NOT NULL,
  `NOM_MATERIA` varchar(100) NOT NULL,
  `ID_DOCENTES` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `mesas`
--

CREATE TABLE `mesas` (
  `ID_MESAS` int(11) NOT NULL,
  `FECHA` date NOT NULL,
  `AULA` varchar(50) DEFAULT NULL,
  `ID_MATERIA` int(11) DEFAULT NULL,
  `ID_DOCENTE` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `alumnos`
--
ALTER TABLE `alumnos`
  ADD PRIMARY KEY (`ID_ALUMNOS`),
  ADD UNIQUE KEY `DNI` (`DNI`);

--
-- Indices de la tabla `correlativas`
--
ALTER TABLE `correlativas`
  ADD PRIMARY KEY (`ID_CORRELATIVA`),
  ADD KEY `ID_MATERIA` (`ID_MATERIA`),
  ADD KEY `ID_MATERIA_CORRELATIVA` (`ID_MATERIA_CORRELATIVA`);

--
-- Indices de la tabla `docentes`
--
ALTER TABLE `docentes`
  ADD PRIMARY KEY (`ID_DOCENTES`),
  ADD UNIQUE KEY `DNI` (`DNI`);

--
-- Indices de la tabla `historial_academico`
--
ALTER TABLE `historial_academico`
  ADD PRIMARY KEY (`ID_HISTORIAL`),
  ADD KEY `ID_ALUMNO` (`ID_ALUMNO`),
  ADD KEY `ID_MATERIA` (`ID_MATERIA`);

--
-- Indices de la tabla `inscripciones`
--
ALTER TABLE `inscripciones`
  ADD PRIMARY KEY (`ID_INSCRIPCIONES`),
  ADD KEY `ID_ALUMNOS` (`ID_ALUMNOS`),
  ADD KEY `ID_MATERIA` (`ID_MATERIA`);

--
-- Indices de la tabla `materias`
--
ALTER TABLE `materias`
  ADD PRIMARY KEY (`ID_MATERIAS`),
  ADD KEY `ID_DOCENTES` (`ID_DOCENTES`);

--
-- Indices de la tabla `mesas`
--
ALTER TABLE `mesas`
  ADD PRIMARY KEY (`ID_MESAS`),
  ADD KEY `ID_MATERIA` (`ID_MATERIA`),
  ADD KEY `ID_DOCENTE` (`ID_DOCENTE`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `alumnos`
--
ALTER TABLE `alumnos`
  MODIFY `ID_ALUMNOS` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `correlativas`
--
ALTER TABLE `correlativas`
  MODIFY `ID_CORRELATIVA` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `docentes`
--
ALTER TABLE `docentes`
  MODIFY `ID_DOCENTES` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `historial_academico`
--
ALTER TABLE `historial_academico`
  MODIFY `ID_HISTORIAL` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `inscripciones`
--
ALTER TABLE `inscripciones`
  MODIFY `ID_INSCRIPCIONES` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `materias`
--
ALTER TABLE `materias`
  MODIFY `ID_MATERIAS` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `mesas`
--
ALTER TABLE `mesas`
  MODIFY `ID_MESAS` int(11) NOT NULL AUTO_INCREMENT;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `correlativas`
--
ALTER TABLE `correlativas`
  ADD CONSTRAINT `correlativas_ibfk_1` FOREIGN KEY (`ID_MATERIA`) REFERENCES `materias` (`ID_MATERIAS`),
  ADD CONSTRAINT `correlativas_ibfk_2` FOREIGN KEY (`ID_MATERIA_CORRELATIVA`) REFERENCES `materias` (`ID_MATERIAS`);

--
-- Filtros para la tabla `historial_academico`
--
ALTER TABLE `historial_academico`
  ADD CONSTRAINT `historial_academico_ibfk_1` FOREIGN KEY (`ID_ALUMNO`) REFERENCES `alumnos` (`ID_ALUMNOS`),
  ADD CONSTRAINT `historial_academico_ibfk_2` FOREIGN KEY (`ID_MATERIA`) REFERENCES `materias` (`ID_MATERIAS`);

--
-- Filtros para la tabla `inscripciones`
--
ALTER TABLE `inscripciones`
  ADD CONSTRAINT `inscripciones_ibfk_1` FOREIGN KEY (`ID_ALUMNOS`) REFERENCES `alumnos` (`ID_ALUMNOS`),
  ADD CONSTRAINT `inscripciones_ibfk_2` FOREIGN KEY (`ID_MATERIA`) REFERENCES `materias` (`ID_MATERIAS`);

--
-- Filtros para la tabla `materias`
--
ALTER TABLE `materias`
  ADD CONSTRAINT `materias_ibfk_1` FOREIGN KEY (`ID_DOCENTES`) REFERENCES `docentes` (`ID_DOCENTES`);

--
-- Filtros para la tabla `mesas`
--
ALTER TABLE `mesas`
  ADD CONSTRAINT `mesas_ibfk_1` FOREIGN KEY (`ID_MATERIA`) REFERENCES `materias` (`ID_MATERIAS`),
  ADD CONSTRAINT `mesas_ibfk_2` FOREIGN KEY (`ID_DOCENTE`) REFERENCES `docentes` (`ID_DOCENTES`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
