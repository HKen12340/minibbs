<?php
    require('dbconnect.php');
    $sql = sprintf('SELECT * FROM thread');
    $thread_table =  mysqli_query($db,$sql) or die(mysqli_error($db));
    
?>
<a href="ThreadMake.php">スレッドを作る</a>
    <?php while($threads = mysqli_fetch_assoc($thread_table)):?>
      <a href="index.php?thread_id=<?php echo $threads['thread_id'] ?>">
      <?php echo $threads['thread_name'] ?></a>
    <?php endwhile; ?>

