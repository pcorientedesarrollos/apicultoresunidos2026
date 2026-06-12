<?php

class conePDO
{

	/**
	 * Si necesita conectarse a una base de datos en específico, enviar el nombre en el método
	 */
	function conectar($database = false)
	{
		// Por defecto la base de control
		$baseDeDatos = 'apicultorescontrol';
		// $baseDeDatos = 'resp_control';


		if ($database) {
			$baseDeDatos = $database;
		}
		try {

			//LOCAL
			// $dsn = 'mysql:host=localhost;dbname=' . $baseDeDatos;
			// $username = 'root';
			// $password = '';
			//DREAMHOST
			// $dsn = 'mysql:host=mysql.apicultoresunidos.com;dbname=' . $baseDeDatos;
			// $username = 'apicultores';
			// $password = 'oaxacaMiel65';
			// AMAZON - IP directa (bypass DNS DreamHost 2026-06-11)
			$dsn = 'mysql:host=3.13.152.128;dbname=' . $baseDeDatos;
			$username = 'root';
			$password = 'Oriente65';
			//PRUEBAS DREAMHOST
			// $dsn = 'mysql:host=178.62.105.229;dbname=' . $baseDeDatos;
			// $username = 'pco';
			// $password = 'Oriente65$';
			$pdo = new PDO($dsn, $username, $password);
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$pdo->exec("SET NAMES 'UTF8'");
			return $pdo;
		} catch (PDOException $e) {
			echo $e->getMessage();
			echo "<br>";
			echo 'Fuera de servicio la base de datos...';
			exit;
		}
	}
}
