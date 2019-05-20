//window.onload = function () {
var output = document.getElementById("out");
//ホーム画面から単独で開かなかった場合に以下のものを表示
if (window.navigator.standalone == false) {
  output.innerHTML = '<div id="store-balloon">'+'<div id="store-balloon-icon"></div>'+'<h6 id="store-balloon-text">ホーム画面に<br />追加</h6></div>';
  alert("運転日報WEB版はホーム画面に追加して使用すると見やすくなります。");
//};
}
//読み込み後にJSアラートの表示は後日検証。
