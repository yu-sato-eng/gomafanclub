<?php 
session_start();
require('dbconnect.php');

if(!isset($_SESSION['join'])){
    header('Location: index.php');
    exit();
}

if(!empty($_POST)){
    //登録処理
    $statement = $db->prepare('INSERT INTO articles SET title=?, article=?, picture1=?, picture2=?, picture3=?, created=NOW()');
    echo $ret = $statement->execute(array(
        $_SESSION['join']['title'],
        $_SESSION['join']['article'],
        $_SESSION['join']['image1'],
        $_SESSION['join']['image2'],
        $_SESSION['join']['image3']
    ));
    //入力情報を削除する
    unset($_SESSION['join']);

    header('Location: home.php');
    exit();
}

//htmlspecialcharsのショートカット
function h($value){
    return htmlspecialchars($value, ENT_QUOTES);
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<title>ごまファンクラブ</title>
	<link rel="stylesheet" href="style.css" />
</head>
<body>
<h1>投稿内容確認</h1>
<form action="" method="post">
    <input type="hidden" name="action" value="submit" />
    <dl>
        <dt>タイトル</dt>
        <dd>
            <?php echo h($_SESSION['join']['title']); ?>
        </dd>
        <dt>本文</dt>
        <dd>
            <?php echo h($_SESSION['join']['article']); ?>
        </dd>
        <dt>写真1</dt>
        <dd>
            <img src="article_picture/<?php echo h($_SESSION['join']['image1']); ?>" height="100" alt="" />
        </dd>
        <dt>写真2</dt>
        <dd>
            <img src="article_picture/<?php echo h($_SESSION['join']['image2']); ?>" height="100" alt="" />
        </dd>
        <dt>写真3</dt>
        <dd>
            <img src="article_picture/<?php echo h($_SESSION['join']['image3']); ?>" height="100" alt="" />
        </dd>
    </dl>
    <div><a href="write.php?action=rewrite">&laquo;&nbsp;書き直す</a> | <input type="submit" value="投稿する" /></div>
</form>
</main>
</body>
</html>