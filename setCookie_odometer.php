<?php
 //secure属性
 $secure = true;
 $httponly = true;
 // POSTされてきたCookie値
 $odometer = $_POST["odometer"];
 // Cookieに値を保存する
 setcookie("odometer", $odometer, time()+60*60*24*30);
 //Cookieの有効期限は60*60*24*30秒=30日で設定
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
<html lang="ja">
<head>
  <meta charset="utf-8">
  <title>運転日報WEB版</title>
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
  <link rel="stylesheet" href="balloon.css">
  <!--上記title以下の設定はあまりいらないかもしれません…-->
</head>
<body>

  <?php
  //データを前画面から引き継ぎ
  $shimeicd = $_SESSION["shimeicd"];
  $date = $_POST["date"];
  $time = $_POST["time"];
  $item = $_POST["item"];
  $place = $_POST["place"];
  $odometer = $_POST["odometer"];
  $highway_use = $_POST["highway_use"];
  $highway_fee = $_POST["highway_fee"];
  $latitude = $_POST["latitude"];
  $longitude = $_POST["longitude"];
  $day_over = $_POST["day_over"];
  ?>

<form class="form" action="insert_return.php" method="post">
  <input type="hidden" name="shimeicd" value="<?php echo $shimeicd;?>">
  <input type="hidden" name="item" value="<?php echo $item;?>">
  <input type="hidden" name="date" value="<?php echo $date;?>">
  <input type="hidden" name="time" value="<?php echo $time;?>">
  <input type="hidden" name="latitude" value="<?php echo $latitude;?>">
  <input type="hidden" name="longitude" value="<?php echo $longitude;?>">
  <input type="hidden" name="day_over" value="<?php echo $day_over;?>">
  <input type="hidden" name="odometer" value="<?php echo $odometer;?>">
  <input type="hidden" name="place" value="<?php echo $place;?>">
  <input type="hidden" name="highway_use" value="<?php echo $highway_use;?>">
  <input type="hidden" name="highway_fee" value="<?php echo $highway_fee;?>">
  <input id="button" type="submit" value="次へ">
</form>

<script type="text/javascript">
  // 指定IDのsubmitボタンをクリックしてentrance.phpに即遷移
 document.getElementById("button").click();
 </script>
</body>
</html>
