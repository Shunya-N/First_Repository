<?php
$file = "mission_2-2.txt";// テキストファイルの定義
$d1 = date('Y/m/d H:i:s');// 年・月・日 時・分・秒
$number = 1;// 投稿番号初期値

// 最新の投稿番号検索------------------------------------------
$toukous_count = file("mission_2-2.txt");
foreach ($toukous_count as $toukou_count) {
  $toukou_count_detail = explode("<>", $toukou_count);
  for($count=0; $count<count($toukou_count_detail); $count++) {
    if (preg_match("/0/", $count)) {
      $number = ltrim(rtrim($toukou_count_detail[0], '}'), '{');
      intval($number);
      $number++;
    }
  }
}
// -----------------------------------------------

// 投稿情報の結合-----------------------------------
// 投稿内容を保存するための形に結合
$toukou = "{" . $number . "}<>{" . htmlspecialchars($_POST['name']) . "}<>{" . htmlspecialchars($_POST['comment']) . "}<>{" . $d1 . "}<>{" . htmlspecialchars($_POST['password']) . "}\n"; 

// 投稿内容の有無確認・テキストファイルへの追記----------
if (preg_match("/.+/", $_POST['name'])) {// 名前入力欄に入力があるかを確認
  if (preg_match("/.+/", $_POST['comment'])) {// コメント入力欄に入力があるかを確認
    if ($_POST['mode'] == "edit") {// 編集モードかを判断
        $toukou2 = "{" . htmlspecialchars($_POST['number']) . "}<>{" . htmlspecialchars($_POST['name']) . "}<>{" . htmlspecialchars($_POST['comment']) . "}<>{" . htmlspecialchars($_POST['time']) . "}<>{" . htmlspecialchars($_POST['password_edit_hidden']) . "}\n";
  	    $toukous_cont = file("mission_2-2.txt");
  
        // テキストファイルの白紙化
        $text_delete = fopen("mission_2-2.txt", "w");
        fwrite($text_delete, "");
        fclose($text_delete);
  
        foreach ($toukous_cont as $toukou_cont) {
          $toukou_cont_detail = explode("<>", $toukou_cont);
          for($count=0; $count<count($toukou_cont_detail); $count++) {
	    	if (preg_match("/0/", $count)) {
	          $number_cont = ltrim(rtrim($toukou_cont_detail[0], '}'), '{');
	          if ($_POST['number'] === $number_cont) {
		        echo "編集完了";
	            $toukou_cont = $toukou2;
	          } else {
	          }
		      file_put_contents($file, $toukou_cont, FILE_APPEND | LOCK_EX);// テキストファイルへの追記
		    }
	      }
        }
	    $_POST['mode'] = "";
    } else {
	  if (preg_match("/.+/", $_POST['password'])) {// パスワード入力欄に入力があるかを確認
	    echo "入力モード";
        file_put_contents($file, $toukou, FILE_APPEND | LOCK_EX);// テキストファイルへの追記
	  } else {
	    echo "パスワードも入力してください<br><br>";
	  }
	}
  } else {
    echo "コメントとパスワードも入力してください<br><br>";
  }
} else {
  echo "名前とコメントとパスワードを入力してください<br><br>";
}
// -----------------------------------------------

// 投稿削除処理-------------------------------------
if (preg_match("/^[0-9]+$/", $_POST['delete'])) {
  //パスワード要求
  if (preg_match("/.+/", $_POST['password_delete'])) {// パスワード入力欄に入力があるかを確認
    $toukous_check = file("mission_2-2.txt");
  
    // テキストファイルの白紙化
    $text_delete = fopen("mission_2-2.txt", "w");
    fwrite($text_delete, "");
    fclose($text_delete);
  
    foreach ($toukous_check as $toukou_check) {
      $toukou_check_detail = explode("<>", $toukou_check);
      for($count=0; $count<count($toukou_check_detail); $count++) {
  	    if (preg_match("/0/", $count)) {
	      $number_check = ltrim(rtrim($toukou_check_detail[0], '}'), '{');
	      if ($_POST['delete'] === $number_check) {
    	    $password_check = ltrim(rtrim(rtrim($toukou_check_detail[4]), '}'), '{');
		    if ($_POST['password_delete'] === $password_check) {
			} else {
			 echo "パスワードが違います。";
		     file_put_contents($file, $toukou_check, FILE_APPEND | LOCK_EX);
			}
	      } else {
	        file_put_contents($file, $toukou_check, FILE_APPEND | LOCK_EX);
	      }
        }
	  }
    }
  } else {
    echo "パスワードを入力してください。";
  }
}
// -----------------------------------------------

// 投稿編集処理-------------------------------------
if (preg_match("/^[0-9]+$/", $_POST['edit'])) {
  //パスワード要求
  if (preg_match("/.+/", $_POST['password_edit'])) {// パスワード入力欄に入力があるかを確認
    $toukous_edit = file("mission_2-2.txt");
    foreach ($toukous_edit as $toukou_edit) {
      $toukou_edit_detail = explode("<>", $toukou_edit);
	  
      for($count=0; $count<count($toukou_edit_detail); $count++) {
  	    if (preg_match("/0/", $count)) {
	      $number_edit_check = ltrim(rtrim($toukou_edit_detail[0], '}'), '{');
	      if ($_POST['edit'] === $number_edit_check) {
		    $password_check = ltrim(rtrim(rtrim($toukou_edit_detail[4]), '}'), '{');
		    if ($_POST['password_edit'] === $password_check) {
	  	      $number_edit = $number_edit_check;
		      $name = ltrim(rtrim($toukou_edit_detail[1], '}'), '{');
		      $comment = ltrim(rtrim($toukou_edit_detail[2], '}'), '{');
		      $time = ltrim(rtrim($toukou_edit_detail[3], '}'), '{');
		      $password = ltrim(rtrim(rtrim($toukou_edit_detail[4]), '}'), '{');
		  
		      echo "編集モード<br>";
			} else {
		      echo "パスワードが違います。";
		    }
		  } else {
	      }
        }
	  }
    }
  } else {
    echo "パスワードを入力してください。";
  }
}
?>

<h6>名前を入力してください。</h6>
<form method="post" action="mission_2-6.php">
  <!--名前入力フォーム-->
  <input type="text" name="name" value="<?php echo $name; ?>">

  <h6>コメントを入力してください。</h6>
  <!--コメント入力フォーム-->
  <input type="text" name="comment" value="<?php echo $comment; ?>">
  <!--編集時の隠しフォーム-->
  <input type="hidden" name="number" value="<?php echo $number_edit; ?>">
  <input type="hidden" name="time" value="<?php echo $time; ?>">
  <input type="hidden" name="mode" value="<?php echo $_POST['mode_edit']; ?>">
  <input type="hidden" name="password_edit_hidden" value="<?php echo $password; ?>">

  <!--パスワード-->
  <h6>登録するパスワードを入力してください。</h6>
  <input type="password" name="password" value="">
  
  <!--送信ボタン-->
  <br><input type="submit" name="submit"> 
  <hr>
</form>


<form method="post" action="mission_2-6.php">
  <!--削除フォーム-->
  <h6>削除したい投稿番号を入力してください</h6>
  <input type="text" name="delete" value="<?php echo $_POST['delete']; ?>">
  <h6>パスワードを入力してください。</h6>
  <input type="password" name="password_delete" value="">
  <br><input type="submit" value="削除">
</form>
<hr>

<form method="post" action="mission_2-6.php">
  <!--編集フォーム-->
  <h6>編集したい投稿番号を入力してください</h6>
  <input type="text" name="edit" value="<?php echo $_POST['edit']; ?>">
  <input type="hidden" name="mode_edit" value="edit">
  <h6>パスワードを入力してください。</h6>
  <input type="password" name="password_edit" value="">
  <br><input type="submit" value="編集">
  </form>
<hr>

<?php
// 投稿内容の表示-----------------------------------
// echo $_POST('password') . "<br>";
$toukous = file("mission_2-2.txt");
foreach ($toukous as $toukou) {
  $toukou_detail = explode("<>", $toukou);
  for($count=0; $count<4; $count++){
	echo $toukou_detail[$count];
  }
  echo "<br>";
}
?>