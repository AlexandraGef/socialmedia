<?php
/**
 * Created by PhpStorm.
 * User: ideo5
 * Date: 24.08.2017
 * Time: 13:09
 */
class DB
{
    private static function connect()
    {
        $pdo = new PDO('mysql:host=localhost;dbname=socialnetwork;charset=utf8', 'root', 'admin');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    }

    public static function query($query, $params = array()) {
        $statement = self::connect()->prepare($query);
        $statement->execute($params);
        if (explode(' ', $query)[0] == 'SELECT') {
            $data = $statement->fetchAll();
            return $data;
        }
    }
}
?>