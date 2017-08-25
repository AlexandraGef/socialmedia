<?php
/**
 * Created by PhpStorm.
 * User: ideo5
 * Date: 23.08.2017
 * Time: 12:49
 */
include ('classes/DB.php');
if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    if (DB::query('SELECT username FROM users WHERE username=:username', array(':username' => $username))) {

        if (password_verify($password, DB::query('SELECT password FROM users WHERE username=:username', array(':username' => $username))[0]['password'])) {
            echo 'Witaj ' . $username;

        } else {
            echo "Nieprawidłowe hasło!";
        }

    } else {


        echo "Podany użytkownik nie istnieje !";

    }
}

?>
<h1>Zaloguj się</h1>

<form action="login.php" method="post">
    <input type="text" name="username" value="" placeholder="Login"> <p/>
    <input type="password" name="password" value="" placeholder="Hasło"><p/>
    <input type="submit" name="login" value="Zaloguj">
</form>

