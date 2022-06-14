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

//返信の場合
if (isset($_REQUEST['res'])){
    $sql = sprintf('SELECT m.name,m.picture,p.* FROM members m,posts p WHERE 
    m.id=p.member_id AND p.id = %d ORDER BY p.created DESC'
    ,mysqli_real_escape_string($db,$_REQUEST['res'])
    );
    $record = mysqli_query($db,$sql) or die(sqli_error($db));
    $table = mysqli_fetch_assoc($record);
    $message = '@'.$table['name'].' '.$table['message'];
}
//htmlspecialcharsのショートカット
function h($value){
    return htmlspecialchars($value,ENT_QUOTES,'UTF-8');
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
                <dt><?php echo  h($member['name']); ?>さん、メッセージをどうぞ</dt>
                    <dd>
                        <textarea name="message"  cols="50" rows="5"><?php 
                            echo  h($message); ?></textarea>
                            <input type = "hidden" name="reply_post_id" 
                            value="<?php  echo  h($_REQUEST['res']);?>" />
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
                <img src="member_picture/<?php  echo  h($post['picture']); ?>" 
                width="48" height="48" alt="<?php h($post['picture']);?>"/>
                <p><?php echo  h($post['message']); ?>
                <span class="name">(<?php echo  h($post['name']); ?>)</span>
                [<a href="index.php?res=<?php echo  h($post['id']); ?>">Re</a>]</p>
                <p class="day"><a href="view.php?id=<?php  echo  h($post['id']); ?>">
                <?php echo  h($post['created']); ?></a></p>
                <?php if($post['reply_post_id']>0): ?>
                    <a href="view.php?id=<?php echo  h($post['reply_post_id']); ?>">送信元のメッセージ</a>
                <?php endif; ?>
            </div>
            <?php endwhile; ?>
        </div>
        <div id="foot">
            <p><img src="images/txt_copyright.png" width="136" height="15" alt="(C) H2O SPACE, Mynavi"/></p>
        </div>
    </body>
</html>