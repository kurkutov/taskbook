<?php

	session_start();


	if (isset($_SESSION['login'])) {
		$login = $_SESSION['login'];
		$is_authorized = true;
	} else {
		header('Location:index.php');
		exit;
	}

	require_once "connect.php";
	require_once "functions.php";

	$id = (isset($_GET['id'])) ? $_GET['id'] : null;

	// массив сообщений
	$message = [];
	// массив неправильно заполненных полей форм
	$error_field = [];

	// редактирование записи
	if (isset($_POST['edit_task_submit'])) {

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

		if ( empty( $_POST['task_id'] ) || ! isset( $_POST['task_id'] ) ) {
			$message['error'][] = "<strong>Ошибка!</strong> Неверный идентификатор.";
		} else {
			$task_id = strip_tags(trim($_POST['task_id']));
		}

		if (isset($_POST['task_check'])) {
			$is_edit = 1;
		} else {
			$is_edit = 0;
		}

		if (empty($message['error']) ) {
			// нет ошибок
			try {

				$stmt = $pdo->prepare("UPDATE `taskbook_tasks` SET `task_username`=:name,`task_useremail`=:email,`task_text`=:task_text,`is_done`=:is_done WHERE `task_id`=:id;");
				$stmt->bindValue(":name", $task_username);
				$stmt->bindValue(":email", $task_useremail);
				$stmt->bindValue(":task_text", $task_text);
				$stmt->bindValue(":is_done", $is_edit);
				$stmt->bindValue(":id", $task_id);
				$is_success = $stmt->execute();
				if ($is_success) {
					$message['success'][] = "<strong>Успех!</strong> задача успешно обновлена.";
				}
			} catch (PDOException  $e) {
				$message['error'][] = "<strong>Ошибка!</strong> Не удалось обновить задачу, " . $e->getMessage();

			}
		}
	}

	if (!is_null($id)) {
		echo "Зашли";
		// получаем данные для редактирования
		try {
			$stmt = $pdo->prepare("SELECT * FROM `taskbook_tasks` WHERE `task_id` = :id");
			$stmt->bindValue(":id", $id);
			$is_success = $stmt->execute();
			$task = $stmt->fetchAll();
			var_dump($task);
		} catch (PDOException  $e) {
			$message['error'][] = "<strong>Ошибка!</strong> Не удалось войти, " . $e->getMessage();
		}
	} else {
		$message['error'][] = "<strong>Ошибка!</strong> Неверный идентификатор задачи ";
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
  					<a class="navbar-brand" href="index.php">Navbar</a>
  				</div>
  				<div class="col-12 col-md-7">
					<div class="justify-content-end">
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

	<?php if (count($task) > 0): ?>
		<?php var_dump($task[0]['task_username']); ?>
	<section class="add-task mb-5">
    	<div class="container">
    		<div class="section-header row mb-3">
    			<div class=" col-12 text-center">
    				<h2>Редактировать задачу</h2>
    			</div>
    		</div>
    		<div class="section-content row">
    			<div class="col-12">
					<form method="POST">
						<input type="hidden" name="task_id" value="<?=$task[0]['task_id']?>"/>
						<input type="text" name="task_hidden" value="" style="display: none !important;"/>
						<input type="checkbox" name="task_anticheck" style="display: none !important;" value="true" checked="checked"/>
	    				<div class="form-group">
					   		<label for="task_username">Имя</label>
					    	<input type="text" name="task_username" class="form-control <?=is_invalid_form_field('task_username', $error_field);?>" id="task_username" placeholder="Введите ваше имя" value="<?=get_edit_form_val('task_username', $task[0]['task_username']);?>">
					  	</div>
					  	<div class="form-group">
					   		<label for="task_useremail">Email адрес</label>
					    	<input type="email" name="task_useremail" class="form-control <?=is_invalid_form_field('task_useremail', $error_field);?>" id="task_useremail" placeholder="name@example.com" value="<?=get_edit_form_val('task_useremail', $task[0]['task_useremail']);?>">
					  	</div>
						<div class="form-group">
							<label for="task_text">Текс задачи</label>
						    <textarea name="task_text" class="form-control  <?=is_invalid_form_field('task_text', $error_field);?>" id="task_text" rows="5"><?=get_edit_form_val('task_text', $task[0]['task_text']);?></textarea>
						</div>
						<div class="form-check mb-2">
							<?php $checked = (($task[0]['is_done'] == 1) || isset($_POST['task_check'])) ? 'checked' : '';?>
						    <input type="checkbox" <?=$checked;?> class="form-check-input" id="task_check" name="task_check">
						    <label class="form-check-label" for="task_check">Отредактировано</label>
					  	</div>
						<button type="submit" name="edit_task_submit" class="btn btn-primary">Изменить</button>
					</form>
    			</div>
    			
    		</div>
    	</div>
    </section>	
	<?php endif ?>


	    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
  </body>
</html>

    