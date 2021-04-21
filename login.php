<?php
session_start();
require('dbconnect.php');

//自動ログイン(クッキーが保存されていたら)
if(isset($_COOKIE['email']) && $_COOKIE['email'] != ''){
    $_POST['email'] = $_COOKIE['email'];
    $_POST['password'] = $_COOKIE['password'];
    $_POST['save'] = 'on';
}

if(!empty($_POST)){
    //ログイン処理
    if($_POST['email'] != '' && $_POST['password'] != ''){
        //emailとpasswordが一致するデータが存在するか検索
        $login = $db->prepare('SELECT * FROM members WHERE email=? AND password=?');
        $login->execute(array(
            $_POST['email'],
            //sha1で暗号化して保存しているため検索時にも暗号化
            sha1($_POST['password'])
        ));
        //DB検索結果を取得
        $member = $login->fetch();

        if($member){
            //ログイン成功
            $_SESSION['id'] = $member['id'];
            $_SESSION['time'] = time();

            //ログイン情報を記録する（クッキー)
            if($_POST['save'] == 'on'){
                setcookie('email', $_POST['email'], time()+60*60*24*14);
                setcookie('password', $_POST['password'], time()+60*60*24*14);
            }

            //index.phpへ移動
            header('Location: home.php');
            exit();
        } else {
            //DBに一致するデータがなければエラー：ログイン.失敗
            $error['login'] = 'failed';
        }
    } else {
        //メールアドレスかパスワードが入力されていなければエラー：ログイン.空白
        $error['login'] = 'blank';
    }
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
    <h1>ログイン</h1>
    <p>メールアドレスとパスワードを入力してログインしてください。</p>
    <p>入会手続きがまだの方はこちらからどうぞ。</p>
    <p>&raquo;<a href="join/">入会手続きをする</a></p>
    <form action="" method="post">
        <dl>
            <dt>メールアドレス</dt>
            <dd>
            <?php if(isset($_POST) && isset($_POST['email'])): ?>
                <input type="text" name="email" size="35" maxlength="255" value="<?php echo htmlspecialchars($_POST['email'], ENT_QUOTES); ?>" />
            <?php else: ?>
                <input type="text" name="email" size="35" maxlength="255" value="" />
            <?php endif; ?>
            <?php if(isset($error) && $error['login'] == 'blank'): ?>
                <p class="error">* メールアドレスとパスワードを入力してください</p>
            <?php endif; ?>
            <?php if(isset($error) && $error['login'] == 'failed'): ?>
                <p class="error">* ログインに失敗しました。正しく入力してください</p>
            <?php endif; ?>
            </dd>
            <dt>パスワード</dt>
            <dd>
            <?php if(isset($_POST) && isset($_POST['password'])): ?>
                <input type="password" name="password" size="35" maxlength="255" value="<?php echo htmlspecialchars($_POST['password'], ENT_QUOTES); ?>" />
            <?php else: ?>
                <input type="password" name="password" size="35" maxlength="255" value="" />
            <?php endif; ?>
            </dd>
            <dt>ログイン情報の記録</dt>
            <dd>
            <input id="save" type="checkbox" name="save" value="on"><label for="save">次回からは自動的にログインする</label> 
            </dd>
        </dl>
        <div><input type="submit" value="ログインする" /></div>
    </form>
</body>
</html>