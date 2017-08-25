<?php
/**
 * Created by PhpStorm.
 * User: ideo5
 * Date: 25.08.2017
 * Time: 11:27
 */
include('./classes/DB.php');
include('./classes/Login.php');
$tokenIsValid = False;
if (Login::isLoggedIn()) {
    if (isset($_POST['changepassword'])) {
        $oldpassword = $_POST['oldpassword'];
        $newpassword = $_POST['newpassword'];
        $newpasswordrepeat = $_POST['newpasswordrepeat'];
        $userid = Login::isLoggedIn();
        if (password_verify($oldpassword, DB::query('SELECT password FROM users WHERE id=:userid', array(':userid'=>$userid))[0]['password'])) {
            if ($newpassword == $newpasswordrepeat) {
                if (strlen($newpassword) >= 6 && strlen($newpassword) <= 60) {
                    DB::query('UPDATE users SET password=:newpassword WHERE id=:userid', array(':newpassword'=>password_hash($newpassword, PASSWORD_BCRYPT), ':userid'=>$userid));
                    echo 'Hasło zostało zmienione!';
                }
            } else {
                echo 'Hasła do siebie nie pasują!';
            }
        } else {
            echo 'Nieprawidłowe stare hasło!';
        }
    }
} else {
    if (isset($_GET['token'])) {
        $token = $_GET['token'];
        if (DB::query('SELECT user_id FROM password_tokens WHERE token=:token', array(':token'=>sha1($token)))) {
            $userid = DB::query('SELECT user_id FROM password_tokens WHERE token=:token', array(':token'=>sha1($token)))[0]['user_id'];
            $tokenIsValid = True;
            if (isset($_POST['changepassword'])) {
                $newpassword = $_POST['newpassword'];
                $newpasswordrepeat = $_POST['newpasswordrepeat'];
                if ($newpassword == $newpasswordrepeat) {
                    if (strlen($newpassword) >= 6 && strlen($newpassword) <= 60) {
                        DB::query('UPDATE users SET password=:newpassword WHERE id=:userid', array(':newpassword'=>password_hash($newpassword, PASSWORD_BCRYPT), ':userid'=>$userid));
                        echo 'Twoje hasło zostało zmienione!';
                        DB::query('DELETE FROM password_tokens WHERE user_id=:userid', array(':userid'=>$userid));
                    }
                } else {
                    echo 'Hasła do siebie nie pasują!';
                }
            }
        } else {
            die('Token wygasł');
        }
    } else {
        die('Jesteś nie zalogowany');
    }
}
?>
<h1>Zmiana hasła</h1>
<form action="<?php if (!$tokenIsValid) { echo 'change-password.php'; } else { echo 'change-password.php?token='.$token.''; } ?>" method="post">
    <?php if (!$tokenIsValid) { echo '<input type="password" name="oldpassword" value="" placeholder="Aktualne hasło"><p />'; } ?>
    <input type="password" name="newpassword" value="" placeholder="Nowe hasło"><p />
    <input type="password" name="newpasswordrepeat" value="" placeholder="Powtórz nowe hasło"><p />
    <input type="submit" name="changepassword" value="Zmień hasło">
</form>