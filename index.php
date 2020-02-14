<?php

	session_start();

	require_once "connect.php";
	require_once "functions.php";

	$login = "";
	$is_authorized = false;

	// Проверка на авторизацию

	if (isset($_SESSION['login'])) {
		$login = $_SESSION['login'];
		$is_authorized = true;
	}

	// массив сообщений
	$message = [];
	// массив неправильно заполненных полей форм
	$error_field = [];

	// Авторизация
	if (isset($_POST['login_submit'])) {

		// Проверяем на спам. Если скрытое поле заполнено или снят чек, то блокируем отправку
		if (false === $_POST['login_anticheck'] || ! empty( $_POST['login_hidden'] ) ) {
			die();
		}
		// Проверяем поле логин, если пустое, то пишем сообщение в массив ошибок
		if ( empty( $_POST['login'] ) || ! isset( $_POST['login'] ) ) {
			$message['error'][] = "<strong>Ошибка!</strong> Поле «Логин» не должно быть пустым.";
			$error_field['login'] = true;
		} else {
			$login = strip_tags(trim($_POST['login']));
		}
		// Проверяем поле пароль, если пустое, то пишем сообщение в массив ошибок
		if ( empty( $_POST['password'] ) || ! isset( $_POST['password'] ) ) {
			$message['error'][] = "<strong>Ошибка!</strong> Поле «Пароль» не должно быть пустым.";
			$error_field['password'] = true;
		} else {
			$password = md5(strip_tags(trim($_POST['password'])));
			echo "Пароль='" . $password . "'";
		}
		

		if (empty($message['error']) ) {
			// нет ошибок
			try {
				$stmt = $pdo->prepare("SELECT * FROM `taskbook_users` WHERE `login` = :login AND `password` = :password");
				$stmt->bindValue(":login", $login);
				$stmt->bindValue(":password", $password);
				$is_success = $stmt->execute();
				if ($is_success) {
					$_SESSION['login'] = $login;
					$is_authorized = true;
				}
			} catch (PDOException  $e) {
				$message['error'][] = "<strong>Ошибка!</strong> Не удалось войти, " . $e->getMessage();
			}
		}

	}


	// Добавление записи
	if (isset($_POST['add_task_submit'])) {

		// Проверяем на спам. Если скрытое поле заполнено или снят чек, то блокируем отправку
		if (false === $_POST['task_anticheck'] || ! empty( $_POST['task_hidden'] ) ) {
			die();
		}
		// Проверяем полей имени, если пустое, то пишем сообщение в массив ошибок
		if ( empty( $_POST['task_username'] ) || ! isset( $_POST['task_username'] ) ) {
			$message['error'][] = "<strong>Ошибка!</strong> Поле «Ваше имя» не должно быть пустым.";
			$error_field['task_username'] = true;
		} else {
			$task_username = strip_tags(trim($_POST['task_username']));
		}
		if ( empty( $_POST['task_useremail'] ) || ! isset( $_POST['task_useremail'] ) ) {
			$message['error'][] = "<strong>Ошибка!</strong> Поле «Email address» не должно быть пустым.";
			$error_field['task_useremail'] = true;
		} else {
			$task_useremail = strip_tags(trim($_POST['task_useremail']));
			if (preg_match("/.+@.+\..+/i", $task_useremail)) {
	      		$message['error'][] = "<strong>Ошибка!</strong> Поле «Email address» введено не верно.";
	      		$error_field['task_useremail'] = true;
	   		}
		}

		if ( empty( $_POST['task_text'] ) || ! isset( $_POST['task_text'] ) ) {
			$message['error'][] = "<strong>Ошибка!</strong> Поле «Текст задачи» не должно быть пустым.";
			$error_field['task_text'] = true;
		} else {
			$task_text = strip_tags(trim($_POST['task_text']));
		}

		if (empty($message['error']) ) {
			// нет ошибок
			try {
				$stmt = $pdo->prepare("INSERT INTO `taskbook_tasks`(task_username, task_useremail, task_text, is_done) VALUES (:name, :email,:task_text, :is_done)");
				$stmt->bindValue(":name", $task_username);
				$stmt->bindValue(":email", $task_useremail);
				$stmt->bindValue(":task_text", $task_text);
				$stmt->bindValue(":is_done", 0);
				$is_success = $stmt->execute();

				if ($is_success) {
					$message['success'][] = "<strong>Успех!</strong> ваша задача успешно добавлена.";
				}
			} catch (PDOException  $e) {
				$message['error'][] = "<strong>Ошибка!</strong> Не удалось добавить вашу задачу, " . $e->getMessage();

			}
		}
	}

	// сортировка
	
	if (isset($_POST['task_sort_submit'])) {
		// Проверяем на спам. Если скрытое поле заполнено или снят чек, то блокируем отправку
		if (false === $_POST['task_sort_anticheck'] || ! empty( $_POST['task_sort_hidden'] ) ) {
			die();
		}
		
		$order_field = strip_tags(trim($_POST['task_sort_select']));
		$desc = "";
		if (isset($_POST['task_sort_desc'])) {
			$desc = "DESC";
		}

		$_SESSION['order_field'] = $order_field;
		$_SESSION['desc'] = $desc;

	}

	if (isset($_SESSION['order_field'])) {
		$order_field = $_SESSION['order_field'];
	} else {
		$order_field = "task_username";
	}

	if (isset($_SESSION['desc'])) {
		$desc = $_SESSION['desc'];
	} else {
		$desc = "";
	}




 ?>

<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

    <title>Hello, world!</title>
  </head>
  <body>
  	<header class="container-fliude bg-dark">
  		<div class="container">
  			<div class="row py-2">
  				<div class="col-12 col-md-5">
  					<a class="navbar-brand" href="index.php">Задачник</a>
  				</div>
  				<div class="col-12 col-md-7">
					<div class="row justify-content-end">
					<?php if ($is_authorized): ?>
						<a href="/logout.php" class="btn btn-primary">Выход</a>
					<?php else: ?>
	  					<!-- Login form -->
						<form method="POST" class="form-row mb-0">
							<input type="text" name="login_hidden" value="" style="display: none !important;"/>
							<input type="checkbox" name="login_anticheck" style="display: none !important;" value="true" checked="checked"/>
							<div class="col">
						    	<input type="text" name="login" class="form-control <?=is_invalid_form_field('login', $error_field);?>" id="login" placeholder="Логин" value="<?=get_form_val('login', $error_field);?>">
						  	</div>
						  	<div class="col">
						    	<input type="password" name="password" class="form-control <?=is_invalid_form_field('password', $error_field);?>" id="password" placeholder="Password" value="<?=get_form_val('password', $error_field);?>">
						  	</div>
							<button type="submit" name="login_submit" class="btn btn-primary">Войти</button>
						</form>
						<!-- #Login form -->
					<?php endif ?>
					</div>


  				</div>
  			</div>
  			
			

		</div>
  	</header>

  	<?php if (! empty($message)): ?>
  		<section class="message">
			<div class="container">
			<?php if (!empty($message['success'])): ?>
				<div class="alert alert-success mt-3 mb-5" role="alert">
					<button type="button" class="close" data-dismiss="alert" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				<?php foreach ($message['success'] as $msg): ?>
					<?php echo $msg . "<br/>" ?>
				<?php endforeach ?>
				</div>
			<?php endif ?>
			<?php if (!empty($message['error'])): ?>
				<div class="alert alert-danger mt-3 mb-5" role="alert">
					<button type="button" class="close" data-dismiss="alert" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				<?php foreach ($message['error'] as $msg): ?>
					<?php echo $msg . "<br/>" ?>
				<?php endforeach ?>
				</div>
			<?php endif ?>
			</div>
  		</section>
  	<?php endif ?>

  	<section class="content mt-3 mb-5">
  		<div class="container">
  			<div class="section-header row mb-3">
  				<div class="col-12">
  					<h2>Задачи</h2>
  				</div>
  			</div>
  			<div class="section-content row">
  				<div class="col-12">
  					<div class="row">
  						<!-- форма сортировки -->
						<form method="POST">
							<input type="text" name="task_sort_hidden" value="" style="display: none !important;"/>
							<input type="checkbox" name="task_sort_anticheck" style="display: none !important;" value="true" checked="checked"/>
		    				<div class="form-group">
					   			<label for="inputState">Сортировка</label>
						      	<select id="task_sort_select" name="task_sort_select" class="form-control">
						        	<option <?=task_sort_selected('task_username');?> value="task_username">По имени пользователя</option>
						        	<option <?=task_sort_selected('task_useremail');?> value="task_useremail">По email</option>
						        	<option <?=task_sort_selected('is_done');?> value="is_done">По статусу</option>
						      	</select>
						  	</div>
							<div class="form-check mb-2">
								<?php $checked = ($desc !== "") ? 'checked' : ''; ?>
								<input <?=$checked;?> type="checkbox" class="form-check-input" id="task_sort_desc" name="task_sort_desc">
							    <label class="form-check-label" for="task_sort_desc">По убыванию</label>
						  	</div>
							<button type="submit" name="task_sort_submit" class="btn btn-primary">Изменить</button>
						</form>
  					</div>

  					<?php
  						$offset = 0;
  						$limit = 3;
  						$page = (isset($_GET['page'])) ? $_GET['page'] : 0;
				        if ($page == 0) {
				            $offset = 0;
				        } else {
				            $offset = $page * $limit;
				        }

  						try {
  							$count_order = $pdo->query("SELECT COUNT(*) FROM `taskbook_tasks`")->fetchColumn();
  							$desc = "DESC";
  							$query = "SELECT * FROM `taskbook_tasks` ORDER BY `" . $order_field."` " . $desc . " LIMIT :start, :num";
  							$stmt = $pdo->prepare($query); //LIMIT :start,:num
  	  						$stmt->bindValue(":start", $offset, PDO::PARAM_INT);
							$stmt->bindValue(":num", $limit, PDO::PARAM_INT);
							$stmt->execute();
							$tasks = $stmt->fetchAll();

							foreach ($tasks as $task) { ?>
							<div class="mb-5">
								<div class="row mb-2">
									<div class="col-12">
										<strong>Имя</strong>:<?php echo $task['task_username']; ?>
									</div>
								</div>
								<div class="row mb-2">
									<div class="col-12">
										<strong>Email</strong>: <?php echo $task['task_useremail']; ?>
									</div>
								</div>
								<div class="row  mb-2">
									<div class="col-12">
										<strong>Текст Задачи</strong><br><?php echo $task['task_text']; ?>
									</div>
								</div>
								<?php if ($task['is_done']): ?>
									<div class="row mb-2">
										<div class="col-12">
											<div class="text-success">Отредактировано Администратором</div>
										</div>
									</div>
								<?php endif ?>

								<?php if ($is_authorized): ?>
									<div class="row">
										<div class="col-12">
											<a href="/edit.php?id=<?=$task['task_id'];?>">Редактировать</a>
										</div>
									</div>	
								<?php endif ?>
								
							</div>

					<?php } ?>

					<div class="row justify-content-center">
						<?php require_once "pagination.php"; ?>
					</div>	
					
					<?php	

							// var_dump($result);
  						} catch (PDOException  $e) {
							$message['error'][] = "<strong>Ошибка!</strong> Не удалось загрузить данные, " . $e->getMessage();

						}
  						

  					?>


  				</div>
  			</div>
  		</div>

  	</section>
    <section class="add-task mb-5">
    	<div class="container">
    		<div class="section-header row mb-3">
    			<div class=" col-12 text-center">
    				<h2>Добавить задачу</h2>
    			</div>
    		</div>
    		<div class="section-content row">
    			<div class="col-12">
    				<form method="POST">
						<input type="text" name="task_hidden" value="" style="display: none !important;"/>
						<input type="checkbox" name="task_anticheck" style="display: none !important;" value="true" checked="checked"/>
	    				<div class="form-group">
					   		<label for="task_username">Имя</label>
					    	<input type="text" name="task_username" class="form-control <?=is_invalid_form_field('task_username', $error_field);?>" id="task_username" placeholder="Введите ваше имя" value="<?=get_form_val('task_username', $error_field);?>">
					  	</div>
					  	<div class="form-group">
					   		<label for="task_useremail">Email адрес</label>
					    	<input type="email" name="task_useremail" class="form-control <?=is_invalid_form_field('task_useremail', $error_field);?>" id="task_useremail" placeholder="name@example.com" value="<?=get_form_val('task_useremail', $error_field);?>">
					  	</div>
						<div class="form-group">
							<label for="task_text">Текс задачи</label>
						    <textarea name="task_text" class="form-control  <?=is_invalid_form_field('task_text', $error_field);?>" id="task_text" rows="5"><?=get_form_val('task_text', $error_field);?></textarea>
						</div>
						<button type="submit" name="add_task_submit" class="btn btn-primary">Добавить</button>
					</form>
    			</div>
    			
    		</div>
    	</div>
    </section>


    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
  </body>
</html>