<?php
require_once("util.php");
require "mysql_login.php";
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
<title>運転日報｜訂正</title>
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
<link href="tablestyle.css" rel="stylesheet">
<link href="tablestyle_kaiten.css" rel="stylesheet">
<link rel="stylesheet" href="input.css">
<link rel="stylesheet" href="button.css">
</head>
<body>
<div class="test">
  <p>＜訂正画面＞<?php echo "CD:{$shimeicd}" ?></p>
  <!--<h6>必要な部分のみ変更ください。</h6>-->

  <!-- 入力フォーム -->
  <form method="POST" name="form" action="insert_add.php">
  <!---データ送信用-->
  <?php
  $dateOriginal = $_POST["dateOriginal"];//元のページに戻るための引き継ぎ用
  $id = $_POST["id"];?>
  <input type="hidden" name="shimeicd" value=<?php echo $shimeicd?>>
  <input type="hidden" name="id" value=<?php echo $id ?>>
  <input type="hidden" name="dateOriginal" value=<?php echo $dateOriginal ?>>
  <?php

  //MySQLデータベースに接続する
  try {
    $pdo = new PDO($dsn, $user, $password);
    // プリペアドステートメントのエミュレーションを無効にする
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    // 例外がスローされる設定にする
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // SQL文を作る
    $sql = "SELECT * FROM driverecord WHERE id = $id";
    // プリペアドステートメントを作る
    $stm = $pdo->prepare($sql);
    // プレースホルダに値をバインドする
    $stm->bindValue(':id', $id, PDO::PARAM_STR);
    // SQL文を実行する
    $stm->execute();
    // 結果の取得（連想配列で受け取る）
    $result = $stm->fetchAll(PDO::FETCH_ASSOC);
      if(count($result)>0){
      foreach ($result as $row) {
      echo
      '<label><input type="hidden" name="item" value="',es($row['item']),'" required="required"></label></li>',
      '<label><input type="hidden" name="id" value="',es($row['id']),'" required="required"></label></li>';
      echo
      '<label><input type="hidden" id="item" name="item" value="',es($row['item']),'" required="required"></label></li>';
      echo '<li>項　　目：',es($row['item']),'</li>';
      echo "<li>日　　付：", '<input type="date" name="date" value="',es($row['date']),'">', '</li>';
      echo "<li>時　　間：", '<input type="time" name="time" value="',es($row['time']),'">', '</li>';
      //日付変更の条件分岐
      switch(es($row['day_over'])){
        case "なし":
        $checked6 = 'checked="checked"';
        break;
        case "あり":
        $checked7 = 'checked="checked"';
      }
      echo "<li id='day_over'>日付変更：",
        '<label><input type="radio" name="day_over" value="なし"',$checked6,'>なし</label>',
        '<label><input type="radio" name="day_over" value="あり" ',$checked7,'>あり</label>','</li>';
      echo '<li id="odometer"><label>メーター：', '<input type="number" name="odometer" value="',es($row['odometer']), '" pattern="[0-9]*">km</label></li>';
      echo '<li id="place">場　　所：', '<input type="text" name="place" value="',es($row['place']), '">','</li>';
      //駐車場利用の分岐条件
      switch(es($row['park_use'])){
        case "利用なし":
        $checked8 = 'checked="checked"';
        break;
        case "タイムズ":
        $checked9 = 'checked="checked"';
        break;
        case "リパーク":
        $checked10 = 'checked="checked"';
        break;
        case "その他駐車場":
        $checked11 = 'checked="checked"';
        break;
        default:
        $checked8 = 'checked="checked"';
      }
      echo
      ' <li id="park_use">駐車場利用：
        <br><label><input type="radio" name="park_use" value="利用なし" onclick="hihyoji1();"',$checked8,'>店舗・無料駐車場</label>
        <br><label><input type="radio" name="park_use" value="タイムズ" onclick="hyoji1();"',$checked9,'>タイムズ（専用カード利用）</label>
        <br><label><input type="radio" name="park_use" value="リパーク" onclick="hyoji1();"',$checked10,'>リパーク（専用カード利用） </label>
        <br><label><input type="radio" name="park_use" value="その他駐車場" onclick="hyoji1();"',$checked11,'>その他駐車場</label></li>';
      echo '<li id="park_fee"><label>駐車場料金：', '<input type="text" name="park_fee" value="',es($row['park_fee']), '" pattern="[0-9]*">','</label></li>';
      //高速利用の分岐条件
      switch(es($row['highway_use'])){
        case "利用なし":
        $checked12 = 'checked="checked"';
        break;
        case "ETC":
        $checked13 = 'checked="checked"';
        break;
        case "立て替え":
        $checked14 = 'checked="checked"';
      }
      echo '<li id="highway_use"><label>高速道路利用：',
        '<br><input type="radio" name="highway_use" value="利用なし" onclick="hihyoji2()"',$checked12,'>利用なし</label>',
        '<br><label><input type="radio" name="highway_use" value="ETC" onclick="hihyoji2();"',$checked13,'>利用あり(専用ETC)</label>',
        '<br><label><input type="radio" name="highway_use" value="立て替え" onclick="hyoji2();"',$checked14,'>利用あり(現金)</label>';
      echo '<li id="highway_fee"><label>高速料金：<input type="number" name="highway_fee" value="',es($row['highway_fee']),'" pattern="[0-9]*">円</label></li>';
      echo '<li id="oil_quantity"><label>給 油 量：<input type="number" name="oil_quantity" step="0.01"  value="',es($row['oil_quantity']),'">L</label></li>';
      //駐車料金支払の分岐条件
      switch(es($row['oil_pay'])){
        case "専用カード":
        $checked15 = 'checked="checked"';
        break;
        case "立て替え":
        $checked16 = 'checked="checked"';
      }
      echo '<li id="oil_pay"><label>支払方法：
          <br><input type="radio" name="oil_pay" value="専用カード" onclick="hihyoji3();"',$checked15,'>専用カード</label>
          <br><label><input type="radio" name="oil_pay" value="立て替え" onclick="hyoji3();"',$checked16,'>立て替え</label></li>
          <li id="oil_fee"><label>給油料金：<input type="number" name="oil_fee" pattern="[0-9]*" value="',es($row['oil_fee']),'">円</label></li>';

      }
    } else {
      echo '<span class="error">エラーがありました。</span><br>';
    };
  } catch (Exception $e) {
    echo '<span class="error">エラーがありました。</span><br>';
    echo $e->getMessage();
  }
  ?>

  <li><input class="button" type="submit" value="確認">
  <input class="button" type="button" value="戻る" onclick="window.history.back();">
  </form>

  <form class="button" action="revise_delete.php" method="post">
    <input type="hidden" name="shimeicd" value=<?php echo $shimeicd ?>>
    <input type="hidden" name="id" value=<?php echo $id ?>>
    <input type="hidden" name="dateOriginal" value=<?php echo $dateOriginal ?>>
    <li><input class="button" type="submit" value="削除">
  </form>

  <!--ラジオボタンを選択して表示・非表示を切り替えるJS(駐車料金)-->
  <script>
  function hyoji1() {
      document.getElementById("park_fee").style.display="block";
  }
  function hihyoji1() {
      document.getElementById("park_fee").style.display="none";
  }
  window.onload = hyoji1();
  window.onload = hihyoji1();
  </script>

  <!--ラジオボタンを選択して表示・非表示を切り替えるJS(高速用)-->
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

  <!--ラジオボタンを選択して表示・非表示を切り替えるJS(給油用)-->
  <script type="text/javascript">
  function hyoji3() {
  document.getElementById('oil_fee').style.display = "block";
  }
  function hihyoji3() {
  document.getElementById('oil_fee').style.display = "none";
  }
  window.onload = hyoji3();
  window.onload = hihyoji3();
  </script>

  <!--ラジオボタンを選択して表示・非表示を切り替えるJS(項目別)-->
  <script type="text/javascript">

  //出社時
  function shussya() {
  document.getElementById('day_over').style.display = "none";//日付変更
  document.getElementById('odometer').style.display = "block";//メーター
  document.getElementById('place').style.display = "block";//場所
  document.getElementById('park_use').style.display = "block";//駐車場利用
  document.getElementById('park_fee').style.display = "block";//駐車場費用
  document.getElementById('highway_use').style.display = "none";//高速利用
  document.getElementById('highway_fee').style.display = "none";//高速料金
  document.getElementById('oil_quantity').style.display = "none";//給油量
  document.getElementById('oil_pay').style.display = "none";//給油量
  document.getElementById('oil_fee').style.display = "none";//給油料金
  }

  //到着時
  function tochaku() {
  document.getElementById('odometer').style.display = "block";//メーター
  document.getElementById('place').style.display = "block";//場所
  document.getElementById('park_use').style.display = "block";//駐車場利用
  document.getElementById('park_fee').style.display = "block";//駐車場費用
  document.getElementById('highway_use').style.display = "block";//高速利用
  document.getElementById('highway_fee').style.display = "block";//高速料金
  document.getElementById('oil_quantity').style.display = "none";//給油量
  document.getElementById('oil_pay').style.display = "none";//給油量
  document.getElementById('oil_fee').style.display = "none";//給油料金
  }

  //出発時
  function shuppatsu() {
  document.getElementById('odometer').style.display = "none";//メーター
  document.getElementById('place').style.display = "none";//場所
  document.getElementById('park_use').style.display = "block";//駐車場利用
  document.getElementById('park_fee').style.display = "block";//駐車場費用
  document.getElementById('highway_use').style.display = "none";//高速利用
  document.getElementById('highway_fee').style.display = "none";//高速料金
  document.getElementById('oil_quantity').style.display = "none";//給油量
  document.getElementById('oil_pay').style.display = "none";//給油量
  document.getElementById('oil_fee').style.display = "none";//給油料金
  }

  //帰宅時
  function kitaku() {
  document.getElementById('odometer').style.display = "block";//メーター
  document.getElementById('place').style.display = "block";//場所
  document.getElementById('park_use').style.display = "none";//駐車場利用
  document.getElementById('park_fee').style.display = "none";//駐車場費用
  document.getElementById('highway_use').style.display = "block";//高速利用
  document.getElementById('highway_fee').style.display = "block";//高速料金
  document.getElementById('oil_quantity').style.display = "none";//給油量
  document.getElementById('oil_pay').style.display = "none";//給油量
  document.getElementById('oil_fee').style.display = "none";//給油料金
  }

  //給油時
  function kyuyu() {
  document.getElementById('odometer').style.display = "none";//メーター
  document.getElementById('place').style.display = "none";//場所
  document.getElementById('park_use').style.display = "none";//駐車場利用
  document.getElementById('park_fee').style.display = "none";//駐車場費用
  document.getElementById('highway_use').style.display = "none";//高速利用
  document.getElementById('highway_fee').style.display = "none";//高速料金
  document.getElementById('oil_quantity').style.display = "block";//給油量
  document.getElementById('oil_pay').style.display = "block";//給油量
  document.getElementById('oil_fee').style.display = "block";//給油料金
}

//私用利用
function shiyou() {
document.getElementById('day_over').style.display = "none";//日付変更
document.getElementById('odometer').style.display = "block";//メーター
document.getElementById('place').style.display = "none";//場所
document.getElementById('park_use').style.display = "none";//駐車場利用
document.getElementById('park_fee').style.display = "none";//駐車場費用
document.getElementById('highway_use').style.display = "none";//高速利用
document.getElementById('highway_fee').style.display = "none";//高速料金
document.getElementById('oil_quantity').style.display = "none";//給油量
document.getElementById('oil_pay').style.display = "none";//給油量
document.getElementById('oil_fee').style.display = "none";//給油料金
}

  var item = document.getElementById('item').value
  switch (item) {
    case '出社':
      shussya();
      break;
    case '到着':
      tochaku();
      break;
    case '出発':
      shuppatsu();
      break;
    case '帰宅':
      kitaku();
      break;
    case '給油':
      kyuyu();
      break;
    case '私用':
      shiyou();
      break;
  }
  </script>

</div>
</body>
</html>
