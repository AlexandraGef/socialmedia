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
include('./classes/Image.php');
$username = "";
$verified = False;
$isFollowing = False;
if (isset($_GET['username'])) {
    if (DB::query('SELECT username FROM users WHERE username=:username', array(':username'=>$_GET['username']))) {
        $username = DB::query('SELECT username FROM users WHERE username=:username', array(':username'=>$_GET['username']))[0]['username'];
        $userid = DB::query('SELECT id FROM users WHERE username=:username', array(':username'=>$_GET['username']))[0]['id'];
        $verified = DB::query('SELECT verified FROM users WHERE username=:username', array(':username'=>$_GET['username']))[0]['verified'];
        $followerid = Login::isLoggedIn();
        if (isset($_POST['follow'])) {
            if ($userid != $followerid) {
                if (!DB::query('SELECT follower_id FROM followers WHERE user_id=:userid AND follower_id=:followerid', array(':userid'=>$userid, ':followerid'=>$followerid))) {
                    if ($followerid == 6) {
                        DB::query('UPDATE users SET verified=1 WHERE id=:userid', array(':userid'=>$userid));
                    }
                    DB::query('INSERT INTO followers (user_id,follower_id) VALUES (:userid, :followerid)', array(':userid'=>$userid, ':followerid'=>$followerid));
                }
                $isFollowing = True;
            }
        }
        if (isset($_POST['unfollow'])) {
            if ($userid != $followerid) {
                if (DB::query('SELECT follower_id FROM followers WHERE user_id=:userid AND follower_id=:followerid', array(':userid'=>$userid, ':followerid'=>$followerid))) {
                    if ($followerid == 6) {
                        DB::query('UPDATE users SET verified=0 WHERE id=:userid', array(':userid'=>$userid));
                    }
                    DB::query('DELETE FROM followers WHERE user_id=:userid AND follower_id=:followerid', array(':userid'=>$userid, ':followerid'=>$followerid));
                }
                $isFollowing = False;
            }
        }
        if (DB::query('SELECT follower_id FROM followers WHERE user_id=:userid AND follower_id=:followerid', array(':userid'=>$userid, ':followerid'=>$followerid))) {

            $isFollowing = True;
        }
        if (isset($_POST['deletepost'])) {
            if (DB::query('SELECT id FROM posts WHERE id=:postid AND user_id=:userid', array(':postid'=>$_GET['postid'], ':userid'=>$followerid))) {
                DB::query('DELETE FROM posts WHERE id=:postid and user_id=:userid', array(':postid'=>$_GET['postid'], ':userid'=>$followerid));
                DB::query('DELETE FROM post_likes WHERE post_id=:postid', array(':postid'=>$_GET['postid']));
            }
        }
        if (isset($_POST['post'])) {
            if ($_FILES['postimg']['size'] == 0) {
                Post::createPost($_POST['postbody'], Login::isLoggedIn(), $userid);
            } else {
                $postid = Post::createImgPost($_POST['postbody'], Login::isLoggedIn(), $userid);
                Image::uploadImage('postimg', "UPDATE posts SET postimg=:postimg WHERE id=:postid", array(':postid'=>$postid));
            }
        }
        if (isset($_GET['postid']) && !isset($_POST['deletepost'])) {
            Post::likePost($_GET['postid'], $followerid);
        }
        $posts = Post::displayPosts($userid, $username, $followerid);
    } else {
        die('Użytkownik nie został znaleziony!');
    }
}
?>
<h1>Profil użytkownika <?php echo $username; ?><?php if ($verified) { echo ' - Verified'; } ?></h1>
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

<form action="profil.php?username=<?php echo $username; ?>" method="post" enctype="multipart/form-data">
    <textarea name="postbody" rows="8" cols="80"></textarea>
    <br />Wgraj zdjęcie:
    <input type="file" name="postimg">
    <input type="submit" name="post" value="Udostępnij">
</form>

<div class="posts">
    <?php echo $posts; ?>
</div>