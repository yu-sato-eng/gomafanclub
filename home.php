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
//現在ページを取得する(ない場合は1ページ目)
if(isset($_REQUEST['page']) && $_REQUEST['page'] != ''){
    $page = $_REQUEST['page'];
} else {
    $page = 1;
}
//マイナスが指定された場合も1ページ目とする
$page = max($page, 1);

//最終ページを取得する(5件ずつ表示)
$counts = $db->query('SELECT COUNT(*) AS cnt FROM articles');
$cnt = $counts->fetch();
$maxPage = ceil($cnt['cnt'] / 5);
//指定ページが最大ページを超えないようにする
$page = min($page, $maxPage);

$start = ($page - 1) * 5;

$articles = $db->prepare('SELECT * FROM articles ORDER BY id DESC LIMIT ?, 5');
$articles->bindParam(1, $start, PDO::PARAM_INT);
$articles->execute();

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
<div id="home">
    <div id="best">
        <h2 id="best"> 今週のベストショット</h2>
        <img src="images/best.jpg">
    </div>

    <div id="articles">
        <h2>記事一覧</h2>
        <dl id="article">
            <?php foreach($articles as $article): ?>
                <dt id="day"><?php echo h($article['created']); ?></dt>
                <dd id="title"><a href="view.php?id=<?php echo $article['id']; ?>"><?php echo h($article['title']); ?></a></dd>
            <?php endforeach; ?>
        </dl>

        <ul class="paging">
            <?php if($page > 1): ?>
                <li><a href="home.php?page=<?php print($page - 1); ?>">前のページへ</a></li>
            <?php else: ?>
                <li>前のページへ</li>
            <?php endif; ?>
            <?php if($page < $maxPage): ?>
                <li><a href="home.php?page=<?php print($page + 1); ?>">次のページへ</a></li>
            <?php else: ?>
                <li>次のページへ</li>      
            <?php endif; ?>
        </ul>
    </div>

    <div class="drawer" id="open_nav">
        <img src="images/drawer.jpg" alt="">
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

$(document).ready(function() {
    $('#open_nav').on('click', function() {
        $('#wrapper, #drawer').toggleClass('show');
    });
});
</script>
</body>
</html>