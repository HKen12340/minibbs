<?php
session_start();
require('dbconnect.php');

error_reporting(0);
if(isset($_SESSION['id']) && $_SESSION['time'] + 3600 > time()){
    //ログインしている
    $_SESSION['time'] = time();
    $sql = sprintf('SELECT * FROM members WHERE id=%d',
                    mysqli_real_escape_string($db,$_SESSION['id'])
                );
    $record = mysqli_query($db,$sql) or die(mysqli_error($db));
    $member = mysqli_fetch_assoc($record);
}else{
    //ログインしていない
    header('Location: login.php');
    exit();
}

//投稿を記録する
if(!empty($_POST)){
    if($_POST['message'] != ''){
        $sql = sprintf('INSERT INTO posts SET member_id=%d,message="%s",
        reply_post_id=%d,created=NOW()',
        mysqli_real_escape_string($db,$member['id']),
       mysqli_real_escape_string($db, $_POST['message']),
       mysqli_real_escape_string($db, $_POST['reply_post_id'])
    );
    mysqli_query($db,$sql) or die(mysqli_error($db));
    header('Location: index.php');
    exit();
    }
}
//投稿を取得する
$sql = sprintf('SELECT m.name,m.picture,p.* FROM members m,
        posts p WHERE m.id = p.member_id ORDER BY p.created DESC');
$posts = mysqli_query($db,$sql) or die(mysqli_error($db));        

if (isset($_REQUEST['res'])){
    $sql = sprintf('SELECT m.name,m.picture,p.* FROM members m,posts p WHERE 
    m.id=p.member_id AND p.id = %d ORDER BY p.created DESC'
    ,mysqli_real_escape_string($db,$_REQUEST['res'])
    );
    $record = mysqli_query($db,$sql) or die(sqli_error($db));
    $table = mysqli_fetch_assoc($record);
    $message = '@'.$table['name'].' '.$table['message'];
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
            <form action="" method="post">
                <dl>
                <dt><?php echo htmlspecialchars($member['name']); ?>さん、メッセージをどうぞ</dt>
                    <dd>
                        <textarea name="message"  cols="50" rows="5"><?php 
                            echo htmlspecialchars($message,ENT_QUOTES,'UTF-8'); ?></textarea>
                            <input type = "hidden" name="reply_post_id" 
                            value="<?php echo htmlspecialchars($_REQUEST['res'],ENT_QUOTES,'UTF-8');?>" />
                    </dd>
                </dl>
                <div>
                    <input type="submit" value="投稿する">
                </div>
            </form>
            <?php 
                while($post = mysqli_fetch_assoc($posts)):
                   
            ?>
            <div class="msg">
                <img src="member_picture/<?php echo htmlspecialchars($post['picture'],ENT_QUOTES,'UTF-8'); ?>" 
                width="48" height="48" alt="<?php echo htmlspecialchars($post['picture'],ENT_QUOTES,'UTF-8'); ?>"/>
                <p><?php echo htmlspecialchars($post['message'],ENT_QUOTES,'UTF-8'); ?>
                <span class="name">(<?php echo htmlspecialchars($post['name'],ENT_QUOTES,'UTF-8'); ?>)</span>
                [<a href="index.php?res=<?php echo htmlspecialchars($post['id'],ENT_QUOTES,'UFT-8'); ?>">Re</a>]</p>
                <p class="day"><?php echo htmlspecialchars($post['created'],ENT_QUOTES,'UTF-8'); ?></p>
            </div>
            <?php endwhile; ?>
        </div>
        <div id="foot">
            <p><img src="images/txt_copyright.png" width="136" height="15" alt="(C) H2O SPACE, Mynavi"/></p>
        </div>
    </body>
</html>