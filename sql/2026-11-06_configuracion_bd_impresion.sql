-- Ejecutar en AWS, base apicultorescontrol

USE apicultorescontrol;

CREATE TABLE IF NOT EXISTS configuracion (
  clave VARCHAR(50) NOT NULL,
  valor VARCHAR(100) NOT NULL,
  idUsuario INT NULL,
  actualizado DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (clave)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO configuracion (clave, valor)
VALUES ('bd_impresion', 'apicultores2026_organica')
ON DUPLICATE KEY UPDATE valor = valor;

SELECT * FROM configuracion;
