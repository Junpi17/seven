<!DOCTYPE html>
<html lang="ja">
 <head>
  <title>mission_5-1</title>
  <meta charset="utf-8"/>
 </head>
    <?php 
     // DB接続設定
    $dsn = 'mysql:dbname=tb230024db;host=localhost';
    $user = 'tb-230024';
    $password = '7zv2hXFuRp';
    $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
    //テーブル作成
    $sql = "CREATE TABLE IF NOT EXISTS mission5"
    ." ("
    . "id INT AUTO_INCREMENT PRIMARY KEY,"
    . "name char(32),"
    . "comment TEXT"
    .");";
    $stmt = $pdo->query($sql);
    if(!empty($_POST["pw"])){//パスワード入力している場合
    $password=$_POST["pw"];//パスワードを取得
    //編集番号確認機能
    if(!empty($_POST["edit_number"])&& !empty($_POST["edit_message"])){//編集番号と編集の送信がある時
        $editnumber=$_POST["edit_number"];//編集番号を取得
        $id = $editnumber;
        $sql = 'SELECT * FROM mission5 WHERE id=:id ';
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $results = $stmt->fetchAll();
        foreach ($results as $row){
            $readpassword=$row['password'];//元々のパスワードを取得
            if ($password == $readpassword){//パスワード認証
                $editnumber1=$row['id'];//編集番号を一時保存、78行と関連
                $editname=$row['name'];//
                $editcomment=$row['comment'];
            }else{
                echo "パスワードが間違っている。";
            }
        }
    }

    //削除機能
    if(!empty($_POST["delete_number"]) && !empty($_POST["delete_message"])){//削除番号と削除の送信がある時
        $deletenumber=$_POST["delete_number"];//削除番号を取得
        $id = $deletenumber;
        $sql = 'SELECT * FROM mission5 WHERE id=:id ';
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $results = $stmt->fetchAll();
        foreach ($results as $row){
            $readpassword=$row['password'];//元々のパスワードを取得
            if ($password == $readpassword){//パスワード認証
                $sql = 'delete from mission5 where id=:id';
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                $stmt->execute();
            }else{
                echo "パスワードが間違っている。";
            }
        }
    }
}
?>

<body>
    <h1>掲示板</h1>
<form action="" method="post">
    
    <h3>新規投稿</h3>
    
        <input type="text" name="name" placeholder="名前を入力" value="<?php if(!empty($editnumber1)){echo $editname;}?>"><br>
        <input type="text" name="text" placeholder="コメントを入力" value="<?php if(!empty($editnumber1)){echo $editcomment;}?>"><br>
        <input type="hidden" name="h_editnumber"value="<?php if(!empty($editnumber1)){echo $_POST["edit_number"];}?>">
        <input type="password" name="pw" placeholder="パスワードを入力" value="<?php if(!empty($editnumber1)){echo $readpassword;}?>">
        <input type="submit" name="send_message" value="送信"><br>
    <h3>削除フォーム</h3>    
        <input type="number" name="delete_number" placeholder="削除する番号を入力">
        <input type="submit" name="delete_message"value="削除"><br>
    <h3>編集フォーム</h3>    
        <input type="number" name="edit_number" placeholder="編集する番号を入力">
        <input type="submit" name="edit_message"value="編集"><br>
   
</form>
<?php
//投稿
// 編集入力機能
if(!empty($_POST["send_message"])){//送信がある時
    if(!empty($_POST["name"])){//名前の入力がある時
        if(!empty($_POST["pw"])){//パスワードの入力がある時
            $name = trim($_POST['name']);//名前を取得
            $comment = trim($_POST['text']); //コメントを取得
            $password= trim($_POST["pw"]);//パスワードを取得
            $TIMESTAMP=new DateTime();//時間取得
            $TIMESTAMP=$TIMESTAMP->format("Y-m-d H:i:s");//時間の格式を決める
            // 編集機能
            if(!empty($_POST["h_editnumber"])){//編集番号がある時、「78行と関連している」
                $editnumber=$_POST["h_editnumber"];//編集番号を取得
                $id = $editnumber;
                $sql = 'UPDATE mission5 SET name=:name,comment=:comment,time=:time, password=:password WHERE id=:id';
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':name', $name, PDO::PARAM_STR);
                $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
                $stmt->bindParam(':password', $password, PDO::PARAM_STR);
                $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                $stmt->bindValue('time',$TIMESTAMP, PDO::PARAM_STR);
                $stmt->execute();
                echo "$comment"." を更新しました。";
            }else{// 入力機能、編集番号がない時、新しいコメントを加える
                $sql = $pdo -> prepare("INSERT INTO mission5 (name, comment, time, password) VALUES (:name, :comment, :time, :password)");
                $sql -> bindParam(':name', $name, PDO::PARAM_STR);
                $sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
                $sql -> bindValue('time',$TIMESTAMP, PDO::PARAM_STR);
                $sql -> bindParam(':password', $password, PDO::PARAM_STR);
                
                echo "$comment"." を受け付けました。";
            }
        }else{//パスワードの入力していない場合、エラーを提示する
            echo "<br>パスワードを入力してください。<br>";
        }
    }else{//名前の入力していない場合、エラーを提示する
        echo "<br>名前を入力してください。<br>";
    }
}

echo "<br>投稿一覧";

//表示機能
$sql = 'SELECT * FROM mission5';
$stmt = $pdo->query($sql);
$results = $stmt->fetchAll();
foreach ($results as $row){
    echo $row['id'].',';
    echo $row['name'].',';
    echo $row['comment'].',';
    echo $row['time'].'<br>';
    echo "<hr>";
}
?>
</body>
</html>