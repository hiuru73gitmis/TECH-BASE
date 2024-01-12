<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<title>mission5-1</title>
</head>
<body>
    <?php
    $dsn = 'mysql:dbname=tb250589db;host=localhost';
    $user = 'tb-250589';
    $password = 'gR7gZ2Fxnh';
    $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
    //DB接続設定
    
    $sql = "CREATE TABLE IF NOT EXISTS dbboard"
    ." ("
    . "id INT AUTO_INCREMENT PRIMARY KEY,"
    . "name CHAR(32),"
    . "comment TEXT,"
    . "date TEXT,"
    . "password TEXT"
    .");";
    $stmt = $pdo->query($sql);
    //入力したレコードを抽出・表示
    $count = 0;
    $sql = 'SELECT * FROM dbboard ORDER BY id';//抽出文　特定のもののみ選びたければ個々にwhere文
    $stmt = $pdo->query($sql);
    $results = $stmt->fetchAll();
    foreach ($results as $row){
        //$rowの中にはテーブルのカラム名が入る
        $count++;
    }
    
    if(!empty($_POST["del"]) && isset($_POST['submit2'])){
        $delid = $_POST["del"];
        $delpass = $_POST["delpass"];
        
        $sql = 'SELECT * FROM dbboard WHERE id=:id ';
        $stmt = $pdo->prepare($sql);                  // ←差し替えるパラメータを含めて記述したSQLを準備し、
        $stmt->bindParam(':id', $delid, PDO::PARAM_INT); // ←その差し替えるパラメータの値を指定してから、
        $stmt->execute();                             // ←SQLを実行する。
        $results = $stmt->fetchAll(); 
        
        foreach ($results as $row){
        //$rowの中にはテーブルのカラム名が入る
        $dbpass =  $row['password'];
        }
        if($dbpass == $delpass && $delpass != "NO PASSWORD IN THIS MESSAGE"){
            $sql = 'delete from dbboard where id=:id';
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':id', $delid, PDO::PARAM_INT);
            $stmt->execute();
        
            $sql = 'set @n:=0';
            $stmt = $pdo->query($sql);
            $sql = 'update dbboard set id=@n:=@n+1'; 
            $stmt = $pdo->query($sql);
            //id振り直し文
        }
    }
    //特定のレコードの削除
    
    $sql = 'alter table dbboard auto_increment = 1';
    $stmt = $pdo->query($sql);
    //連番の振り直し
    
    //編集タグ
    if(!empty($_POST["remake"]) && $_POST["remake"] > 0 && $_POST["remake"] <= $count ){
        $renum = $_POST["remake"];
        $editpass = $_POST["editpass"];
        
        $sql = 'SELECT * FROM dbboard WHERE id=:id ';
        $stmt = $pdo->prepare($sql);                  // ←差し替えるパラメータを含めて記述したSQLを準備し、
        $stmt->bindParam(':id', $renum, PDO::PARAM_INT); // ←その差し替えるパラメータの値を指定してから、
        $stmt->execute();                             // ←SQLを実行する。
        $results = $stmt->fetchAll(); 
        
        foreach ($results as $row){
        //$rowの中にはテーブルのカラム名が入る
        $dbpass =  $row['password'];
        }
        if($dbpass == $editpass){
            foreach ($results as $row){
                //$rowの中にはテーブルのカラム名が入る
                $rename =  $row['name'];
                $recom = $row['comment'];
            }
        }else{
            $renum = "";
            $rename = "";
            $recom = "";
        }
    }else{
        $renum = "";
        $rename = "";
        $recom = "";
    }
   
    //編集と書き込み
   if(isset($_POST['submit1'])){
        if(empty($_POST["edittag"])){ //通常書き込み
            if(!empty($_POST["name"]) && !empty($_POST["comment"])){
            $name = $_POST["name"];
            $comment = $_POST["comment"];
            $date = date("Y年m月d日 H時i分s秒");
            if(!empty($_POST["password"])){
                $pass = $_POST["password"];
            }else{
                $pass = "NO PASSWORD IN THIS MESSAGE";
            }
    
                if($name != NULL && $comment != NULL){
                    $sql = "INSERT INTO dbboard (name, comment,date, password) VALUES (:name, :comment, :date, :password)";
                    $stmt = $pdo->prepare($sql);
                    $stmt->bindParam(':name', $name, PDO::PARAM_STR);
                    $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
                    $stmt->bindParam(':date', $date, PDO::PARAM_STR);
                    $stmt->bindParam(':password', $pass, PDO::PARAM_STR);
                    $stmt->execute();    
                }
            }
        }else if($_POST["edittag"] > 0){ //編集モード
            $edit = $_POST["edittag"];
            $editname = $_POST["name"];
            $editcmt = $_POST["comment"];
            $newpass = $_POST["password"];
            
            $id = $edit; //変更する投稿番号
            $name = $editname;
            $comment = $editcmt; //変更したい名前、変更したいコメントは自分で決めること
            $sql = 'UPDATE dbboard SET name=:name,comment=:comment, password =:password WHERE id=:id';
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':name', $name, PDO::PARAM_STR);
            $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
            $stmt->bindParam(':password', $newpass, PDO::PARAM_STR);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            echo $edit."番の変更を受け付けました<br>";
        }   
    }
    
    ?>
    <form action="" method="post">
        <input type="hidden" name="edittag" value= "<?php echo $renum; ?>" placeholder="編集番号表示" >
        <label for = "name">名前:</label>
        <input type="text" name="name" value= "<?php echo $rename; ?>" placeholder="フルネーム" >
        
        <label for = "comment">コメント:</label>
        <input type="text" name="comment" value= "<?php echo $recom; ?>" placeholder="コメントを書いてね" >
        
        <label for = "password">パスワード:</label>
        <input type="password" name="password" value= "" placeholder="記入" >
        
        <input type="submit" name="submit1" >
    </form>
    <form action="" method="post">
        <label for = "del">削除番号:</label>
        <input type="number" name="del" value= "" placeholder="削除したい番号を入力" >
        
        <label for = "delpass">パスワード:</label>
        <input type="password" name="delpass" value= "" placeholder="記入" >
        
        <input type="submit" name="submit2" value = "削除">
    </form>
     <form action="" method="post">
        <label for = "remake">編集したい番号:</label>
        <input type="number" name="remake" value= "" placeholder="番号" >
        
        <label for = "editpass">パスワード:</label>
        <input type="password" name="editpass" value= "" placeholder="記入" >

        <input type="submit" name="submit3" value = "編集">
    </form>
    <?php
    $sql = 'SELECT * FROM dbboard ORDER BY id';//抽出文　特定のもののみ選びたければ個々にwhere文
    $stmt = $pdo->query($sql);
    $results = $stmt->fetchAll();
    foreach ($results as $row){
        //$rowの中にはテーブルのカラム名が入る
        echo $row['id'].' / ';
        echo $row['name'].' / ';
        echo $row['comment'].' / ';
        echo $row['date'].'<br>';
    echo "<hr>";
    }
    ?>
</body>
</html>