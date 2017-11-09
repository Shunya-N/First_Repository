<!DOCTYPE html>
<html>
	<head>
		<title>メール確認画面</title>
		<meta charset="UTF-8">
	</head>

<?php
// セッション開始
session_start();

require_once 'functions.php';// 定義集

//クロスサイトリクエストフォージェリ（CSRF）対策のトークン判定
if ($_POST['token'] != $_SESSION['token']){
	echo "不正アクセスの可能性あり";
	exit();
}

//クリックジャッキング対策
header('X-FRAME-OPTIONS: SAMEORIGIN');

//エラーメッセージの初期化
$errors = array();

if(empty($_POST)) {
	header("Location: mission_3-7.php");
}else{
	//POSTされたデータを変数に入れる
	$mail = isset($_POST['mail']) ? $_POST['mail'] : NULL;

	//メール入力判定
	if ($mail == ''){
		$errors['mail'] = "メールが入力されていません。";
	}else{
		if(!preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/", $mail)){
			$errors['mail_check'] = "メールアドレスの形式が正しくありません。";
		}

		/*
		ここで本登録用のmemberテーブルにすでに登録されているmailかどうかをチェックする。
		$errors['member_check'] = "このメールアドレスはすでに利用されております。";
		*/
	}
}

if (count($errors) === 0){
	$urltoken = hash('sha256',uniqid(rand(),1));
	$url = "http://db_name/mission_3-9_registrate.php"."?urltoken=".$urltoken;

	//ここでデータベースに登録する
	try{
		$dbh = open_database();
		$statement = $dbh->prepare("INSERT INTO " . tbl_name(2) . " (urltoken,mail,date) VALUES (:urltoken,:mail,now() )");
		// 挿入する値を配列に格納する
		$params = array(':urltoken' => $urltoken, ':mail' => $mail);
		// 挿入する値が入った変数をexecuteにセットしてSQLを実行
		$statement -> execute($params);

		//データベース接続切断
		$dbh = null;	

	}catch (PDOException $e){
		print('Error:'.$e->getMessage());
		die();
	}

	//メールの宛先
	$mailTo = $mail;

	//Return-Pathに指定するメールアドレス
	$returnMail = '管理者メールアドレス';

	$name = "掲示板システム";
	$mail = '管理者メールアドレス';
	$subject = "掲示板システムの新規登録のお知らせ";

$body = <<< EOM
現在は仮登録状態ですので、
24時間以内に下記のURLからご登録下さい。
{$url}
EOM;

	mb_language("japanese");
	mb_internal_encoding("UTF-8");

	//Fromヘッダーを作成
	$header = 'From: ' . mb_encode_mimeheader($name). ' <' . $mail. '>';

	if (mb_send_mail($mailTo, $subject, $body, $header, '-f'. $returnMail)) {

		//セッション変数を全て解除
		$_SESSION = array();

		//クッキーの削除
		if (isset($_COOKIE["PHPSESSID"])) {
			setcookie("PHPSESSID", '', time() - 1800, '/');
		}

		//セッションを破棄する
		session_destroy();

		$message = "メールをお送りしました。24時間以内にメールに記載されたURLからご登録下さい。";

	} else {
		$errors['mail_error'] = "メールの送信に失敗しました。";
	}
}
?>

	<body>
	<h1>メール確認画面</h1>

<?php if (count($errors) === 0): ?>

<p><?=$message?></p>

<!--<p>↓このURLが記載されたメールが届きます。</p>
<a href="<?=$url?>"><?=$url?></a>
-->
<?php elseif(count($errors) > 0): ?>

<?php
foreach($errors as $value){
	echo "<p>".$value."</p>";
}
?>

<input type="button" value="戻る" onClick="history.back()">

<?php endif; ?>

</body>
</html>