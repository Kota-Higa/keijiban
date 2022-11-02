<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="./css/style.css">
</head>

<body>
    <div class = main-container>
        <p class = main-title>
            
            掲示板
            
        </p>
            
    </div>
    <?php
        //データベース名
        // DB接続設定
        $dsn ='データベース名';
        //ユーザー名
        $user ='ユーザー名';
        $pass ='パスワード';
        $pdo = new PDO($dsn,$user,$pass,array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));

        //テーブル作成
        $sql = "CREATE TABLE IF NOT EXISTS forum"
        ."("
        ."id INT AUTO_INCREMENT PRIMARY KEY," //id:投稿番号
        ."name char(32),"                    //name:ユーザー名
        ."comment TEXT,"                     //comment:コメント
        ."password TEXT,"                   //password:パスワード
        ."date TEXT,"                       //date:日付
        ."edit TEXT"                        //edit:（編集済み）用box
        .");";
        $stmt = $pdo->query($sql);
                
                
                
                
                /*
                テーブル確認用
                $sql2 ='SHOW TABLES';
                $result = $pdo -> query($sql2);
                foreach ($result as $row){
                    echo $row[0];
                    echo '<br>';
                }
                echo "<hr>";
                */
                
                
                
                /*
                テーブルの構成詳細確認用
                $sql3 ='SHOW CREATE TABLE forum';
                $result = $pdo -> query($sql3);
                foreach ($result as $row){
                    echo $row[1];
                }
                echo "<hr>";
                */
                
                /*
                テーブル削除用
                $sql0 = 'DROP TABLE forum';
                $stmt0 = $pdo->query($sql0);
                */
                
                
                //掲示板
                if(!empty($_POST["edit"]) && !empty($_POST["password-edit"])){
                    
                    //編集機能
                    $edit = $_POST["edit"];
                    $editkey = $_POST["password-edit"];
                    
                    $sql = 'SELECT * FROM forum';
                    $stmt = $pdo->query($sql);
                    $results = $stmt->fetchAll();
                    //レコードの数だけループ
                    foreach($results as $row){
                        //投稿番号と入力番号が一致、かつ登録パスワードと入力パスワードが一致した場合
                        if($edit == $row['id'] && $editkey == $row['password']){
                            //フォームに値を表示させるための変数
                            $editNum = $row['id'];
                            $editName = $row['name'];
                            $editContents = $row['comment'];
                            $editPass = $row['password'];
                        }
                    }
                }
                
                
                if(!empty($_POST["edit-number"]) && !empty($_POST["password"])){
                    //編集後の処理
                    $editNum = $_POST["edit-number"];
                    $edipass = $_POST["password"];
                    $UserName = $_POST["name"];
                    $UserContents = $_POST["contents"];
                    $date = date("Y年m月d日 H:i:s");
                    $edited = "(編集済み)";
                    
                    $sql = 'SELECT * FROM forum';
                    $stmt = $pdo->query($sql);
                    $results = $stmt->fetchAll();
                    
                    //レコードの数だけループ
                    foreach($results as $row){
                        //投稿番号と入力番号が一致、かつ登録パスワードと入力パスワードが一致した場合
                        if($editNum == $row['id'] && $edipass == $row['password']){
                            $sql = 'UPDATE forum SET name=:name,comment=:comment,password=:password,date=:date,edit=:edit WHERE id=:id';
                            $stmt = $pdo->prepare($sql);
                            $stmt->bindParam(':name', $UserName, PDO::PARAM_STR);        //変更したい名前
                            $stmt->bindParam(':comment', $UserContents, PDO::PARAM_STR);//変更したいコメント
                            $stmt->bindParam(':password', $edipass, PDO::PARAM_STR);    
                            $stmt->bindParam(':date', $date, PDO::PARAM_STR);           //編集後の日付変更のため
                            $stmt->bindParam(':edit', $edited, PDO::PARAM_STR);         //編集後（編集済み）表示のため
                            $stmt->bindParam(':id', $row['id'], PDO::PARAM_INT);
                            $stmt->execute();
                        }
                    }
                    
                }elseif(!empty($_POST["name"]) && !empty($_POST["contents"]) && !empty($_POST["password"])){
                    
                    //投稿機能
                    $UserName = $_POST["name"];
                    $UserContents = $_POST["contents"];
                    $password = $_POST["password"];
                    
                    $date = date("Y年m月d日 H:i:s");
                    $Edit = " ";
                    
                    //レコードの作成
                    $sql3 = $pdo -> prepare("INSERT INTO forum (name, comment, password, date, edit) VALUES (:name, :comment, :password, :date, :edit)");
                    $sql3 -> bindParam(':name', $UserName, PDO::PARAM_STR);
                    $sql3 -> bindParam(':comment', $UserContents, PDO::PARAM_STR);
                    $sql3 -> bindParam(':password', $password, PDO::PARAM_STR);
                    $sql3 -> bindParam(':date', $date, PDO::PARAM_STR);
                    $sql3 -> bindParam(':edit', $Edit, PDO::PARAM_STR);  //最初の投稿では（編集済み）を表示させたくないため、”空”にしてある
                    
                    
                    $sql3 -> execute();
                    
                }elseif(!empty($_POST["delete"]) && !empty($_POST["password-delete"])){
                    
                    //削除機能
                    $delete = $_POST["delete"];
                    $delpass = $_POST["password-delete"];
                    
                    $sql = 'SELECT * FROM forum';
                    $stmt = $pdo->query($sql);
                    $results = $stmt->fetchAll();
                     //レコードの数だけループ
                    foreach($results as $row){
                        //投稿番号と入力番号が一致、かつ登録パスワードと入力パスワードが一致した場合
                        if($delete == $row['id'] && $delpass == $row['password']){
                            $sql2 = 'delete from forum where id=:id';
                            $stmt2 = $pdo->prepare($sql2);
                            $stmt2->bindParam(':id', $row['id'], PDO::PARAM_INT);
                            $stmt2->execute();
                        }
                    }
                }
            ?>
            
            
            <!---フォーム. -->
            <form  method="POST" action="">
                <input class= "user-name" type="text" name="name" placeholder='Name' required = "required" value = "<?php if(isset($editName)){echo $editName;}?>">
                <input  type = "hidden" name ="edit-number" value = "<?php if(isset($editNum)){echo $editNum;}?>"><br>
                <input class = "message" type = "text" name="contents" placeholder = "Message" required = "required" value = "<?php if(isset($editContents)){echo $editContents;}?>"><br>
                <input class = "password" type="text" name="password" placeholder='Password' required = "required" value = "<?php if(isset($editPass)){echo $editPass;}?>"><br>
                <input type="submit" name="submit" value="送信">
            </form>
            <br>
            <hr>
            <br>
            <form method = "POST" action = "">
                 <input type="num" name="delete" placeholder='Delete number' required = "required">
                 <input type="text" name="password-delete" placeholder='Password' required = "required" value = "">
                 <input type="submit" name="delete-submit" value="削除"><br>
            </form>
           <br>
            <form method = "POST" action = "">
                <input type="num" name="edit" placeholder='Edit number' required = "required">
                <input type="text" name="password-edit" placeholder='Password' required = "required" value = "">
                <input type="submit" name="edit-submit" value="編集"><br>
                <br>
                <br>
            </form>
            <hr>
            
            <div class = "php">
                <?php
                       //レコードの表示
                        $sql4 = 'SELECT * FROM forum';
                        $stmt = $pdo->query($sql4);
                        $results = $stmt->fetchAll();
                        foreach ($results as $row){
                            //$rowの中にはテーブルのカラム名が入る
                            echo $row['id'].' ';
                            echo $row['name'].' ';
                            echo $row['comment'].' ';
                            //echo $row['password'].' ';
                            echo $row['date']." ";
                            echo $row['edit']." ";
                            '<br>';
                        echo "<hr>";
                        }
                ?>
            </div>
    
</body>
</html>
        

  
        
