<?php
require_once("util.php");
// セッションの開始
session_start();
?>

 <?php
 // 文字エンコードの検証
 if (!cken($_POST)){
   $encoding = mb_internal_encoding();
   $err = "Encoding Error! The expected encoding is " . $encoding ;
   // エラーメッセージを出して、以下のコードをすべてキャンセルする
   exit($err);
 }
 ?>

 <?php
 // POSTされた値をセッション変数に受け渡す
 if (isset($_POST['shimeicd'])){
   $_SESSION['shimeicd'] = $_POST['shimeicd'];
 }
 // 入力データの取り出しとチェック
 $error = [];
 // 名前
 if (empty($_SESSION['shimeicd'])){
   // 未設定のときエラー
   $error[] = "名前を入力してください。";
 } else {
   // 名前を取り出す
   $shimeicd = trim($_SESSION['shimeicd']);
 }
  ?>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>運転日報（旧画面）</title>
    <!--論理ウィンドウサイズを設定(文字サイズをデバイスに合わせる)-->
    <meta name="viewport" content="width=device-width">
    <!--ホーム画面から開くと全画面表示になる-->
    <meta name="apple-mobile-web-app-capable" content="yes">
    <!--電話番号の自動検出をオフにする-->
    <meta name="format-detection" content="telephone=no">
    <!--favicon・アイコン設定-->
    <link rel="shortcut icon" href="http://aikidomasata.html.xdomain.jp/apple-touch-icon.png">
    <link rel="apple-touch-icon" href="http://aikidomasata.html.xdomain.jp/apple-touch-icon.png">
    <!--アプリアイコン時の表示名-->
    <meta name="apple-mobile-web-app-title" content="運転日報">
    <!--CSS書式リンク設定-->
    <link rel="stylesheet" href="input.css">
    <link rel="stylesheet" href="button.css">
  </head>
  <body>
    <div class="test">
    <p>＜追加登録＞<?php echo "CD:{$shimeicd}" ?></p>
    <form action="add_home.php" method="post">
      <input class="enter" type="submit" value="出社">
    </form>
    <form action="add_park.php" method="post">
      <input class="enter" type="submit" value="到着">
    </form>
    <form action="add_go.php" method="post">
      <input class="enter" type="submit" value="出発">
    </form>
    <form action="add_return.php" method="post">
      <input class="enter" type="submit" value="帰宅">
    </form>
    <form action="add_oil.php" method="post">
      <input class="enter" type="submit" value="給油">
    </form>
    <form action="add_private.php" method="post">
      <input class="enter" type="submit" value="私用">
    </form>

    <form name="form" method="post">
    <input class="button" type="button" value="戻る" onclick="location.href='entrance.php'">
    </form>

  </div>
  </body>
</html>
