<?php
$file = "mission_2-2.txt";// �e�L�X�g�t�@�C���̒�`
$d1 = date('Y/m/d H:i:s');// �N�E���E�� ���E���E�b
$number = 1;// ���e�ԍ������l

// �ŐV�̓��e�ԍ�����------------------------------------------
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

// ���e���̌���-----------------------------------
// ���e���e��ۑ����邽�߂̌`�Ɍ���
$toukou = "{" . $number . "}<>{" . htmlspecialchars($_POST['name']) . "}<>{" . htmlspecialchars($_POST['comment']) . "}<>{" . $d1 . "}<>{" . htmlspecialchars($_POST['password']) . "}\n"; 

// ���e���e�̗L���m�F�E�e�L�X�g�t�@�C���ւ̒ǋL----------
if (preg_match("/.+/", $_POST['name'])) {// ���O���͗��ɓ��͂����邩���m�F
  if (preg_match("/.+/", $_POST['comment'])) {// �R�����g���͗��ɓ��͂����邩���m�F
    if ($_POST['mode'] == "edit") {// �ҏW���[�h���𔻒f
        $toukou2 = "{" . htmlspecialchars($_POST['number']) . "}<>{" . htmlspecialchars($_POST['name']) . "}<>{" . htmlspecialchars($_POST['comment']) . "}<>{" . htmlspecialchars($_POST['time']) . "}<>{" . htmlspecialchars($_POST['password_edit_hidden']) . "}\n";
  	    $toukous_cont = file("mission_2-2.txt");
  
        // �e�L�X�g�t�@�C���̔�����
        $text_delete = fopen("mission_2-2.txt", "w");
        fwrite($text_delete, "");
        fclose($text_delete);
  
        foreach ($toukous_cont as $toukou_cont) {
          $toukou_cont_detail = explode("<>", $toukou_cont);
          for($count=0; $count<count($toukou_cont_detail); $count++) {
	    	if (preg_match("/0/", $count)) {
	          $number_cont = ltrim(rtrim($toukou_cont_detail[0], '}'), '{');
	          if ($_POST['number'] === $number_cont) {
		        echo "�ҏW����";
	            $toukou_cont = $toukou2;
	          } else {
	          }
		      file_put_contents($file, $toukou_cont, FILE_APPEND | LOCK_EX);// �e�L�X�g�t�@�C���ւ̒ǋL
		    }
	      }
        }
	    $_POST['mode'] = "";
    } else {
	  if (preg_match("/.+/", $_POST['password'])) {// �p�X���[�h���͗��ɓ��͂����邩���m�F
	    echo "���̓��[�h";
        file_put_contents($file, $toukou, FILE_APPEND | LOCK_EX);// �e�L�X�g�t�@�C���ւ̒ǋL
	  } else {
	    echo "�p�X���[�h�����͂��Ă�������<br><br>";
	  }
	}
  } else {
    echo "�R�����g�ƃp�X���[�h�����͂��Ă�������<br><br>";
  }
} else {
  echo "���O�ƃR�����g�ƃp�X���[�h����͂��Ă�������<br><br>";
}
// -----------------------------------------------

// ���e�폜����-------------------------------------
if (preg_match("/^[0-9]+$/", $_POST['delete'])) {
  //�p�X���[�h�v��
  if (preg_match("/.+/", $_POST['password_delete'])) {// �p�X���[�h���͗��ɓ��͂����邩���m�F
    $toukous_check = file("mission_2-2.txt");
  
    // �e�L�X�g�t�@�C���̔�����
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
			 echo "�p�X���[�h���Ⴂ�܂��B";
		     file_put_contents($file, $toukou_check, FILE_APPEND | LOCK_EX);
			}
	      } else {
	        file_put_contents($file, $toukou_check, FILE_APPEND | LOCK_EX);
	      }
        }
	  }
    }
  } else {
    echo "�p�X���[�h����͂��Ă��������B";
  }
}
// -----------------------------------------------

// ���e�ҏW����-------------------------------------
if (preg_match("/^[0-9]+$/", $_POST['edit'])) {
  //�p�X���[�h�v��
  if (preg_match("/.+/", $_POST['password_edit'])) {// �p�X���[�h���͗��ɓ��͂����邩���m�F
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
		  
		      echo "�ҏW���[�h<br>";
			} else {
		      echo "�p�X���[�h���Ⴂ�܂��B";
		    }
		  } else {
	      }
        }
	  }
    }
  } else {
    echo "�p�X���[�h����͂��Ă��������B";
  }
}
?>

<h6>���O����͂��Ă��������B</h6>
<form method="post" action="mission_2-6.php">
  <!--���O���̓t�H�[��-->
  <input type="text" name="name" value="<?php echo $name; ?>">

  <h6>�R�����g����͂��Ă��������B</h6>
  <!--�R�����g���̓t�H�[��-->
  <input type="text" name="comment" value="<?php echo $comment; ?>">
  <!--�ҏW���̉B���t�H�[��-->
  <input type="hidden" name="number" value="<?php echo $number_edit; ?>">
  <input type="hidden" name="time" value="<?php echo $time; ?>">
  <input type="hidden" name="mode" value="<?php echo $_POST['mode_edit']; ?>">
  <input type="hidden" name="password_edit_hidden" value="<?php echo $password; ?>">

  <!--�p�X���[�h-->
  <h6>�o�^����p�X���[�h����͂��Ă��������B</h6>
  <input type="password" name="password" value="">
  
  <!--���M�{�^��-->
  <br><input type="submit" name="submit"> 
  <hr>
</form>


<form method="post" action="mission_2-6.php">
  <!--�폜�t�H�[��-->
  <h6>�폜���������e�ԍ�����͂��Ă�������</h6>
  <input type="text" name="delete" value="<?php echo $_POST['delete']; ?>">
  <h6>�p�X���[�h����͂��Ă��������B</h6>
  <input type="password" name="password_delete" value="">
  <br><input type="submit" value="�폜">
</form>
<hr>

<form method="post" action="mission_2-6.php">
  <!--�ҏW�t�H�[��-->
  <h6>�ҏW���������e�ԍ�����͂��Ă�������</h6>
  <input type="text" name="edit" value="<?php echo $_POST['edit']; ?>">
  <input type="hidden" name="mode_edit" value="edit">
  <h6>�p�X���[�h����͂��Ă��������B</h6>
  <input type="password" name="password_edit" value="">
  <br><input type="submit" value="�ҏW">
  </form>
<hr>

<?php
// ���e���e�̕\��-----------------------------------
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