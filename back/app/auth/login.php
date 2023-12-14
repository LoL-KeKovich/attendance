<?php
require_once '../vendor/autoload.php';
require_once '../redirect.php';
require_once '../pdo_connect.php';
$request_method = $_SERVER['REQUEST_METHOD'];
$validator = new Valitron\Validator($_POST);
$password = hash('sha256',$_POST['password']);
$password = substr($password, 45);
$token_body = hash('sha256' ,random_int(100000,500000));
//$time_expires = date('Y-m-d H:i:s', time() + 60 * 30);
$select_user = "SELECT * FROM user WHERE user.login = :email";
$select_token = "SELECT * FROM token WHERE token.user_id = :id_user";
$create_token = "INSERT INTO token (`token_body`, `user_id`, `time_expires`) VALUES (:token_body, :user_id, :time_expires)";
$delete_tokens = "DELETE FROM token WHERE token.user_id = :id_user";
switch ($request_method) {
    case 'POST':
        $validator -> rule('required', ['email', 'password']);
        $validator -> rule('email', 'email');
        $validator -> rule('lengthMax', ['email', 'password'], 50);
        $validator -> rule('ascii', ['password']);
        if ($validator -> validate()) {
            $stmt_user = $pdo -> prepare($select_user);
            try {
                $stmt_user -> execute([
                    'email' => $_POST['email']
                ]);
            } catch (PDOException $exception) {
                redirect_to_login();
            }
            $user = $stmt_user -> fetch(PDO::FETCH_ASSOC);
            //var_dump($user);
            if($user != false && $user['password'] == $password) { //Проверка на существование пользователя и соответствие пароля
                $stmt_token = $pdo -> prepare($select_token);
                try {
                    $stmt_token -> execute([
                        'id_user' => $user['id_user']
                    ]);
                } catch (PDOException $exception) {
                    var_dump($exception -> getMessage());
                }
                $tokens = $stmt_token -> fetchAll(PDO::FETCH_ASSOC);
                if($tokens != false) { //Если есть токены удаляем их
                    $stmt_token_del = $pdo -> prepare($delete_tokens);
                    try {
                        $stmt_token_del -> execute([
                            'id_user' => $user['id_user']
                        ]);
                    } catch (PDOException $exception) {
                        var_dump($exception -> getMessage());
                    }
                }
                $stmt_token_ins = $pdo -> prepare($create_token);
                $time_expires = date('Y-m-d H:i:s', time() + 60 * 30);
                //print(date('Y-m-d H:i:s', time() + 60 * 30));
                try {
                    $stmt_token_ins -> execute([
                        'token_body' => $token_body,
                        'user_id' => $user['id_user'],
                        'time_expires' => $time_expires
                    ]);
                } catch (PDOException $exception) {
                    var_dump($exception -> getMessage());
                }
                // $stmt_token = $pdo -> prepare($select_token);
                // $stmt_token -> execute([
                //     'id_user' => $user['id_user']
                // ]);
                // $tokens = $stmt_token -> fetchAll(PDO::FETCH_ASSOC);
                //var_dump($tokens);
                setcookie('body', $token_body, time() + 60 * 60, '/');
                //var_dump($_COOKIE);
                header('Location: ../api/info.php');
            } else {
                redirect_to_login();
            }
        } else {
            redirect_to_login();
        } break;
    default:
        redirect_to_login();
        break;
}