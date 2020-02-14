<?php
	// Добавление класса ошибки, для поля формы
	function is_invalid_form_field(string $field_name, array $error_field) {
		return (isset($_POST[$field_name]) && array_key_exists($field_name, $error_field)) ? 'is-invalid' : '';
	}
	// Сохранение значения формы, при ошибочном вводе
	function get_form_val(string $field_name, array $error_field) {
		return (isset($_POST[$field_name]) && array_key_exists($field_name, $error_field)) ? $_POST[$field_name] : '';
	}

	// Сохранение значения формы, при ошибочном вводе, во время редактирования записи
	function get_edit_form_val(string $field_name, $data_field) {
		return (isset($_POST[$field_name])) ?  $_POST[$field_name] : $data_field;
	}

	function task_sort_selected(string $field) {
		return (isset($_SESSION['order_field']) && ($_SESSION['order_field'] == $field)) ?  'selected' : '';
	}