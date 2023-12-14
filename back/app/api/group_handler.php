<?php
require_once '../vendor/autoload.php';
require_once '../redirect.php';
require_once '../pdo_connect.php';
require_once '../auth/check_cookie.php';
require_once '../auth/check_token.php';
$request_method = $_SERVER['REQUEST_METHOD'];
$validator = new Valitron\Validator($_POST);
$select_group = "SELECT * FROM `group` WHERE group.group_name = :group_name";
$select_token = "SELECT * FROM token WHERE token.token_body = :token_body";
$select_user = "SELECT * FROM user WHERE user.id_user = :user_id";
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
            }
            $validator->rule('required', ['group_name', 'email']);
            $validator->rule('ascii', 'group_name');
            $validator->rule('email', 'email');
            $validator->rule('lengthMax', 'group_name', 50);
            if($validator->validate()) {
                //var_dump("success");
                $stmt_group = $pdo -> prepare($select_group);
                try {
                    $stmt_group -> execute([
                        'group_name' => $_POST['group_name']
                    ]);
                } catch (PDOException $exception) {
                    var_dump($exception->getMessage());
                }
                $group = $stmt_group -> fetch(PDO::FETCH_ASSOC);
                $stmt_student = $pdo -> prepare($select_student);
                try {
                    $stmt_student -> execute([
                        'email' => $_POST['email']
                    ]);
                } catch (PDOException $exception) {
                    var_dump($exception->getMessage());
                }
                $student = $stmt_student -> fetch(PDO::FETCH_ASSOC);
                if(!$student || !$group || $student['id_group'] != $group['id_group']) {
                    header('Location: ../front/for_teacher.php');
                } else {
                    header('Location: ../front/group_by_name.php', true, 307);
                }
            } else {
                redirect_to_login();
            }
        } else {
            redirect_to_login();
        }
        break;
    default:
        redirect_to_login();
}