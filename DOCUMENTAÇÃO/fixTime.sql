CREATE DATABASE IF NOT EXISTS fixTime;

USE fixTime;

CREATE TABLE IF NOT EXISTS cliente (
    id_usuario INT AUTO_INCREMENT PRIMARY KEY,
    nome_usuario VARCHAR(100) NOT NULL,
    cpf VARCHAR(14) NOT NULL UNIQUE,
    telefone_usuario VARCHAR(15),
    email_usuario VARCHAR(100) NOT NULL UNIQUE,
    senha_usuario VARCHAR(255) NOT NULL,
    data_cadastro_usuario TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS oficina (
    id_oficina INT AUTO_INCREMENT PRIMARY KEY,
    categoria ENUM('Borracharia', 'Auto Elétrica', 'Oficina Mecânica', 'Lava Car') NOT NULL,
    nome_oficina VARCHAR(100) NOT NULL,
    cep_oficina VARCHAR(9) NOT NULL,
    cnpj VARCHAR(18) NOT NULL UNIQUE,
    endereco_oficina VARCHAR(100) NOT NULL,
    numero_oficina VARCHAR(10) NOT NULL,
    complemento VARCHAR(50),
    bairro_oficina VARCHAR(50) NOT NULL,
    cidade_oficina VARCHAR(50) NOT NULL,
    estado_oficina CHAR(2) NOT NULL,
    telefone_oficina VARCHAR(15) NOT NULL,
    email_oficina VARCHAR(100) NOT NULL UNIQUE,
    senha_oficina VARCHAR(255) NOT NULL,
    data_cadastro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE veiculos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tipo_veiculo ENUM('carro', 'moto', 'caminhao', 'van', 'onibus') NOT NULL,
    marca VARCHAR(50) NOT NULL,
    modelo VARCHAR(50) NOT NULL,
    ano INT NOT NULL,
    cor VARCHAR(30) NOT NULL,
    placa VARCHAR(10) NOT NULL UNIQUE,
    quilometragem DECIMAL(10, 2) NOT NULL,
    data_cadastro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    data_atualizacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    id_usuario INT NOT NULL, 
    FOREIGN KEY (id_usuario) REFERENCEs cliente (id_usuario)
);

CREATE TABLE IF NOT EXISTS servico (
    id_servico INT AUTO_INCREMENT PRIMARY KEY,
    data_servico TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('Pendente', 'Em Andamento', 'Concluído') DEFAULT 'Pendente',
    id_veiculo INT NOT NULL,
    id_oficina INT NOT NULL,
    FOREIGN KEY (id_veiculo) REFERENCES veiculos(id),
    FOREIGN KEY (id_oficina) REFERENCES oficina(id_oficina)
);