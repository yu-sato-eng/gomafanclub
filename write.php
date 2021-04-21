<?php 
session_start();
require('dbconnect.php');

//管理者権限の確認
if(isset($_SESSION['id']) && $_SESSION['time'] + 3600 > time()) {
    if($_SESSION['id'] == "5"){
    //管理者権限がある場合
    $_SESSION['time'] = time();
    } else {
       //管理者権限がない場合はログインページに戻す
    header('Location: home.php');
    exit(); 
    }
} else {
    //ログインしていない場合はログインページに戻す
    header('Location: login.php');
    exit();
}

//送信後に起動
if(!empty($_POST)){
    //エラー項目の確認
    if($_POST['title'] == ''){
        $error['title'] = 'blank';
    }
    if($_POST['article'] == ''){
        $error['article'] = 'blank';
    }
    $fileName1 = $_FILES['image1']['name'];
    $fileName2 = $_FILES['image2']['name'];
    $fileName3 = $_FILES['image3']['name'];
    if(empty($fileName1) || empty($fileName2) || empty($fileName3)){
        $error['image'] = 'blank';
    } else {
        $ext1 = substr($fileName1, -3);
        $ext2 = substr($fileName2, -3);
        $ext3 = substr($fileName3, -3);
        if(($ext1 != 'jpg' && $ext1 != 'gif') || ($ext2 != 'jpg' && $ext2 != 'gif') || ($ext3 != 'jpg' && $ext3 != 'gif')){
                $error['image'] = 'type';
        }
    }

    if(empty($error)){
        $_SESSION['join'] = $_POST;
        //画像をアップロードする
        $image1 = date('YmdHis') . $_FILES['image1']['name'];
		move_uploaded_file($_FILES['image1']['tmp_name'], 'article_picture/' . $image1);
        $_SESSION['join']['image1'] = $image1;

        $image2 = date('YmdHis') . $_FILES['image2']['name'];
		move_uploaded_file($_FILES['image2']['tmp_name'], 'article_picture/' . $image2);
        $_SESSION['join']['image2'] = $image2;

        $image3 = date('YmdHis') . $_FILES['image3']['name'];
		move_uploaded_file($_FILES['image3']['tmp_name'], 'article_picture/' . $image3);
        $_SESSION['join']['image3'] = $image3;

        header('Location: checkWrite.php');
        exit();
    }
}

//htmlspecialcharsのショートカット
function h($value){
    return htmlspecialchars($value, ENT_QUOTES);
}
?>

<!doctype html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="description" content="キジトラ模様の保護猫「ごま」のファンクラブサイトです。ごまの日常を覗いてみませんか。">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<title>ごまファンクラブ</title>

	<link rel="stylesheet" href="style.css" />
</head>

<body>
<div id="wrapper">
<main>
<h1>記事投稿</h1>
    <form action="" method="POST" enctype="multipart/form-data">
        <dl>
            <dt>タイトル<span class="required">必須</span></dt>
            <dd>
                <?php if(isset($_POST) && isset($_POST['title'])): ?>
                    <input type="text" name="title" size="35" maxlength="255" value="<?php echo h($_POST['title']); ?>"/>
                <?php else: ?>
                    <input type="text" name="title" size="35" maxlength="255" value=""/>
                <?php endif; ?>
                <?php if(isset($error) && isset($error['title']) && $error['title'] == 'blank'): ?>
                    <p class="error">* タイトルを入力してください</p>
                <?php endif; ?>
            </dd>
            <dt>本文<span class="required">必須</span></dt>
            <dd>
                <?php if(isset($_POST) && isset($_POST['article'])): ?>
                    <textarea name="article" cols="50" rows="5"><?php echo h($_POST['article']); ?></textarea>
                <?php else: ?>
                    <textarea name="article" cols="50" rows="5"></textarea>
                <?php endif; ?>
                <?php if(isset($error) && isset($error['article']) && $error['article'] == 'blank'): ?>
                    <p class="error">* メールアドレスを入力してください</p>
                <?php endif; ?>
            </dd>
            <dt>写真1<span class="required">必須</span></dt>
            <dd>
                <input type="file" name="image1" size="35" />
            </dd>
            <dt>写真2<span class="required">必須</span></dt>
            <dd>
                <input type="file" name="image2" size="35" />
            </dd>
            <dt>写真3<span class="required">必須</span></dt>
            <dd>
                <input type="file" name="image3" size="35" />
                <?php if(isset($error) && isset($error['image']) && $error['image'] == 'blank'): ?>
                    <p class="error">* 写真を3つ指定してくださいしてください</p>
                <?php endif; ?>
                <?php if(isset($error) && isset($error['image']) && $error['image'] == 'type'): ?>
                    <p class="error">* アイコンには「.gif」または「.jpg」の画像を指定してください</p>
                <?php endif; ?>
            </dd>
        </dl>
        <div><input type="submit" value="投稿内容を確認する" /></div>
    </form>
</body>
</html>