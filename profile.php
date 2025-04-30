<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Профиль</title>
	<link rel="stylesheet" href="style.css">
</head>
<body>
<?php
    session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$pdo = new PDO("mysql:host=localhost;dbname=bdaccount", "root", "Password");
$user = $_SESSION['user'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['username'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $pass = $_POST['password'];

    if ($pass) {
        $pass = password_hash($pass, PASSWORD_BCRYPT);
        $pdo->prepare("UPDATE users SET username=?, email=?, phone=?, password=? WHERE id=?")
            ->execute([$name, $email, $phone, $pass, $user['id']]);
    } else {
        $pdo->prepare("UPDATE users SET username=?, email=?, phone=? WHERE id=?")
            ->execute([$name, $email, $phone, $user['id']]);
    }

    $_SESSION['user'] = $pdo->query("SELECT * FROM users WHERE id = {$user['id']}")->fetch();
    echo "Данные обновлены.";
}
?>
<div class="form">
<h2>Профиль</h2>
<form method="post">
    <input name="username" value="<?= htmlspecialchars($user['username']) ?>" required>
    <input name="email" type="email" value="<?= htmlspecialchars($user['email']) ?>" required>
    <input name="phone" type="tel" value="<?= htmlspecialchars($user['phone']) ?>" required>
    <input name="password" type="password" placeholder="Новый пароль (если нужно)">
    <button type="submit">Сохранить</button>
</form></div>
