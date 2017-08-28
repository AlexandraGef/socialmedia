<?php
/**
 * Created by PhpStorm.
 * User: ideo5
 * Date: 28.08.2017
 * Time: 13:02
 */
include('./classes/DB.php');
include('./classes/Login.php');
include('./classes/Image.php');
if (Login::isLoggedIn()) {
    $userid = Login::isLoggedIn();
} else {
    die('Proszę się zalogować!');
}
if (isset($_POST['uploadprofileimg'])) {
    Image::uploadImage('profileimg', "UPDATE users SET profileimg = :profileimg WHERE id=:userid", array(':userid'=>$userid));
}
?>
<h1>Moje konto</h1>
<form action="my-account.php" method="post" enctype="multipart/form-data">
    Prosze wgrać zdjęcie profilowe:
    <input type="file" name="profileimg">
    <input type="submit" name="uploadprofileimg" value="Wgraj">
</form>