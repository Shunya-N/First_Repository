<?php
// �ϐ���`----------------------------------------
$d1 = date('Y/m/d H:i:s');// �N�E���E�� ���E���E�b
$number = 1;// ���e�ԍ������l
// ���������΍�
$options = array(PDO::MYSQL_ATTR_INIT_COMMAND=>"SET CHARACTER SET 'sjis'");
$dsn = '�f�[�^�x�[�X��';
$user = '���[�U�[��';
$password = '�p�X���[�h';
$tbl_name = "commentData";
// -----------------------------------------------

// SQL���̒�`-------------------------------------
// insert SQL���̍쐬
$sql_insert = "INSERT INTO {$tbl_name} "
. "("
. "name, comment, commentTime, password"
. ")"
. " VALUES "
. "("
. ":name, :comment, now(), :password"
. ");";

// select SQL���̍쐬
$sql_select = "SELECT * FROM {$tbl_name};";

function sql_select ($tbl_name, $id) {
  $sql_select = "SELECT * FROM {$tbl_name}"
  . " WHERE commentId = {$id};";
  return $sql_select;
}

// update SQL���̍쐬
function  sql_update ($tbl_name, $name, $comment, $id) {
  $sql_update = "UPDATE {$tbl_name} "
  . "SET name = '{$name}', comment = '{$comment}'"
  . " WHERE commentId = {$id};";
  return $sql_update;
}

// delete SQL���̍쐬
function  sql_delete ($tbl_name, $id) {
  $sql_delete = "DELETE FROM {$tbl_name} "
  . " WHERE commentId = {$id};";
  return $sql_delete;
}
// -----------------------------------------------

// ���e���e�̗L���m�F�E�e�L�X�g�t�@�C���ւ̒ǋL----------
if (preg_match("/.+/", $_POST['name'])) {// ���O���͗��ɓ��͂����邩���m�F
  if (preg_match("/.+/", $_POST['comment'])) {// �R�����g���͗��ɓ��͂����邩���m�F
    if ($_POST['mode'] == "edit") {// �ҏW���[�h���𔻒f
        $id = $_POST['number'];
		$name_update = $_POST['name'];
		$comment_update = $_POST['comment'];
  
        try{
          $dbh = new PDO($dsn, $user, $password, $options);
		  echo "�ҏW����";
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
	  if (preg_match("/.+/", $_POST['password'])) {// �p�X���[�h���͗��ɓ��͂����邩���m�F
	     try{
          $dbh = new PDO($dsn, $user, $password, $options);
          echo "���̓��[�h";
          // �}������l�͋�̂܂܁ASQL���s�̏���������
          $stmt = $dbh->prepare($sql_insert);
          // �}������l��z��Ɋi�[����
          $params = array(':name' => $_POST['name'], ':comment' => $_POST['comment'], ':password' => $_POST['password']);
          // �}������l���������ϐ���execute�ɃZ�b�g����SQL�����s
          $stmt->execute($params);
        }catch (PDOException $e){
          print('Error:'.$e->getMessage());
          die();
        }
	  } else {
	    echo "�p�X���[�h�����͂��Ă�������<br>";
	  }
	}
  } else {
    echo "�R�����g�ƃp�X���[�h�����͂��Ă�������<br>";
  }
} else {
  echo "���O�ƃR�����g�ƃp�X���[�h����͂��Ă�������<br>";
}
// -----------------------------------------------

// ���e�폜����-------------------------------------
if (preg_match("/^[0-9]+$/", $_POST['delete'])) {
  if (preg_match("/.+/", $_POST['password_delete'])) {// �p�X���[�h���͗��ɓ��͂����邩���m�F
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
		echo "�p�X���[�h���Ⴂ�܂��B";
	  }    
    }catch (PDOException $e){
      print('Error:'.$e->getMessage());
      die();
    }
	$id = "";
  } else {
    echo "�p�X���[�h����͂��Ă��������B";
  }
}
// -----------------------------------------------

// ���e�ҏW����-------------------------------------
if (preg_match("/^[0-9]+$/", $_POST['edit'])) {
  //�p�X���[�h�v��
  if (preg_match("/.+/", $_POST['password_edit'])) {// �p�X���[�h���͗��ɓ��͂����邩���m�F
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
	    echo "�ҏW���[�h" . "<br>";
	  } else {
		echo "�p�X���[�h���Ⴂ�܂��B";
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
    echo "�p�X���[�h����͂��Ă��������B";
  }
}
?>

<!--���̓t�H�[���W-->
<table bordercolor="#ffffff" rules="rows" border="1" bgcolor="#aa99ff" align="center">
  <tr>
    <td>&emsp;</td><td align="center">���e�ԍ����͗�</td><td align="center">�y���l�[�����͗�</td><td align="center">���e���e���͗�</td><td align="center">�p�X���[�h���͗�</td><td>&emsp;</td>
  </tr>
  <tr>
    <td align="center">���e</td>
	<td>&emsp;</td>
    <!--���O����͂��Ă��������B-->
    <form method="post" action="mission_2-15.php">
	  <td>
        <!--���O���̓t�H�[��-->
        <input type="text" name="name" value="<?php echo $name; ?>">
	  </td>

      <td>
        <!--�R�����g����͂��Ă��������B-->
        <!--�R�����g���̓t�H�[��-->
        <input type="text" name="comment" value="<?php echo $comment; ?>">
        <!--�ҏW���̉B���t�H�[��-->
        <input type="hidden" name="number" value="<?php echo $number_edit; ?>">
        <input type="hidden" name="time" value="<?php echo $time; ?>">
        <input type="hidden" name="mode" value="<?php echo $_POST['mode_edit']; ?>">
        <input type="hidden" name="password_edit_hidden" value="<?php echo $password_edit; ?>">
	  </td>

      <td>
        <!--�p�X���[�h-->
        <!--�o�^����p�X���[�h����͂��Ă��������B-->
        <input type="password" name="password" value="">
	  </td>
  
      <td>
        <!--���M�{�^��-->
        <input type="submit" value="���e"> 
	  </td>
    </form>
  </tr>
  
  <tr>
    <td align="center">���e�̍폜</td>
    <form method="post" action="mission_2-15.php">
	  <td>
        <!--�폜�t�H�[��-->
        <!--�폜���������e�ԍ�����͂��Ă�������-->
        <input type="text" name="delete" value="<?php echo $_POST['delete']; ?>">
	  </td>
	  <td>&emsp;</td>
	  <td>&emsp;</td>
	  <td>
        <!--�p�X���[�h����͂��Ă��������B-->
        <input type="password" name="password_delete" value="">
	  </td>
	  <td>
        <input type="submit" value="�폜">
	  </td>
    </form>
  </tr>

  <tr>
    <td align="center">���e�̕ҏW</td>
    <form method="post" action="mission_2-15.php">
	  <td>
        <!--�ҏW�t�H�[��-->
        <!--�ҏW���������e�ԍ�����͂��Ă�������-->
        <input type="text" name="edit" value="<?php echo $_POST['edit']; ?>">
        <input type="hidden" name="mode_edit" value="edit">
	  </td>
	  <td>&emsp;</td>
	  <td>&emsp;</td>
	  <td>
        <!--�p�X���[�h����͂��Ă��������B-->
        <input type="password" name="password_edit" value="">
	  </td>
	  <td>
        <input type="submit" value="�ҏW">
	  </td>
    </form>
  </tr>
<table>

<hr>

<table bordercolor="#ffffff" rules="rows" border="1" bgcolor="#ff99aa" align="center">
<?php
// ���e���e�̕\��-----------------------------------
try{
  $dbh = new PDO($dsn, $user, $password, $options);
  $result = $dbh->query($sql_select);
  echo '<tr><td align="center">'. '���e�ԍ�' . '</td><td>' . '&emsp;' . '</td><td align="center">' . '�y���l�[��' . '</td><td>' . '&emsp;' . '</td><td align="center">' . '���e���e' . '</td><td>' . '&emsp;' . '</td><td align="center">' . '���e����' . '</td>' . '</tr>';
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