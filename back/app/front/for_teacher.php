<?php
require_once '../auth/check_cookie.php';
require_once '../auth/check_token.php';
require_once '../pdo_connect.php';
$select_token = "SELECT * FROM token WHERE token.token_body = :token_body";
$select_user = "SELECT * FROM user WHERE user.id_user = :user_id";
$select_teacher = "SELECT * FROM teacher WHERE teacher.email = :login";
$select_lesson = "SELECT * FROM lesson WHERE lesson.id_teacher = :id_teacher";
$select_group = "SELECT * FROM `group` WHERE group.id_group = :id_group";
$select_subject = "SELECT * FROM `subject` WHERE subject.id_subject = :id_subject";
$select_count_stud = "SELECT COUNT(id_student) from student WHERE student.id_group = :id_group";
if(check_cookie() && $_COOKIE != null) {
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
            exit;
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
        $id_department = $teacher['id_department'];
    }
} else {
    header('Location: ./front_login.php');
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
                <h3>Группы</h3>
            </header>
        <div>
        <table>
            <caption>Список пар у групп</caption>
            <tr>
                <th>Группа</th>
                <th>Предмет</th>
            </tr>
            <?php
                $stmt_lesson = $pdo -> prepare($select_lesson);
                try {
                    $stmt_lesson -> execute([
                        'id_teacher' => $teacher['id_teacher']
                    ]);
                } catch (PDOException $exception) {
                    var_dump($exception->getMessage());
                }
                $lessons = $stmt_lesson -> fetchAll(PDO::FETCH_ASSOC); //Пары
                $stmt_count_stud = $pdo -> prepare($select_count_stud);
                $stmt_group = $pdo -> prepare($select_group);
                $stmt_subject = $pdo -> prepare($select_subject);
                for($i = 0; $i < count($lessons) / 2; $i++) {
                    try {
                        $stmt_subject -> execute([
                            'id_subject' => $lessons[$i]['id_subject']
                        ]);
                    } catch (PDOException $exception) {
                        var_dump($exception->getMessage());
                    }
                    $subject = $stmt_subject -> fetch(PDO::FETCH_ASSOC); //Считывает в цикле предметы и студентов поочерёдно
                    try {
                        $stmt_group -> execute([
                            'id_group' => $lessons[$i]['id_group']
                        ]);
                    } catch (PDOException $exception) {
                        var_dump($exception->getMessage());
                    }
                    $group = $stmt_group -> fetch(PDO::FETCH_ASSOC);
                    echo '
                    <tr>
                        <td>'. $group['group_name'] .'</td>
                        <td>'. $subject['title'] .'</td>
                    </tr>
                    ';
                }
            ?>
        </table>
        <div class="form1">
            <form action="../api/group_handler.php" method="post">
                <h3>Пары студента</h3>
                <input type="text" name="group_name" placeholder="Группа"> <br>
                <input type="text" name="email" placeholder="Почта студента"> <br>
                <button type="submit">Показать</button>
            </form>
            <form action="./group_list.php" method="post">
                <h3>Список группы</h3>
                <input type="text" name="group_name" placeholder="Группа"> <br>
                <button type="submit">Показать</button>
            </form>
        </div>
        <?php
            echo'
            <a href="../auth/logout.php">Выйти</a>
            ';
        ?>
    </body>
</html>