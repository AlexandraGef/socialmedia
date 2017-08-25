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
$isFollowing = False;
$verified = False;
if (isset($_GET['username'])) {
    if (DB::query('SELECT username FROM users WHERE username=:username', array(':username'=>$_GET['username']))) {
        $username = DB::query('SELECT username FROM users WHERE username=:username', array(':username'=>$_GET['username']))[0]['username'];
        $userid = DB::query('SELECT id FROM users WHERE username=:username', array(':username'=>$_GET['username']))[0]['id'];
        $verified = DB::query('SELECT verified FROM users WHERE username=:username',array(':username'=>$_GET['username']))[0]['verified'];
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
                if (DB::query('SELECT follower_id FROM followers WHERE user_id=:userid', array(':userid'=>$userid))) {
                    DB::query('DELETE FROM followers WHERE user_id=:user_id AND follower_id=:follower_id', array(':user_id'=>$userid, ':follower_id'=>$followerid));
                }
                $isFollowing = False;
            }
        }
        if (DB::query('SELECT follower_id FROM followers WHERE user_id=:user_id', array(':user_id'=>$userid))) {

            $isFollowing = True;
        }
        if (isset($_POST['post'])) {
            $postbody = $_POST['postbody'];
            $loggedInUserId = Login::isLoggedIn();
            if (strlen($postbody) > 160 || strlen($postbody) < 1) {
                die('Nie prawidłowa długość!');
            }
            if ($loggedInUserId == $userid) {
                DB::query('INSERT INTO posts (body,posted_at,user_id, likes) VALUES (:body, NOW(), :user_id, 0)', array(':body'=>$postbody, ':user_id'=>$userid));
            } else {
                die('Nieprawidłowy użytkownik!');
            }
        }
        if (isset($_GET['postid'])) {
            if (!DB::query('SELECT user_id FROM post_likes WHERE post_id=:post-id AND user_id=:user_id', array(':post_id'=>$_GET['postid'], ':user_id'=>$userid))) {
                DB::query('UPDATE posts SET likes=likes+1 WHERE id=:postid', array(':post_id'=>$_GET['postid']));
                DB::query('INSERT INTO post_likes (post_id,user_id) VALUES (:postid, :userid)', array(':post_id'=>$_GET['postid'], ':user_id'=>$userid));
            } else {
                DB::query('UPDATE posts SET likes=likes-1 WHERE id=:postid', array(':post_id'=>$_GET['postid']));
                DB::query('DELETE FROM post_likes WHERE post_id=:postid AND user_id=:userid', array(':post_id'=>$_GET['postid'], ':user_id'=>$userid));
            }
        }
        $dbposts = DB::query('SELECT * FROM posts WHERE user_id=:userid ORDER BY id DESC', array(':userid'=>$userid));
        $posts = "";
        foreach($dbposts as $p) {
            $posts .= htmlspecialchars($p['body'])."
                        <form action='profile.php?username=$username&postid=".$p['id']."' method='post'>
                                <input type='submit' name='like' value='Like'>
                        </form>
                        <hr /></br />
                        ";
        }
    } else {
        die('Nie znaleziono użytkownika!');
    }
}
?>
<h1>Profil użytkownika <?php echo $username; ?></h1>
<form action="profile.php?username=<?php echo $username; ?>" method="post">
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
<form action="profile.php?username=<?php echo $username; ?>" method="post">
    <textarea name="postbody" rows="8" cols="80"></textarea>
    <input type="submit" name="post" value="Post">
</form>

<div class="posts">
    <?php echo $posts; ?>
</div>
