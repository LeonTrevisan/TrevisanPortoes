CREATE DATABASE if NOT EXISTS bd_trevisanportoes
CHARACTER SET utf8mb4
COLLATE UTF8MB4_UNICODE_CI;

USE bd_trevisanportoes;

CREATE TABLE tb_cliente (
	id_cliente				SMALLINT 	NOT NULL AUTO_INCREMENT PRIMARY KEY,
	id_admin				SMALLINT,
	id_sindico				SMALLINT,
	id_tipo_cliente			TINYINT		NOT NULL,
	telefone 				VARCHAR(11) NOT NULL,
	nome			 		VARCHAR(40)	NOT NULL,
	email					VARCHAR(100),
	cnpj					VARCHAR(255)	UNIQUE,
	
	INDEX idx_nome (nome),
	INDEX idx_cnpj (cnpj),
	FOREIGN KEY (id_admin) REFERENCES tb_admin_cond (id_admin) ON DELETE RESTRICT ON UPDATE CASCADE,
	FOREIGN KEY (id_sindico) REFERENCES tb_sindico (id_sindico) ON DELETE RESTRICT ON UPDATE CASCADE,
	FOREIGN KEY (id_tipo_cliente) REFERENCES tb_tipo_cliente (id_tipo_cliente) ON DELETE RESTRICT ON UPDATE CASCADE
	)
	CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
	
CREATE TABLE tb_tipo_cliente (
	id_tipo_cliente	TINYINT NOT NULL AUTO_INCREMENT PRIMARY KEY,
	tipo_cliente		VARCHAR(20),
	
	INDEX idx_tipo (tipo_cliente)
	)
	CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
	
	SHOW ENGINE INNODB STATUS;
	LATEST FOREIGN KEY ERROR
	
CREATE TABLE tb_admin_cond (
	id_admin				SMALLINT		NOT NULL AUTO_INCREMENT PRIMARY KEY,
	nome					VARCHAR(40)	NOT NULL,
	telefone				VARCHAR(11)	NOT NULL,
	email					VARCHAR(50)	NOT NULL,
	
	INDEX idx_nome(nome)
	)
	CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;	
	
CREATE TABLE tb_sindico (
	id_sindico			SMALLINT		NOT NULL AUTO_INCREMENT PRIMARY KEY,
	nome					VARCHAR(40)	NOT NULL,
	telefone				VARCHAR(11)	NOT NULL,
	
	INDEX idx_nome(nome)
	)
	CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;		
	
CREATE TABLE tb_tipo_cliente (
	id_tipo				TINYINT		NOT NULL AUTO_INCREMENT PRIMARY KEY,
	tipo_cliente		VARCHAR(20)	NOT NULL,
	
	INDEX idx_tipo_cliente (tipo_cliente)
	)
	CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
	
CREATE TABLE tb_endereco (
	id_endereco			SMALLINT 	NOT NULL AUTO_INCREMENT PRIMARY KEY,
	id_cliente			SMALLINT		NOT NULL,
	rua					VARCHAR(50) NOT NULL,
	bairro				VARCHAR(50)	NOT NULL,
	numero				SMALLINT		NOT NULL,
	cidade				VARCHAR(50)	NOT NULL,
	complemento			VARCHAR(50),	
	
	FOREIGN KEY (id_cliente) REFERENCES tb_cliente(id_cliente) ON DELETE RESTRICT ON UPDATE CASCADE
	
	) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE TABLE tb_tipo_servico (
	id_tipo				TINYINT		NOT NULL AUTO_INCREMENT PRIMARY KEY,
	tipo_servico		VARCHAR(10)	NOT null
	) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
	
CREATE TABLE tb_status_servico (
	id_status			TINYINT		NOT NULL AUTO_INCREMENT	PRIMARY KEY,
	status_servico		VARCHAR(10)	NOT null
	) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE TABLE tb_servico (
	id_servico			INT 			NOT NULL AUTO_INCREMENT PRIMARY KEY,
	id_cliente			SMALLINT		NOT NULL,
	id_tipo				TINYINT		NOT NULL,
	id_status			TINYINT		NOT NULL,
	descricao			VARCHAR(100),
	data_hora			DATETIME		NOT NULL,
	
	FOREIGN KEY (id_cliente) REFERENCES tb_cliente(id_cliente) ON DELETE RESTRICT ON UPDATE CASCADE,
	FOREIGN KEY (id_tipo) REFERENCES tb_tipo_servico(id_tipo) ON DELETE RESTRICT ON UPDATE CASCADE,
	FOREIGN KEY (id_status) REFERENCES tb_status_servico(id_status) ON DELETE RESTRICT ON UPDATE CASCADE,
	
	INDEX idx_data_hora (data_hora)
	) 
	CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
	
CREATE TABLE tb_forma_pagamento(
	id_forma_pagamento	TINYINT		NOT NULL AUTO_INCREMENT PRIMARY KEY,
	forma_pagamento		VARCHAR(10)	NOT NULL
	) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
	
CREATE TABLE tb_status_pagamento(
	id_status			TINYINT		NOT NULL AUTO_INCREMENT	PRIMARY KEY,
	status_pagamento	VARCHAR(10)	NOT NULL
	
	) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
	
CREATE TABLE tb_pagamento (
	id_pagamento			INT				NOT NULL AUTO_INCREMENT	PRIMARY KEY,
	id_servico				INT				NOT NULL,
	id_forma_pagamento	TINYINT			NOT NULL,
	valor						DECIMAL(10,2) 	NOT NULL,
	id_status				TINYINT			NOT NULL,
	
	FOREIGN KEY (id_servico) REFERENCES tb_servico(id_servico) ON DELETE RESTRICT ON UPDATE CASCADE,
	FOREIGN KEY (id_forma_pagamento) REFERENCES tb_forma_pagamento(id_forma_pagamento) ON DELETE RESTRICT ON UPDATE CASCADE,
	FOREIGN KEY (id_status) REFERENCES tb_status_pagamento(id_status) ON DELETE RESTRICT ON UPDATE CASCADE
	
	) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
	
CREATE TABLE tb_compras(
	id_compra			INT				NOT NULL AUTO_INCREMENT PRIMARY KEY,
	data_compra			DATE				NOT NULL,
	material				VARCHAR(50)		NOT NULL,
	qtd_compra			TINYINT			NOT NULL,
	valor_un				DECIMAL(10,2)	NOT NULL,
	valor_total			DECIMAL(10,2)	NOT NULL,
	id_distribuidora INT
	
	FOREIGN KEY (id_distribuidora) REFERENCES tb_distribuidora (id_distribuidora) ON DELETE CASCADE ON UPDATE CASCADE;
	
	INDEX idx_material (material)
	) 
	CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
	
CREATE TABLE tb_distribuidora(
	id_distribuidora		INT			NOT NULL AUTO_INCREMENT PRIMARY KEY,
	nome_distribuidora	VARCHAR(20)	NOT NULL,
	
	INDEX idx_nome_distribuidora (nome_distribuidora)
	)
	CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
	
	
INSERT INTO tb_distribuidora (id_distribuidora, nome_distribuidora)
VALUES ('','Só Portões');
