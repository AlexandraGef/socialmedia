<?php
/**
 * Created by PhpStorm.
 * User: ideo5
 * Date: 23.08.2017
 * Time: 12:49
 */
include ('classes/DB.php');
if(isset($_POST['createaccount']))
{
    $username= $_POST['username'];
    $password = $_POST['password'];
    $email = $_POST['email'];

    if (!DB::query('SELECT username FROM users WHERE username=:username', array(':username'=>$username))) {
        if (strlen($username) >= 3) {
            if (strlen($username) <= 45) {
            if (preg_match('/[a-zA-Z0-9_]+/', $username)) {
                if (strlen($password) >= 6 && strlen($password) <= 60) {
                    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                        if (!DB::query('SELECT email FROM users WHERE email=:email', array(':email'=>$email))) {
                        DB::query('INSERT INTO users (username, password,email) VALUES (:username, :password, :email)', array(':username'=>$username, ':password'=>password_hash($password, PASSWORD_BCRYPT), ':email'=>$email));
                        echo "Gratulujemy dołączyłeś do Naszego grona użytkowników !";
                        } else {
                            echo 'Email jest w użyciu!';
                        }
                    } else {
                        echo 'Nieprawidłowy email!';
                    }
                } else {
                    echo 'Nieprawidłowe hasło!';
                }
            } else {
                echo 'Login zawiera nie dozwolone znaki !';
            }
            } else {
                echo 'Podany login jest za długi';
            }
        } else {
            echo 'Podany login jest za krótki';
        }
    } else {
        echo 'Dany użytkownik już istnieje !';
    }

}
?>

<h1>Rejestracja</h1>

<form action="create-account.php" method="post">
    <input type="text" name="username" value="" placeholder="Login..."><p/>
    <input type="password" name="password" value="" placeholder="Hasło..."><p/>
    <input type="email" name="email" value="" placeholder="przykład@email.pl"><p/>
    <input type="submit" name="createaccount" value="Utwórz konto">
</form>

