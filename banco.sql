
CREATE TABLE banco_noite (
id SERIAL PRIMARY KEY,
nome VARCHAR(100),
sobrenome VARCHAR(100),
email VARCHAR(150) UNIQUE,
telefone VARCHAR(30)
);