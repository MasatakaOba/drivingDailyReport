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
   $error[] = "最初の画面に戻って氏名CDを入力してください";
 } else {
   // 氏名CDを取り出す
   $shimeicd = trim($_SESSION['shimeicd']);
 }
 ?>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>運転日報_私用走行</title>
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
    <p>＜私用走行＞<?php echo "CD:{$shimeicd}" ?></p>
    <h6>※注意：私用走行終了時に記入下さい。<br>
      走行距離は私用走行の登録の前の距離から引かれて算出されます。</h6>
      <!---データ送信用-->
    <form name="form" action="insert_private.php" method="post">

      <li><input type="hidden" name="item" value="私用"></li>
      <li><input type="hidden" name="date" value=<?php echo date('Y-m-d');?>></li>
      <li><input type="hidden" name="time" value=<?php echo date('H:i:s');?>></li>
      <li><label>日　　付：<?php echo date('Y年m月d日');?> </label></li>
      <li><label>現在時刻：<?php echo date('H時i分');?></label>
      <li><label>メーター：<input type="number" name="odometer" pattern="[0-9]*" placeholder="私用走行終了時の距離" required="required">km</label></li>
      <input class="button" type="submit" value="確認" onclick="return checkForm();">
      <input class="button" type="button" value="戻る" onclick="location.href='entrance.php'">
    </form>

      <!--バックスペースで戻れないようにする-->
      <script>
      window.location.hash="no-back";
      window.location.hash="no-back-button";
      window.onhashchange=function(){
      window.location.hash="no-back";
      }
      </script>

      <!--入力フォームが空欄の時アラート-->
      <script type="text/javascript" src="inputForm_private.js">
      </script>

    </div>
  </body>
</html>
