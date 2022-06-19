<?php
header("Content-Type: application/json");

$msg=array("return"=>false, "message"=>"no data");

if (isset($_REQUEST['file'])) {
  $suffix=str_pad($_REQUEST['chunk'], strlen($_REQUEST['limit']), "0", STR_PAD_LEFT);
  $upload=$_REQUEST['file'].".".$suffix;

  if (!isset($_REQUEST['md5'])) {
    $msg=array("message"=>$upload, "exists"=>false, "filesize"=>0);
    if (file_exists($upload)) {
      $msg=array("message"=>$upload, "exists"=>true, "filesize"=>filesize($upload));
    }
  } else {
    $ret=move_uploaded_file($_FILES['file']['tmp_name'], $upload);
    $msg=array("message"=>$_FILES['file'], "return"=>$ret, "cmd"=>$_FILES['file']['tmp_name']." => ".$_REQUEST['file'].".".$_REQUEST['chunk']);
  }
}
echo json_encode($msg);
?>
