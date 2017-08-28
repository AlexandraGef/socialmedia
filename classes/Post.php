<?php
/**
 * Created by PhpStorm.
 * User: ideo5
 * Date: 28.08.2017
 * Time: 10:48
 */

class Post {
    public static function createPost($postbody, $loggedInUserId, $profileUserId) {
        if (strlen($postbody) > 160 || strlen($postbody) < 1) {
            die('Nieprawidłowa długość postu!');
        }
        $topics = self::getTopics($postbody);

        if ($loggedInUserId == $profileUserId) {
            DB::query('INSERT INTO posts (body,posted_at,user_id, likes,postimg,topics) VALUES (:body, NOW(), :userid, 0,\'\',:topics)', array(':body'=>$postbody, ':userid'=>$profileUserId, ':topics'=>$topics));
        } else {
            die('Nieprawidłowy użytkownik!');
        }
    }
    public static function likePost($postId, $likerId) {
        if (!DB::query('SELECT user_id FROM post_likes WHERE post_id=:postid AND user_id=:userid', array(':postid'=>$postId, ':userid'=>$likerId))) {
            DB::query('UPDATE posts SET likes=likes+1 WHERE id=:postid', array(':postid'=>$postId));
            DB::query('INSERT INTO post_likes (post_id, user_id) VALUES (:postid, :userid)', array(':postid'=>$postId, ':userid'=>$likerId));
        } else {
            DB::query('UPDATE posts SET likes=likes-1 WHERE id=:postid', array(':postid'=>$postId));
            DB::query('DELETE FROM post_likes WHERE post_id=:postid AND user_id=:userid', array(':postid'=>$postId, ':userid'=>$likerId));
        }
    }

    public static function createImgPost($postbody, $loggedInUserId, $profileUserId) {
        if (strlen($postbody) > 160) {
            die('Nieprawidłowa długość postu!');
        }
        $topics = self::getTopics($postbody);
        if ($loggedInUserId == $profileUserId) {
            DB::query('INSERT INTO posts (body,posted_at,user_id, likes,postimg,topics) VALUES (:postbody, NOW(), :userid, 0, \'\', \'\')', array(':postbody'=>$postbody, ':userid'=>$profileUserId, ':topics'=>$topics));
            $postid = DB::query('SELECT id FROM posts WHERE user_id=:userid ORDER BY ID DESC LIMIT 1;', array(':userid'=>$loggedInUserId))[0]['id'];
            return $postid;
        } else {
            die('Nieprawidłowy użytkownik!');
        }
    }

    public static function getTopics($text) {
        $text = explode(" ", $text);
        $topics = "";
        foreach ($text as $word) {
            if (substr($word, 0, 1) == "#") {
                $topics .= substr($word, 1).",";
            }
        }
        return $topics;
    }
    public static function link_add($text) {
        $text = explode(" ", $text);
        $newstring = "";
        foreach ($text as $word) {
            if (substr($word, 0, 1) == "@") {
                $newstring .= "<a href='profil.php?username=".substr($word, 1)."'>".htmlspecialchars($word)."</a> ";
            } else if (substr($word, 0, 1) == "#") {
                $newstring .= "<a href='topics.php?topic=".substr($word, 1)."'>".htmlspecialchars($word)."</a> ";
            } else {
                $newstring .= htmlspecialchars($word)." ";
            }
        }
        return $newstring;
    }

    public static function displayPosts($userid, $username, $loggedInUserId) {
        $dbposts = DB::query('SELECT * FROM posts WHERE user_id=:userid ORDER BY id DESC', array(':userid'=>$userid));
        $posts = "";
        foreach($dbposts as $p) {
            if (!DB::query('SELECT post_id FROM post_likes WHERE post_id=:postid AND user_id=:userid', array(':postid'=>$p['id'], ':userid'=>$loggedInUserId))) {
                $posts .= "<img src='".$p['postimg']."'>".self::link_add($p['body'])."
                                <form action='profil.php?username=$username&postid=".$p['id']."' method='post'>
                                        <input type='submit' name='like' value='Lubię'>
                                        <span>".$p['likes']." polubień</span>
                                ";
                if ($userid == $loggedInUserId) {
                    $posts .= "<input type='submit' name='deletepost' value='x' />";
                }
                $posts .= "
                                </form><hr /></br />
                                ";
            } else {
                $posts .= "<img src='".$p['postimg']."'>".self::link_add($p['body'])."
                                <form action='profil.php?username=$username&postid=".$p['id']."' method='post'>
                                <input type='submit' name='unlike' value='Nie lubie'>
                                <span>".$p['likes']." polubień</span>
                                ";
                if ($userid == $loggedInUserId) {
                    $posts .= "<input type='submit' name='deletepost' value='x' />";
                }
                $posts .= "
                                </form><hr /></br />
                                ";
            }
        }
        return $posts;
    }
}
?>