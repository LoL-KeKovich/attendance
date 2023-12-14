<?php
require_once '../auth/check_cookie.php';
require_once '../auth/check_token.php';
require_once '../pdo_connect.php';
require_once '../vendor/autoload.php';
require_once '../redirect.php';
$request_method = $_SERVER['REQUEST_METHOD'];
$validator = new Valitron\Validator($_POST);
$select_token = "SELECT * FROM token WHERE token.token_body = :token_body";
$select_user = "SELECT * FROM user WHERE user.id_user = :user_id";
$select_lesson = "SELECT * FROM lesson WHERE lesson.id_lesson = :id_lesson AND lesson.id_teacher = :id_teacher and lesson.id_student = :id_student";
$select_teacher = "SELECT * FROM teacher WHERE teacher.email = :login";
$update_mark = "UPDATE lesson SET lesson.mark = :mark WHERE lesson.id_lesson = :id_lesson";
$select_student = "SELECT * FROM student WHERE student.email = :email";
switch($request_method) {
    case 'POST':
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
                    exit;
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
                $stmt_student = $pdo -> prepare($select_student);
                try {
                    $stmt_student -> execute([
                        'email' => $_POST['email']
                    ]);
                } catch (PDOException $exception) {
                    var_dump($exception->getMessage());
                }
                $student = $stmt_student -> fetch(PDO::FETCH_ASSOC);
            }
            $validator->rule('required', ['id_lesson', 'mark']);
            $validator->rule('numeric', ['id_lesson', 'mark']);
            $validator->rule('lengthMax', 'mark', 1);
            $mark = $_POST['mark'];
            if((int)$mark > 5) {
                redirect_to_login();
                exit;
            }
            if($validator -> validate()) {
                $stmt_lesson = $pdo -> prepare($select_lesson);
                try {
                    $stmt_lesson -> execute([
                        'id_lesson' => $_POST['id_lesson'],
                        'id_teacher' => $teacher['id_teacher'],
                        'id_student' => $student['id_student']
                    ]);
                } catch (PDOException $exception) {
                    var_dump($exception->getMessage());
                }
                $lesson = $stmt_lesson -> fetch(PDO::FETCH_ASSOC);
                if(!$lesson) {
                    redirect_to_login();
                }
                $stmt_mark = $pdo -> prepare($update_mark);
                try {
                    $stmt_mark -> execute([
                        'mark' => $mark,
                        'id_lesson' => $lesson['id_lesson']
                    ]);
                } catch (PDOException $exception) {
                    var_dump($exception->getMessage());
                }
                redirect_to_login();
            } else {
                redirect_to_login();
                exit;
            }
        } else {
            redirect_to_login();
        }
        break;
    default:
        redirect_to_login();
        break;
}