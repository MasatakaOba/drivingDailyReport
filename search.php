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
<title>運転日報｜検索結果</title>
<!--論理ウィンドウサイズを設定(文字サイズをデバイスに合わせる)
<meta name="viewport" content="width=device-width">
-->
<!--電話番号の自動検出をオフにする-->
<meta name="format-detection" content="telephone=no">
<link rel="stylesheet" href="input.css">
<link rel="stylesheet" href="button.css">
<link href="tablestyle.css" rel="stylesheet">
<!--クリップボードにコピーするためのJSその①-->
<script src="https://cdnjs.cloudflare.com/ajax/libs/clipboard.js/1.6.0/clipboard.min.js"></script>
</head>
<body>
<button class="btn" type="button" data-clipboard-target="#input_test">テーブルをコピーする</button>
<div id="input_test">
  <?php
  $shimeicd = $_POST["shimeicd"];
  $date = $_POST["date"];
  //MySQLデータベースに接続する
  try {
    $pdo = new PDO($dsn, $user, $password);
    // プリペアドステートメントのエミュレーションを無効にする
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    // 例外がスローされる設定にする
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // SQL文を作る
    //注：氏名CDの属性をZEROFILLにすること
    $sql = "SELECT * FROM recordSummary WHERE shimeicd LIKE (:shimeicd) AND date LIKE (:date) order by shimeicd, date";
    // プリペアドステートメントを作る
    $stm = $pdo->prepare($sql);
    // プレースホルダに値をバインドする
    $stm->bindValue(':shimeicd', "{$shimeicd}", PDO::PARAM_STR);
    $stm->bindValue(':date', "%{$date}%", PDO::PARAM_STR);
    // SQL文を実行する
    $stm->execute();
    // 結果の取得（連想配列で受け取る）
    $result = $stm->fetchAll(PDO::FETCH_ASSOC);
    if(count($result)>0){
      // テーブルのタイトル行
      echo "<table>";
      echo "<thead><tr>";
      echo "<th>", "氏名CD", "</th>";
      echo "<th>", "日付", "</th>";
      echo "<th>", "出社", "</th>";
      echo "<th>", "出勤", "</th>";
      echo "<th>", "退勤", "</th>";
      echo "<th>", "帰宅", "</th>";
      echo "<th>", "経路", "</th>";
      echo "<th>", "駐車料_ﾘﾊﾟｰｸ", "</th>";
      echo "<th>", "駐車料_ﾀｲﾑｽﾞ", "</th>";
      echo "<th>", "駐車料_その他", "</th>";
      echo "<th>", "給油量", "</th>";
      echo "<th>", "給油料金", "</th>";
      echo "<th>", "高速料金", "</th>";
      echo "<th>", "私用走行", "</th>";
      echo "</tr></thead>";
      // 値を取り出して行に表示する
      echo "<tbody>";
      foreach ($result as $row){
        // １行ずつテーブルに入れる
      echo "<tr>";
      echo "<td>", sprintf('%07d', es($row['shimeicd'])), "</td>";//7桁以下の場合は右詰めで0を挿入
      echo "<td>", es($row['date']), "</td>";
      echo "<td>", es($row['shussha']), "</td>";
      echo "<td>", es($row['shukkin']), "</td>";
      echo "<td>", es($row['taikin']), "</td>";
      echo "<td>", es($row['kitaku']), "</td>";
      echo "<td>", es($row['keiro']), "</td>";
      echo "<td>", es($row['sumOtherParkFee']), "</td>";
      echo "<td>", es($row['sumTimesParkFee']), "</td>";
      echo "<td>", es($row['sumReparkParkFee']), "</td>";
      echo "<td>", es($row['kyuyuryo']), "</td>";
      echo "<td>", es($row['kyuyuryokin']), "</td>";
      echo "<td>", es($row['kosokuryokin']), "</td>";
      echo "<td>", es($row['shiyosoko']), "</td>";
      echo "</tr>";
      }
      echo "</tbody>";
      echo "</table>";

    } else {
      echo "条件に合うデータは見つかりませんでした。";
    }
  } catch (Exception $e) {
    echo '<span class="error">エラーがありました。</span><br>';
    echo $e->getMessage();
  }
  ?>
  <form class="" action="entrance.php" method="post">
  <input type="hidden" name="date" value=<?php echo $_SESSION["date"]; ?>>
  <input class="button" type="submit" value="ﾄｯﾌﾟ">
  <input class="button" type="button" value="戻る" onclick="window.history.back();">
  </form>
  </div>

<!--クリップボードにコピーするためのJSその②-->
<script>
(function() {
'use strict';

var clipboard = new Clipboard('.btn');

clipboard.on('success', function(e) {
    console.info('Action:', e.action);
    console.info('Text:', e.text);
    console.info('Trigger:', e.trigger);
    e.clearSelection();
    window.alert("クリップボードにコピーしました。\n指定のシートに値で貼り付けて下さい。");
});

clipboard.on('error', function(e) {
    console.error('Action:', e.action);
    console.error('Trigger:', e.trigger);
    window.alert("クリップボードにコピーできませんでした。\nもう一度試してみて下さい。")
});

})();
</script>

</body>
</html>
