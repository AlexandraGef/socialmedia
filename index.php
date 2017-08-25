<?php
/**
 * Created by PhpStorm.
 * User: ideo5
 * Date: 23.08.2017
 * Time: 12:48
 */
include('./classes/DB.php');
include('./classes/Login.php');
if (Login::isLoggedIn()) {
    echo 'Jesteś zalogowany';
    echo Login::isLoggedIn();
} else {
    echo 'Jesteś nie zalogowany';
}
?>