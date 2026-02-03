CREATE DATABASE IF NOT EXISTS bd_trevisanportoes
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE bd_trevisanportoes;

CREATE TABLE tb_admin_cond (
	id_admin 		SMALLINT 	NOT NULL AUTO_INCREMENT PRIMARY KEY,
	nome 				VARCHAR(40) NOT NULL,
	telefone 		VARCHAR(11) NOT NULL,
	email 			VARCHAR(50) NOT NULL,
	INDEX idx_nome (nome)
) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE TABLE tb_sindico (
	id_sindico 		SMALLINT 	NOT NULL AUTO_INCREMENT PRIMARY KEY,
	nome 				VARCHAR(40) NOT NULL,
	telefone 		VARCHAR(11) NOT NULL,
	INDEX idx_nome (nome)
) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE TABLE tb_tipo_cliente (
	id_tipo_cliente 	TINYINT 		NOT NULL AUTO_INCREMENT PRIMARY KEY,
	tipo_cliente 		VARCHAR(20) NOT NULL,
	INDEX idx_tipo_cliente (tipo_cliente)
) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE TABLE tb_cliente (
	id_cliente 			SMALLINT 	NOT NULL AUTO_INCREMENT PRIMARY KEY,
	id_admin 			SMALLINT,
	id_sindico 			SMALLINT,
	id_tipo_cliente 	TINYINT 		NOT NULL,
	telefone 			VARCHAR(11) NOT NULL,
	nome 					VARCHAR(40) NOT NULL,
	email 				VARCHAR(100),
	cnpj 					VARCHAR(255) UNIQUE,
	INDEX idx_nome (nome),
	INDEX idx_cnpj (cnpj)
) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE TABLE tb_endereco (
	id_endereco 		SMALLINT 	NOT NULL AUTO_INCREMENT PRIMARY KEY,
	id_cliente 			SMALLINT 	NOT NULL,
	rua 					VARCHAR(50) NOT NULL,
	bairro 				VARCHAR(50) NOT NULL,
	numero 				SMALLINT 	NOT NULL,
	cidade 				VARCHAR(50) NOT NULL,
	complemento 		VARCHAR(50)
) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE TABLE tb_tipo_servico (
	id_tipo 				TINYINT 		NOT NULL AUTO_INCREMENT PRIMARY KEY,
	tipo_servico 		VARCHAR(50) NOT NULL
) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE TABLE tb_servico (
	id_servico 			INT 			NOT NULL AUTO_INCREMENT PRIMARY KEY,
	id_cliente 			SMALLINT 	NOT NULL,
	id_tipo 				TINYINT 		NOT NULL,
	descricao 			VARCHAR(100),
	observacao 			VARCHAR(100),
	foto 					VARCHAR(255),
	comprovante 		VARCHAR(255),
	data_hora 			DATETIME 	NOT NULL,
	INDEX idx_data_hora (data_hora)
) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE TABLE tb_forma_pagamento (
	id_forma_pagamento TINYINT 	NOT NULL AUTO_INCREMENT PRIMARY KEY,
	forma_pagamento 	VARCHAR(10) NOT NULL
) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE TABLE tb_status_pagamento (
	id_status 			TINYINT 		NOT NULL AUTO_INCREMENT PRIMARY KEY,
	status_pagamento 	VARCHAR(10) NOT NULL
) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE TABLE tb_pagamento (
	id_pagamento 			INT 				NOT NULL AUTO_INCREMENT PRIMARY KEY,
	id_servico 				INT 				NOT NULL,
	id_forma_pagamento 	TINYINT,
	valor 					DECIMAL(10,2) 	NOT NULL,
	id_status 				TINYINT 			NOT NULL
) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE TABLE tb_distribuidora (
	id_distribuidora 		INT 				NOT NULL AUTO_INCREMENT PRIMARY KEY,
	nome_distribuidora 	VARCHAR(20) 	NOT NULL,
	INDEX idx_nome_distribuidora (nome_distribuidora)
) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE TABLE tb_compras (
	id_compra 				INT 				NOT NULL AUTO_INCREMENT PRIMARY KEY,
	data_compra 			DATE 				NOT NULL,
	material 				VARCHAR(50) 	NOT NULL,
	qtd_compra 				TINYINT 			NOT NULL,
	valor_un 				DECIMAL(10,2) 	NOT NULL,
	valor_total 			DECIMAL(10,2) 	NOT NULL,
	id_distribuidora 		INT,
	INDEX idx_material (material)
) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Foreign Keys
ALTER TABLE tb_cliente
	ADD CONSTRAINT fk_cliente_admin
	FOREIGN KEY (id_admin) REFERENCES tb_admin_cond(id_admin)
	ON DELETE RESTRICT ON UPDATE CASCADE,
	
	ADD CONSTRAINT fk_cliente_sindico
	FOREIGN KEY (id_sindico) REFERENCES tb_sindico(id_sindico)
	ON DELETE RESTRICT ON UPDATE CASCADE,
	
	ADD CONSTRAINT fk_cliente_tipo
	FOREIGN KEY (id_tipo_cliente) REFERENCES tb_tipo_cliente(id_tipo_cliente)
	ON DELETE RESTRICT ON UPDATE CASCADE;

ALTER TABLE tb_endereco
	ADD CONSTRAINT fk_endereco_cliente
	FOREIGN KEY (id_cliente) REFERENCES tb_cliente(id_cliente)
	ON DELETE RESTRICT ON UPDATE CASCADE;

ALTER TABLE tb_servico
	ADD CONSTRAINT fk_servico_cliente
	FOREIGN KEY (id_cliente) REFERENCES tb_cliente(id_cliente)
	ON DELETE RESTRICT ON UPDATE CASCADE;
	
ALTER TABLE tb_pagamento
	ADD CONSTRAINT fk_pagamento_servico
	FOREIGN KEY (id_servico) REFERENCES tb_servico(id_servico)
	ON DELETE RESTRICT ON UPDATE CASCADE,
	
	ADD CONSTRAINT fk_pagamento_forma
	FOREIGN KEY (id_forma_pagamento) REFERENCES tb_forma_pagamento(id_forma_pagamento)
	ON DELETE RESTRICT ON UPDATE CASCADE,
	
	ADD CONSTRAINT fk_pagamento_status
	FOREIGN KEY (id_status) REFERENCES tb_status_pagamento(id_status)
	ON DELETE RESTRICT ON UPDATE CASCADE;
	
ALTER TABLE tb_compras
	ADD FOREIGN KEY (id_distribuidora) REFERENCES tb_distribuidora (id_distribuidora)
	ON DELETE RESTRICT ON UPDATE CASCADE;

-- Inserts
INSERT INTO tb_tipo_cliente (tipo_cliente) VALUES
('Residencial'),
('Condomínio');

INSERT INTO tb_tipo_servico (tipo_servico) VALUES
('Instalação'),
('Manutenção Corretiva'),
('Automação Preventiva');

INSERT INTO tb_forma_pagamento (forma_pagamento) VALUES
('Pix'),
('Débito'),
('Crédito'),
('Dinheiro');

INSERT INTO tb_status_pagamento (status_pagamento) VALUES
('Pendente'),
('Pago'),
('Cancelado');

INSERT INTO tb_admin_cond (nome, telefone, email) VALUES
('Carlos Silva', '11999990001', 'carlos@condominio.com'),
('Ana Souza', '11999990002', 'ana@condominio.com');

INSERT INTO tb_sindico (nome, telefone) VALUES
('João Pereira', '11988880001'),
('Marcos Lima', '11988880002');

INSERT INTO tb_distribuidora (nome_distribuidora) VALUES
('Só Portões'),
('Metal Forte');

INSERT INTO tb_cliente (
	id_admin,
	id_sindico,
	id_tipo_cliente,
	telefone,
	nome,
	email,
	cnpj
) VALUES
(1, NULL, 1, '11977770001', 'Pedro Almeida', 'pedro@email.com', NULL),
(2, 1, 2, '11977770002', 'Condomínio Jardim Azul', 'contato@jardimazul.com', '12345678000199');

INSERT INTO tb_endereco (
	id_cliente,
	rua,
	bairro,
	numero,
	cidade,
	complemento
) VALUES
(1, 'Rua das Flores', 'Centro', 120, 'São Paulo', 'Casa'),
(2, 'Av. Brasil', 'Jardins', 1500, 'São Paulo', 'Bloco A');

-- Adicionar coluna deleted_at para soft delete
ALTER TABLE tb_admin_cond ADD COLUMN deleted_at DATETIME NULL;
ALTER TABLE tb_sindico ADD COLUMN deleted_at DATETIME NULL;
ALTER TABLE tb_cliente ADD COLUMN deleted_at DATETIME NULL;




