CREATE DATABASE IF NOT EXISTS gestao_pessoas CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE gestao_pessoas;

CREATE TABLE IF NOT EXISTS departamentos (
  id   INT AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(80) NOT NULL
);

CREATE TABLE IF NOT EXISTS funcionarios (
  id              INT AUTO_INCREMENT PRIMARY KEY,
  nome            VARCHAR(120) NOT NULL,
  email           VARCHAR(120) NOT NULL UNIQUE,
  cargo           VARCHAR(80)  NOT NULL,
  data_admissao   DATE         NOT NULL,
  status          ENUM('ativo','afastado','desligado') DEFAULT 'ativo',
  departamento_id INT NOT NULL,
  FOREIGN KEY (departamento_id) REFERENCES departamentos(id)
);

CREATE TABLE IF NOT EXISTS ferias (
  id             INT AUTO_INCREMENT PRIMARY KEY,
  funcionario_id INT NOT NULL,
  data_inicio    DATE NOT NULL,
  data_fim       DATE NOT NULL,
  situacao       ENUM('pendente','aprovada','recusada') DEFAULT 'pendente',
  FOREIGN KEY (funcionario_id) REFERENCES funcionarios(id)
);

-- Dados de exemplo
INSERT INTO departamentos (nome) VALUES ('Recursos Humanos'), ('Tecnologia'), ('Comercial'), ('Financeiro');

INSERT INTO funcionarios (nome, email, cargo, data_admissao, status, departamento_id) VALUES
('Marina Souza',    'marina@empresa.com',   'Analista de RH',        CURDATE() - INTERVAL 900 DAY, 'ativo',    1),
('Carlos Pereira',  'carlos@empresa.com',   'Desenvolvedor PHP',     CURDATE() - INTERVAL 400 DAY, 'ativo',    2),
('Fernanda Lima',   'fernanda@empresa.com', 'Desenvolvedora Front',  CURDATE() - INTERVAL 60 DAY,  'ativo',    2),
('Rafael Gomes',    'rafael@empresa.com',   'Vendedor',              CURDATE() - INTERVAL 700 DAY, 'ativo',    3),
('Juliana Costa',   'juliana@empresa.com',  'Gerente Comercial',     CURDATE() - INTERVAL 1500 DAY,'afastado', 3),
('Pedro Almeida',   'pedro@empresa.com',    'Contador',              CURDATE() - INTERVAL 30 DAY,  'ativo',    4),
('Aline Martins',   'aline@empresa.com',    'Assistente Financeiro', CURDATE() - INTERVAL 250 DAY, 'ativo',    4),
('Bruno Ferreira',  'bruno@empresa.com',    'Analista de Suporte',   CURDATE() - INTERVAL 120 DAY, 'ativo',    2);

INSERT INTO ferias (funcionario_id, data_inicio, data_fim, situacao) VALUES
(2, CURDATE() + INTERVAL 30 DAY, CURDATE() + INTERVAL 49 DAY, 'pendente'),
(4, CURDATE() + INTERVAL 60 DAY, CURDATE() + INTERVAL 74 DAY, 'pendente'),
(1, CURDATE() + INTERVAL 10 DAY, CURDATE() + INTERVAL 24 DAY, 'aprovada'),
(7, CURDATE() - INTERVAL 40 DAY, CURDATE() - INTERVAL 26 DAY, 'aprovada');
