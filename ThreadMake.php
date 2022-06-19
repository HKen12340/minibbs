<html>
    <head>
        <meta charset="UTF-8">
    </head>
    <body>
       <p>スレッド名を入力してください</p>
        <form action="" method="post">
            <input type="text" name="thread_name">
            <input type="submit" value="送信">
        </form>
    </body>
    <?php
        require("dbconnect.php");
        if(!empty($_POST['thread_name'])){
            if($_POST['thread_name'] != ''){
        $sql = sprintf('INSERT INTO thread SET thread_name="%s",created= NOW()',
                 mysqli_real_escape_string($db,$_POST['thread_name']));
        mysqli_query($db,$sql) or die(mysqli_error($db)); 
        header("Location: ThreadList.php");
        exit();
    }
        }
    ?>
</html>