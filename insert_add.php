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
<title>運転日報｜訂正登録完了</title>
<meta name="viewport" content="width=device-width">
<link href="style.css" rel="stylesheet">
<link href="tablestyle.css" rel="stylesheet">
<link rel="stylesheet" href="input.css">
<link rel="stylesheet" href="button.css">
</head>
<body>
<div class="test">
  <?php
  echo "下記の通り登録しました。";
  $id = $_POST["id"];
  $date = $_POST["date"];
  $time = $_POST["time"];
  $item = $_POST["item"];
  $place = $_POST["place"];
  $odometer = $_POST["odometer"];
  $highway_use = $_POST["highway_use"];
  $highway_fee = $_POST["highway_fee"];
  $park_use = $_POST["park_use"];
  $park_fee = $_POST["park_fee"];
  $oil_pay = $_POST["oil_pay"];
  $oil_quantity = $_POST["oil_quantity"];
  $oil_fee = $_POST["oil_fee"];
  $day_over = $_POST["day_over"];
  if (empty($_POST['dateOriginal'])) {
    $dateOriginal = $_POST["date"];
  }else {
    $dateOriginal = $_POST["dateOriginal"];
  }
  //dateOriginalの分岐は旧画面と新画面でのエラー回避のため

//元のデータを削除
  try {
    $pdo = new PDO($dsn, $user, $password);
    // プリペアドステートメントのエミュレーションを無効にする
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    // 例外がスローされる設定にする
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // SQL文を作る
    $sql = "DELETE FROM driverecord WHERE id = $id";
    // プリペアドステートメントを作る
    $stm = $pdo->prepare($sql);
    // プレースホルダに値をバインドする
    // SQL文を実行する
    $stm->execute();

  } catch (Exception $e) {
    echo '<span class="error">エラーがありました。</span><br>';
    echo $e->getMessage();
    exit();
    }

  //訂正データを登録
  try {
    $pdo = new PDO($dsn, $user, $password);
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    //修正したデータを挿入
    $sql = "INSERT INTO driverecord (shimeicd, date, time, item, odometer, highway_use, highway_fee, park_use, park_fee, oil_pay, oil_quantity, oil_fee, day_over, place) VALUES (:shimeicd, :date, :time, :item, :odometer, :highway_use, :highway_fee, :park_use, :park_fee, :oil_pay, :oil_quantity, :oil_fee, :day_over, :place)";
    // プリペアドステートメントを作る
    $stm = $pdo->prepare($sql);
    // プレースホルダに値をバインドする
    $stm->bindValue(':shimeicd', $shimeicd, PDO::PARAM_STR);
    $stm->bindValue(':date', $date, PDO::PARAM_STR);
    $stm->bindValue(':time', $time, PDO::PARAM_STR);
    $stm->bindValue(':item', $item, PDO::PARAM_STR);
    $stm->bindValue(':odometer', $odometer, PDO::PARAM_STR);
    $stm->bindValue(':highway_use', $highway_use, PDO::PARAM_STR);
    $stm->bindValue(':highway_fee', $highway_fee, PDO::PARAM_STR);
    $stm->bindValue(':park_use', $park_use, PDO::PARAM_STR);
    $stm->bindValue(':park_fee', $park_fee, PDO::PARAM_STR);
    $stm->bindValue(':oil_pay', $oil_pay, PDO::PARAM_STR);
    $stm->bindValue(':oil_quantity', $oil_quantity, PDO::PARAM_STR);
    $stm->bindValue(':oil_fee', $oil_fee, PDO::PARAM_STR);
    $stm->bindValue(':day_over', $day_over, PDO::PARAM_STR);
    $stm->bindValue(':place', $place, PDO::PARAM_STR);

    // SQL文を実行する
    if ($stm->execute()){
      // レコード追加後のレコードリストを取得する
      $sql = "SELECT * FROM driverecord ORDER BY id DESC LIMIT 1";      // プリペアドステートメントを作る
      $stm = $pdo->prepare($sql);
      // SQL文を実行する
      $stm->execute();
      // 結果の取得（連想配列で受け取る）
      $result = $stm->fetchAll(PDO::FETCH_ASSOC);
      foreach ($result as $row) {
      echo "<br><li>項目：", es($row['item']);
      echo "<li>日付：", es($row['date']);
      echo "<li>時間：", es($row['time']);
      if (isset($row['day_over'])) {
      echo "<li>日付変更：", es($row['day_over']);
      }
      if ($row['odometer']>0) {
          echo "<li>距離：", es($row['odometer']),'km';
      }
      if (empty($row['place'])) {
      }else {
      echo "<li>場所：", es($row['place']);
      }
      if (isset($row['park_use'])) {
      echo "<li>駐車場利用：", es($row['park_use']);
      }
      if ($row['park_fee']>0) {
      echo "<li>駐車場料金：", es($row['park_fee']),'円';
      }
      if (es($row['oil_quantity'])>0) {
      echo "<li>給油量：", es($row['oil_quantity']),'L';
      }
      if (isset($row['oil_pay'])) {
      echo "<li>支払方法：", es($row['oil_pay']);
      }
      if (es($row['oil_fee'])>0) {
      echo "<li>給油料金：", es($row['oil_fee']),'円';
      }
      if (isset($row['highway_use'])) {
      echo "<li>高速利用：", es($row['highway_use']);
      }
      if ((es($row['highway_fee']))>0) {
      echo "<li>高速料金：", es($row['highway_fee']),'円';
      }
      }
    } else {
      echo '<span class="error">エラーがありました。</span><br>';
    };
  } catch (Exception $e) {
    echo '<span class="error">エラーがありました。</span><br>';
    echo $e->getMessage();
  }
  ?>
  <form action="entrance.php" method="post">
  <input type="hidden" name="date" value="<?php echo $dateOriginal ?>">
  <input class="button" type="submit" value="ﾄｯﾌﾟ">
  </form>

  <!--バックスペースで戻れないようにする-->
  <script>
  window.location.hash="no-back";
  window.location.hash="no-back-button";
  window.onhashchange=function(){
     window.location.hash="no-back";
  }
  </script>

</div>
</body>
</html>
