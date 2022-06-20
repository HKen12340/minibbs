<?php
session_start();
ini_set("error_reporting", 0);
require('../dbconnect.php');

if(!isset($_SESSION['join'])){
    header('Location: index.php');
    exit();
}
if(!empty($_POST)){
    // 登録処理をする
    $sql = sprintf('INSERT INTO members SET name="%s", email="%s",
    password="%s", picture="%s", created="%s"',
    mysqli_real_escape_string($db, $_SESSION['join']['name']),
    mysqli_real_escape_string($db, $_SESSION['join']['email']),
    mysqli_real_escape_string($db, sha1($_SESSION['join']
    ['password'])),
    mysqli_real_escape_string($db, $_SESSION['join']['image']),
    date('Y-m-d H:i:s')
);
    mysqli_query($db,$sql) or die(mysqli_error($db));
    unset($_SESSION['join']);

    header('Location: thanks.php');
    exit();
}
?>
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
<form action="" method="post">
    <input type="hidden" name="action" value="submit"/>
    <dl>
        <dt>ニックネーム</dt>
        <dd><?php echo htmlspecialchars($_SESSION['join']['name'],ENT_QUOTES); ?></dd>
        <dt>メールアドレス</dt>
        <dd><?php echo htmlspecialchars($_SESSION['join']['email'],ENT_QUOTES); ?></dd>
        <dt>パスワード</dt>
        <dd>
            【表示されません】
        </dd>
        <dt>写真など</dt>
        <dd><img src="../member_picture/<?php echo htmlspecialchars($_SESSION['join']['image'],ENT_QUOTES);?>" width="100" height="100" alt=""></dd>
    </dl>
    <div><a href="index.php?action=rewrite">&laquo;&nbsp;書き直す</a>
    <input type = "submit" value="登録する"/></div>
</form>
</body>
</html>