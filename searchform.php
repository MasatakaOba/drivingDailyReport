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
<title>運転日報｜検索フォーム</title>
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
  <p>＜検索画面＞<?php echo "CD:{$shimeicd}" ?></p>
  <h5>※この画面はパソコンでの確認を推奨しております。</h5>
  <!-- 入力フォーム -->
  <form method="POST" action="search.php">
    <input type="hidden" name="shimeicd" value=<?php echo $shimeicd ?>>
    <li><label>年月：<input type="month" name="date" value=<?php echo date('Y-m');?>></label></li>
    <li><input class="button" type="submit" value="検索">
    <input class="button" type="button" value="戻る" onclick="window.history.back();"></li>
  </form>
</div>
</body>
</html>
