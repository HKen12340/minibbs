<?php
session_start();
require('dbconnect.php');

if(isset($_SESSION['id']) && $_SESSION['time'] + 3600 > time()){
    //ログインしている
    $_SESSION['time'] = time();
    $sql = sprintf('SELECT * FROM members WHERE id=%d',
                    mysqli_escape_string($db,$_SESSION['id'])
                );
    $record = mysqli_query($db,$sql) or die(mysqli_error($db));
    $member = mysqli_fetch_assoc($record);
}else{
    //ログインしていない
    header('Location: login.php');
    exit();
}
?>
<!DOCUTYPE html>
<html>
    <head>
        <meta content="text/html" charset="UTF-8"/>
        <link rel="stylesheet" type="text/css" href="sytle.css"/>
        <title>ひとこと掲示板</title>
    </head>
    <body>
        <div id="worp">
            <h1>ひとこと掲示板</h1>
        </div>
        <div id="content">
            <form action="" method="">
                <dl>
                <dt><?php echo htmlspecialchars($member['name']); ?>さん、メッセージをどうぞ</dt>
                    <dd>
                        <textarea name="messasge"  cols="50" rows="5"></textarea>
                    </dd>
                </dl>
                <div>
                    <input type="submit" value="投稿する">
                </div>
            </form>
        </div>
        <div id="foot">
            <p><img src="images/txt_copyright.png" width="136" height="15" alt="(C) H2O SPACE, Mynavi"/></p>
        </div>
    </body>
</html>