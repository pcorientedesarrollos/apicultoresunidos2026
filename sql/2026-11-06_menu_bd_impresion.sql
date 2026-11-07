-- Ejecutar en AWS: agrega "BD de impresión" al menú Utilerías (idSeccion 9) en ambas BD

INSERT INTO apicultores2026.modulos (modulo, idSeccion, ruta)
SELECT 'BD de impresión', 9, 'bdImpresion'
WHERE NOT EXISTS (SELECT 1 FROM apicultores2026.modulos WHERE ruta = 'bdImpresion');

INSERT INTO apicultores2026_organica.modulos (modulo, idSeccion, ruta)
SELECT 'BD de impresión', 9, 'bdImpresion'
WHERE NOT EXISTS (SELECT 1 FROM apicultores2026_organica.modulos WHERE ruta = 'bdImpresion');

SELECT 'apicultores2026' bd, m.* FROM apicultores2026.modulos m WHERE m.idSeccion = 9
UNION ALL
SELECT 'apicultores2026_organica', m.* FROM apicultores2026_organica.modulos m WHERE m.idSeccion = 9;
