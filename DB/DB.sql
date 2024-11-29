CREATE DATABASE Dinamize;

USE Dinamize;

CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombreUsuario VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(100) NOT NULL
);

CREATE TABLE infousuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    FOREIGN KEY (user_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    nombres VARCHAR(100) NOT NULL,
    apellidos VARCHAR(100) NOT NULL,
    fechaNacimiento DATE NOT NULL,
    numeroTelefono VARCHAR(100),
    genero VARCHAR(100),
    fotoPerfil LONGBLOB
);

CREATE TABLE plazas_comerciales (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    FOREIGN KEY (user_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    nombre VARCHAR(100) NOT NULL,
    logo LONGBLOB,
    direccion TEXT NOT NULL,
    telefono VARCHAR(20),
    horarioApertura TIME NOT NULL,
    horarioCierre TIME NOT NULL,
    sitioWeb VARCHAR(255),
    facebook VARCHAR(255),
    instagram VARCHAR(255),
    descripcion TEXT
);

CREATE TABLE negocios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    plaza_id INT NOT NULL,
    FOREIGN KEY (plaza_id) REFERENCES plazas_comerciales(id) ON DELETE CASCADE,
    NumeroLocal INT,
    logo LONGBLOB,
    nombre VARCHAR(100) NOT NULL,
    categoria VARCHAR(100) NOT NULL,
    telefono VARCHAR(20),
    DiasLaborales VARCHAR(100),
    horarioApertura TIME NOT NULL,
    horarioCierre TIME NOT NULL,
    sitioWeb VARCHAR(255),
    facebook VARCHAR(255),
    instagram VARCHAR(255),
    descripcion TEXT
);

INSERT INTO usuarios (nombreUsuario, email, password)
VALUES ('admin', 'robiiaragon@gmail.com', sha1('admin')),
       ('Cocoro', 'caromartine18@gmail.com', sha1('cocoro12'));

INSERT INTO infousuarios (user_id, nombres, apellidos, fechaNacimiento, numeroTelefono, genero)
VALUES (1, 'Jesus Roberto', 'Aragon Lopez', '2002-10-01', '6633016320', 'Masculino');