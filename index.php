<?php
session_start();
require('dbconnect.php');
error_reporting(0);
if(isset($_SESSION['id']) && $_SESSION['time'] + 3600 > time()){
    //ログインしている
    $_SESSION['time'] = time();
    $_SESSION['thread_id'] = $_REQUEST['thread_id'];
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
        $sql = sprintf('INSERT INTO posts SET thread_id=%d,member_id=%d,message="%s",
        reply_post_id=%d,created=NOW()',
        mysqli_real_escape_string($db,$_SESSION['thread_id']),
        mysqli_real_escape_string($db,$member['id']),
       mysqli_real_escape_string($db, $_POST['message']),
       mysqli_real_escape_string($db, $_POST['reply_post_id'])
    );
    mysqli_query($db,$sql) or die(mysqli_error($db));
   header(sprintf('Location: index.php?thread_id=%d',$_SESSION['thread_id']));
    exit();
    }
}
//投稿を取得する

$page = $_REQUEST['page'];
if($page == ''){
    $page = 1;
}
$page = max($page, 1);
$page = max($page, 1);

//最終ページを取得する
$sql = sprintf('SELECT COUNT(*) AS cnt FROM posts p WHERE thread_id=%d',$_SESSION['thread_id']);
$recordSet = mysqli_query($db, $sql);
$table = mysqli_fetch_assoc($recordSet);
$maxPage = ceil($table['cnt'] / 5);
$page = min($page, $maxPage);

$start = ($page - 1) * 5;
$start = max(0, $start);


$sql = sprintf('SELECT m.name,m.picture,p.* FROM members m,
posts p WHERE m.id = p.member_id AND p.thread_id = %d ORDER BY p.created DESC LIMIT %d , 5'
,$_SESSION['thread_id'],$start);
$posts = mysqli_query($db,$sql) or die(mysqli_error($db));        

$thread_title = sprintf('SELECT thread_name FROM thread WHERE thread_id=%d',$_SESSION['thread_id']);
$titles = mysqli_query($db,$thread_title) or die(mysqli_error($db));        
$title = mysqli_fetch_assoc($titles);

//返信の場合
if (isset($_REQUEST['res'])){
    $sql = sprintf('SELECT m.name,m.picture,p.* FROM members m,posts p WHERE 
    m.id=p.member_id AND p.id = %d AND p.thread_id = %d ORDER BY p.created DESC'
    ,mysqli_real_escape_string($db,$_REQUEST['res']),
    $_SESSION['thread_id']
    );
    $record = mysqli_query($db,$sql) or die(sqli_error($db));
    $table = mysqli_fetch_assoc($record);
    $message = '@'.$table['name'].' '.$table['message'];
}
//htmlspecialcharsのショートカット
function h($value){
    return htmlspecialchars($value,ENT_QUOTES,'UTF-8');
}

//本文内のURLリンクを設定します
function makeLink($value){
    return mb_ereg_replace("(https?)(://[[:alnum:]¥+¥$¥;¥?¥.%,!#~*/:@&=_-]+)", 
    '<a href="\1\2">\1\2</a>' , $value);
}
?>
<!DOCUTYPE html>
<html>
    <head>
        <meta content="text/html" charset="UTF-8"/>
        <link rel="stylesheet" type="text/css" href="style.css"/>
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <title>ひとこと掲示板</title>
    </head>
    <body>
        <div id="worp">
            <h1>ひとこと掲示板</h1>
        </div>
        <div id="content">
        <a href="ThreadList.php">一覧へ戻る</a>
            <div style="text-align:right"><a href="logout.php">ログアウト</a></div>
            <form action="" method="post">
            <div class="message_form">    
                    <?php echo  h($member['name']); ?>さん、メッセージをどうぞ
                        <textarea name="message"><?php 
                            echo  h($message); ?></textarea>
                            <input type = "hidden" name="reply_post_id" 
                            value="<?php  echo  h($_REQUEST['res']);?>" />
                            <p><input id = "submit" type="submit" value="投稿する"></p>
                </div>
                </div>
            </form>
            
            <?php 
            echo '<h1 style="text-align:center">'.$title['thread_name'].'</h1>';
                while($post = mysqli_fetch_assoc($posts)):
            ?>
            <div class="msg">
                <img src="member_picture/<?php  echo  h($post['picture']); ?>" 
                width="48" height="48" alt="<?php h($post['picture']);?>"/>
                <p><?php echo makeLink(h($post['message'])); ?>
                <span class="name">(<?php echo  h($post['name']); ?>)</span>
                [<a href="index.php?res=<?php echo  h($post['id']); ?>&thread_id=<?php echo $_SESSION['thread_id']; ?>">Re</a>]</p>
                <p class="day"><a href="view.php?id=<?php  echo  h($post['id']); ?>">
                <?php echo  h($post['created']); ?></a></p>
                <?php if($post['reply_post_id']>0): ?>
                    <a href="view.php?id=<?php echo  h($post['reply_post_id']); ?>">送信元のメッセージ</a>
                <?php endif; ?>
                <?php if($_SESSION['id'] == $post['member_id']): ?>
                    [<a href="delete.php?id=<?php echo h($post['id']); ?>" 
                    style="color:#F33;" onclick="return confirm('削除してよろしいですか？');">削除</a>]
                <?php endif; ?>
            </div>
            <?php endwhile; ?>
            <ul class="paging">
            <?php 
                if($page > 1){
            ?><li><a href="index.php?page=<?php print($page - 1); ?>&thread_id=<?php print $_SESSION['thread_id'] ?>">前のページへ</a></li>
            <?php 
                }else{ 
            ?>
                <li>前のページへ</li>
            <?php
                 } 
             ?>
             <?php 
                if($page < $maxPage){
            ?><li><a href="index.php?page=<?php print($page + 1); ?>&thread_id=<?php print $_SESSION['thread_id'] ?>">次のページへ</a></li>
            <?php 
                }else{ 
            ?>
                <li>次のページへ</li>
            <?php
                 } 
             ?>
            </ul>
        </div>
        <div id="foot">
            <p><img src="images/txt_copyright.png" width="136" height="15" alt="(C) H2O SPACE, Mynavi"/></p>
        </div>
        
    </body>
</html>