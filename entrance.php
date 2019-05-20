<?php
require_once("util.php");
require "mysql_login.php";
//セッションの開始
session_start();
?>

<?php
//文字エンコードの検証
if (!cken($_POST)){
  $encoding = mb_internal_encoding();
  $err = "Encoding Error! The expected encoding is " . $encoding ;
  //エラーメッセージを出して、以下のコードをすべてキャンセルする
  exit($err);
}
?>

<?php
//POSTされた値をセッション変数に受け渡す(氏名CD)
if (isset($_POST['shimeicd'])){
  $_SESSION['shimeicd'] = $_POST['shimeicd'];
}
//入力データの取り出しとチェック
$error = [];
//名前
if (empty($_SESSION['shimeicd'])){
  //未設定のときエラー
  $error[] = "最初の画面に戻って氏名CDを入力してください";
} else {
  //氏名CDを取り出す
  $shimeicd = trim($_SESSION['shimeicd']);
}

//元の日付の画面に戻るためのセッション
//日付の指定がない場合、4時〜24時までは当日、0時〜4時までは前の日が表示される
  if (isset($_POST["date"])) {
    $_SESSION["date"] = $_POST["date"];
    $date = $_POST["date"];//念の為
    //指定された日付が今日のとき
    switch ($date) {
      case date('Y-m-d'):
        $dateToday = $date;//フッダーのボタンを本日のものにする
        break;
      default:
        break;
    }
  }else {
    $checkTime = strtotime(date('H:i:s'));//現在時刻
    if ($checkTime > strtotime('04:00:00') && $checkTime < strtotime('23:59:59'))//04〜24時まで
    {
    $date = date('Y-m-d');//本日日付に設定
    $dateToday = $date;
    $_SESSION["date"] = $date;
  }else {//00〜04時まで
    $date = date('Y-m-d');
    $date_ts = strtotime($date);//タイムスタンプ化
    $date_ts = strtotime('-1 day',$date_ts);//一日引く
    $date = date('Y-m-d', $date_ts);//dateの形式に直す
    $dateToday = $date;
    $_SESSION["date"] = $date;
  }
  }
?>

<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="utf-8">
<title>運転日報｜トップ画面</title>
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

<!--コンテンツ、フッター、ヘッダーの表示調整-->
<style>
.header_area {/*ヘッダーエリアの調整*/
  background-color:white;
  position: fixed; /* 固定 */
  top: 0px; /* 最上段より表示 */
  width: 100%; /*中央に表示 */
}
.Contents{/*コンテンツの調整*/
	overflow: auto; 	/* コンテンツの表示を自動に設定（スクロール） */
  width: 100%;
  padding-top: 30px;/*ヘッダーにかぶるのを防ぐために設定 */
  padding-bottom: 200px;/*フッダーにかぶるのを防ぐために設定 */
}
.footer_area {/*フッターエリアの調整*/
  background-color:white;
  position: fixed; /* 固定 */
  bottom: 5px; /* 5px最下部より浮かせて表示 */
  width: 100%; /* 中央に表示 */
  text-align: center;
}
.button{
      cursor:pointer;
}
.button_ent {
      cursor:pointer;
      -webkit-appearance: none;
      width: 75px;
      text-align: center;
      margin: 2px 3px;
      font-size: 0.8em;
      font-weight: bold;
      padding-top:6px;
      padding-bottom:6px;
      background-color: #248;
      color: #fff;
      border-style: none;
      box-shadow: 2px 2px 3px 1px #666;
      -moz-box-shadow: 2px 2px 3px 1px #666;
      -webkit-box-shadow: 2px 2px 3px 1px #666;
  }
  .button_ent_big {
        cursor:pointer;
        -webkit-appearance: none;
        width: 160px;
        text-align: center;
        margin: 2px 3px;
        font-size: 0.9em;
        font-weight: bold;
        padding-top:6px;
        padding-bottom:6px;
        background-color: #248;
        color: #fff;
        border-style: none;
        box-shadow: 2px 2px 3px 1px #666;
        -moz-box-shadow: 2px 2px 3px 1px #666;
        -webkit-box-shadow: 2px 2px 3px 1px #666;
    }
    .button_ent_small {
          cursor:pointer;
          -webkit-appearance: none;
          width: 74px;
          text-align: center;
          margin: 2px 3px;
          font-size: 0.9em;
          font-weight: bold;
          padding-top:6px;
          padding-bottom:6px;
          background-color: #248;
          color: #fff;
          border-style: none;
          box-shadow: 2px 2px 3px 1px #666;
          -moz-box-shadow: 2px 2px 3px 1px #666;
          -webkit-box-shadow: 2px 2px 3px 1px #666;
      }
      span{
          cursor:pointer;
      }
      .text{
          cursor:text;
      }

</style>
</head>
<body>
<div class="test">

<?php
//変数定義
$dateOriginal = $date;//データ削除時にこのページに戻るために使用
$date_ts = strtotime($date);//タイムスタンプ化
$date_ts = strtotime('+1 day',$date_ts);//一日足す
$date_next = date('Y-m-d', $date_ts);//dateの形式に直す
$date_ts = strtotime($date);//タイムスタンプ化
$date_ts = strtotime('-1 day',$date_ts);//一日足す
$date_prev = date('Y-m-d', $date_ts);//dateの形式に直す
$date_week = date('w',strtotime($date));
//曜日設定
$week = [
  '(日)', //0
  '(月)', //1
  '(火)', //2
  '(水)', //3
  '(木)', //4
  '(金)', //5
  '(土)', //6
];
 ?>

 <div class="header_area">
  <!--翌日に移動-->
  <form align="right" action="entrance.php" method="post">
    <input type="hidden" name="date" value="<?php echo $date_next ?>">
    <input class="button" type="submit" value="翌日">
  </form>
  <!--前日に移動-->
  <form align="right" action="entrance.php" method="post">
    <input type="hidden" name="date" value="<?php echo $date_prev ?>">
    <input class="button" type="submit" value="前日">
  </form>

  <span class="text" align="left" style="font-size:0.95em;">
  <li><?php echo "氏名CD:{$shimeicd}" ?>
  <li><?php echo "組織CD:0000000" ?>
 </span>
 </div>

<div class="Contents" style="font-size: 0.95em;"><!--font-sizeで文字の大きさを調整-->
  <!-- 入力フォーム -->
  <form method="POST" name="form" action="revise.php">
  <!---データ送信用-->
  <input type="hidden" name="shimeicd" value=<?php echo $shimeicd ?>>
  <input type="hidden" name="dateOriginal" value=<?php echo $dateOriginal ?>>

  <?php
  $shimeicd = intval($shimeicd);//氏名コードを数字化（必要？）
  $date_ts = strtotime($date);//タイムスタンプ化
  $date_ts = strtotime('+1 day',$date_ts);//一日足す
  $date_next = date('Y-m-d', $date_ts);//dateの形式に直す
  echo '<br><span class="error" style="background-color: #ffff00;font-weight:bold;" onclick="selectDateOfEntrance()">日付：',$date,' ',$week[$date_week],'</span>';//class="error"は便宜上つけました。

  //MySQLデータベースに接続する
  try {
    $pdo = new PDO($dsn, $user, $password);
    //プリペアドステートメントのエミュレーションを無効にする
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    //例外がスローされる設定にする
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    //////出社の表示//////
    //SQL文を作る
    $sql = "SELECT * FROM driverecord WHERE item LIKE '%出社%' AND shimeicd LIKE(:shimeicd) AND (date LIKE(:date) OR (date LIKE(:date2) AND day_over LIKE '%なし%') OR (date LIKE(:date_next) AND day_over LIKE '%あり%'))";
    //プリペアドステートメントを作る
    $stm = $pdo->prepare($sql);
    //プレースホルダに値をバインドする
    $stm->bindValue(':shimeicd', "%{$shimeicd}%", PDO::PARAM_STR);
    $stm->bindValue(':date', "%{$date}%", PDO::PARAM_STR);
    $stm->bindValue(':date2', "%{$date}%", PDO::PARAM_STR);
    $stm->bindValue(':date_next', "%{$date_next}%", PDO::PARAM_STR);
    //SQL文を実行する
    $stm->execute();
    //結果の取得（連想配列で受け取る）
    $result = $stm->fetchAll(PDO::FETCH_ASSOC);
      $countMax = count($result);//最大値の設定
      $count = 0;//countを0に設定
      if(count($result)>0){
      echo '<details><summary>出社：';//文章の前準備
      foreach ($result as $row) {
      echo es($row['odometer']),'km/',es($row['time']);
      $odometerShussha = es($row['odometer']);//updateテスト用
      $count++;//1ループごとにカウント
        if($count == $countMax){
        echo '</summary>';
          //summaryの中
          if(count($result)>0){
          foreach ($result as $row) {
          echo '<li>・',es($row['odometer']),'km/',es($row['time']),
               '<button type="submit" name="id" value=',es($row['id']),'>訂正/削除</button>';
          }
          } else {
            echo '<li><span class="error" style="color:red;">データなし</span></summary>';//追加ボタンにする？
          };
          //完：summaryの中
            echo '</details>';
        break;
        }
      }
    } else {
      echo '<li><span class="error" style="color:red;" onclick="home()">■出社：データなし</span>';
    };

    //////出勤の表示//////
    //SQL文を作る
    $sql = "SELECT * FROM driverecord WHERE item LIKE '%到着%' AND shimeicd LIKE(:shimeicd) AND (date LIKE(:date) OR (date LIKE(:date_next) AND day_over LIKE '%あり%')) AND odometer > 0  ORDER BY odometer ASC LIMIT 1";
    $stm = $pdo->prepare($sql);//プリペアドステートメントを作る
    $stm->bindValue(':shimeicd', "%{$shimeicd}%", PDO::PARAM_STR);//プレースホルダに値をバインドする
    $stm->bindValue(':date', "%{$date}%", PDO::PARAM_STR);
    $stm->bindValue(':date_next', "%{$date_next}%", PDO::PARAM_STR);
    $stm->execute();//SQL文を実行する
    $result = $stm->fetchAll(PDO::FETCH_ASSOC);//結果の取得（連想配列で受け取る）
      $countMax = count($result);//最大値の設定
      $count = 0;//countを0に設定
      if(count($result)>0){
      echo '<details><summary>出勤：';//文章の前準備
      foreach ($result as $row) {
      echo es($row['odometer']),'km/',es($row['time']);
      $odometerShukkin = es($row['odometer']);//recordSummary挿入用
      $count++;//1ループごとにカウント(念の為)
        if($count == $countMax){
        echo '</summary>';//以下、summaryの中
          if(count($result)>0){
          foreach ($result as $row) {
          echo '<li>・',es($row['odometer']),'km/',es($row['time']),'<button type="submit" name="id" value=',es($row['id']),'>訂正/削除</button>','<li style="font-size: 0.7em; color:red;">…最も早い時刻の到着データを出勤データとして表示';
          }
          } else {
            echo '<span class="error" style="color:red;">データなし</span></summary>';//追加ボタンにする？
          };//完：summaryの中
            echo '</details>';
        break;
        }
      }
    } else {//データがなかった場合
      echo '<li><span class="error" style="color:red;" onclick="park()">■出勤：データなし</span>';
    };

    //****************//
    //////退勤の表示//////
    //SQL文を作る
    $sql = "SELECT * FROM driverecord WHERE item LIKE '%到着%' AND shimeicd LIKE(:shimeicd) AND (date LIKE(:date) OR (date LIKE(:date_next) AND day_over LIKE '%あり%')) AND odometer > 0 ORDER BY odometer DESC LIMIT 1";
    $stm = $pdo->prepare($sql);//プリペアドステートメントを作る
    $stm->bindValue(':shimeicd', "%{$shimeicd}%", PDO::PARAM_STR);//プレースホルダに値をバインドする
    $stm->bindValue(':date', "%{$date}%", PDO::PARAM_STR);
    $stm->bindValue(':date_next', "%{$date_next}%", PDO::PARAM_STR);
    $stm->execute();//SQL文を実行する
    $result = $stm->fetchAll(PDO::FETCH_ASSOC);//結果の取得（連想配列で受け取る）
      $countMax = count($result);//最大値の設定
      $count = 0;//countを0に設定
      if(count($result)>0){
      echo '<details><summary>退勤：';//文章の前準備
      foreach ($result as $row) {
      echo es($row['odometer']),'km/',es($row['time']);
      $odometerTaikin = es($row['odometer']);//recordSummary挿入用
      $count++;//1ループごとにカウント
        if($count == $countMax){
        echo '</summary>';          //以下、summaryの中
          if(count($result)>0){
          foreach ($result as $row) {
          echo '<li>・',es($row['odometer']),'km/',es($row['time']),'<button type="submit" name="id" value=',es($row['id']),'>訂正/削除</button>','<input style="font-size: 0.55em;" type="button" value="追加" onclick="park()">','<li style="font-size: 0.7em; color:red;">…最も遅い時刻の到着データを退勤データとして表示';
          }
          } else {
            echo '<span class="error" style="color:red;">データなし</span></summary>';//追加ボタンにする？
          };//完：summaryの中
            echo '</details>';
        break;
        }
      }
    } else {//データがなかった場合
      echo '<li><span class="error" style="color:red;" onclick="go()">■退勤：データなし</span>';
    };

    //****************//
    //////帰宅の表示//////
    //****************//
    //SQL文を作る
    $sql = "SELECT * FROM driverecord WHERE item LIKE '%帰宅%' AND shimeicd LIKE(:shimeicd) AND ((date LIKE(:date) AND day_over LIKE '%なし%') OR (date LIKE(:date_next) AND day_over LIKE '%あり%'))";
    $stm = $pdo->prepare($sql);//プリペアドステートメントを作る
    $stm->bindValue(':shimeicd', "%{$shimeicd}%", PDO::PARAM_STR);//プレースホルダに値をバインドする
    $stm->bindValue(':date', "%{$date}%", PDO::PARAM_STR);
    $stm->bindValue(':date_next', "%{$date_next}%", PDO::PARAM_STR);
    $stm->execute();//SQL文を実行する
    $result = $stm->fetchAll(PDO::FETCH_ASSOC);//結果の取得（連想配列で受け取る）
      $countMax = count($result);//最大値の設定
      $count = 0;//countを0に設定
      if(count($result)>0){
      echo '<details><summary>帰宅：';//文章の前準備
      foreach ($result as $row) {
      echo es($row['odometer']),'km/',es($row['time']);
      $odometerKitaku = es($row['odometer']);//recordSummary項目「kitaku」挿入用
      $count++;//1ループごとにカウント
        if($count == $countMax){
        echo '</summary>';          //以下、summaryの中
          if(count($result)>0){
          foreach ($result as $row) {
          echo '<li>・',es($row['odometer']),'km/',es($row['time']),'<button type="submit" name="id" value=',es($row['id']),'>訂正/削除</button>';
          }
          } else {
            echo '<span class="error" style="color:red;">データなし</span></summary>';//追加ボタンにする？
          };//完：summaryの中
            echo '</details>';
        break;
        }
      }
    } else {//データがなかった場合
      echo '<li><span class="error" style="color:red;" onclick="return2()">■帰宅：データなし</span>';
    };

    //////場所の表示//////
    //SQL文を作る
    $sql = "SELECT * FROM driverecord WHERE shimeicd LIKE(:shimeicd) AND (item LIKE '%出社%' OR item LIKE '%到着%' OR item LIKE '%帰宅%') AND ((date LIKE(:date) AND day_over IS NULL) OR (date LIKE(:date2) AND day_over LIKE '%なし%') OR (date LIKE(:date_next) AND day_over LIKE '%あり%')) ORDER BY date ASC, time ASC";
    $stm = $pdo->prepare($sql);//プリペアドステートメントを作る
    $stm->bindValue(':shimeicd', "%{$shimeicd}%", PDO::PARAM_STR);//プレースホルダに値をバインドする
    $stm->bindValue(':date', "%{$date}%", PDO::PARAM_STR);
    $stm->bindValue(':date2', "%{$date}%", PDO::PARAM_STR);//SQL文への条件追加のため
    $stm->bindValue(':date_next', "%{$date_next}%", PDO::PARAM_STR);
    $stm->execute();//SQL文を実行する
    $result = $stm->fetchAll(PDO::FETCH_ASSOC);//結果の取得（連想配列で受け取る）
      $countMax = count($result);//最大値の設定
      $count = 0;//countを0に設定
      if(count($result)>0){
      echo '<details><summary>場所：';//文章の前準備
      foreach ($result as $row) {
      $count++;//1ループごとにカウント
      if ($count == 1) {  //カウントが１のとき
        echo es($row['place']);
        $keiro1 = es($row['place']);//挿入用
        $keiro2 = NULL;//初期化
        }
      if ($count > 1) {  //カウントが2以上のとき
        echo '→',es($row['place']);
        $yajirushi = '→';//矢印の変数化
        $keiro2 = $keiro2 . $yajirushi . es($row['place']);
        }
        if($count == $countMax){
        $keiro = $keiro1 .$keiro2;//挿入用最終
        echo '</summary>';          //以下、summaryの中
          if(count($result)>0){
          foreach ($result as $row) {
          echo '<li>・',es($row['place']),'/',es($row['time']),'<button type="submit" name="id" value=',es($row['id']),'>訂正/削除</button>';
          }
          } else {
            echo '<span class="error" style="color:red;">データなし</span></summary>';//追加ボタンにする？
          };//完：summaryの中
            echo '</details>';
        break;
        }
      }
    } else {//データがなかった場合
      echo '<li><span class="error" style="color:red;" onclick="park()">■場所：データなし</span>';
    };

    //駐車料金（すべて）の表示
    //SQL文を作る
    $sql = "SELECT * FROM driverecord WHERE (item LIKE '%出発%' OR item LIKE '%出社%') AND shimeicd LIKE(:shimeicd) AND ((date LIKE(:date) AND day_over IS NULL) OR (date LIKE(:date2) AND day_over LIKE '%なし%') OR (date LIKE(:date_next) AND day_over LIKE '%あり%')) AND ((park_use LIKE '%タイムズ%') OR (park_use LIKE '%リパーク%') OR(park_use LIKE '%その他駐車場%'))";
    $stm = $pdo->prepare($sql);//プリペアドステートメントを作る
    $stm->bindValue(':shimeicd', "%{$shimeicd}%", PDO::PARAM_STR);//プレースホルダに値をバインドする
    $stm->bindValue(':date', "%{$date}%", PDO::PARAM_STR);
    $stm->bindValue(':date2', "%{$date}%", PDO::PARAM_STR);
    $stm->bindValue(':date_next', "%{$date_next}%", PDO::PARAM_STR);
    $stm->execute();//SQL文を実行する
    $result = $stm->fetchAll(PDO::FETCH_ASSOC);//結果の取得（連想配列で受け取る）
      $countMax = count($result);//最大値の設定
      $count = 0;//countを0に設定
      if(count($result)>0){
      echo '<details><summary>駐車：';//文章の前準備
      foreach ($result as $row) {
      $sumAllParkFee += es($row['park_fee']);//タイムズ使用での合計金額の表示
      $count++;//1ループごとにカウント
        if($count == $countMax){//すべてのデータ数の読み込みが終了した場合
        echo $sumAllParkFee,'円(現金・ｶｰﾄﾞ計)';
        break;
        }
      }
    } else {//データがなかった場合
      echo '<li><span class="error" style="color:red;" onclick="go()">■駐車：データなし</span>';
    };
        echo '</summary>';

    //駐車料金（タイムズ）の表示
    //SQL文を作る
    $sql = "SELECT * FROM driverecord WHERE (item LIKE '%出発%' OR item LIKE '%出社%') AND shimeicd LIKE(:shimeicd) AND ((date LIKE(:date) AND day_over IS NULL) OR (date LIKE(:date2) AND day_over LIKE '%なし%') OR (date LIKE(:date_next) AND day_over LIKE '%あり%')) AND park_use LIKE '%タイムズ%'";
    $stm = $pdo->prepare($sql);//プリペアドステートメントを作る
    $stm->bindValue(':shimeicd', "%{$shimeicd}%", PDO::PARAM_STR);//プレースホルダに値をバインドする
    $stm->bindValue(':date', "%{$date}%", PDO::PARAM_STR);
    $stm->bindValue(':date2', "%{$date}%", PDO::PARAM_STR);
    $stm->bindValue(':date_next', "%{$date_next}%", PDO::PARAM_STR);
    $stm->execute();//SQL文を実行する
    $result = $stm->fetchAll(PDO::FETCH_ASSOC);//結果の取得（連想配列で受け取る）
      $countMax = count($result);//最大値の設定
      $count = 0;//countを0に設定
      if(count($result)>0){
      echo '<details><summary>ﾀｲﾑｽﾞ：';//文章の前準備
      foreach ($result as $row) {
      $sumTimesParkFee += es($row['park_fee']);//タイムズ使用での合計金額の表示
      $count++;//1ループごとにカウント
        if($count == $countMax){//すべてのデータ数の読み込みが終了した場合
        echo $sumTimesParkFee,'円';
        echo '</summary>';          //以下、summaryの中
          if(count($result)>0){
          foreach ($result as $row) {
          echo '<li>・',es($row['park_fee']),'円/',es($row['time']),'<button type="submit" name="id" value=',es($row['id']),'>訂正/削除</button>';
          }
          } else {
            echo '<span class="error" style="color:red;">データなし</span></summary>';//追加ボタンにする？
          };//完：summaryの中
            echo '</details>';
        break;
        }
      }
    } else {//データがなかった場合
    };

    //駐車料金（リパーク）の表示
    //SQL文を作る
    $sql = "SELECT * FROM driverecord WHERE (item LIKE '%出発%' OR item LIKE '%出社%') AND shimeicd LIKE(:shimeicd) AND ((date LIKE(:date) AND day_over IS NULL) OR (date LIKE(:date2) AND day_over LIKE '%なし%') OR (date LIKE(:date_next) AND day_over LIKE '%あり%')) AND park_use LIKE '%リパーク%'";
    $stm = $pdo->prepare($sql);//プリペアドステートメントを作る
    $stm->bindValue(':shimeicd', "%{$shimeicd}%", PDO::PARAM_STR);//プレースホルダに値をバインドする
    $stm->bindValue(':date', "%{$date}%", PDO::PARAM_STR);
    $stm->bindValue(':date2', "%{$date}%", PDO::PARAM_STR);
    $stm->bindValue(':date_next', "%{$date_next}%", PDO::PARAM_STR);
    $stm->execute();//SQL文を実行する
    $result = $stm->fetchAll(PDO::FETCH_ASSOC);//結果の取得（連想配列で受け取る）
      $countMax = count($result);//最大値の設定
      $count = 0;//countを0に設定
      if(count($result)>0){
      echo '<details><summary>ﾘﾊﾟｰｸ：';//文章の前準備
      foreach ($result as $row) {
      $sumReparkParkFee += es($row['park_fee']);//タイムズ使用での合計金額の表示
      $count++;//1ループごとにカウント
        if($count == $countMax){//すべてのデータ数の読み込みが終了した場合
        echo $sumReparkParkFee,'円';
        echo '</summary>';          //以下、summaryの中
          if(count($result)>0){
          foreach ($result as $row) {
          echo '<li>・',es($row['park_fee']),'円/',es($row['time']),'<button type="submit" name="id" value=',es($row['id']),'>訂正/削除</button>';
          }
          } else {
            echo '<span class="error" style="color:red;">データなし</span></summary>';//念の為残す
          };//完：summaryの中
            echo '</details>';
        break;
        }
      }
    } else {//データがなかった場合
    };

    //駐車料金(その他)の表示
    //SQL文を作る
    $sql = "SELECT * FROM driverecord WHERE (item LIKE '%出発%' OR item LIKE '%出社%') AND shimeicd LIKE(:shimeicd) AND ((date LIKE(:date) AND day_over IS NULL) OR (date LIKE(:date2) AND day_over LIKE '%なし%') OR (date LIKE(:date_next) AND day_over LIKE '%あり%')) AND park_use LIKE 'その他駐車場'";
    $stm = $pdo->prepare($sql);//プリペアドステートメントを作る
    $stm->bindValue(':shimeicd', "%{$shimeicd}%", PDO::PARAM_STR);//プレースホルダに値をバインドする
    $stm->bindValue(':date', "%{$date}%", PDO::PARAM_STR);
    $stm->bindValue(':date2', "%{$date}%", PDO::PARAM_STR);
    $stm->bindValue(':date_next', "%{$date_next}%", PDO::PARAM_STR);
    $stm->execute();//SQL文を実行する
    $result = $stm->fetchAll(PDO::FETCH_ASSOC);//結果の取得（連想配列で受け取る）
      $countMax = count($result);//最大値の設定
      $count = 0;//countを0に設定
      if(count($result)>0){
      echo '<details><summary>その他：';//文章の前準備
      foreach ($result as $row) {
      $sumOtherParkFee += es($row['park_fee']);//タイムズ使用での合計金額の表示
      $count++;//1ループごとにカウント
        if($count == $countMax){//すべてのデータ数の読み込みが終了した場合
        echo $sumOtherParkFee,'円';
        echo '</summary>';          //以下、summaryの中
          if(count($result)>0){
          foreach ($result as $row) {
          echo '<li>・',es($row['park_fee']),'円/',es($row['time']),'<button type="submit" name="id" value=',es($row['id']),'>訂正/削除</button>';
          }
          } else {
            echo '<span class="error" style="color:red;">データなし</span></summary>';//念の為残す
          };//完：summaryの中
            echo '</details>';
        break;
        }
      }
    } else {//データがなかった場合
    };
    echo '</details>';

    //////給油の表示（全体）//////
    //SQL文を作る
    $sql = "SELECT * FROM driverecord WHERE item LIKE '%給油%' AND shimeicd LIKE(:shimeicd) AND ((date LIKE(:date) AND day_over LIKE '%なし%') OR (date LIKE(:date_next) AND day_over LIKE '%あり%'))";
    $stm = $pdo->prepare($sql);//プリペアドステートメントを作る
    $stm->bindValue(':shimeicd', "%{$shimeicd}%", PDO::PARAM_STR);//プレースホルダに値をバインドする
    $stm->bindValue(':date', "%{$date}%", PDO::PARAM_STR);
    $stm->bindValue(':date_next', "%{$date_next}%", PDO::PARAM_STR);
    $stm->execute();//SQL文を実行する
    $result = $stm->fetchAll(PDO::FETCH_ASSOC);//結果の取得（連想配列で受け取る）
      $countMax = count($result);//最大値の設定
      $count = 0;//countを0に設定
      if(count($result)>0){
      echo '<details><summary>給油：';//文章の前準備
      foreach ($result as $row) {
      $count++;//1ループごとにカウント
      $kyuyuryo += es($row['oil_quantity']);//recordSummary項目「kyuyuryo」挿入用
      $kyuyuryokin += es($row['oil_pay']);//recordSummary項目「kyuyuryokin」挿入用
        echo es($row['oil_quantity']),'L/',es($row['time']);
        if($count == $countMax){
        echo '</summary>';          //以下、summaryの中
          if(count($result)>0){
          foreach ($result as $row) {
          echo '<li>・',es($row['oil_pay']),'/',es($row['oil_fee']),'円 ','<button type="submit" name="id" value=',es($row['id']),'>訂正/削除</button>';
          }
          } else {
            echo '<span class="error" style="color:red;">データなし</span></summary>';//念の為
          };//完：summaryの中
            echo '</details>';
        break;
        }
      }
    } else {//データがなかった場合
      echo '<li><span class="error" style="color:red;" onclick="oil()">■給油：データなし</span>';
    };

    //********************//
    //////高速利用の表示//////
    //********************//
    //SQL文を作る
    $sql = "SELECT * FROM driverecord WHERE (item LIKE '%到着%' OR item LIKE '%帰宅%') AND shimeicd LIKE(:shimeicd) AND (highway_use LIKE '%ETC%' OR highway_use LIKE '%現金%') AND ((date LIKE(:date) AND day_over LIKE '%なし%') OR (date LIKE(:date_next) AND day_over LIKE '%あり%'))";
    $stm = $pdo->prepare($sql);//プリペアドステートメントを作る
    $stm->bindValue(':shimeicd', "%{$shimeicd}%", PDO::PARAM_STR);//プレースホルダに値をバインドする
    $stm->bindValue(':date', "%{$date}%", PDO::PARAM_STR);
    $stm->bindValue(':date_next', "%{$date_next}%", PDO::PARAM_STR);
    $stm->execute();//SQL文を実行する
    $result = $stm->fetchAll(PDO::FETCH_ASSOC);//結果の取得（連想配列で受け取る）
      $countMax = count($result);//最大値の設定
      $count = 0;//countを0に設定
      if(count($result)>0){
      echo '<details><summary>高速：利用あり/';//文章の前準備
      foreach ($result as $row) {
      $count++;//1ループごとにカウント
      $sumHighwayFee += es($row['highway_fee']);//高速利用での合計金額の表示
        if($count == $countMax){
        echo $sumHighwayFee,'円';
        echo '</summary>';          //以下、summaryの中
          if(count($result)>0){
          foreach ($result as $row) {
          echo '<li>・',es($row['highway_use']),'/',es($row['highway_fee']),'円/',es($row['time']),'<button type="submit" name="id" value=',es($row['id']),'>訂正/削除</button>';
          }
          } else {
            echo '<span class="error" style="color:red;">データなし</span></summary>';//念の為
          };//完：summaryの中
            echo '</details>';
        break;
        }
      }
    } else {//データがなかった場合
      echo '<li><span class="error" onclick="park()">■高速利用なし</span>';
    };

    //////私用利用の表示//////
    //SQL文を作る
    $sql = "SELECT * FROM driverecord WHERE item LIKE '%私用%' AND shimeicd LIKE(:shimeicd) AND date LIKE(:date)";
    $stm = $pdo->prepare($sql);//プリペアドステートメントを作る
    $stm->bindValue(':shimeicd', "%{$shimeicd}%", PDO::PARAM_STR);//プレースホルダに値をバインドする
    $stm->bindValue(':date', "%{$date}%", PDO::PARAM_STR);
    $stm->execute();//SQL文を実行する
    $result = $stm->fetchAll(PDO::FETCH_ASSOC);//結果の取得（連想配列で受け取る）
      $countMax = count($result);//最大値の設定
      $count = 0;//countを0に設定
      if(count($result)>0){
      echo '<details><summary>私用:';//文章の前準備
      foreach ($result as $row) {
      $count++;//1ループごとにカウント
        if($count == $countMax){
        echo es($row['odometer']),'km/終了時';
        $shiyosoko = es($row['odometer']);
        echo '</summary>';//以下、summaryの中
          if(count($result)>0){
          foreach ($result as $row) {
          echo '<li>・',es($row['time']),'<button type="submit" name="id" value=',es($row['id']),'>訂正/削除</button>';
          }
          } else {
            echo '<span class="error" style="color:red;">データなし</span></summary>';//念の為
          };//完：summaryの中
            echo '</details>';
        break;
        }
      }
    } else {//データがなかった場合
      echo '<li><span class="error" onclick="private()">■私用利用なし</span>';
    };

    //////UPDATEのテスト用//////
    //該当のデータがない場合はデータを挿入する
    $sql = "SELECT * FROM recordSummary WHERE date LIKE(:date) AND shimeicd LIKE(:shimeicd)";
    // AND shimeicd LIKE(:shimeicd) AND
    $stm = $pdo->prepare($sql);//プリペアドステートメントを作る
    $stm->bindValue(':shimeicd', "%{$shimeicd}%", PDO::PARAM_STR);//プレースホルダに値をバインドする
    $stm->bindValue(':date', "{$date}", PDO::PARAM_STR);
    $stm->execute();//SQL文を実行する
    $result = $stm->fetchAll(PDO::FETCH_ASSOC);//結果の取得（連想配列で受け取る）
      if(count($result)==0){
      $sql = "INSERT INTO recordSummary (shimeicd, date) VALUES (:shimeicd, :date)";
      $stm = $pdo->prepare($sql);//プリペアドステートメントを作る
      $stm->bindValue(':shimeicd', "{$shimeicd}", PDO::PARAM_STR);//プレースホルダに値をバインドする
      $stm->bindValue(':date', "{$date}", PDO::PARAM_STR);
      $stm->execute();//SQL文を実行する
    };

    //UPDATEの実施
    $sql = "UPDATE recordSummary SET shussha = :odometerShussha, shukkin = :odometerShukkin, taikin = :odometerTaikin, kitaku = :odometerKitaku,keiro = :keiro, kyuyuryo = :kyuyuryo, kyuyuryokin = :kyuyuryokin, sumOtherParkFee = :sumOtherParkFee, sumTimesParkFee = :sumTimesParkFee, sumReparkParkFee = :sumReparkParkFee, kosokuryokin = :sumHighwayFee, shiyosoko = :shiyosoko WHERE shimeicd = :shimeicd AND date = :date";
    $stm = $pdo->prepare($sql);//プリペアドステートメントを作る
    $stm->bindValue(':shimeicd', "{$shimeicd}", PDO::PARAM_STR);//プレースホルダに値をバインドする
    $stm->bindValue(':date', "{$date}", PDO::PARAM_STR);
    $stm->bindValue(':odometerShussha', "{$odometerShussha}", PDO::PARAM_STR);
    $stm->bindValue(':odometerShukkin', "{$odometerShukkin}", PDO::PARAM_STR);
    $stm->bindValue(':odometerTaikin', "{$odometerTaikin}", PDO::PARAM_STR);
    $stm->bindValue(':odometerKitaku', "{$odometerKitaku}", PDO::PARAM_STR);
    $stm->bindValue(':keiro', "{$keiro}", PDO::PARAM_STR);
    $stm->bindValue(':kyuyuryo', "{$kyuyuryo}", PDO::PARAM_STR);
    $stm->bindValue(':kyuyuryokin', "{$kyuyuryokin}", PDO::PARAM_STR);
    $stm->bindValue(':sumReparkParkFee', "{$sumReparkParkFee}", PDO::PARAM_STR);
    $stm->bindValue(':sumOtherParkFee', "{$sumOtherParkFee}", PDO::PARAM_STR);
    $stm->bindValue(':sumTimesParkFee', "{$sumTimesParkFee}", PDO::PARAM_STR);
    $stm->bindValue(':sumHighwayFee', "{$sumHighwayFee}", PDO::PARAM_STR);
    $stm->bindValue(':shiyosoko', "{$shiyosoko}", PDO::PARAM_STR);

    $stm->execute();//SQL文を実行する
    $result = $stm->fetchAll(PDO::FETCH_ASSOC);//結果の取得（連想配列で受け取る）

////接続エラーの場合（TRY終了）
  }
    catch (Exception $e) {
    echo '<span class="error">エラーがありました。</span><br>';
    echo $e->getMessage();
  }
  echo '<br>';
  ?>
</div>

<?php
//表示データの日付によりリンク先を変更する
switch ($date) {
  case $dateToday://表示データが本日4時〜28時の場合

  echo
  '<!--フッター部分(日付＝本日)-->
  <div class="footer_area">
    <hr><!--境界線-->
    <li><input class="button_ent_big" type="button" value="出社" onclick="home()">
    <input class="button_ent_big" type="button" value="帰宅" onclick="return2()">
    <li><input class="button_ent_big" type="button" value="到着" onclick="park()">
    <input class="button_ent_big" type="button" value="出発(駐車料)" onclick="go()">
    <li><input class="button_ent_big" type="button" value="給油" onclick="oil()">
    <input class="button_ent_big" type="button" value="私用" onclick="private()">
    <li><input class="button_ent_big" type="button" value="ﾛｸﾞｱｳﾄ" onclick="login()">
    <input class="button_ent_big" type="button" value="検索" onclick="searchform()"></li>
    </form>
  </div>',
  '<script type="text/javascript">
    function selectDateOfEntrance() {
      location.href = "selectDateOfEntrance.php";//トップ日付選択画面に行く
    }
    function home() {
      location.href = "home.php";//出社登録画面に行く
    }
    function park() {
      location.href = "park.php";//到着登録画面に行く
    }
    function go() {
      location.href = "go.php";//出発登録画面に行く
    }
    function return2() {
      location.href = "return.php";//出発登録画面に行く
    }
    function oil() {
      location.href = "oil.php";//給油登録画面に行く
    }
    function private() {
      location.href = "private.php";//私用利用登録画面に行く
    }
    function login() {
      location.href = "login.php";//ログイン画面に行く
    }
    function searchform() {
      location.href = "searchform.php";//検索画面に行く
    }
  </script>';
    break;

  default://表示データが0時〜4時の場合
  echo
  '<!--フッター部分(日付＝本日)-->
  <div class="footer_area">
    <hr><!--境界線-->
    <li><input class="button_ent_big" type="button" value="出社" onclick="home()">
    <input class="button_ent_big" type="button" value="帰宅" onclick="return2()">
    <li><input class="button_ent_big" type="button" value="到着" onclick="park()">
    <input class="button_ent_big" type="button" value="出発(駐車料)" onclick="go()">
    <li><input class="button_ent_big" type="button" value="給油" onclick="oil()">
    <input class="button_ent_big" type="button" value="私用" onclick="private()">
    <li><input class="button_ent_big" type="button" value="ﾛｸﾞｱｳﾄ" onclick="login()">
    <input class="button_ent_big" type="button" value="検索" onclick="searchform()"></li>
    </form>
  </div>',
  '<script type="text/javascript">
    function selectDateOfEntrance() {
      location.href = "selectDateOfEntrance.php";//トップ日付選択画面に行く
    }
    function home() {
      location.href = "add_home.php";//出社登録画面に行く
    }
    function park() {
      location.href = "add_park.php";//到着登録画面に行く
    }
    function go() {
      location.href = "add_go.php";//出発登録画面に行く
    }
    function return2() {
      location.href = "add_return.php";//出発登録画面に行く
    }
    function oil() {
      location.href = "add_oil.php";//給油登録画面に行く
    }
    function private() {
      location.href = "private.php";//私用利用登録画面に行く
    }
    function login() {
      location.href = "login.php";//ログイン画面に行く
    }
    function searchform() {
      location.href = "searchform.php";//検索画面に行く
    }
  </script>';
    break;
}
 ?>

 <div id="out"></div>
 <!--ホームに追加を促す画像を表示-->
 <script type="text/javascript">// src="standalone.js"
 var output = document.getElementById("out");
 //ホーム画面から単独で開かなかった場合に以下のものを表示
 var no_output = function() {
 output.innerHTML='';
 }
 if (window.navigator.standalone == false) {
   output.innerHTML = '<div id="store-balloon">'+'<div id="store-balloon-icon"></div>'+'<h6 id="store-balloon-text">ホーム画面に<br />追加</h6></div>';
   setTimeout(no_output, 5000);//5秒で表示を消す
 }
 </script>
</div>
</body>
</html>
