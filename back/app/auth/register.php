<?php
require_once '../pdo_connect.php';
require_once '../vendor/autoload.php';
require_once '../redirect.php';
$request_method = $_SERVER['REQUEST_METHOD'];
$validator = new Valitron\Validator($_POST);
$password = hash('sha256' ,$_POST['password']);
$password = substr($password, 45);
$select_users = "SELECT * FROM user WHERE user.login = :email";   //Шаблоны запросов
$select_students = "SELECT * FROM student WHERE student.email = :email";
$select_teachers = "SELECT * FROM teacher WHERE teacher.email = :email";
$insert_user = "INSERT INTO user (`id_user`, `login`, `password`, `role`) VALUES (NULL, :login, :password, :role)";
switch ($request_method) {
    case 'GET':
        redirect_to_register(); //Перенаправление на страницу регистрации
        break;
    case 'POST':
        $validator->rule('required',['name', 'surname', 'email', 'password']);  //Валидируем форму
        $validator->rule('email', 'email');
        $validator->rule('regex', ['name','surname'], '/[А-Яа-яЁё]+/');
        $validator->rule('lengthMax', ['name', 'surname', 'email', 'password'], 50);
        $validator->rule('ascii', 'password');
        if($validator->validate()) { //Если валидация прошла успешно
            if(isset($_POST['is_teacher'])) {    //Если заходит учитель
                $stmt_teachers = $pdo ->prepare($select_teachers);
                $stmt_users = $pdo->prepare($select_users);
                try {
                    $stmt_teachers -> execute([
                        'email' => $_POST['email']
                    ]);
                } catch (PDOException $exception) {
                    redirect_to_register();
                }
                try {
                    $stmt_users -> execute([
                        'email'=> $_POST['email']
                    ]);
                } catch (PDOException $exception) {
                    redirect_to_register();
                }
                $teacher = $stmt_teachers -> fetch(PDO::FETCH_ASSOC);
                $user = $stmt_users -> fetch(PDO::FETCH_ASSOC);
                if(!$teacher || $user != false) {
                    //var_dump($user);
                    redirect_to_register();
                } else {
                    $stmt_users_ins = $pdo->prepare($insert_user);
                    try {
                        $stmt_users_ins -> execute([
                            'login' => $_POST['email'],
                            'password' =>  $password,
                            'role' => 'teacher'
                        ]);
                    } catch (PDOException $exception) {
                        redirect_to_register();
                    }
                    redirect_to_login();
                }
            } else {  //Если заходит студент
                $stmt_users = $pdo->prepare($select_users);
                $stmt_students = $pdo->prepare($select_students);
                try {
                    $stmt_users->execute([
                        'email' => $_POST['email']
                    ]);
                } catch (PDOException $exception) {
                    redirect_to_register();
                }
                try {
                    $stmt_students->execute([
                        'email'=> $_POST['email']
                    ]);
                } catch (PDOException $exception) {
                    redirect_to_register();
                }
                $student = $stmt_students->fetch(PDO::FETCH_ASSOC);
                $user = $stmt_users -> fetch(PDO::FETCH_ASSOC);
                if(!$student || $user != false ) {   //Если есть пользователь или нет студента в базе
                    //var_dump($user);
                    redirect_to_register();
                    exit;
                } else {
                    $stmt_users_ins = $pdo->prepare($insert_user);
                    try {
                        $stmt_users_ins->execute([
                            'login' => $_POST['email'],
                            'password'=> $password,
                            'role'=> 'user'
                        ]);
                    } catch (PDOException $exception) {
                        redirect_to_register();
                    }
                    redirect_to_login();
                }
            }
        } else {
            redirect_to_register();
            //var_dump($validator->errors());
            exit;
        }
        print_r($_POST);
        break;
    default:
        redirect_to_register();
        break;
}