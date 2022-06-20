<?php
error_reporting(0);
require('../dbconnect.php');
session_start();
if(!empty($_POST)) {
    // エラー項目の確認
    if($_POST['name'] == ''){
        $error['name'] = 'blank';
    }
    if ($_POST['email'] == '') {
        $error['email'] = 'blank';
    }
    if (strlen($_POST['password']) < 4) {
        $error['password'] = 'length';
    }
    if ($_POST['password'] == '') {
        $error['password'] = 'blank';
    }
    $fileName = $_FILES['imgage']['name'];
    if(!empty($fileName)){
        $ext = substr($fileName,-3);
        if($ext != 'jpg' &&  $ext != 'gif'){
            $error['image']  = 'type';
        }
    }

    //アカウントの重複チェック
    if(empty($error)){
        $sql = sprintf('SELECT COUNT(*) AS cnt FROM members WHERE email="%s"',
            mysqli_real_escape_string($db,$_POST['email'])
            );
        $record = mysqli_query($db,$sql) or die(mysqli_error($db));
        $table = mysqli_fetch_assoc($record);
        if($table['cnt'] > 0){
            $error['email'] = 'duplicate';
        }
    }

        //ニックネームの重複チェック
        if(empty($error)){
            $sql = sprintf('SELECT COUNT(*) AS cnt FROM members WHERE name="%s"',
                mysqli_real_escape_string($db,$_POST['name'])
                );
            $record = mysqli_query($db,$sql) or die(mysqli_error($db));
            $table = mysqli_fetch_assoc($record);
            if($table['cnt'] > 0){
                $error['name'] = 'duplicate';
            }
        }
    

    if (empty($error)){
        // 画像をアップロードする
        $image = date('YmdHis') . $_FILES['image']['name'];
        move_uploaded_file($_FILES['image']['tmp_name'],'../member_picture/'. $image);
        $_SESSION['join'] = $_POST;
        $_SESSION['join']['image'] = $image;
        header('Location: check.php');
        exit();
    }
}
// 書き直し
if ($_REQUEST['action'] == 'rewrite') {
    $_POST = $_SESSION['join'];
    $error['rewrite'] = true;
    }

?>
<!DOCUTYPE html>
    <html>
        <head>
            <meta htt-equiv="Content-Type" content="text/html"; charset="UTF-8"/>
            <link rel="stylesheet" type="text/css" href="../style.css">
            <title>ひとこと掲示板</title>
        </head>
    <body>
    <div id="worp">
            <h1>ひとこと掲示板</h1>
        </div>
        
<p>次のフォームに必要事項をご記入ください。</p>
<form action="" method="post" enctype="multipart/form-data">
<dl>
<dt>ニックネーム<span class="required">必須</span></dt>
<dd><input type="text" name="name" size="35" maxlength="255"
    value = "<?php echo htmlspecialchars($_POST['name'],ENT_QUOTES,'UTF-8') ?>"/>
 <?php if ($error['name'] == 'blank'): ?>
<p class="error">* ニックネームを入力してください</p>
<?php endif; ?>
<?php if ($error['name'] == 'duplicate'): ?>
<p class="error">* 指定したニックネームはすでに登録されています</p>
<?php endif; ?>
</dd>
<dt>メールアドレス<span class="required">必須</span></dt>
<dd><input type="text" name="email" size="35" maxlength="255"
value = "<?php echo htmlspecialchars($_POST['email'],ENT_QUOTES,'UTF-8') ?>"/>
<?php if ($error['email'] == 'blank'): ?>
<p class="error">* メールアドレスを入力してください</p>
<?php endif; ?></dd>
<dt>パスワード<span class="required">必須</span></dt>
<dd><input type="password" name="password" size="10" maxlength="20" 
value = "<?php echo htmlspecialchars($_POST['password'],ENT_QUOTES,'UTF-8') ?>"/>
<?php if ($error['password'] == 'blank'): ?>
<p class="error">* パスワードを入力してください</p>
<?php endif; ?>
<?php if($error['email'] == 'duplicate'):?>
    <p class="error">* 指定されたメールアドレスはすでに登録されています</p> 
<?php endif; ?>   
<?php if ($error['password'] == 'length'): ?>
<p class="error">パスワードは4文字で入力して下さい</p>
<?php endif; ?>
</dd>
<dt>写真など</dt>
<dd><input type="file" name="image" size="35" />
<?php if (!empty($error)): ?>
<p class="error">* 恐れ入りますが、画像を改めて指定してください</p>
<?php endif; ?>
</dd>
</dl>
<div><input type="submit" value="入力内容を確認する" /></div>
</form>
</body>

</html>