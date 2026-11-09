-- Ejecutar en AWS: la impresion ahora lee ambas BD, se retira el switch "BD de impresion"

DELETE FROM apicultores2026.modulos WHERE ruta = 'bdImpresion';
DELETE FROM apicultores2026_organica.modulos WHERE ruta = 'bdImpresion';
DELETE FROM apicultorescontrol.configuracion WHERE clave = 'bd_impresion';

SELECT 'apicultores2026' bd, COUNT(*) FROM apicultores2026.modulos WHERE ruta = 'bdImpresion'
UNION ALL SELECT 'apicultores2026_organica', COUNT(*) FROM apicultores2026_organica.modulos WHERE ruta = 'bdImpresion'
UNION ALL SELECT 'configuracion', COUNT(*) FROM apicultorescontrol.configuracion WHERE clave = 'bd_impresion';
