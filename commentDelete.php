<?php
session_start();
require('dbconnect.php');

//ログイン判定
if(isset($_SESSION['id'])){
  $commentId = $_REQUEST['commentId'];

  //自身の投稿か判定
  $comments = $db->prepare('SELECT * FROM comments WHERE id=?');
  $comments->execute(array($commentId));
  $comment = $comments->fetch();
  //投稿者IDと現在ログイン中のIDが一致するか判定
  if($comment['member_id'] == $_SESSION['id']){
    $delete = $db->prepare('DELETE FROM comments WHERE id=?');
    $delete->execute(array($commentId));
  }
}
$id = $_REQUEST['id'];
header("Location: view.php?id=$id");
exit();
?>