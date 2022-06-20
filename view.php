<?php
    session_start();
    require('dbconnect.php');

    if(empty($_REQUEST['id'])){
        header('Location: index.php');
        exit();
    }

    //投稿を取得する
    $sql = sprintf('SELECT m.name,m.picture,p.* FROM members m,posts p WHERE
        m.id = p.member_id AND p.id=%d ORDER BY p.created DESC ',
        mysqli_real_escape_string($db,$_REQUEST['id'])
    );
    $posts = mysqli_query($db,$sql) or die(mysqli_error($db));
?>
    <!DOCUTYPE html>
    <html>
        <head>
            <meta htt-equiv="Content-Type" content="text/html"; charset="UTF-8"/>
            <link rel="stylesheet" type="text/css" href="style.css">
            <title>ひとこと掲示板</title>
        </head>
        <div id="worp">
            <h1>ひとこと掲示板</h1>
        </div>
            <div id="content">
                <p>&laquo;<a href='index.php?thread_id=<?php echo $_SESSION['thread_id']; ?>'>
                一覧に戻る</a></p>
                <?php                 
                    if($post = mysqli_fetch_assoc($posts)):
                ?>
                <div class="msg">
                <img src="member_picture/<?php echo htmlspecialchars($post['picture'],
                 ENT_QUOTES, 'UTF-8'); ?>" width="48" height="48" alt="<?php
                echo htmlspecialchars($post['name'], ENT_QUOTES, 'UTF-8'); ?>" />
                <p><?php echo htmlspecialchars($post['message'], ENT_QUOTES,'UTF-8'); ?>
                <span class="name">（<?php echo htmlspecialchars($post['name'], 
                ENT_QUOTES, 'UTF-8'); ?>）</span></p><p class="day"><?php 
                echo htmlspecialchars($post['created'],ENT_QUOTES, 'UTF-8'); ?></p>
                </div>
                <?php else: ?>
                <p>その投稿は削除されたか、URLが間違えています</p>
                <?php endif; ?>
            </div>
            <div id="foot">
            <p><img src="images/txt_copyright.png" width="136"
                height="15" alt="(C) H2O Space. MYNAVI" /></p>
            </div>
        </div>
    </html>