<?php
require_once '../redirect.php';
require_once './check_cookie.php';
require_once '../pdo_connect.php';
require_once './check_token.php';
$request_method = $_SERVER['REQUEST_METHOD'];
$select_token = "SELECT * FROM token WHERE token.token_body = :token_body";
switch ($request_method) {
    case 'GET':
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
            } else {
                delete_token($token);
                redirect_to_login();
            }
        } else {
            redirect_to_login();
        }
    default:
        redirect_to_register();
}