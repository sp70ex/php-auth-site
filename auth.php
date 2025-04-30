<?php 
session_start();
?>
<html>
<head>
</head>
<body>
<link rel="stylesheet" href="style.css">
<?php 
if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
	try {
    $pdo = new PDO("mysql:host=localhost;port=3306;dbname=bdaccount", "root", "Password");
}
catch (PDOException $e) {
    echo "Ошибка базы данных." . $e->getMessage();
}
$action = $_POST['action'] ?? '';

    if ($action === 'login') {
	$stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? OR phone = ?");
	$stmt->execute([$_POST['login'],$_POST['login']]);
	$row = $stmt->fetch();
	if(!empty($row)){
	if ($row['password'] == $_POST['pass1'])
	{
		$name = $row['username'];
		$_SESSION['auth'] = $name;
	}
	else echo 'Пароль не верный.';} else echo 'Вы не зарегистрированы.';
	
	
if (isset($_SESSION['auth'])){
	  $username = $name;
$stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
$stmt->execute([$name]);
$row = $stmt->fetch();
echo 'Логин - '.$username;
echo '<br>Почта - '.$row['email'];
echo '<br>Номер - '.$row['phone'];
echo '<br>Пароль - '.$row['password'];

?>
<form class="form" action="auth.php" method="post">
<input type="hidden" name="action" value="update">
  <input class="input" type="text" id="username" name="username" placeholder="Ваше новое имя">
  <input class="input" type="email" id="email" name="email" placeholder="Ваш новый e-mail">
  <input class="input" type="tel" id="phone" name="phone" placeholder="Ваш новый телефон">
  <input class="input" type="password" id="pass1" name="pass1" placeholder="Новый пароль">
  <button class="btn" type="submit">Сохранить</button>
</form>
<?php

}
	}
	if ($action === 'update')
	{
		$pdo->prepare("UPDATE users SET username = ? WHERE username = ?")->execute([$_POST['username'],$_SESSION['auth']]);
		echo '<p style="color:green;">Данные успешно обновлены!</p>';
	}
} else {?>
	
<form class="form" action="auth.php" method="post">
<input type="hidden" name="action" value="login">
  <input class="input" type="text" id="login" name="login" placeholder="Ваш e-mail или номер телефона" required>
  <input class="input" type="password" id="pass1" name="pass1" placeholder="Пароль" required>
  
  <button class="btn" type="submit">Вход</button>
</form>
<?php
}
?>
</body>
</html>
