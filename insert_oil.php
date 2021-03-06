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
<title>運転日報｜給油登録(追加)</title>
<meta name="viewport" content="width=device-width">
<!--ホーム画面から開くと全画面表示になる-->
<meta name="apple-mobile-web-app-capable" content="yes">
<!--電話番号の自動検出をオフにする-->
<meta name="format-detection" content="telephone=no">
<!--favicon・アイコン設定-->
<link rel="shortcut icon" sizes="196x196" href="https://oshigotokaizen.club/driverecord/favicon-196x196.png">
<link rel="apple-touch-icon" href="https://oshigotokaizen.club/driverecord/apple-touch-icon-57x57.png" />
<link href="style.css" rel="stylesheet">
<link href="tablestyle.css" rel="stylesheet">
<link href="tablestyle_kaiten.css" rel="stylesheet">
<link rel="stylesheet" href="input.css">
<link rel="stylesheet" href="button.css">
</head>
<body>
<div class="test">
  <?php
  echo "下記の通り登録しました。";
  $date = $_POST["date"];
  $time = $_POST["time"];
  $item = $_POST["item"];
  $oil_quantity = $_POST["oil_quantity"];
  $oil_pay = $_POST["oil_pay"];
  $oil_fee = $_POST["oil_fee"];
  $day_over = $_POST["day_over"];

  //MySQLデータベースに接続する
  try {
    $pdo = new PDO($dsn, $user, $password);
    // プリペアドステートメントのエミュレーションを無効にする
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    // 例外がスローされる設定にする
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // SQL文を作る
    $sql = "INSERT INTO driverecord (shimeicd, date, time, item, oil_quantity, oil_pay, oil_fee, day_over) VALUES (:shimeicd, :date, :time, :item, :oil_quantity, :oil_pay, :oil_fee, :day_over)";
    // プリペアドステートメントを作る
    $stm = $pdo->prepare($sql);
    // プレースホルダに値をバインドする
    $stm->bindValue(':shimeicd', $shimeicd, PDO::PARAM_STR);
    $stm->bindValue(':date', $date, PDO::PARAM_STR);
    $stm->bindValue(':time', $time, PDO::PARAM_STR);
    $stm->bindValue(':item', $item, PDO::PARAM_STR);
    $stm->bindValue(':oil_quantity', $oil_quantity, PDO::PARAM_STR);
    $stm->bindValue(':oil_pay', $oil_pay, PDO::PARAM_STR);
    $stm->bindValue(':oil_fee', $oil_fee, PDO::PARAM_STR);
    $stm->bindValue(':day_over', $day_over, PDO::PARAM_STR);

    // SQL文を実行する
    if ($stm->execute()){
      // レコード追加後のレコードリストを取得する
      $sql = "SELECT * FROM driverecord ORDER BY id DESC LIMIT 1";      // プリペアドステートメントを作る
      // プリペアドステートメントを作る
      $stm = $pdo->prepare($sql);
      // SQL文を実行する
      $stm->execute();
      // 結果の取得（連想配列で受け取る）
      $result = $stm->fetchAll(PDO::FETCH_ASSOC);
      // テーブルのタイトル行
      echo "<table>";
      echo "<thead><tr>";
      echo "<th>", "日付", "</th>";
      echo "<th>", "時間", "</th>";
      echo "<th>", "項目", "</th>";
      echo "<th>", "給油量", "</th>";
      echo "<th>", "支払い方法", "</th>";
      echo "<th>", "給油料金", "</th>";
      echo "<th>", "日付変更", "</th>";
      echo "</tr></thead>";
      echo "<tbody>";
      foreach ($result as $row) {
        // １行ずつテーブルに入れる
        echo "<tr>";
        echo "<td>", es($row['date']), "</td>";
        echo "<td>", es($row['time']), "</td>";
        echo "<td>", es($row['item']), "</td>";
        echo "<td>", es($row['oil_quantity']), "</td>";
        echo "<td>", es($row['oil_pay']), "</td>";
        echo "<td>", es($row['oil_fee']), "</td>";
        echo "<td>", es($row['day_over']), "</td>";
        echo "</tr>";
      }
      echo "</tbody>";
      echo "</table>";
    } else {
      echo '<span class="error">エラーがありました。</span><br>';
    };
  } catch (Exception $e) {
    echo '<span class="error">エラーがありました。</span><br>';
    echo $e->getMessage();
  }
  ?>
  <form action="entrance.php" method="post">
  <input type="hidden" name="date" value="<?php echo $_SESSION['date']; ?>">
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
