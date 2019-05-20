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
    <title>運転日報_給油</title>
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
    <p>＜給油登録＞<?php echo "CD:{$shimeicd}" ?></p>
    <h6>※注意：運転中は登録せず、車を停めるか、降りてから登録を実施下さい。</h6>
    <form action="insert_oil.php" name="form" method="post">
      <input type="hidden" name="item" value="給油">
      <input type="hidden" name="date" value=<?php echo date('Y-m-d');?>>
      <input type="hidden" name="time" value=<?php echo date('H:i:s');?>>
      <li><label>日　　付：<?php echo date('Y年m月d日');?> </label></li>
      <li><label>現在時刻：<?php echo date('H時i分');?></label>
      <li><label>日付変更：
        <?php //時間帯によって日付変更のチェックの場所を変更する
        date_default_timezone_set('Asia/Tokyo');//東京にタイムゾーンを設置
        $checkTime = strtotime(date('H:i'));//現在時刻
        if ($checkTime > strtotime('04:00') && $checkTime < strtotime('24:00'))//04〜24時まで
        {
        $check_nashi = 'checked="checked"';//なしにチェック
      }else {
        $check_ari = 'checked="checked"';//ありにチェック
      }
        ?>
        <input type="radio" name="day_over" value="なし" <?php echo $check_nashi;//4-24時 ?> required="required">なし</label>
        <label><input type="radio" name="day_over" value="あり" <?php echo $check_ari;//0-4時 ?> required="required">あり</label>
        <li><label>給 油 量：<br><input type="number" name="oil_quantity" step="0.01" required="required">L</label></li>
        <li><label>支払方法：
        <br><input type="radio" name="oil_pay" value="専用カード" required="required" checked="checked" onclick="checkradio('none');">専用カード</label>
        <br><label><input type="radio" name="oil_pay" value="立て替え" required="required" onclick="checkradio('inline');">立て替え</label></li>
        <li id="hyoji"><label>給油料金：<input type="number" name="oil_fee" value="0" pattern="[0-9]*" required="required">円</label></li>
      <input class="button" type="submit" value="確認" onclick="return checkForm();">
      <input class="button" type="button" value="戻る" onclick="location.href='entrance.php'">
    </form>

    <!--ラジオボタンを選択して表示・非表示を切り替えるJS-->
    <script type="text/javascript">
    function checkradio( disp ) {
	  document.getElementById('hyoji').style.display = disp;
    }
    window.onload = checkradio('none');
    </script>

    <!--バックスペースで戻れないようにする-->
    <script>
    window.location.hash="no-back";
    window.location.hash="no-back-button";
    window.onhashchange=function(){
       window.location.hash="no-back";}
    </script>

    <script type="text/javascript" src="inputForm_oil.js">
    </script>

    </div>
  </body>
</html>
