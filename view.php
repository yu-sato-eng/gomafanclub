<?php 
session_start();
require('dbconnect.php');

//ログインの確認
if(isset($_SESSION['id']) && $_SESSION['time'] + 3600 > time()){
    //ログインしている場合
    $_SESSION['time'] = time();
    //メンバー情報を取得
    $members = $db->prepare('SELECT * FROM members WHERE id=?');
    $members->execute(array($_SESSION['id']));
    $member = $members->fetch();
} else {
    //ログインしていない場合はログインページに戻す
    header('Location: login.php');
    exit();
}

//記事を取得する
$articles = $db->prepare('SELECT * FROM articles WHERE id=?');
$articles->execute(array(
    $_GET['id']
));
$article = $articles->fetch();
//コメントを取得する
$comments = $db->prepare('SELECT m.name, m.picture, c.* FROM members m, comments c WHERE m.id=c.member_id AND c.reply_id=? ORDER BY c.created DESC');
$comments->execute(array(
    $_GET['id']
));

//コメントをDBに登録する
if(!empty($_POST)){
    if($_POST['comment'] != ''){
        $commentToDB = $db->prepare('INSERT INTO comments SET comment=?, member_id=?, reply_id=?, created=NOW()');
        $commentToDB->execute(array(
            $_POST['comment'],
            $member['id'],
            $_GET['id']
        ));
        header('Location: #');
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
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<title>ごまファンクラブ</title>

	<link rel="stylesheet" href="style.css" />
</head>

<body>
<div id="wrapper">
<header>
<p class="top"><img src="images/top.jpg"></p>
</header>
<main>
<div id="articleTitle">
    <div>
        <h2><?php echo $article['title']; ?></h2>
    </div>
    <div id="back">
        <a href="home.php">戻る</a>
    </div>
    <div class="drawer" id="open_nav">
        <img src="images/drawer.jpg" alt="">
    </div>
</div>
<div id="articleContent">
    <div class="center">
        <div>
            <img id="closeUp" src="article_picture/<?php echo $article['picture1']; ?>">
        </div>
        <ul>
            <li><img src="article_picture/<?php echo $article['picture1']; ?>" class="thumb" data-image="article_picture/<?php echo $article['picture1']; ?>"></li>
            <li><img src="article_picture/<?php echo $article['picture2']; ?>" class="thumb" data-image="article_picture/<?php echo $article['picture2']; ?>"></li>
            <li><img src="article_picture/<?php echo $article['picture3']; ?>" class="thumb" data-image="article_picture/<?php echo $article['picture3']; ?>"></li>
        </ul>  
    </div>
    <div id="paper"><?php echo $article['article']; ?></div>
</div>
<div id="comments">
    <form action="" method="post">
        <textarea name="comment" cols="50" rows="5"></textarea>
        <input type="submit" value="コメントを書く" />
    </form>
    <div id="comentList">
        <?php if(!empty($comments)): ?>
            <?php foreach($comments as $comment): ?>
                <div id="comment">
                    <img src="member_picture/<?php echo h($comment['picture']); ?>" width="50" height="50" ait="<?php echo h($comment['name']); ?>" />
                    <p id="commentText"><?php echo h($comment['comment']); ?> <span class="name"> (<?php echo h($comment['name']); ?>) </span></p>
                    <p class="day">
                        <?php echo h($comment['created']); ?>
                        <?php if($_SESSION['id'] == $comment['member_id']): ?>
                            [<a href="commentDelete.php?commentId=<?php echo h($comment['id']); ?>&id=<?php echo h($_GET['id']); ?>" style="color: #F33;">削除</a>]
                        <?php endif; ?>
                    </p>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>
</main>
</div>

<nav id="drawer">
    <ul>
        <li><a href="home.php">Home</a></li>
        <li><a href="index.php">自己紹介</a></li>
        <li><a href="write.php">記事を書く</a></li>
        <li><a href="#">お問い合わせ</a></li>
        <li><a href="logout.php">ログアウト</a></li>
    </ul>
</nav>

<script src="common/jquery-3.4.1.min.js"></script>
<script>
'use strict';

// 写真の切り替え
const thumbs = document.querySelectorAll('.thumb');
thumbs.forEach(function(item, index){
    item.onclick = function(){
        console.log(this.dataset.image);
        document.getElementById('closeUp').src = this.dataset.image;
    }
});

// ドロワーの表示
$(document).ready(function() {
    $('#open_nav').on('click', function() {
        $('#wrapper, #drawer').toggleClass('show');
    });
});
</script>
</body>
</html>