<?php
function redirect_to_register() {
    header('Location: ../front/front_register.php');
}

function redirect_to_login() {
    header('Location: ../front/front_login.php');
}

function redirect_to_register_from_index() {
    header('Location: ./front/front_register.php');
}

function redirect_to_check_user_from_log() {
    header('Location: ../api/check_user.php', true, 307);
}

function redrirect_to_register_from_check_user() {
    header('Location: ../front/front_register.php');
}

function redirect_to_info() {
    header('Location: ./api/info.php');
}

function redirect_to_group() {
    header('Location: ../front/group_by_name.php');
}