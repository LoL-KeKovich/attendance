<?php
require_once '../auth/check_cookie.php';
require_once '../auth/check_token.php';
require_once '../pdo_connect.php';
$request_method = $_SERVER['REQUEST_METHOD'];
$select_group = "SELECT * FROM `group` WHERE group.group_name = :group_name";
$select_token = "SELECT * FROM token WHERE token.token_body = :token_body";
$select_user = "SELECT * FROM user WHERE user.id_user = :user_id";
$select_lesson = "SELECT * FROM lesson WHERE lesson.id_teacher = :id_teacher and lesson.id_group = :id_group and lesson.id_student = :id_student";
$select_subject = "SELECT * FROM `subject` WHERE subject.id_subject = :id_subject";
$select_teacher = "SELECT * FROM teacher WHERE teacher.email = :login";
$select_student = "SELECT * FROM student WHERE student.email = :email";
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
            header('Location: ./front_register.php');
            exit;
        }
        $stmt_student = $pdo -> prepare($select_student);
        try {
            $stmt_student -> execute([
                'email' => $_POST['email']
            ]);
        } catch (PDOException $exception) {
            var_dump($exception->getMessage());
        }
        $student = $stmt_student -> fetch(PDO::FETCH_ASSOC);
        //var_dump($student);
        if(!$student) {
            header('Location: ./front_register.php');
            exit;
        }
    }
} else {
    header('Location ./front_login.php');
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
                <h3>Пары</h3>
            </header>
        <div>
        <table>
            <?php echo'<caption>Список пар студента '. $_POST['email'] .'  группы - '. $_POST['group_name'] .'</caption> ';?>
            <tr>
                <th>ID пары</th>
                <th>Предмет</th>
                <th>Посещение</th>
                <th>Оценка</th>
            </tr>
            <?php
                $stmt_lesson = $pdo -> prepare($select_lesson);
                try {
                    $stmt_lesson -> execute([
                        'id_teacher' => $teacher['id_teacher'],
                        'id_group' => $group['id_group'],
                        'id_student' => $student['id_student']
                    ]);
                } catch (PDOException $exception) {
                    var_dump($exception->getMessage());
                }
                $lessons = $stmt_lesson -> fetchAll(PDO::FETCH_ASSOC); //Пары
                //var_dump($lessons);
                $stmt_subject = $pdo -> prepare($select_subject);
                for($i = 0; $i < count($lessons); $i++) {
                    if ($lessons[$i]['attendance_status'] == 1) {
                        $attendance = "+";
                    } else {
                        $attendance = "-";
                    }
                    if (empty($lessons[$i]['mark']) || $lessons[$i]['mark'] == 0) {
                        $mark = "нет";
                    } else {
                        $mark = $lessons[$i]['mark'];
                    }
                    try {
                        $stmt_subject -> execute([
                            'id_subject' => $lessons[$i]['id_subject']
                        ]);
                    } catch (PDOException $exception) {
                        var_dump($exception->getMessage());
                    }
                    $subject = $stmt_subject -> fetch(PDO::FETCH_ASSOC);
                    echo '
                    <tr>
                        <td>'. $lessons[$i]['id_lesson'] .'</td>
                        <td>'. $subject['title'] .'</td>
                        <td>'. $attendance .'</td>
                        <td>'. $mark .'</td>
                    </tr>
                    ';
                }
            ?>
        </table>
        <div class="form1">
            <form action="../api/update_attendance.php" method="post">
                <h3>Изменить статус посещаемости</h3>
                <?php echo'<input type="hidden" name="email" value='. $_POST['email'] .'>' ?>
                <input type="number" name="id_lesson" placeholder="ID пары"> <br>
                <input type="number" name="attendance_status" placeholder="Новый статус (0 или 1)">
                <button type="submit">Изменить</button>
            </form>
            <form action="../api/update_mark.php" method="post">
                <h3>Изменить оценку</h3>
                <?php echo'<input type="hidden" name="email" value='. $_POST['email'] .'>' ?>
                <input type="number" name="id_lesson" placeholder="ID пары"> <br>
                <input type="number" name="mark" placeholder="Новая оценка">
                <button type="submit">Изменить</button>
            </form>
        </div>
        <?php
            echo'
            <a href="../auth/logout.php">Выйти</a>
            ';
        ?> <br> <br> <br>
        <a href="./for_teacher.php">Вернуться</a>
    </body>
<html>