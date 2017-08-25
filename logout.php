<?php
/**
 * Created by PhpStorm.
 * User: ideo5
 * Date: 25.08.2017
 * Time: 10:58
 */
include('./classes/DB.php');
include('./classes/Login.php');
if (!Login::isLoggedIn()) {
    die("Jestęs nie zalogowany");
}
if (isset($_POST['confirm'])) {
    if (isset($_POST['alldevices'])) {
        DB::query('DELETE FROM login_tokens WHERE user_id=:userid', array(':userid'=>Login::isLoggedIn()));
    } else {
        if (isset($_COOKIE['SNID'])) {
            DB::query('DELETE FROM login_tokens WHERE token=:token', array(':token'=>sha1($_COOKIE['SNID'])));
        }
        setcookie('SNID', '1', time()-3600);
        setcookie('SNID_', '1', time()-3600);
    }
}
?>
<h1>Chcesz się wylogować ?</h1>
<p>Czy napewno chcesz się wylogować?</p>
<form action="logout.php" method="post">
    <input type="checkbox" name="alldevices" value="alldevices"> Wylogować użytkownika ze wszystkich urządzeń?<br />
    <input type="submit" name="confirm" value="Wyloguj">
</form>