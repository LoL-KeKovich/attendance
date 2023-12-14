<?php
require_once '../auth/check_cookie.php';
require_once '../pdo_connect.php';
require_once '../auth/check_token.php';
$select_token = "SELECT * FROM token WHERE token.token_body = :token_body";
$select_user = "SELECT * FROM user WHERE user.id_user = :user_id";
$select_student = "SELECT * FROM student WHERE student.email = :login";
$select_teacher = "SELECT * FROM teacher WHERE teacher.email = :login";
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
            header('Location: ./front/front_register.php');
            exit;
        }
    }
} else {
    redirect_to_login();
}
if ($user['role'] == 'user') {
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
        header('Location: ./front/front_register.php');
        exit;
    }
    header('Location: ../front/for_student.php');
} else if ($user['role'] == 'teacher') {
    $stmt_teacher = $pdo -> prepare($select_teacher);
    try {
        $stmt_teacher -> execute([
            'login'=> $user['login']
        ]);
    } catch (PDOException $exception) {
        var_dump($exception->getMessage());
    }
    $teacher = $stmt_teacher -> fetch(PDO::FETCH_ASSOC);
    if (!$teacher) {
        header('Location: ./front/front_register.php');
        exit;
    }
    header('Location: ../front/for_teacher.php');
}