-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:8889
-- Tempo de geração: 25/03/2025 às 21:47
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
-- Banco de dados: `painel_admin`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `administrador`
--

CREATE TABLE `administrador` (
  `id` int NOT NULL,
  `nome` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'pendente',
  `ultimo_login_em` timestamp NULL DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `doctrine_migration_versions`
--

CREATE TABLE `doctrine_migration_versions` (
  `version` varchar(191) NOT NULL,
  `executed_at` datetime DEFAULT NULL,
  `execution_time` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Despejando dados para a tabela `doctrine_migration_versions`
--

INSERT INTO `doctrine_migration_versions` (`version`, `executed_at`, `execution_time`) VALUES
('DoctrineMigrations\\Version20250325213126', '2025-03-25 21:31:37', 289);

-- --------------------------------------------------------

--
-- Estrutura para tabela `empresas`
--

CREATE TABLE `empresas` (
  `id` int NOT NULL,
  `nome` varchar(255) NOT NULL,
  `host_db` varchar(255) NOT NULL,
  `usuario_db` varchar(255) NOT NULL,
  `senha_db` varchar(255) NOT NULL,
  `plano_de_pagamento_id` int NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'pendente',
  `data_de_expiracao_plano` date DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `historico_de_pagamentos`
--

CREATE TABLE `historico_de_pagamentos` (
  `id` int NOT NULL,
  `empresa_id` int NOT NULL,
  `plano_de_pagamento_id` int NOT NULL,
  `data_pagamento` date NOT NULL,
  `valor_pago` decimal(10,2) NOT NULL,
  `metodo_pagamento` varchar(50) NOT NULL,
  `status_pagamento` varchar(20) NOT NULL DEFAULT 'pendente',
  `transacao_id` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `planos_de_pagamento`
--

CREATE TABLE `planos_de_pagamento` (
  `id` int NOT NULL,
  `nome` varchar(255) NOT NULL,
  `descricao` longtext,
  `preco` decimal(10,2) NOT NULL,
  `periodicidade` varchar(20) NOT NULL,
  `recursos` longtext,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `administrador`
--
ALTER TABLE `administrador`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_44F9A521E7927C74` (`email`);

--
-- Índices de tabela `doctrine_migration_versions`
--
ALTER TABLE `doctrine_migration_versions`
  ADD PRIMARY KEY (`version`);

--
-- Índices de tabela `empresas`
--
ALTER TABLE `empresas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_70DD49A551DB1D98` (`plano_de_pagamento_id`);

--
-- Índices de tabela `historico_de_pagamentos`
--
ALTER TABLE `historico_de_pagamentos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_53BFA696521E1991` (`empresa_id`),
  ADD KEY `IDX_53BFA69651DB1D98` (`plano_de_pagamento_id`);

--
-- Índices de tabela `planos_de_pagamento`
--
ALTER TABLE `planos_de_pagamento`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_80E823B654BD530C` (`nome`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `administrador`
--
ALTER TABLE `administrador`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `empresas`
--
ALTER TABLE `empresas`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `historico_de_pagamentos`
--
ALTER TABLE `historico_de_pagamentos`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `planos_de_pagamento`
--
ALTER TABLE `planos_de_pagamento`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `empresas`
--
ALTER TABLE `empresas`
  ADD CONSTRAINT `empresas_ibfk_1` FOREIGN KEY (`plano_de_pagamento_id`) REFERENCES `planos_de_pagamento` (`id`);

--
-- Restrições para tabelas `historico_de_pagamentos`
--
ALTER TABLE `historico_de_pagamentos`
  ADD CONSTRAINT `historico_de_pagamentos_ibfk_1` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`),
  ADD CONSTRAINT `historico_de_pagamentos_ibfk_2` FOREIGN KEY (`plano_de_pagamento_id`) REFERENCES `planos_de_pagamento` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
