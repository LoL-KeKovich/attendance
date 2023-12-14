<?php
require_once '../auth/check_cookie.php';
require_once '../auth/check_token.php';
require_once '../pdo_connect.php';
require_once '../vendor/autoload.php';
$select_token = "SELECT * FROM token WHERE token.token_body = :token_body";
$select_user = "SELECT * FROM user WHERE user.id_user = :user_id";
$select_teacher = "SELECT * FROM teacher WHERE teacher.email = :login";
$select_group = "SELECT * FROM `group` WHERE group.group_name = :group_name";
$select_student = "SELECT * FROM student WHERE student.id_group = :id_group";
$validator = new Valitron\Validator($_POST);
if(check_cookie() && $_COOKIE != null && !empty($_POST['group_name']) && $_POST['group_name'] != null) {
    $stmt_token = $pdo ->prepare($select_token);
    try {
        $stmt_token -> execute([
        'token_body' => $_COOKIE['body']
        ]);
    } catch (PDOException $exception) {
        var_dump($exception->getMessage());
    }
    $token = $stmt_token->fetch(PDO::FETCH_ASSOC);
    if($token == false) {
        redirect_to_login();
    } else if(!check_token($token)) {
		redirect_to_login();
	} else {
        $stmt_user = $pdo -> prepare($select_user);
        try {
            $stmt_user -> execute([
                'user_id' => $token['user_id']
            ]);
        } catch (PDOException $exception) {
            var_dump($exception->getMessage());
        }
        $user = $stmt_user -> fetch(PDO::FETCH_ASSOC);
        if(!$user) {
            header('Location: ./front_register.php');
            //exit;
        }
        if ($user['role'] != 'teacher') {
            header('Location: ./front_login.php');
        }
        $stmt_teacher = $pdo -> prepare($select_teacher);
        try {
            $stmt_teacher -> execute([
                'login' => $user['login']
            ]);
        } catch (PDOException $exception) {
            var_dump($exception->getMessage());
        }
        $teacher = $stmt_teacher -> fetch(PDO::FETCH_ASSOC);
        if(!$teacher || $teacher == null) {
            header('Location: ./front_register.php');
            exit;
        }
        $stmt_group = $pdo -> prepare($select_group);
        try {
            $stmt_group -> execute([
                'group_name' => $_POST['group_name']
            ]);
        } catch (PDOException $exception) {
            var_dump($exception->getMessage());
        }
        $group = $stmt_group -> fetch(PDO::FETCH_ASSOC);
        if(!$group) {
            header('Location: ./front_login.php');
            exit;
        }
        $validator->rule('required', ['group_name']);
        $validator->rule('alpha', ['group_name']);
        if(!$validator->validate()) {
            header('Location: ./front_login.php');
            exit;
        }
    }
} else {
    header('Location: ./front_login.php');
    exit;
}
?>
<!DOCTYPE html>
<html>
    <head>
    <meta charset="UTF-8">
    <title>Информация о посещениях и успеваемости</title>
    <link rel="stylesheet" type="text/css" href="for_info.css">
    </head>
    <body>
        <div class="wrap">
            <header>
                <h3>Список группы</h3>
            </header>
        <div>
        <table>
            <caption>Список</caption>
            <tr>
                <th>Студенты</th>
            </tr>
            <?php
                $stmt_student = $pdo -> prepare($select_student);
                try {
                    $stmt_student -> execute([
                        'id_group' => $group['id_group']
                    ]);
                } catch (PDOException $exception) {
                    var_dump($exception->getMessage());
                }
                $students = $stmt_student -> fetchAll(PDO::FETCH_ASSOC);
                for($i = 0; $i < count($students); $i++) {
                    echo'
                    <tr>
                        <td>'. $students[$i]['email'] .'</td>
                    </tr>
                    ';
                }
            ?>
        </table> <br> <br> <br>
        <?php
            echo'
            <a href="../auth/logout.php">Выйти</a>
            ';
        ?> <br> <br> <br>
        <a href="./for_teacher.php">Вернуться</a>
    </body>
</html>