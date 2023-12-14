<?php
require_once 'redirect.php';
require_once 'pdo_connect.php';
require_once './auth/check_cookie.php';
require_once './auth/check_token.php';
$request_method = $_SERVER['REQUEST_METHOD'];
$select_token = "SELECT * FROM token WHERE token.token_body = :token_body";
$select_user = "SELECT * FROM user WHERE user.id_user = :user_id";
switch ($request_method) {
    case 'GET':
        if(check_cookie()) {
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
                header('Location: ./front/front_register.php');
            } else if(!check_token($token)) {
                    header('Location: ./front/front_register.php');
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
                if(!$user) {
                    header('Location: ./front/front_register.php');
                    exit;
                }
                header("Location: ../api/info.php");
            } 
        } else {
            header('Location: ./front/front_register.php');
        }break;
    default:
        redirect_to_register_from_index();
        break;
}