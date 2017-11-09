<?php
// select SQL文の作成
function sql_select_table ($tbl_name, $col, $mode) {
  $sql_select_table = "SELECT * FROM {$tbl_name} ORDER BY {$col} {$mode};";
  return $sql_select_table;
}

function sql_select_all ($tbl_name, $col, $id) {
  $sql_select_all = "SELECT * FROM {$tbl_name}"
  . " WHERE {$col} = {$id};";
  return $sql_select_all;
}

//update SQL文の作成
function sql_update ($tbl_name, $name, $comment, $id) {
  $sql_update = "UPDATE {$tbl_name} "
  . "SET name = '{$name}', comment = '{$comment}'"
  . " WHERE commentId = {$id};";
  return $sql_update;
}


// delete SQL文の作成
function sql_delete ($tbl_name, $id) {
  $sql_delete = "DELETE FROM {$tbl_name} "
  . " WHERE commentId = {$id};";
  return $sql_delete;
}

function require_unlogined_session () {
    // セッション開始
    session_start();
    // ログインしていれば
    if (isset($_SESSION["NAME"])) {
        header('Location: ./mission_3-8.php');
        exit;
    }
}

function require_logined_session() {
    // セッション開始
    session_start();
    // ログインしていなければmission_3-7.phpに遷移
    if (!isset($_SESSION["NAME"])) {
        header('Location: ./mission_3-7.php');
        exit;
    }
}

// パスワードに使っても良い文字集合
$password_chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
$password_chars_count = strlen($password_chars);

// $sizeに指定された長さのパスワードを生成
function generate_password($size) {
  global $password_chars;
  global $password_chars_count;
  $data = mcrypt_create_iv($size, MCRYPT_DEV_URANDOM);
  $pin = '';
  for ($n = 0; $n < $size; $n ++) {
    $pin .= substr($password_chars, ord(substr($data, $n, 1)) % $password_chars_count, 1);
  }
  return $pin;
}

// 前後にある半角全角スペースを削除する関数
function spaceTrim ($str) {
  // 行頭
  $str = preg_replace('/^[ 　]+/', '', $str);
  // 末尾
  $str = preg_replace('/[ 　]+$/', '', $str);
  return $str;
}

// データベース基本情報--------------------------------------
$options = array(PDO::MYSQL_ATTR_INIT_COMMAND=>"SET CHARACTER SET 'utf8'");// 文字化け対策
$dsn = 'データベース名';
$user = 'ユーザー名';
$password = 'パスワード';
// -------------------------------------------------------

// データベース接続
function open_database() {
  global $options;// 文字化け対策
  global $dsn;
  global $user;
  global $password;
  $dbh = '';
  $dbh = new PDO($dsn, $user, $password, $options);
  return $dbh;
}

// テーブル基本情報------------------------------------------
// テーブル名配列
$tbl_name_x = array(
  "commentData",// コメントデータ
  "userData",// ユーザーデータ
  "preMenber"// 仮登録ユーザーデータ
);
// -------------------------------------------------------

// テーブル名
function tbl_name($number) {
  global $tbl_name_x;// テーブル名配列
  return $tbl_name_x[$number];
}
  
?>
