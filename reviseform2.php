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
<link rel="stylesheet" href="input.css">
<link rel="stylesheet" href="button.css">
</head>
<body>
<div class="test">

  <?php
  $shimeicd = $_POST["shimeicd"];
  $date = $_POST["date"];
  $date_ts = strtotime($date);//タイムスタンプ化
  $date_ts = strtotime('+1 day',$date_ts);//一日足す
  $date_next = date('Y-m-d', $date_ts);//dateの形式に直す
  $date_ts = strtotime($date);//タイムスタンプ化
  $date_ts = strtotime('-1 day',$date_ts);//一日足す
  $date_prev = date('Y-m-d', $date_ts);//dateの形式に直す
   ?>

    <!--翌日に移動-->
    <form align="right" action="reviseform2.php" method="post">
      <input type="hidden" name="date" value="<?php echo $date_next ?>">
      <input type="hidden" name="shimeicd" value="<?php echo $shimeicd ?>">
      <input class="button" type="submit" value="翌日">
    </form>
    <!--前日に移動-->
    <form align="right" action="reviseform2.php" method="post">
      <input type="hidden" name="date" value="<?php echo $date_prev ?>">
      <input type="hidden" name="shimeicd" value="<?php echo $shimeicd ?>">
      <input class="button" type="submit" value="前日">
    </form>

  <p>＜訂正画面＞<?php echo "CD:{$shimeicd}" ?></p>
  <h6>訂正したいデータを選び、「訂正」を押してください。
  <br>※スマホの場合、画面を横にすると見やすいです。</h6>


  <!-- 入力フォーム -->
  <form method="POST" name="form" action="revise.php">
  <!---データ送信用-->
  <input type="hidden" name="shimeicd" value=<?php echo $shimeicd ?>>

  <?php
  //MySQLデータベースに接続する
  try {
    $pdo = new PDO($dsn, $user, $password);
    // プリペアドステートメントのエミュレーションを無効にする
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    // 例外がスローされる設定にする
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // SQL文を作る
    $sql = "SELECT * FROM driverecord WHERE shimeicd LIKE(:shimeicd) AND ((date LIKE(:date) AND day_over IS NULL) OR (date LIKE(:date2) AND day_over LIKE '%なし%') OR (date LIKE(:date_next) AND day_over LIKE '%あり%')) order by date, time";
    //訂正前…$sql = "SELECT * FROM driverecord WHERE shimeicd LIKE(:shimeicd) AND (date LIKE(:date) OR (date LIKE(:date_next) AND day_over LIKE '%あり%')) order by date, time";
    // プリペアドステートメントを作る
    $stm = $pdo->prepare($sql);
    // プレースホルダに値をバインドする
    $stm->bindValue(':shimeicd', "%{$shimeicd}%", PDO::PARAM_STR);
    $stm->bindValue(':date', "%{$date}%", PDO::PARAM_STR);
    $stm->bindValue(':date2', "%{$date}%", PDO::PARAM_STR);
    $stm->bindValue(':date_next', "%{$date_next}%", PDO::PARAM_STR);
    // SQL文を実行する
    $stm->execute();
    // 結果の取得（連想配列で受け取る）
    $result = $stm->fetchAll(PDO::FETCH_ASSOC);
    if(count($result)>0){
      // テーブルのタイトル行
      echo '<table align="left">';//テーブルを左寄せにできない？
      echo "<thead><tr>";
      echo "<th>", "選択", "</th>";
      echo "<th>", "日付", "</th>";
      echo "<th>", "時間", "</th>";
      echo "<th>", "日付変更", "</th>";
      echo "<th>", "項目", "</th>";
      echo "<th>", "距離", "</th>";
      echo "<th>", "場所", "</th>";
      echo "<th>", "駐車場利用", "</th>";
      echo "<th>", "駐車場料金", "</th>";
      echo "<th>", "高速利用", "</th>";
      echo "<th>", "高速料金", "</th>";
      echo "</tr></thead>";
      // 値を取り出して行に表示する
      echo "<tbody>";
      foreach ($result as $row){
        // １行ずつテーブルに入れる
        echo "<tr>";
        //チェックボックス
        echo "<td>",'<input type="radio" name="id" value="',es($row['id']),'" required>',"</td>";
        echo "<td>",es($row['date']), "</td>";
        echo "<td>",es($row['time']), "</td>";
        echo "<td>",es($row['day_over']),"</td>";
        echo "<td>",es($row['item']),"</td>";
        echo "<td>",es($row['odometer']),"</td>";
        echo "<td>",es($row['place']),"</td>";
        echo "<td>",es($row['park_use']),"</td>";
        echo "<td>",es($row['park_fee']),"</td>";
        echo "<td>",es($row['highway_use']),"</td>";
        echo "<td>",es($row['highway_fee']),"</td>";
        echo "</tr>";
      }
    } else {
      echo "条件に合うデータは見つかりませんでした。";
    }
  } catch (Exception $e) {
    echo '<span class="error">エラーがありました。</span><br>';
    echo $e->getMessage();
  }
  ?>

  <li><input class="button" type="submit" value="訂正">
  <input class="button" type="button" value="追加" onclick="location.href='add_entrance.php'">
  <input class="button" type="button" value="戻る" onclick="location.href='reviseform1.php'"></li>
  </form>

</div>
</body>
</html>
