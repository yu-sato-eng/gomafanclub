<?php
session_start();
require('../dbconnect.php');

if(!empty($_POST)){
    //エラー項目の確認
    if($_POST['name'] == ''){
        $error['name'] = 'blank';
    }
    if($_POST['email'] == ''){
        $error['email'] = 'blank';
    }
    if(strlen($_POST['password']) < 4){
        $error['password'] = 'length';
    }
    if($_POST['password'] == ''){
        $error['password'] = 'blank';
    }
    $fileName = $_FILES['image']['name'];
    if(!empty($fileName)){
        $ext = substr($fileName, -3);
        if($ext != 'jpg' && $ext != 'gif'){
            $error['image'] = 'type';
        }
    }

    //アカウント重複チェック
    if(empty($error)){
        $member = $db->prepare('SELECT COUNT(*) AS cnt FROM members WHERE email=?');
        $member->execute(array($_POST['email']));
        $record = $member->fetch();
        if($record['cnt'] > 0){
            $error['email'] = 'duplicate';
        }
    }

    if(empty($error)){
        $_SESSION['join'] = $_POST;
        //画像をアップロードする
        if(!empty($fileName)){
        $image = date('YmdHis') . $_FILES['image']['name'];
		move_uploaded_file($_FILES['image']['tmp_name'], '../member_picture/' . $image);
        $_SESSION['join']['image'] = $image;
        }
        
        header('Location: check.php');
        exit();
    }
}

//書き直し
if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'rewrite'){
    $_POST = $_SESSION['join'];
    $error['rewrite'] = true;
}
?>
<!doctype html>
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
    <p>次のフォームに必要事項をご記入ください。</p>
    <form action="" method="post" enctype="multipart/form-data">
        <dl>
            <dt>ニックネーム<span class="required">必須</span></dt>
            <dd>
                <?php if(isset($_POST) && isset($_POST['name'])): ?>
                    <input type="text" name="name" size="35" maxlength="255" value="<?php echo htmlspecialchars($_POST['name'], ENT_QUOTES); ?>"/>
                <?php else: ?>
                    <input type="text" name="name" size="35" maxlength="255" value=""/>
                <?php endif; ?>
                <?php if(isset($error) && isset($error['name']) && $error['name'] == 'blank'): ?>
                    <p class="error">* ニックネームを入力してください</p>
                <?php endif; ?>
            </dd>
            <dt>メールアドレス<span class="required">必須</span></dt>
            <dd>
                <?php if(isset($_POST) && isset($_POST['email'])): ?>
                    <input type="text" name="email" size="35" maxlength="255" value="<?php echo htmlspecialchars($_POST['email'], ENT_QUOTES); ?>" />
                <?php else: ?>
                    <input type="text" name="email" size="35" maxlength="255" value="" />
                <?php endif; ?>
                <?php if(isset($error) && isset($error['email']) && $error['email'] == 'blank'): ?>
                    <p class="error">* メールアドレスを入力してください</p>
                <?php endif; ?>
                <?php if(isset($error) && isset($error['email']) && $error['email'] == 'duplicate'): ?>
                    <p class="error">* 指定されたメールアドレスはすでに登録されています</p>
                <?php endif; ?>
            </dd>
            <dt>パスワード<span class="required">必須</span></dt>
            <dd>
                <?php if(isset($_POST) && isset($_POST['password'])): ?>
                    <input type="password" name="password" size="10" maxlength="20" value="<?php echo htmlspecialchars($_POST['password'], ENT_QUOTES); ?>" />
                <?php else: ?>
                    <input type="password" name="password" size="10" maxlength="20" value="" />
                <?php endif; ?>
                <?php if(isset($error) && isset($error['password']) && $error['password'] == 'blank'): ?>
                    <p class="error">* パスワードを入力してください</p>
                <?php endif; ?>
                <?php if(isset($error) && isset($error['password']) && $error['password'] == 'length'): ?>
                <p class="error">* パスワードは4文字以上で入力してください</p>
                <?php endif; ?>
            </dd>
            <dt>アイコン</dt>
            <dd>
                <input type="file" name="image" size="35" />
                <?php if(isset($error) && isset($error['image']) && $error['image'] == 'type'): ?>
                <p class="error">* アイコンには「.gif」または「.jpg」の画像を指定してください</p>
                <?php endif; ?>
                <?php if(!empty($error)): ?>
                <p class="error">* 恐れ入りますが、画像を改めて指定してください</p>
                <?php endif; ?>
            </dd>
        </dl>
        <div><input type="submit" value="入力内容を確認する" /></div>
    </form>
</body>
</html>