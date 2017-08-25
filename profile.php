<?php
/**
 * Created by PhpStorm.
 * User: ideo5
 * Date: 25.08.2017
 * Time: 12:21
 */
include('./classes/DB.php');
include('./classes/Login.php');
$username = "";
if (isset($_GET['username'])) {
    if (DB::query('SELECT username FROM users WHERE username=:username', array(':username'=>$_GET['username']))) {
        $username = DB::query('SELECT username FROM users WHERE username=:username', array(':username'=>$_GET['username']))[0]['username'];
        if (isset($_POST['follow'])) {
            $userid = DB::query('SELECT id FROM users WHERE username=:username', array(':username'=>$_GET['username']))[0]['id'];
            $followerid = Login::isLoggedIn();
            if (!DB::query('SELECT follower_id FROM followers WHERE user_id=:user_id', array(':user_id'=>$userid))) {
                DB::query('INSERT INTO followers (user_id, follower_id) VALUES (:user_id, :follower_id)', array(':user_id'=>$userid, ':follower_id'=>$followerid));
            } else {
                echo 'Użytkownik jest już obserwowany!';
            }
        }
    } else {
        die('Użytkownik nie został znaleziony!');
    }
}
?>
<h1>Profil użytkownika <?php echo $username; ?></h1>
<form action="profile.php?username=<?php echo $username; ?>" method="post">
    <input type="submit" name="follow" value="Obserwuj">
</form>