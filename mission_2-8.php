<html>
<head><title>MISSION 2-8</title></head>
<meta charset="UTF-8">
<body>

<?php
require_once 'functions.php';// 定義集

$sql_create_commentdata = "CREATE TABLE IF NOT EXISTS "
. tbl_name(0)
. " ("
. "commentId INTEGER AUTO_INCREMENT PRIMARY KEY COMMENT '投稿暗号',"
. "name CHAR(255) NOT NULL COMMENT 'ペンネーム',"
. "comment CHAR(255) NOT NULL COMMENT '投稿内容',"
. "commentTime TIMESTAMP COMMENT '投稿日時',"
. "password varchar(50) NOT NULL COMMENT 'パスワード',"
. "mp MEDIUMBLOB COMMENT '動画像'"
. ");";

$sql_create_userdata = "CREATE TABLE IF NOT EXISTS "
. tbl_name(1)
. " ("
. "`userId` INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY COMMENT 'ユーザー番号',"
. "`userName` CHAR(255) NOT NULL COMMENT 'ユーザー名',"
. "`mail` VARCHAR(50) NOT NULL COMMENT 'メールアドレス',"
. "`password` varchar(50) NOT NULL COMMENT 'パスワード'"
. ");";

$sql_create_premenber = "CREATE TABLE IF NOT EXISTS "
. tbl_name(2)
. " ("
. "userId INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY COMMENT 'ユーザー番号',"
. "urltoken VARCHAR(128) NOT NULL,"
. "mail VARCHAR(50) NOT NULL,"
. "date DATETIME NOT NULL,"
. "flag TINYINT(1) NOT NULL DEFAULT 0"
. ");";

function sql_drop ($tbl_name) {
  $sql_drop = "DROP TABLE {$tbl_name};";
  return $sql_drop;
}

try{
    $dbh = new PDO($dsn, $user, $password, $options);

    print('接続に成功しました。<br>');	
	if (isset($_POST['commentdata'])) {
	  $result = $dbh->query(sql_drop(tbl_name(0)));
	  $result = $dbh->query($sql_create_commentdata);
	  print('「commentData」の初期化に成功しました。<br>');	
	} elseif (isset($_POST['userdata'])) {
	  $result = $dbh->query(sql_drop(tbl_name(1)));
	  $result = $dbh->query($sql_create_userdata);
	  print('「userData」の初期化に成功しました。<br>');
	  $result = $dbh->query(sql_drop(tbl_name(2)));
	  $result = $dbh->query($sql_create_premenber);
	  print('「preMenber」の初期化に成功しました。<br>');
	}

}catch (PDOException $e){
    print('Error:'.$e->getMessage());
    die();
}

$dbh = null;

?>
    <form action="mission_2-8.php" method="post">
      <fieldset>          
        <legend>commentData</legend>
		<p>データベースのcommentDataの初期化</p>
		<label for="commentdata">初期化</label>
        <input type="submit" name="commentdata" value="初期化">
      </fieldset>
    </form>
	<form action="mission_2-8.php" method="post">
      <fieldset>          
        <legend>userData</legend>
		<p>データベースのuserDataの初期化</p>
		<label for="userdata">初期化</label>
        <input type="submit" name="userdata" value="初期化">
      </fieldset>
    </form>
  </body>
</html>