DROP DATABASE IF EXISTS loja;
CREATE DATABASE loja;
USE loja;

CREATE TABLE Utilizador(
	id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    telemovel VARCHAR(9) NOT NULL,
    nif VARCHAR(9) UNIQUE,
    token VARCHAR(255),
    active BOOLEAN DEFAULT false,
    created_at DATETIME DEFAULT Now(),
    updated_at DATETIME DEFAULT Now()
    );

