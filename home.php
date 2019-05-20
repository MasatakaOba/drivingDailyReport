<?php
 if (isset($_COOKIE['odometer'])){
    $odometer = $_COOKIE['odometer'];
}else{
    $odometer = '';
}
// Cookieが存在する場合は$odometerに代入し、なければ空欄として判断
?>

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
    <title>運転日報_出社</title>
    <meta charset="utf-8">
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
    <p>＜出社登録＞<?php echo "CD:{$shimeicd}" ?></p>

      <form class="" name="form" action="insert_home.php" onsubmit="return checkForm();" method="post">
      <!---データ送信用-->
      <input type="hidden" name="item" value="出社">
      <input type="hidden" name="date" value=<?php echo date('Y-m-d');?>>
      <input type="hidden" name="time" value=<?php echo date('H:i:s');?>>
      <input type="hidden" id="latitude" name="latitude">
      <input type="hidden" id="longitude" name="longitude">
      <!---入力・表示用-->
      <li><label>日　　付：<?php echo date('Y年m月d日');?> </label></li>
      <li><label>現在時刻：<?php echo date('H時i分');?></label></li>
      <li><label>メーター：<input type="number" name="odometer" value="<?php echo $odometer;?>" pattern="[0-9]*" required="required">km</label></li>
      <li><label>出発場所：<input type="text" name="place" value="自宅" required="required"></label></li>
      <li>駐車場利用：
      <br><label><input type="radio" name="park_use" value="利用なし" required="required" onclick="checkradio('none');" checked="checked">自宅・無料駐車場</label>
      <br><label><input type="radio" name="park_use" value="タイムズ" required="required" onclick="checkradio('inline');">タイムズ（専用カード利用）</label>
      <br><label><input type="radio" name="park_use" value="リパーク"　required="required" onclick="checkradio('inline');">リパーク（専用カード利用）</label>
      <br><label><input type="radio" name="park_use" value="その他駐車場"　required="required" onclick="checkradio('inline');">その他駐車場</label>
      </li>
      <li id="hyouji"><label>駐車金額：<input type="number" name="park_fee" value="0" pattern="[0-9]*" required="required">円</label></li>

      <!--Geolocationのボタン導入-->
      <li><input type="button" value="現在地登録" onclick="geoFindMe()"></li>
      <div id="out"></div>

      <input class="button" type="submit" value="確認">
      <input class="button" type="button" value="戻る" onclick="location.href='entrance.php'">
    </form>

    <!--ラジオボタンを選択して表示・非表示を切り替えるJS-->
    <script type="text/javascript">
    function checkradio( disp ) {
	  document.getElementById('hyouji').style.display = disp;
    }
    window.onload = checkradio('none');
    </script>

    <!--バックスペースで戻れないようにする-->
    <script>
    window.location.hash="no-back";
    window.location.hash="no-back-button";
    window.onhashchange=function(){
       window.location.hash="no-back";
    }
    </script>

    <!--入力フォームが空欄の時アラート-->
    <script type="text/javascript" src="inputForm_home.js">
    </script>

    <!--位置情報の表示-->
    <script type="text/javascript" src="geolocation.js">
    </script>

    </div>
  </body>
</html>
