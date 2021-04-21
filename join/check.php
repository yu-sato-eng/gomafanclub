<?php 
session_start();
require('../dbconnect.php');

if(!isset($_SESSION['join'])){
    header('Location: index.php');
    exit();
}

if(!empty($_POST)){
    //登録処理
    $statement = $db->prepare('INSERT INTO members SET name=?, email=?, password=?, picture=?, created=NOW()');
    echo $ret = $statement->execute(array(
        $_SESSION['join']['name'],
        $_SESSION['join']['email'],
        //shal()で暗号化
        sha1($_SESSION['join']['password']),
        $_SESSION['join']['image']
    ));
    //登録完了メールを送信する
    $email = $_SESSION['join']['email'];
    $from = 'noreply@example.com';
    $subject = 'ごまファンクラブへご入会いただきありがとうございます';
    $body = "この度はごまファンクラブへご入会いただきありがとうございます。\n保護猫ごまのかわいい姿をお楽しみください。";
    mb_language('japanese');
    mb_internal_encoding('UTF-8');
    $success = mb_send_mail($email, $subject, $body, 'From: ' . $from);
    //入力情報を削除する
    unset($_SESSION['join']);

    header('Location: thanks.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<title>ごまファンクラブ</title>
	<link rel="stylesheet" href="../style.css" />
</head>
<body>
<h1>ファンクラブ入会</h1>
<form action="" method="post">
    <input type="hidden" name="action" value="submit" />
    <dl>
        <dt>ニックネーム</dt>
        <dd>
            <?php echo htmlspecialchars($_SESSION['join']['name'], ENT_QUOTES, 'UTF-8'); ?>
        </dd>
        <dt>メールアドレス</dt>
        <dd>
            <?php echo htmlspecialchars($_SESSION['join']['email'], ENT_QUOTES, 'UTF-8'); ?>
        </dd>
        <dt>パスワード</dt>
        <dd>
            【表示されません】
        </dd>
        <dt>アイコン</dt>
        <dd>
            <?php if(!empty($_SESSION['join']['image'])): ?>
                <img src="../member_picture/<?php echo htmlspecialchars($_SESSION['join']['image'], ENT_QUOTES); ?>" width="100" height="100" alt="" />
            <?php else: ?>
                なし
            <?php endif; ?>
        </dd>
    </dl>
    <div><a href="index.php?action=rewrite">&laquo;&nbsp;書き直す</a> | <input type="submit" value="登録する" /></div>
</form>
</main>
</body>
</html>