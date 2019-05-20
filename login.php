<?php
 if (isset($_COOKIE['shimeicd'])){
    $shimeicd = $_COOKIE['shimeicd'];
}else{
    $shimeicd = '';
}
// Cookieが存在する場合は$shimeicdに代入し、なければ空欄として判断
?>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>運転日報</title>
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
  </head>
  <body>
    <div class="test">
      <img src="login_image.jpg" alt="運転日報WEB版" align="left"><br><br>
      <form action="setCookie.php" name="form" onsubmit="return checkForm();" method="post">
        <li><label></label><input type="hidden" name="date" value="<?php echo date('Y-m-d'); ?>">
        <li><label><br>氏名CD：<!--onkeyup="zentohan(this)"を追加-->
        <br><input name="shimeicd" type="number" pattern="[0-9]*" onkeyup="zentohan(this)" placeholder="7桁の数字を入力" value="<?php echo $shimeicd; ?>" required="required"></label></li>
        <li><input class="button" type="submit" value="確認"></li>
      </form>
    </div>

<!--追加-->
<!--全角を半角に変更-->
<script type="text/javascript" src="zenToHan.js">
</script>

  </body>
</html>
