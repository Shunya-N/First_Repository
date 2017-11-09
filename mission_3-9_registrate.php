<!DOCTYPE html>
<html>
	<head>
		<title>会員登録画面</title>
		<meta charset="UTF-8">
	</head>

<?php
session_start();

require_once 'functions.php';// 定義集

//クロスサイトリクエストフォージェリ（CSRF）対策
$_SESSION['token'] = base64_encode(generate_password(32));
$token = $_SESSION['token'];
 
//クリックジャッキング対策
header('X-FRAME-OPTIONS: SAMEORIGIN');

//エラーメッセージの初期化
$errors = array();
 
if(empty($_GET)) {
	header("Location: mission_3-7.php");
}else{
	//GETデータを変数に入れる
	$urltoken = isset($_GET[urltoken]) ? $_GET[urltoken] : NULL;
	//メール入力判定
	if ($urltoken == ''){
		$errors['urltoken'] = "もう一度登録をやりなおして下さい。";
	}else{
		try{
			$dbh = open_database();
			//flagが0の未登録者・仮登録日から24時間以内
			$statement = $dbh->prepare("SELECT mail FROM " . tbl_name(2) . " WHERE urltoken=(:urltoken) AND flag =0 AND date > now() - interval 24 hour");
			// 挿入する値を配列に格納する
			$params = array(':urltoken' => $urltoken);
			// 挿入する値が入った変数をexecuteにセットしてSQLを実行
			$statement -> execute($params);

			//レコード件数取得
			$row_count = $statement -> rowCount();

			//24時間以内に仮登録され、本登録されていないトークンの場合
			if( $row_count ==1){
				$mail_array = $statement -> fetch();
				$mail = $mail_array[mail];
				$_SESSION['mail'] = $mail;
			}else{
				$errors['urltoken_timeover'] = "このURLはご利用できません。有効期限が過ぎた等の問題があります。もう一度登録をやりなおして下さい。";
			}

			//データベース接続切断
			$dbh = null;

		}catch (PDOException $e){
			print('Error:'.$e->getMessage());
			die();
		}
	}
}
?>

	<body>
	<h1>会員登録画面</h1>
<?php if (count($errors) === 0): ?>
	<form action="mission_3-9_check.php" method="post">
		<p>メールアドレス：<?=htmlspecialchars($mail, ENT_QUOTES, 'UTF-8')?></p>
		<p>アカウント名：<input type="text" name="name_user"></p>
		<p>パスワード：<input type="text" name="password_user"></p>
		<input type="hidden" name="token" value="<?=$token?>">
		<input type="submit" value="確認する">
		</form>
<?php elseif(count($errors) > 0): ?>
<?php
foreach($errors as $value){
	echo "<p>".$value."</p>";
}
?>
<?php endif; ?>
	</body>
</html>