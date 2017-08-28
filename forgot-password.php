<?php
/**
 * Created by PhpStorm.
 * User: ideo5
 * Date: 25.08.2017
 * Time: 11:50
 */
include('./classes/DB.php');
if (isset($_POST['resetpassword'])) {
    $cstrong = True;
    $token = bin2hex(openssl_random_pseudo_bytes(64, $cstrong));
    $email = $_POST['email'];
    $user_id = DB::query('SELECT id FROM users WHERE email=:email', array(':email'=>$email))[0]['id'];
    DB::query('INSERT INTO password_tokens (token,user_id) VALUES (:token, :user_id)', array(':token'=>sha1($token), ':user_id'=>$user_id));
    echo 'Email został wysłany!';
    echo '<br />';
    echo $token;
}
?>
<h1>Zapomniane hasło</h1>
<form action="forgot-password.php" method="post">
    <input type="text" name="email" value="" placeholder="Email"><p/>
    <input type="submit" name="resetpassword" value="Resetuj hasło">
</form>