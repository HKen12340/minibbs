<?php
session_start();

if(!isset($_SESSION['join'])){
    header('Location: index.php');
    exit();
}

?>
<form action="" method="post">
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