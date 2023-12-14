<?php
require_once '../auth/check_cookie.php';
require_once '../auth/check_token.php';
require_once '../pdo_connect.php';
$select_token = "SELECT * FROM token WHERE token.token_body = :token_body";
$select_user = "SELECT * FROM user WHERE user.id_user = :user_id";
$select_student = "SELECT * FROM student WHERE student.email = :login";
$select_group = "SELECT * FROM `group` WHERE group.id_group = ?";
$select_lesson = "SELECT * FROM lesson WHERE lesson.id_group = :id_group and lesson.id_student = :id_student";
$select_teacher = "SELECT * FROM teacher WHERE teacher.id_teacher = :id_teacher";
$select_subject = "SELECT * FROM `subject` WHERE subject.id_subject = :id_subject";
$select_count_att = "SELECT COUNT(attendance_status) FROM lesson WHERE attendance_status = :attendance_status and lesson.id_student = :id_student";
$select_avg = "SELECT AVG(mark) FROM lesson WHERE lesson.id_student = :id_student";
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
        if ($user['role'] != 'user') {
            header('Location: ./front_login.php');
        }
        $stmt_student = $pdo -> prepare($select_student);
        try {
            $stmt_student -> execute([
                'login' => $user['login']
            ]);
        } catch (PDOException $exception) {
            var_dump($exception->getMessage());
        }
        $student = $stmt_student -> fetch(PDO::FETCH_ASSOC);
        if(!$student) {
            header('Location: ./front_register.php');
            exit;
        }
        $id_group = $student['id_group'];
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
                <h3>Ваши пары</h3>
            </header>
        <div>
        <table>
            <caption> Пары</caption>
            <tr>
                <th>Почта учителя</th>
                <th>Группа</th>
                <th>Предмет</th>
                <th>Посещение</th>
                <th>Оценка</th>
            </tr>
            <?php
                $stmt_group = $pdo -> prepare($select_group);
                try {
                    $stmt_group -> execute([$id_group]);
                } catch (PDOException $exception) {
                    var_dump($exception->getMessage());
                }
                $group = $stmt_group -> fetch(PDO::FETCH_ASSOC); //Группа
                $stmt_lesson = $pdo -> prepare($select_lesson);
                try {
                    $stmt_lesson -> execute([
                        'id_group' => $group['id_group'],
                        'id_student' => $student['id_student']
                    ]);
                } catch (PDOException $exception) {
                    var_dump($exception->getMessage());
                }
                $stmt_teacher = $pdo -> prepare($select_teacher);
                $lessons = $stmt_lesson -> fetchAll(PDO::FETCH_ASSOC); //Уроки
                $stmt_subject = $pdo -> prepare($select_subject);
                $stmt_attendance = $pdo -> prepare($select_count_att);
                try {
                    $stmt_attendance -> execute([
                        'attendance_status' => 1,
                        'id_student' => $student['id_student']
                    ]);
                } catch (PDOException $exception) {
                    var_dump($exception->getMessage());
                }
                $attendance_count = $stmt_attendance -> fetch(PDO::FETCH_ASSOC); //Сколько посетил
                $attendance_count = $attendance_count["COUNT(attendance_status)"];
                $stmt_absent = $pdo -> prepare($select_count_att);
                try {
                    $stmt_absent -> execute([
                        'attendance_status' => 0,
                        'id_student' => $student['id_student']
                    ]);
                } catch (PDOException $exception) {
                    var_dump($exception->getMessage());
                }
                $absent = $stmt_absent -> fetch(PDO::FETCH_ASSOC); //Сколько пропустил
                $absent = $absent["COUNT(attendance_status)"];
                $stmt_avg  = $pdo -> prepare($select_avg);
                try {
                    $stmt_avg -> execute([
                        'id_student' => $student['id_student']
                    ]);
                } catch (PDOException $exception) {
                    var_dump($exception->getMessage());
                }
                $avg_mark = $stmt_avg -> fetch(PDO::FETCH_ASSOC);
                if ($avg_mark["AVG(mark)"] == null || $avg_mark == "Пока нет оценок") {
                    $avg_mark = "пока нет оценок";
                } else {
                    $avg_mark = $avg_mark["AVG(mark)"];
                }
                //var_dump($avg_mark);
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
                        $stmt_teacher -> execute([
                            'id_teacher' => $lessons[$i]['id_teacher']
                        ]);
                    } catch (PDOException $exception) {
                        var_dump($exception->getMessage());
                    }
                    $teacher = $stmt_teacher -> fetch(PDO::FETCH_ASSOC); //Учитель
                    try {
                        $stmt_subject -> execute([
                            'id_subject' => $lessons[$i]['id_subject']
                        ]);
                    } catch (PDOException $exception) {
                        var_dump($exception->getMessage());
                    }
                    $subject = $stmt_subject -> fetch(PDO::FETCH_ASSOC); //Предмет
                    echo '
                    <tr>
                        <td>'. $teacher['email'] .'</td>
                        <td>'. $group['group_name'] .'</td>
                        <td>'. $subject['title'] .'</td>
                        <td>'. $attendance .'</td>
                        <td>'. $mark .'</td>
                    </tr>
                    ';
                }
            ?>
        </table>
        <?php
            echo '
            <br>
            <div class="data">
                <span>Количество посещённых занятий:  '. $attendance_count .'</span> <br> <br>
                <span>Количество пропущенных занятий занятий:  '. $absent .'</span> <br> <br>
                <span>Средняя оценка по всем занятиям:  '. $avg_mark .'</span>
            </div> <br>
            <a href="../auth/logout.php">Выйти</a>
            ';
         ?>
    </body>
</html>