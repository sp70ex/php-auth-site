<?php session_start(); ?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Регистрация</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="form">
    <h2>Регистрация</h2>

<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $pdo = new PDO("mysql:host=localhost;dbname=bdaccount", "root", "Password");

    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $pass1 = $_POST['password'];
    $pass2 = $_POST['password_confirm'];

    $errors = [];

    // Проверка на уникальность
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? OR phone = ? OR username = ?");
    $stmt->execute([$email, $phone, $username]);
    if ($stmt->fetch()) $errors[] = "Пользователь с таким email, телефоном или именем уже существует.";

    // Проверка паролей
    if ($pass1 !== $pass2) $errors[] = "Пароли не совпадают.";

    if (empty($errors)) {
        $hash = password_hash($pass1, PASSWORD_BCRYPT);
        $pdo->prepare("INSERT INTO users (username, email, phone, password) VALUES (?, ?, ?, ?)")
            ->execute([$username, $email, $phone, $hash]);
        echo "<div class='success'>Регистрация прошла успешно. <a href='login.php'>Войти</a></div>";
        exit;
    } else {
        echo "<div class='error'>" . implode("<br>", $errors) . "</div>";
    }
}
?>

    <form method="post">
        <input class="input" name="username" placeholder="Имя" required>
        <input class="input" type="email" name="email" placeholder="Email" required>
        <input class="input" type="tel" name="phone" placeholder="Телефон" required>
        <input class="input" type="password" name="password" placeholder="Пароль" required>
        <input class="input" type="password" name="password_confirm" placeholder="Повторите пароль" required>
        <button class="btn" type="submit">Зарегистрироваться</button>
    </form>
</div>

</body>
</html>
