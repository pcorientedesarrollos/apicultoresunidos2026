<?php
class conePDO
{

	function conectar($selectYear = false)
	{
		// Por defecto
		$baseDeDatos = 'apicultorescontrol';
		// $baseDeDatos = 'resp_control';

		// Si la sesión ya está creada, entonces que tome el nombre de la base de datos de la sesión
		if (session_status() === PHP_SESSION_NONE) {
			session_start();
		}
		if (isset($_SESSION['database'])) {
			$baseDeDatos = $_SESSION['database'];
		}
		session_write_close();

		// Por defecto: control
		/**OAXACA MIEL 2019 */
		// $baseDeDatos = 'apicultores2019';

		/**PASAS 2019 */
		// $baseDeDatos = 'erpasas2019';

		/**ERPOM 2019 */
		// $baseDeDatos = 'erpom2019';

		try {

// 			LOCAL
// 			$dsn = 'mysql:host=localhost;dbname=' . $baseDeDatos;
// 			$username = 'root';
// 			$password = '';
// 			PHPMYADMIN
// 			$dsn = 'mysql:host=aup.apicultoresunidos.com;dbname=' . $baseDeDatos;
// 			$username = 'apicultores';
// 			$password = 'oaxacaMiel65';
// 			AMAZON
			$dsn = 'mysql:host=dbpcoriente.ct5sef54k5xo.us-east-2.rds.amazonaws.com;dbname=' . $baseDeDatos;
			$username = 'root';
			$password = 'Oriente65';
// 			PRUEBAS DE DREAMHOST
// 			$dsn = 'mysql:host=178.62.105.229;dbname=' . $baseDeDatos;
// 			$username = 'pco';
// 			$password = 'Oriente65$';
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
?>
