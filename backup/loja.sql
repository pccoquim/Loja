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


CREATE TABLE Carrinho( 
	id int PRIMARY KEY AUTO_INCREMENT,
    userId int NOT NULL,
    produtoId int NOT NULL,
    quantidade int NOT NULL,
    FOREIGN KEY (userId) REFERENCES Utilizador(id),
    FOREIGN KEY (produtoId) REFERENCES produtos(id)
    );
    
CREATE TABLE Vendas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    userId INT NOT NULL,
    data DATETIME NOT NULL,
    paypal_order_id VARCHAR(100),
    FOREIGN KEY (userId) REFERENCES Utilizador(id)
);

CREATE TABLE VendasProdutos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    vendaId INT NOT NULL,
    produtoId INT NOT NULL,
    quantidade INT NOT NULL,
    FOREIGN KEY (vendaId) REFERENCES Vendas(id),
    FOREIGN KEY (produtoId) REFERENCES produtos(id)
);


