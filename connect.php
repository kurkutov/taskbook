<?php ## Соединение с БД
	try {
		$pdo = new PDO('mysql:host=localhost;dbname=u0955138_default', 'u0955138_default', 'paGV_yn0');
	} catch (Exception $e) {
		die("Невозможно установить соединение с базой данных");
	}
