<?php ## Соединение с БД
	try {
		$pdo = new PDO('mysql:host=localhost;dbname=db_taskbook', 'root', '');
	} catch (Exception $e) {
		die("Невозможно установить соединение с базой данных");
	}
