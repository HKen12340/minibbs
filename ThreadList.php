<?php
    require('dbconnect.php');
    $sql = sprintf('SELECT * FROM thread');
    $thread_table =  mysqli_query($db,$sql) or die(mysqli_error($db));
    
?>
<html>
    <head>
        <meta content="text/html" charset="UTF-8"/>
        <link rel="stylesheet" type="text/css" href="style.css"/>
        <title>ひとこと掲示板</title>
    </head>
    <body>
        <div id="worp">
            <h1>ひとこと掲示板</h1>
        </div>
        <div id="content">
            <div style="text-align:right"><a href="logout.php">ログアウト</a></div>
<a href="ThreadMake.php">スレッドを作る</a>
</body>
    <?php while($threads = mysqli_fetch_assoc($thread_table)):?>
      <hr>
     <a href="index.php?thread_id=<?php echo $threads['thread_id'] ?>">
      <h1 class="Thread_list"><?php echo $threads['thread_name'] ?></h1></a>
      <p><?php echo $threads['created'] ?></p>
    <?php endwhile; ?>
    <hr>
    </html>
