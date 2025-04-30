<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Вход</title>
    <script src="https://smartcaptcha.yandexcloud.net/captcha.js" defer></script>
	<link rel="stylesheet" href="style.css">
</head>
<body>
<?php
    session_start();
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $login = $_POST['login'];
    $pass = $_POST['password'];
    $token = $_POST['smart-token'];

    //Yandex SmartCaptcha проверка
    $secret = 'ВАШ_СЕКРЕТНЫЙ_КЛЮЧ';
    $captcha_response = file_get_contents("https://smartcaptcha.yandexcloud.net/validate?secret=$secret&token=$token");
    $captcha_result = json_decode($captcha_response, true);

    if (!$captcha_result['status'] ?? false) {
        die("Капча не пройдена.");
    }

    $pdo = new PDO("mysql:host=localhost;dbname=bdaccount", "root", "Password");

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? OR phone = ?");
    $stmt->execute([$login, $login]);
    $user = $stmt->fetch();

    if ($user && password_verify($pass, $user['password'])) {
        $_SESSION['user'] = $user;
		
        header("Location: profile.php");
        exit;
    } else {
        echo "Неверные данные.";
    }
}
?>
<div class="form">
    <h2>Вход</h2>
<form method="post">
    <input name="login" placeholder="Телефон или Email" required>
    <input type="password" name="password" placeholder="Пароль" required>
    <div id="captcha-container" class="smart-captcha" data-sitekey="ВАШ_SITE_KEY"></div>
    <input type="hidden" name="smart-token" id="smart-token">
    <script>
        window.smartCaptchaCallback = function () {
            smartCaptcha.render("captcha-container", {
                onSuccess: token => document.getElementById("smart-token").value = token
            });
        }
    </script>
    <button type="submit">Войти</button>
</form>
</div>
