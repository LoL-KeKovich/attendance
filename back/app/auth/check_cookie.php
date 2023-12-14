<?php
function check_cookie() {
    if (!empty($_COOKIE['body'])) {
        return true;
    }
    return false;
}