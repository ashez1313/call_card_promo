<?
require_once(__DIR__ . '/crest.php');
?>

<!DOCTYPE html>
<html lang="ru">

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

<?php

?>

<body>
    <?php
    //запрашиваем номер телефона из встройки и передаем в post-запросе
    $placement_options = json_decode($_REQUEST['PLACEMENT_OPTIONS'], true);
    $phone_number = $placement_options["PHONE_NUMBER"]; //номер телефона из встройки
    $counter = 3; //счетчик попыток ввода
    ?>
    <div class="box">
        <h1 class="header"> Акция </h1>
        <h2 class="header"> Условия проведения: </h2>
        <p> Во время звонка клиент может назвать<u><b class="header">#ПРОМОКОД</b></u> вида" <code>[#12345]</code>) </p>
        <p> Введите этот промокод в поле ниже:
        <p>
        <form method='post' action='handler.php' enctype='multipart/form-data'>
            <p><input type="text" name="promo">
            <p><input type="hidden" name="phone_number" value="<?php echo $placement_options["PHONE_NUMBER"]; ?>">
            <p><input type="hidden" name="counter" value="<?php echo $counter; ?>">
            <p><input class="btn-new" type="submit" value="Отправить">
        </form>
    </div>
</body>

</html>