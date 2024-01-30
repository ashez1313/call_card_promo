<?php
require_once(__DIR__ . '/crest.php');
require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");

use Bitrix\Main\Loader;

Loader::includeModule("iblock");
Loader::IncludeModule("crm");
?>

<html>

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Document</title>
	<script src="//api.bitrix24.com/api/v1/"></script>
	<style>
		body {
			font-family: "Roboto", "Arial", sans-serif;
			font-weight: 400;
			background-color: #EFEBDC;
			text-align: center;
			vertical-align: middle;
		}

		.box {
			text-align: center;
		}

		code {
			font-size: 150%;
		}

		.header {
			color: rgb(42, 80, 74);
		}

		.btn-new {
			position: relative;
			display: inline-block;
			font-size: 100%;
			font-weight: 700;
			color: #fff;
			text-shadow: #053852 -1px 1px, #053852 1px 1px, #053852 1px -1px, #053852 -1px -1px;
			text-decoration: none;
			user-select: none;
			padding: .3em .7em;
			outline: none;
			border-radius: 7px;
			background: rgb(42, 80, 74);
			box-shadow:
				inset -2px -2px rgba(0, 0, 0, .3),
				inset 2px 2px rgba(255, 255, 255, .3);
			transition: background-position 999999s, color 999999s, text-shadow 999999s;
		}

		.btn-new:hover {
			background: rgb(59, 106, 99);
			background-position: 0 0;
		}

		.btn-new:focus {
			text-shadow: rgb(42, 80, 74) -1px 1px, rgb(42, 80, 74)1 1px 1px, rgb(42, 80, 74) 1px -1px, rgb(42, 80, 74) -1px -1px;
			background: rgb(42, 80, 74) repeating-linear-gradient(135deg, rgb(42, 80, 74), rgb(42, 80, 74) 10px, rgb(119, 170, 162) 10px, rgb(119, 170, 162) 20px, rgb(42, 80, 74) 20px) no-repeat;
			background-size: 1000% 100%;
			color: rgba(255, 255, 255, 0);
			text-shadow: rgba(1, 117, 177, 0) -1px 1px, rgba(1, 117, 177, 0) 1px 1px, rgba(1, 117, 177, 0) 1px -1px, rgba(1, 117, 177, 0) -1px -1px;
			background-position: 900% 0;
			transition: background-position linear 600s, color .5s, text-shadow .5s;
		}

		.btn-new:after {
			content: "Загрузка\2026";
			position: absolute;
			top: 0;
			left: 0;
			padding: .3em .7em;
			color: rgba(0, 0, 0, 0);
			text-shadow: none;
			transition: 999999s;
		}

		.btn-new:focus:after {
			color: #fff;
			text-shadow: rgb(42, 80, 74) -1px 1px, rgb(42, 80, 74) 1px 1px, rgb(42, 80, 74) 1px -1px, rgb(42, 80, 74) -1px -1px;
			transition: .5s;
		}
	</style>

</head>

<body>

	<body>
		<div class="box">
			<form method='post' action='handler.php' enctype='multipart/form-data'>
				<p><input type="hidden" name="phone_number" value="<?php echo $_POST['phone_number']; ?>">
				<p><input type="hidden" name="counter" value="<?php echo --$counter; ?>">
					<?php

					//проверка, что промокод не используют повторно
					function check_promo_not_in_list($promo)
					{
						$phone_number = preg_replace('/[^+0-9]/', '', $promo);
						//echo $phone_number;
						$result = CRest::call(
							'lists.element.get',
							[
								'FILTER' => [
									'=PROPERTY_1' => [   //номер свойства списка с акцией
										'*',
										$promo
									],
								],
								'IBLOCK_ID' => '123', //id списка с акцией
								'IBLOCK_TYPE_ID' => 'lists'
							]
						)['result'];

						if ($result) {
							return false;
						} else {
							return true;
						}
					}

					//проверка, что клиент новый - клиент по его номеру телефона не существует
					function check_client_is_new($phone_number)
					{

						//поиск записей по клиентам с таким номером телефона
						$result = CRest::call(
							'crm.duplicate.findbycomm',
							[
								'ENTITY_TYPE' => "CONTACT", 
								'TYPE' => "PHONE",
								'VALUES' => ["*", $phone_number],
							]
						)['result'];

						if ($result) {
							return false;
						} else {
							return true;
						}
					}

					if ($counter > 0) {
						if (isset($_POST["promo"]) && $_POST["promo"] != "") {

							//инициализация
							$my_phone_number = $_POST['phone_number'];
							$my_promo = $_POST["promo"];
							$current_user = \Bitrix\Main\Engine\CurrentUser::get()->getId();

							//если проходит проверки, то создаем элемент списка
							if (check_client_is_new($my_phone_number)) {

								if (check_promo_not_in_list($my_promo)) {
									
									$el = new CIBlockElement;
									$arProp = array(

										'IBLOCK_ID' => '123', //ид списка
										'NAME' => $my_phone_number,
										'IBLOCK_TYPE_ID' => 'lists',
										"PROPERTY_VALUES" => array(
											"1" => $my_phone_number, //номер телефона звонящего
											"2" => $promo, //номер промокода
											"3" => $current_user, //текущий пользователь, кто нажал кнопку
										)

									);
									$element = $el->Add($arProp);

									echo '<h2 style="color: green;"> &#9989; Успешно!  <br> Клиент: ' . $phone_number . ' |   Промокод: ' . $promo . '</h2>';

								} else {
									echo '<h2 style="color: red;">&#10060; Ошибка! <br> Клиент уже воспользовался данной акцией! </h2>';
								}
							} else {
								echo '<h2 style="color: red;">&#10060; Ошибка! <br> Клиент не подпадает под условия акции! </h2>';
							}
						} else {
							echo '<h2 style="color: red;">&#10060; Ошибка! <br> Поле должно быть заполнено! </h2>';
							echo '<p><input type="text" name="promo">';
							echo '<p><input class="btn-new" type="submit" value="Повторить">';
							echo '<h2>Осталось попыток: ' . $counter . "!</h2>";
						}
					} else {
						echo '<h2 style="color: red;">&#10060; Ошибка! <br> Превышено количество попыток ввода! </h2>';
					}
					?>
			</form>
		</div>
	</body>

</html>