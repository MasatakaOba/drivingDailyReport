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
    <title>運転日報｜到着(追加)</title>
    <meta name="viewport" content="width=device-width">
    <!--ホーム画面から開くと全画面表示になる-->
    <meta name="apple-mobile-web-app-capable" content="yes">
    <!--電話番号の自動検出をオフにする-->
    <meta name="format-detection" content="telephone=no">
    <!--favicon・アイコン設定-->
    <link rel="shortcut icon" sizes="196x196" href="https://oshigotokaizen.club/driverecord/favicon-196x196.png">
    <link rel="apple-touch-icon" href="https://oshigotokaizen.club/driverecord/apple-touch-icon-57x57.png" />

    <link rel="stylesheet" href="input.css">
    <link rel="stylesheet" href="button.css">
  </head>
  <body>
    <div class="test">
    <p>＜到着登録＞<?php echo "CD:{$shimeicd}" ?></p>
    <form class="" name="form" action="insert_park.php" method="post">
      <!---データ送信用-->
      <input type="hidden" name="item" value="到着">
      <input type="hidden" id="latitude" name="latitude">
      <input type="hidden" id="longitude" name="longitude">
      <!--表示用-->
      <li><label>日　　付：<input type="date" name="date" value=<?php echo $_SESSION["date"];?> required="required" onkeyup="zentohan(this)"></label></li>
      <li><label>時　　刻：<input type="time" name="time" value=<?php echo date('H:i');?> required="required" onkeyup="zentohan(this)"></label></li>
      <li><label>日付変更：
        <input type="radio" name="day_over" value="なし" checked="checked" required="required">なし</label>
        <label><input type="radio" name="day_over" value="あり" required="required">あり</label>
      <li><label>メーター：<input type="number" name="odometer" pattern="[0-9]*">km</label></li>
      <span style="font-size: 0.8em; color:red;">＊出勤・退勤時以外は距離の入力は不要です。</span>
      <li><label>到着場所：<input type="text" name="place" required="required"></label></li>
      <li><label>高速道路利用：
        <br><input type="radio" name="highway_use" value="利用なし" onclick="hihyoji2();" checked="checked" required="required">利用なし</label>
        <br><label><input type="radio" name="highway_use" value="ETC" onclick="hihyoji2();" required="required">利用あり(専用ETC)</label>
        <br><label><input type="radio" name="highway_use" value="現金" required="required" onclick="hyoji2();">利用あり(現金)</label>
      <li id="highway_fee"><label>高速道路利用金額：<input type="number" name="highway_fee" value="0" pattern="[0-9]*" required="required">円</label></li>

      <input class="button" type="submit" value="確認" onclick="return checkForm();">
      <input class="button" type="button" value="戻る" onclick="window.history.back();">
    </form>

    <!--追加-->
    <!--全角を半角に変更-->
    <script type="text/javascript" src="zehToHan.js">
    </script>

    <!--ラジオボタンを選択して表示・非表示を切り替えるJS-->
    <script>
    function hyoji2() {
        document.getElementById("highway_fee").style.display="block";
    }
    function hihyoji2() {
        document.getElementById("highway_fee").style.display="none";
    }
    window.onload = hyoji2();
    window.onload = hihyoji2();
    </script>

    </div>
  </body>
</html>
