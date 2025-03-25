-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:8889
-- Tempo de geração: 25/03/2025 às 20:37
-- Versão do servidor: 8.0.40
-- Versão do PHP: 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `db_medici`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `Lista`
--

CREATE TABLE `Lista` (
  `id_lista` int NOT NULL,
  `nome_lista` varchar(255) NOT NULL,
  `descricao_lista` text,
  `id_usuario` int NOT NULL,
  `data_criacao` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `Tarefa`
--

CREATE TABLE `Tarefa` (
  `id_tarefa` int NOT NULL,
  `titulo_tarefa` varchar(255) NOT NULL,
  `descricao_tarefa` text,
  `data_criacao` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `data_conclusao` date DEFAULT NULL,
  `status_tarefa` enum('Pendente','Em Andamento','Concluída') DEFAULT 'Pendente',
  `prioridade_tarefa` enum('Baixa','Média','Alta') DEFAULT 'Média',
  `id_lista` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `Usuario`
--

CREATE TABLE `Usuario` (
  `id_usuario` int NOT NULL,
  `nome_usuario` varchar(255) NOT NULL,
  `email_usuario` varchar(255) NOT NULL,
  `senha_usuario` varchar(255) NOT NULL,
  `data_cadastro` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `Lista`
--
ALTER TABLE `Lista`
  ADD PRIMARY KEY (`id_lista`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Índices de tabela `Tarefa`
--
ALTER TABLE `Tarefa`
  ADD PRIMARY KEY (`id_tarefa`),
  ADD KEY `id_lista` (`id_lista`);

--
-- Índices de tabela `Usuario`
--
ALTER TABLE `Usuario`
  ADD PRIMARY KEY (`id_usuario`),
  ADD UNIQUE KEY `email_usuario` (`email_usuario`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `Lista`
--
ALTER TABLE `Lista`
  MODIFY `id_lista` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `Tarefa`
--
ALTER TABLE `Tarefa`
  MODIFY `id_tarefa` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `Usuario`
--
ALTER TABLE `Usuario`
  MODIFY `id_usuario` int NOT NULL AUTO_INCREMENT;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `Lista`
--
ALTER TABLE `Lista`
  ADD CONSTRAINT `lista_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `Usuario` (`id_usuario`) ON DELETE CASCADE;

--
-- Restrições para tabelas `Tarefa`
--
ALTER TABLE `Tarefa`
  ADD CONSTRAINT `tarefa_ibfk_1` FOREIGN KEY (`id_lista`) REFERENCES `Lista` (`id_lista`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
