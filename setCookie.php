<?php
 //secure属性
 $secure = true;
 $httponly = true;
 // POSTされてきたCookie値
 $shimeicd = $_POST["shimeicd"];
 // Cookieに値を保存する
 setcookie("shimeicd", $shimeicd, time()+60*60*24*30);
 //Cookieの有効期限は60*60*24*30秒=30日で設定
?>

<?php
require_once("util.php");
// セッションの開始
session_start();
?>

<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="utf-8">
  <title>運転日報｜ログイン</title>
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
<form class="form" action="entrance.php" method="post">
  <input type="hidden" name="shimeicd" value="<?php echo $shimeicd;?>">
  <input id="button" type="submit" value="-">
</form>

<!--追加-->
<!--運転中使用しないようにアラート-->
<script type="text/javascript">
window.onload = onLoad;
function onLoad(){
     myRet = confirm("運転中ではありませんか？\n（運転中の携帯電話の使用は法律で禁じられています。）");
     if ( myRet == false ){
         alert("車を停車し、安全を確認してから記録してください。");
         document.location.href = 'login.php';
     }else if  (window.navigator.standalone == false) {
        document.getElementById("button").click();
        alert("「運転日報」はホーム画面に追加して使用すると見やすくなります。");
     }
     else{
        document.getElementById("button").click();
     }
}
</script>

</body>
</html>
