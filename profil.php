<?php
/**
 * Created by PhpStorm.
 * User: ideo5
 * Date: 25.08.2017
 * Time: 12:21
 */
include('./classes/DB.php');
include('./classes/Login.php');
include('./classes/Post.php');
$username = "";
$isFollowing = False;
$verified = False;
if (isset($_GET['username'])) {
    if (DB::query('SELECT username FROM users WHERE username=:username', array(':username'=>$_GET['username']))) {
        $username = DB::query('SELECT username FROM users WHERE username=:username', array(':username' => $_GET['username']))[0]['username'];
        $userid = DB::query('SELECT id FROM users WHERE username=:username', array(':username' => $_GET['username']))[0]['id'];
        $verified = DB::query('SELECT verified FROM users WHERE username=:username', array(':username' => $_GET['username']))[0]['verified'];
        $followerid = Login::isLoggedIn();
        if (isset($_POST['follow'])) {
            if ($userid != $followerid) {
                if (!DB::query('SELECT follower_id FROM followers WHERE user_id=:user_id', array(':user_id' => $userid))) {
                    DB::query('INSERT INTO followers (user_id, follower_id) VALUES (:user_id, :follower_id)', array(':user_id' => $userid, ':follower_id' => $followerid));
                }
                $isFollowing = True;
            }
        }
        if (isset($_POST['unfollow'])) {
            if ($userid != $followerid) {
                if (DB::query('SELECT follower_id FROM followers WHERE user_id=:user_id', array(':user_id' => $userid))) {
                    DB::query('DELETE FROM followers WHERE user_id=:user_id AND follower_id=:follower_id', array(':user_id' => $userid, ':follower_id' => $followerid));
                }
                $isFollowing = False;
            }
        }
        if (DB::query('SELECT follower_id FROM followers WHERE user_id=:user_id', array(':user_id' => $userid))) {
            $isFollowing = True;
        }
        if (isset($_POST['post'])) {
            Post::createPost($_POST['postbody'], Login::isLoggedIn(), $userid);
        }
        if (isset($_GET['postid'])) {
            Post::likePost($_GET['postid'], $followerid);
        }
        $posts = Post::displayPosts($userid, $username, $followerid);
    } else {
        die('Nie zanleziono użytkownika!');
    }
}
?>
<h1>Profil użytkownika <?php echo $username; ?></h1>
<form action="profil.php?username=<?php echo $username; ?>" method="post">
    <?php
    if ($userid != $followerid) {
        if ($isFollowing) {
            echo '<input type="submit" name="unfollow" value="Przestań obserwować">';
        } else {
            echo '<input type="submit" name="follow" value="Obserwuj">';
        }
    }
    ?>
</form>
</form>
<form action="profil.php?username=<?php echo $username; ?>" method="post">
    <textarea name="postbody" rows="8" cols="80"></textarea>
    <input type="submit" name="post" value="Post">
</form>

<div class="posts">
    <?php echo $posts; ?>
</div>