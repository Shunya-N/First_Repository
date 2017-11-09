<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		<title>メインページ</title>
	</head>

<?php
// セッション開始
session_start();

if (isset($_SESSION["NAME"]) && $_SESSION["NAME"] != null && $_SESSION["TIME"] >= time()-600) {
	$_SESSION["TIME"] = time();
	setcookie("my_name", $_SESSION["NAME"], time()+60*60*24*365);
	setcookie("my_time", date('Y年m月d日H時i分s秒'), time()+60*60*24*365);

	require_once 'functions.php';// 定義集
// ログアウト処理----------------------------------
	if (isset($_POST["logout"])) {
		$_SESSION = array();
		session_destroy();
	}
// -----------------------------------------------

	require_logined_session();// ログインしていなければmission_3-7.phpに遷移
	echo "こんにちは　" . $_SESSION["NAME"] . "　さん<br>";

// SQL文の定義-------------------------------------
// insert SQL文の作成
$sql_insert = "INSERT INTO "
. tbl_name(0)
. " ("
. "name, comment, commentTime, password"
. ")"
. " VALUES "
. "("
. ":name, :comment, now(), :password"
. ");";

$sql_insert_mp = "INSERT INTO "
. tbl_name(0)
. " ("
. "name, comment, commentTime, password, mp"
. ")"
. " VALUES "
. "("
. ":name, :comment, now(), :password, :mp"
. ");";

// regist SQL文の作成
$sql_regist = "INSERT INTO "
. tbl_name(1)
. " ("
. "userName, password"
. ")"
. " VALUES "
. "("
. ":userName, :password"
. ");";
// -----------------------------------------------

// 投稿内容の有無確認・テキストファイルへの追記----------
// 送信ボタンが押された場合
	if (isset($_POST["submit"])) {
		// 1. ペンネームとコメントの入力チェック
		if (empty($_POST["name"])) {  // emptyは値が空のとき
			$errorMessage = 'ペンネームが未入力です。';
		} else if (empty($_POST["comment"])) {
			$errorMessage = 'コメントが未入力です。';
		}

		if (!empty($_POST["name"]) && !empty($_POST["comment"])) {
			if ($_POST['mode'] == "edit") {// 編集モードかを判断
				$id = $_POST['number'];
				$name_update = $_POST['name'];
				$comment_update = $_POST['comment'];

				try{
					$dbh = open_database();
					$result = $dbh->query(sql_update(tbl_name(0), $name_update, $comment_update, $id));
				}catch (PDOException $e){
					print('Error:'.$e->getMessage());
					die();
				}

				$id = "";
				$name_update = "";
				$comment_update = "";
			} else {
				if (!empty($_POST['password'])) {// パスワード入力欄に入力があるかを確認
					try{
						$dbh = open_database();
						echo "入力モード<br>";
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
					echo "投稿: パスワードも入力してください<br>";
				}
			}
		}
	}
// -----------------------------------------------

// 動画・画像のアップロード---------------------------
// アップロードボタンが押された場合
	if (isset($_POST["upload"])) {
		$upfile = $_FILES['filename'];
		if ($upfile['size'] > 0 && (($upfile['type'] == 'image/jpeg' || $upfile['type'] == 'image/pjpeg') || $upfile['type'] == 'video/mp4')) {
			if ($upfile['size'] > 1024*1024) {
				unlink($upfile['tmp_name']);
?>
<p>アップするファイルのサイズは1MB以下にしてください</p>
<?php
			} else {
				if (strtolower(mb_strrchr($upfile['name'], '.', FALSE)) == ".jpg") {
					// アップロードされた画像ファイルを移動
					$ima = date('YmdHis');
					$fn = $ima . $upfile['name'];
					move_uploaded_file($upfile['tmp_name'], './mp/'.$fn);

					// サムネイルの作成
					$motogazo = imagecreatefromjpeg("./mp/$fn");
					list($w, $h) = getimagesize("./mp/$fn");
					$new_h = 200;
					$new_w = $w * 200 / $h;
					$mythumb = imagecreatetruecolor($new_h, $new_w);
					imagecopyresized($mythumb, $motogazo, 0, 0, 0, 0, $new_w, $new_h, $w ,$h);
					imagejpeg($mythumb, "./mp/thumb_$fn");

					// サムネイルの表示
					print "<p>" . $upfile['name'] . "のアップロードに成功！<br><img src='./mp/thumb_$fn'></p>";
				} elseif (strtolower(mb_strrchr($upfile['name'], '.', FALSE)) == ".mp4") {
					// アップロードされた画像ファイルを移動
					$ima = date('YmdHis');
					$fn = $ima . $upfile['name'];
					move_uploaded_file($upfile['tmp_name'], './mp/'.$fn);

					// サムネイルの表示
					print "<p>" . $upfile['name'] . "のアップロードに成功！<br><video src='./mp/$fn' width=" . 320 . " height=" . 240 . "></video></p>";
				}

				// データベースに追加
				try{
					$dbh = open_database();
					echo "アップロードモード<br>";
					// 挿入する値は空のまま、SQL実行の準備をする
					$stmt = $dbh->prepare($sql_insert_mp);
					// 挿入する値を配列に格納する
					$params = array(':name' => $_SESSION["NAME"], ':comment' => "picture", ':password' => "shunya", ':mp' => $fn);
					// 挿入する値が入った変数をexecuteにセットしてSQLを実行
					$stmt->execute($params);
				}catch (PDOException $e){
					print('Error:'.$e->getMessage());
					die();
				}
			}
		} else {
?>
<p>名前とメッセージを入力しJPEGファイルを選択してください<br>

<?php
		}
	}
// -----------------------------------------------

// 投稿削除処理-------------------------------------
	if (preg_match("/^[0-9]+$/", $_POST['delete'])) {
		if (!empty($_POST['password_delete'])) {// パスワード入力欄に入力があるかを確認
			$id = $_POST['delete'];
			try{
				$dbh = open_database();
				$result = $dbh->query(sql_select_all(tbl_name(0), 'commentId', $id));
				foreach ($result as $row) {
					$pass_check = $row['password'];
				}
				if ($_POST['password_delete'] === $pass_check) {
					$result = $dbh->query(sql_delete(tbl_name(0), $id));
				} else {
					echo "削除: パスワードが違います<br>";
				}    
			}catch (PDOException $e){
				print('Error:'.$e->getMessage());
				die();
			}
				$id = "";
		} else {
			echo "削除: パスワードを入力してください<br>";
		}
	}
// -----------------------------------------------

// 投稿編集処理-------------------------------------
	if (preg_match("/^[0-9]+$/", $_POST['edit'])) {
		//パスワード要求
		if (!empty($_POST['password_edit'])) {// パスワード入力欄に入力があるかを確認
			$id = $_POST['edit'];
			try{
				$dbh = open_database();
				$result = $dbh->query(sql_select_all(tbl_name(0), 'commentId', $id));
				foreach ($result as $row) {
					$number_edit = $_POST['edit'];
					$name = $row['name'];
					$comment = $row['comment'];
					$time = $row['commentTime'];
					$password_edit = $row['password'];
				}
				if ($_POST['password_edit'] === $password_edit) {
					echo "編集モード" . "<br>";
				} else {
					echo "編集: パスワードが違います<br>";
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
			echo "編集: パスワードを入力してください<br>";
		}
	}
// -----------------------------------------------
?>

	<body>
		<h1>メインページ</h1>
		<!--入力フォーム集-->
		<table bordercolor="#ffffff" rules="rows" border="1" bgcolor="#aa99ff" align="center">
			<tr>
				<td>&emsp;</td><td align="center">投稿番号入力欄</td><td align="center">名前入力欄</td><td align="center">投稿内容入力欄</td><td align="center">パスワード入力欄</td><td>&emsp;</td><td align="center">ファイル選択</td>
			</tr>
			<tr>
				<td align="center">投稿</td>
				<td>&emsp;</td>
				<!--名前を入力してください。-->
				<form method="post" action="mission_3-8.php">
					<td>
						<!--名前入力フォーム-->
						<input type="text" name="name" placeholder="ペンネームを入力" value="<?php if ($_POST['mode_edit'] === 'edit') {echo $name;} else {echo htmlspecialchars($_SESSION["NAME"], ENT_QUOTES);} ?>">
					</td>

					<td>
						<!--コメント入力フォーム-->
						<input type="text" name="comment" placeholder="コメントを入力" value="<?php echo $comment; ?>">
						<!--編集時の隠しフォーム-->
						<input type="hidden" name="number" value="<?php echo $number_edit; ?>">
						<input type="hidden" name="time" value="<?php echo $time; ?>">
						<input type="hidden" name="mode" value="<?php echo $_POST['mode_edit']; ?>">
						<input type="hidden" name="password_edit_hidden" value="<?php echo $password_edit; ?>">
					</td>

					<td>
						<!--パスワード-->
						<input type="password" name="password" placeholder="パスワードを入力"　value="">
					</td>

					<td>
						<!--送信ボタン-->
						<input type="submit" name="submit" value="投稿"> 
					</td>
				</form>

				<!--アップロード-->
				<td align="center">
					<form action="mission_3-8.php" method="post" enctype="multipart/form-data">
						<input type="file" name="filename">
						<input type="submit" name="upload" value="アップロード">
					</form>
				</td>
			</tr>

			<tr>
				<td align="center">投稿の削除</td>
				<form method="post" action="mission_3-8.php">
					<td>
						<!--削除フォーム-->
						<input type="text" name="delete" placeholder="削除したい投稿番号を入力"　value="<?php echo $_POST['delete']; ?>">
					</td>
					<td>&emsp;</td>
					<td>&emsp;</td>
					<td>
						<!--パスワードを入力してください。-->
						<input type="password" name="password_delete" placeholder="パスワードを入力"　value="">
					</td>
					<td>
						<!--送信ボタン-->
						<input type="submit" value="削除">
					</td>
				</form>
				<td>&emsp;</td>
			</tr>

			<tr>
				<td align="center">投稿の編集</td>
				<form method="post" action="mission_3-8.php">
					<td>
						<!--編集フォーム-->
						<input type="text" name="edit" placeholder="編集したい投稿番号を入力"　value="<?php echo $_POST['edit']; ?>">
						<input type="hidden" name="mode_edit" value="edit">
					</td>
					<td>&emsp;</td>
					<td>&emsp;</td>
					<td>
						<!--パスワードを入力してください。-->
						<input type="password" name="password_edit" placeholder="パスワードを入力"　value="">
					</td>
					<td>
						<!--送信ボタン-->
						 <input type="submit" value="編集">
					</td>
				</form>
				<td>&emsp;</td>
			</tr>
		</table>

		<hr>

<?php
// 投稿内容の表示-----------------------------------
	echo '<table bordercolor="#ffffff" rules="rows" border="1" bgcolor="#ff99aa" align="center">';
	try{
		$dbh = open_database();
		$result = $dbh->query(sql_select_table(tbl_name(0), 'commentId', 'DESC'));
		echo '<tr><td align="center">'. '投稿番号' . '</td><td>' . '&emsp;' . '</td><td align="center">' . 'ペンネーム' . '</td><td>' . '&emsp;' . '</td><td align="center">' . '投稿内容' . '</td><td>' . '&emsp;' . '</td><td align="center">' . '投稿時間' . '</td>' . '</tr>';
		foreach ($result as $row) {
			echo '<tr>';
			echo '<td align="center">' . $row['commentId'] . '</td><td></td><td align="center">' . $row['name'] . '</td><td></td><td>';
			if (!empty($row['mp'])) {
				$tn = $row['mp'];
				if (strtolower(mb_strrchr($tn, '.', FALSE)) == ".jpg") {
					echo "<a href='./mp/$tn' target='_blank'><img src='./mp/thumb_$tn'>";
				} elseif (strtolower(mb_strrchr($tn, '.', FALSE)) == ".mp4") {
					echo "<video src='./mp/$tn' width=" . 320 . " height=" . 240 . " controls></video>";
				}
			} else {
				echo $row['comment'];
			}
			echo '</td><td></td><td>' . $row['commentTime'] . '</td>';
			echo '</tr>';
		}
	}catch (PDOException $e){
		print('Error:'.$e->getMessage());
		die();
	}
	echo '</table>';
?>

		<hr>

<?php
// ユーザー情報の表示-------------------------------
	echo '<table bordercolor="#ffffff" rules="rows" border="1" bgcolor="#aaff99" align="center">';
	try{
		$dbh = open_database();
		$result = $dbh->query(sql_select_table(tbl_name(1), 'userId', 'ASC'));
		echo '<tr><td align="center">'. 'ユーザーID' . '</td><td>' . '&emsp;' . '</td><td align="center">' . 'ユーザー名' . '</td>' . '</tr>';
		foreach ($result as $row) {
			echo '<tr>';
			echo '<td align="center">' . $row['userId'] . '</td><td></td><td align="center">' . $row['userName'] . '</td>';
			echo '</tr>';
		}
	}catch (PDOException $e){
		print('Error:'.$e->getMessage());
		die();
	}
	echo '</table>';
// ----------------------------------------------
?>
		<!--ログアウト-->
		<form method="post" action="mission_3-8.php">
			<!--送信ボタン-->
			<input type="submit" name="logout" value="ログアウト">
		</form>
<?php
} else {
	session_destroy();
	print "<p>ちゃんとログインしてね！<br>
	<a href='mission_3-7.php'>ログイン画面</a></p>";
}
?>
	</body>
</html>