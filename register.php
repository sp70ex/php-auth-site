<?php 
session_start();
?>
<html>
<head>
</head>
<body>
<?php 
if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
	$err = [];
	  $username = $_POST['username'];
	  $email = $_POST['email'];
	  $phone = $_POST['phone'];
	  $pass1 = $_POST['pass1'];
	  $pass2 = $_POST['pass2'];
try {
    // подключаемся к серверу
    $pdo = new PDO("mysql:host=localhost;port=3306;dbname=bdaccount", "root", "Password");
    //echo "Успешное соединение.";
}
catch (PDOException $e) {
    echo "Ошибка базы данных." . $e->getMessage();
}


$stmt = $pdo->prepare("SELECT username FROM users WHERE username = ?");
$stmt->execute([$username]);
$row = $stmt->fetch();
if ($row) {
    $err[] = 'Логин уже зарегистрирован.';
}
$stmt = $pdo->prepare("SELECT email FROM users WHERE email = ?");
$stmt->execute([$email]);
$row = $stmt->fetch();
if ($row) {
    $err[] = 'Почта уже зарегистрирована.';
}
$stmt = $pdo->prepare("SELECT phone FROM users WHERE phone = ?");
$stmt->execute([$phone]);
$row = $stmt->fetch();
if ($row) {
    $err[] = 'Номер занят.';
}
if(!filter_var($email, FILTER_VALIDATE_EMAIL))
{
	$err[] = 'Почта введена некорректно.';
}
if(!preg_match('/^((8|\+7)[\- ]?)?(\(?\d{3}\)?[\- ]?)?[\d\- ]{7,10}$/', $phone))
	$err[] = 'Формат телефона введен не верно.';
if($pass1 != $pass2)
	$err[] = 'Пароли не совпадают.';

if (empty($err))
{
	$stmt = $pdo->prepare("INSERT INTO users (username, password, email, phone) VALUES (?, ?, ?, ?)");
	$stmt->execute([$username, $pass1, $email, $phone]);
	echo 'reg';
} else foreach($err as $key => $value)
{
	echo $value."<br>";
	}
}
?>

<?php if (!$_SESSION['auth']){ ?>
<link rel="stylesheet" href="style.css">
<form class="form" action="register.php" method="post">
  <input class="input" type="text" id="username" name="username" placeholder="Ваше имя" required>
  <input class="input" type="email" id="email" name="email" placeholder="Ваш e-mail" required>
  <input class="input" type="tel" id="phone" name="phone" placeholder="Ваш телефон" required>
  <input class="input" type="password" id="pass1" name="pass1" placeholder="Пароль" required>
  <input class="input" type="password" id="pass2" name="pass2" placeholder="Пароль еще раз" required>
  <button class="btn" type="submit">Регистрация</button>
</form>
<?php } else header("Location:auth.php");;?>
</body>
<html>
