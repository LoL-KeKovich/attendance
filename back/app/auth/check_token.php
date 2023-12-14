<?php
function check_token($token) {
    try {
        $pdo = new PDO("mysql:host=mysql;dbname=attendance_control;", "user", "password");
    } catch (PDOException $exception) {
        var_dump($exception->getMessage());
    }
    $delete_token = "DELETE FROM token WHERE token.token_body = :token_body";
    if(date('Y-m-d H:i:s', time()) > $token['time_expires']) {
        $stmt_token_del = $pdo -> prepare($delete_token);
            try {
                $stmt_token_del -> execute([
                    'token_body' => $_COOKIE['body']
                ]);
            } catch (PDOException $exception) {
                var_dump($exception->getMessage());
            }
            return false;
    }
    return true;
}

function delete_token($token) {
    try {
        $pdo = new PDO("mysql:host=mysql;dbname=attendance_control;", "user", "password");
    } catch (PDOException $exception) {
        var_dump($exception->getMessage());
    }
    $delete_token = "DELETE FROM token WHERE token.token_body = :token_body";
    $stmt_token_del = $pdo -> prepare($delete_token);
    try {
        $stmt_token_del -> execute([
            'token_body' => $token['token_body']
        ]);
    } catch (PDOException $exception) {
        var_dump($exception->getMessage());
    }
}