<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		<title>ログイン</title>
	</head>

<?php
// セッション開始
session_start();

require_once 'functions.php';// 定義集
require_unlogined_session();// ログインしていれば, メインページへ遷移させる

//クロスサイトリクエストフォージェリ（CSRF）対策
$_SESSION['token'] = base64_encode(generate_password(32));
$token = $_SESSION['token'];

//クリックジャッキング対策
header('X-FRAME-OPTIONS: SAMEORIGIN');

// ログインボタンが押された場合
if (isset($_POST["login"])) {
	// 1. ユーザIDの入力チェック
	if (empty($_POST["username"])) {  // emptyは値が空のとき
		$errorMessage = 'ユーザーIDが未入力です。';
	} else if (empty($_POST["password"])) {
		$errorMessage = 'パスワードが未入力です。';
	}

	if (!empty($_POST["username"]) && !empty($_POST["password"])) {
		// 入力したユーザIDを格納
		$username = $_POST["username"];
		// 3. エラー処理
		try{
			$dbh = open_database();
			$result = $dbh->query("SELECT * FROM " . tbl_name(1) . " WHERE userName = '" . $username . "';");
			$password = $_POST["password"];

			foreach ($result as $row) {
				if ($_POST['username'] === $row['userName']) {
					if ($password === $row['password']) {
						session_regenerate_id(true);

						$_SESSION["NAME"] = $row['userName'];
						$_SESSION["TIME"] = time();
						header("Location: mission_3-8.php");  // メイン画面へ遷移
						exit();  // 処理終了
					} else {
						// 認証失敗
						$errorMessage = 'ユーザー名あるいはパスワードに誤りがあります。';
					}
				} else {
					// 4. 認証成功なら、セッションIDを新規に発行する
					// 該当データなし
					$errorMessage = 'ユーザー名あるいはパスワードに誤りがあります。';
				}
			}
		}catch (PDOException $e){
			print('Error:'.$e->getMessage());
			die();
		}
	}
}
?>

	 <body>
		<h1>ログイン画面</h1>
		<form id="loginForm" name="loginForm" action="" method="POST">
			<fieldset>
				<legend>ログインフォーム</legend>
				<div><font color="#ff0000"><?php echo htmlspecialchars($errorMessage, ENT_QUOTES); ?></font></div>
				<label for="userid">ユーザー名</label><input type="text" id="username" name="username" placeholder="ユーザー名を入力" value="<?php if (!empty($_POST["username"])) {echo htmlspecialchars($_POST["username"], ENT_QUOTES);} ?>">
				<br>
				<label for="password">パスワード</label><input type="password" id="password" name="password" value="" placeholder="パスワードを入力">
				<br>
				<input type="submit" id="login" name="login" value="ログイン">
			</fieldset>
		</form>
		<br>
		<form action="mission_3-9.php" method="post">
			<fieldset>          
				<legend>新規登録フォーム</legend>
				<p>新規登録URLを受け取るためのメールアドレスを入力してください。</p>
				<label for="mail">メールアドレス</label><input type="email"  name="mail" value="" placeholder="メールアドレスを入力">
				<input type="hidden" name="token" value="<?=$token?>">
				<input type="submit" value="新規登録">
			</fieldset>
		</form>
	</body>
</html>