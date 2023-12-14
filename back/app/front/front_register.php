<?php
require_once '../auth/check_cookie.php';
require_once '../pdo_connect.php';
require_once '../auth/check_token.php';
$select_token = "SELECT * FROM token WHERE token.token_body = :token_body";
$select_user = "SELECT * FROM user WHERE user.id_user = :user_id";
if (check_cookie() && $_COOKIE != null) {
	$stmt_token = $pdo ->prepare($select_token);
    try {
        $stmt_token -> execute([
        'token_body' => $_COOKIE['body']
        ]);
    } catch (PDOException $exception) {
        var_dump($exception->getMessage());
    }
    $token = $stmt_token->fetch(PDO::FETCH_ASSOC);
	//var_dump($token);
    if($token == false) {
        $error = 'Нет текущей сессии';
    } else if(!check_token($token)) {
		$error = 'Сессия просрочена';
	}
	else {
        $stmt_user = $pdo -> prepare($select_user);
        try {
            $stmt_user -> execute([
                'user_id' => $token['user_id']
            ]);
        } catch (PDOException $exception) {
            var_dump($exception->getMessage());
        }
        $user = $stmt_user -> fetch(PDO::FETCH_ASSOC);
        if($user != false) {
            header("Location: ../api/info.php");
        }
    } 
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
	<title>Регистрация на учебном портале</title>
	<link rel="stylesheet" type="text/css" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Jost:wght@500&display=swap" rel="stylesheet">
</head>
<body>
	<div class="main">  	
		<input type="checkbox" id="chk" aria-hidden="true">
			<?php if (isset($error)):?>
				<span style="color: red"><?= $error ?></span>
			<?php endif; ?>
			<div class="signup">
				<form action="../auth/register.php" method="post">
					<label for="chk" aria-hidden="true" name="forchk">Зарегистрируйтесь</label>
					<input type="text" name="name" placeholder="Имя">
                    <input type="text" name="surname" placeholder="Фамилия">
					<input type="text" name="email" placeholder="Email">
					<input type="password" name="password" placeholder="Пароль">
					<input type="checkbox" name="is_teacher" class="is teacher">
					<p>Я учитель</p>
					<button type="submit">Зарегистрироваться</button>
				</form>
			</div>

			<div class="login">
				<label for="chk" aria-hidden="true">Есть аккаунт?</label>
				<input type="button" class="voyti" value="Войти" onclick="location.href='front_login.php'">
			</div>
	</div>
</body>
</html>