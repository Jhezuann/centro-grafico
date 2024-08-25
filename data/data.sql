-- Crear la tabla Productos con los campos requeridos
CREATE TABLE Productos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    codigo VARCHAR(255) NOT NULL,
    descripcion TEXT NOT NULL,
    precio_por_metro DECIMAL(10, 2),
    cantidad_disponible DECIMAL(10, 2),
    rollos_disponibles INT,
    bobina INT,
    tipo_producto ENUM('vinil', 'lamina_acrilico', 'impresion_tabloide', 'lamina_pvc') NOT NULL,
    color VARCHAR(50),
    grosor_lamina_acrilico ENUM('2mm', '3mm', '5mm'),
    grosor_lamina_pvc ENUM('3mm', '5mm'),
    formato_impresion_tabloide ENUM('Papel boom', 'Glasee 150', 'Glasee 115', 'Glasee 300', 'Glasee adhesivo'),
    cantidad_minima DECIMAL(10, 2),
    precio DECIMAL(10, 2) NOT NULL
);

-- Crear la tabla Ventas
CREATE TABLE Ventas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    fecha DATE NOT NULL,
    id_producto INT NOT NULL,
    cantidad_vendida DECIMAL(10, 2) NOT NULL,
    precio_total DECIMAL(10, 2) NOT NULL,
    metodo_de_pago VARCHAR(50) NOT NULL,
    descripcion TEXT,
    monto_bs DECIMAL(10, 2),
    FOREIGN KEY (id_producto) REFERENCES Productos(id)
);

-- Crear la tabla Usuario
CREATE TABLE Usuario (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(255) NOT NULL,
    contraseña VARCHAR(255) NOT NULL
);

INSERT INTO Usuario (nombre, contraseña) VALUES ('centro grafico', SHA2('centro grafico', 256));
