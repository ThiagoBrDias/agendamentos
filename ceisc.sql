-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Tempo de geração: 05-Mar-2025 às 00:06
-- Versão do servidor: 8.3.0
-- versão do PHP: 8.2.18

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "-03:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `ceisc`
--

-- --------------------------------------------------------

--
-- Estrutura da tabela `events`
--

DROP TABLE IF EXISTS `events`;
CREATE TABLE IF NOT EXISTS `events` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(220) COLLATE utf8mb3_unicode_ci NOT NULL,
  `color` varchar(45) COLLATE utf8mb3_unicode_ci NOT NULL,
  `start` datetime NOT NULL,
  `end` datetime NOT NULL,
  `resp` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `user_id` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=86 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Extraindo dados da tabela `events`
--

INSERT INTO `events` (`id`, `title`, `color`, `start`, `end`, `resp`, `user_id`) VALUES
(43, 'Reunião Vendas', '#0071c5', '2025-03-13 13:00:00', '2025-03-13 14:00:00', 'Marina', 1),
(45, 'Reunião Atendimento', '', '2025-03-06 13:00:00', '2025-03-06 14:00:00', NULL, 1),
(46, 'TI', '#A020F0', '2025-03-07 11:00:00', '2025-03-07 12:00:00', 'Thiago', 1),
(49, 'Alinhamento de equipe', '#8B4513', '2025-03-14 09:00:00', '2025-03-14 11:00:00', 'Thiago', 3),
(58, 'Reunião líderes', '#40E0D0', '2025-03-04 10:00:00', '2025-03-04 12:00:00', 'Thiago', 1),
(84, 'Reunião sala 2', '#FFD700', '2025-03-04 13:30:00', '2025-03-04 15:00:00', 'Thiago', 1),
(85, 'uhjuhjuhju', '#436EEE', '2025-02-25 22:20:00', '2025-02-25 22:30:00', 'jjjjjjjj', 1);

-- --------------------------------------------------------

--
-- Estrutura da tabela `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `sala` varchar(220) COLLATE utf8mb3_unicode_ci NOT NULL,
  `email` varchar(220) COLLATE utf8mb3_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Extraindo dados da tabela `users`
--

INSERT INTO `users` (`id`, `sala`, `email`) VALUES
(1, 'Sala Reunião 01', 'sala-reunioes-01@ceisc.com.br'),
(2, 'Sala Reunião 02', 'sala-reunioes-02@ceisc.com.br'),
(3, 'Sala Reunião 03', 'sala-reunioes-03@ceisc.com.br'),
(4, 'Cabine Reunião - 3º andar', 'cabine-reunioes-3andar@ceisc.com.br'),
(5, 'Cabine Reunião - Térreo', 'cabine-reunioes-terreo@ceisc.com.br');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
