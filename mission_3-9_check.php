<!DOCTYPE html>
<html>
	<head>
		<title>会員登録確認画面</title>
		<meta charset="UTF-8">
	</head>

<?php
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
	exit();
}else{
	//POSTされたデータを各変数に入れる
	$account = isset($_POST['name_user']) ? $_POST['name_user'] : NULL;
	$password = isset($_POST['password_user']) ? $_POST['password_user'] : NULL;

	//前後にある半角全角スペースを削除
	$account = spaceTrim($account);
	$password = spaceTrim($password);

	//アカウント入力判定
	if ($account == ''):
		$errors['account'] = "アカウントが入力されていません。";
	elseif(mb_strlen($account)>10):
		$errors['account_length'] = "アカウントは10文字以内で入力して下さい。";
	endif;

	//パスワード入力判定
	if ($password == ''):
		$errors['password'] = "パスワードが入力されていません。";
	elseif(!preg_match('/^[0-9a-zA-Z]{5,30}$/', $_POST["password_user"])):
		$errors['password_length'] = "パスワードは半角英数字の5文字以上30文字以下で入力して下さい。";
	else:
		$password_hide = str_repeat('*', strlen($password));
	endif;
}
//エラーが無ければセッションに登録
if(count($errors) === 0){
	$_SESSION['account'] = $account;
	$_SESSION['password'] = $password;
}
?>

	<body>
	<h1>会員登録確認画面</h1>
<?php if (count($errors) === 0): ?>
	<form action="mission_3-9_insert.php" method="post">
		<p>メールアドレス：<?=htmlspecialchars($_SESSION['mail'], ENT_QUOTES)?></p>
		<p>アカウント名：<?=htmlspecialchars($account, ENT_QUOTES)?></p>
		<p>パスワード：<?=$password_hide?></p>
		<input type="button" value="戻る" onClick="history.back()">
		<input type="hidden" name="token" value="<?=$_POST['token']?>">
		<input type="submit" value="登録する">
	</form>
<?php elseif(count($errors) > 0):
	foreach($errors as $value){
		echo "<p>".$value."</p>";
	}
?>
	<input type="button" value="戻る" onClick="history.back()">
<?php endif; ?>
	</body>
</html>