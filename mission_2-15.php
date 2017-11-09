<?php
// 変数定義----------------------------------------
$d1 = date('Y/m/d H:i:s');// 年・月・日 時・分・秒
$number = 1;// 投稿番号初期値
// 文字化け対策
$options = array(PDO::MYSQL_ATTR_INIT_COMMAND=>"SET CHARACTER SET 'sjis'");
$dsn = 'データベース名';
$user = 'ユーザー名';
$password = 'パスワード';
$tbl_name = "commentData";
// -----------------------------------------------

// SQL文の定義-------------------------------------
// insert SQL文の作成
$sql_insert = "INSERT INTO {$tbl_name} "
. "("
. "name, comment, commentTime, password"
. ")"
. " VALUES "
. "("
. ":name, :comment, now(), :password"
. ");";

// select SQL文の作成
$sql_select = "SELECT * FROM {$tbl_name};";

function sql_select ($tbl_name, $id) {
  $sql_select = "SELECT * FROM {$tbl_name}"
  . " WHERE commentId = {$id};";
  return $sql_select;
}

// update SQL文の作成
function  sql_update ($tbl_name, $name, $comment, $id) {
  $sql_update = "UPDATE {$tbl_name} "
  . "SET name = '{$name}', comment = '{$comment}'"
  . " WHERE commentId = {$id};";
  return $sql_update;
}

// delete SQL文の作成
function  sql_delete ($tbl_name, $id) {
  $sql_delete = "DELETE FROM {$tbl_name} "
  . " WHERE commentId = {$id};";
  return $sql_delete;
}
// -----------------------------------------------

// 投稿内容の有無確認・テキストファイルへの追記----------
if (preg_match("/.+/", $_POST['name'])) {// 名前入力欄に入力があるかを確認
  if (preg_match("/.+/", $_POST['comment'])) {// コメント入力欄に入力があるかを確認
    if ($_POST['mode'] == "edit") {// 編集モードかを判断
        $id = $_POST['number'];
		$name_update = $_POST['name'];
		$comment_update = $_POST['comment'];
  
        try{
          $dbh = new PDO($dsn, $user, $password, $options);
		  echo "編集完了";
          $result = $dbh->query(sql_update($tbl_name, $name_update, $comment_update, $id));
        }catch (PDOException $e){
          print('Error:'.$e->getMessage());
          die();
        }
		
		$id = "";
		$name_update = "";
		$comment_update = "";
	    $_POST['mode'] = "";
    } else {
	  if (preg_match("/.+/", $_POST['password'])) {// パスワード入力欄に入力があるかを確認
	     try{
          $dbh = new PDO($dsn, $user, $password, $options);
          echo "入力モード";
          // 挿入する値は空のまま、SQL実行の準備をする
          $stmt = $dbh->prepare($sql_insert);
          // 挿入する値を配列に格納する
          $params = array(':name' => $_POST['name'], ':comment' => $_POST['comment'], ':password' => $_POST['password']);
          // 挿入する値が入った変数をexecuteにセットしてSQLを実行
          $stmt->execute($params);
        }catch (PDOException $e){
          print('Error:'.$e->getMessage());
          die();
        }
	  } else {
	    echo "パスワードも入力してください<br>";
	  }
	}
  } else {
    echo "コメントとパスワードも入力してください<br>";
  }
} else {
  echo "名前とコメントとパスワードを入力してください<br>";
}
// -----------------------------------------------

// 投稿削除処理-------------------------------------
if (preg_match("/^[0-9]+$/", $_POST['delete'])) {
  if (preg_match("/.+/", $_POST['password_delete'])) {// パスワード入力欄に入力があるかを確認
    $id = $_POST['delete'];
	try{
      $dbh = new PDO($dsn, $user, $password, $options);
	  $result = $dbh->query(sql_select($tbl_name, $id));
	  foreach ($result as $row) {
        $pass_check = $row['password'];
      }	
	  if ($_POST['password_delete'] == $pass_check) {
	    $result = $dbh->query(sql_delete($tbl_name, $id));
		
	  } else {
		echo "パスワードが違います。";
	  }    
    }catch (PDOException $e){
      print('Error:'.$e->getMessage());
      die();
    }
	$id = "";
  } else {
    echo "パスワードを入力してください。";
  }
}
// -----------------------------------------------

// 投稿編集処理-------------------------------------
if (preg_match("/^[0-9]+$/", $_POST['edit'])) {
  //パスワード要求
  if (preg_match("/.+/", $_POST['password_edit'])) {// パスワード入力欄に入力があるかを確認
    $id = $_POST['edit'];
	try{
      $dbh = new PDO($dsn, $user, $password, $options);
	  $result = $dbh->query(sql_select($tbl_name, $id));
	  foreach ($result as $row) {
		$number_edit = $_POST['edit'];
		$name = $row['name'];
		$comment = $row['comment'];
		$time = $row['commentTime'];
		$password_edit = $row['password'];
      }	
	  if ($_POST['password_edit'] == $password_edit) {
	    echo "編集モード" . "<br>";
	  } else {
		echo "パスワードが違います。";
		$name = "";
		$comment = "";
		$time = "";
		$password_edit = "";
	  }    
    }catch (PDOException $e){
      print('Error:'.$e->getMessage());
      die();
    }
	$id = "";
  } else {
    echo "パスワードを入力してください。";
  }
}
?>

<!--入力フォーム集-->
<table bordercolor="#ffffff" rules="rows" border="1" bgcolor="#aa99ff" align="center">
  <tr>
    <td>&emsp;</td><td align="center">投稿番号入力欄</td><td align="center">ペンネーム入力欄</td><td align="center">投稿内容入力欄</td><td align="center">パスワード入力欄</td><td>&emsp;</td>
  </tr>
  <tr>
    <td align="center">投稿</td>
	<td>&emsp;</td>
    <!--名前を入力してください。-->
    <form method="post" action="mission_2-15.php">
	  <td>
        <!--名前入力フォーム-->
        <input type="text" name="name" value="<?php echo $name; ?>">
	  </td>

      <td>
        <!--コメントを入力してください。-->
        <!--コメント入力フォーム-->
        <input type="text" name="comment" value="<?php echo $comment; ?>">
        <!--編集時の隠しフォーム-->
        <input type="hidden" name="number" value="<?php echo $number_edit; ?>">
        <input type="hidden" name="time" value="<?php echo $time; ?>">
        <input type="hidden" name="mode" value="<?php echo $_POST['mode_edit']; ?>">
        <input type="hidden" name="password_edit_hidden" value="<?php echo $password_edit; ?>">
	  </td>

      <td>
        <!--パスワード-->
        <!--登録するパスワードを入力してください。-->
        <input type="password" name="password" value="">
	  </td>
  
      <td>
        <!--送信ボタン-->
        <input type="submit" value="投稿"> 
	  </td>
    </form>
  </tr>
  
  <tr>
    <td align="center">投稿の削除</td>
    <form method="post" action="mission_2-15.php">
	  <td>
        <!--削除フォーム-->
        <!--削除したい投稿番号を入力してください-->
        <input type="text" name="delete" value="<?php echo $_POST['delete']; ?>">
	  </td>
	  <td>&emsp;</td>
	  <td>&emsp;</td>
	  <td>
        <!--パスワードを入力してください。-->
        <input type="password" name="password_delete" value="">
	  </td>
	  <td>
        <input type="submit" value="削除">
	  </td>
    </form>
  </tr>

  <tr>
    <td align="center">投稿の編集</td>
    <form method="post" action="mission_2-15.php">
	  <td>
        <!--編集フォーム-->
        <!--編集したい投稿番号を入力してください-->
        <input type="text" name="edit" value="<?php echo $_POST['edit']; ?>">
        <input type="hidden" name="mode_edit" value="edit">
	  </td>
	  <td>&emsp;</td>
	  <td>&emsp;</td>
	  <td>
        <!--パスワードを入力してください。-->
        <input type="password" name="password_edit" value="">
	  </td>
	  <td>
        <input type="submit" value="編集">
	  </td>
    </form>
  </tr>
<table>

<hr>

<table bordercolor="#ffffff" rules="rows" border="1" bgcolor="#ff99aa" align="center">
<?php
// 投稿内容の表示-----------------------------------
try{
  $dbh = new PDO($dsn, $user, $password, $options);
  $result = $dbh->query($sql_select);
  echo '<tr><td align="center">'. '投稿番号' . '</td><td>' . '&emsp;' . '</td><td align="center">' . 'ペンネーム' . '</td><td>' . '&emsp;' . '</td><td align="center">' . '投稿内容' . '</td><td>' . '&emsp;' . '</td><td align="center">' . '投稿時間' . '</td>' . '</tr>';
  foreach ($result as $row) {
    echo '<tr>';
	echo '<td align="center">' . $row['commentId'] . '</td><td></td><td align="center">' . $row['name'] . '</td><td></td><td>' . $row['comment'] . '</td><td></td><td>' . $row['commentTime'] . '</td>';
	echo '</tr>';
  }	   
}catch (PDOException $e){
  print('Error:'.$e->getMessage());
  die();
}
?>
</table>