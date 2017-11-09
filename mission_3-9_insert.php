<!DOCTYPE html>
<html>
	<head>
		<title>会員登録完了画面</title>
		<meta charset="UTF-8">
	</head>

<?php
session_start();

require_once 'functions.php';// 定義集

//クロスサイトリクエストフォージェリ（CSRF）対策のトークン判定
if ($_POST['token'] != $_SESSION['token']){
	$errors['unaccess'] = "不正アクセスの可能性あり";
	exit();
}

//クリックジャッキング対策
header('X-FRAME-OPTIONS: SAMEORIGIN');

// regist SQL文の作成-------------------------------
$sql_regist = "INSERT INTO "
. tbl_name(1)
. " ("
. "userName, mail, password"
. ")"
. " VALUES "
. "("
. ":userName, :mail, :password"
. ");";
// -----------------------------------------------

//エラーメッセージの初期化
$errors = array();

if(empty($_POST)) {
	header("Location: mission_3-7.php");
	exit();
}

$mail = $_SESSION['mail'];
$account = $_SESSION['account'];

//パスワードのハッシュ化
$password_hash =  $_SESSION['password'];

//ここでデータベースに登録する
try{
	// 1. ペンネームとコメントの入力チェック
	if (empty($_SESSION["account"])) {  // emptyは値が空のとき
		$errors['name_user'] = 'ユーザー名が未入力です。';
	} else if (empty($_SESSION["password"])) {
		$errors['password_user'] = 'パスワードが未入力です。';
	}

	if (!empty($_SESSION["account"]) && !empty($_SESSION["password"])) {
		$dbh = open_database();
		//例外処理を投げる（スロー）ようにする
		$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

		//トランザクション開始
		$dbh->beginTransaction();

		//userDataテーブルに本登録する
		$result = $dbh->query(sql_select_table(tbl_name(1), 'userId', 'ASC'));
		$user_name_check = 0;
		foreach ($result as $row) {
			if ($_SESSION['account'] === $row[1]) {
				$user_name_check = 1;
			}
		}

		if ($user_name_check === 1) {
			$errors['user_name_check'] = "登録: そのユーザー名は既に使用されています<br>";
			header("Location: mission_3-9_registrate.php");
		} elseif ($user_name_check === 0) {
			// 挿入する値は空のまま、SQL実行の準備をする
			$stmt = $dbh->prepare($sql_regist);
			// 挿入する値を配列に格納する
			$params = array(':userName' => $account, ':mail' => $mail, ':password' => $password_hash);
			 // 挿入する値が入った変数をexecuteにセットしてSQLを実行
			$stmt->execute($params);
			echo "ユーザー登録完了<br>";

			//pre_memberのflagを1にする
			$statement = $dbh->prepare("UPDATE preMenber SET flag=1 WHERE mail=(:mail)");
			//プレースホルダへ実際の値を設定する
			$statement->bindValue(':mail', $mail, PDO::PARAM_STR);
			$statement->execute();

			// トランザクション完了（コミット）
			$dbh->commit();

			//データベース接続切断
			$dbh = null;

			//セッション変数を全て解除
			$_SESSION = array();

			//セッションクッキーの削除・sessionidとの関係を探れ。つまりはじめのsesssionidを名前でやる
			if (isset($_COOKIE["PHPSESSID"])) {
				setcookie("PHPSESSID", '', time() - 1800, '/');
			}

			//セッションを破棄する
			session_destroy();

			/*
			登録完了のメールを送信
			*/
		}
	}
}catch (PDOException $e){
	//トランザクション取り消し（ロールバック）
	$dbh->rollBack();
	$errors['error'] = "もう一度やりなおして下さい。";
	print('Error:'.$e->getMessage());
}
?>

	<body>

<?php if (count($errors) === 0): ?>
	<h1>会員登録完了画面</h1>

	<p>登録完了いたしました。ログイン画面からどうぞ。</p>
	<p><a href="http://co-978.it.99sv-coco.com/mission_3-7.php">ログイン画面</a></p>

<?php elseif(count($errors) > 0): ?>

<?php
	foreach($errors as $value){
		echo "<p>".$value."</p>";
	}
?>
 
<?php endif; ?>

	</body>
</html>