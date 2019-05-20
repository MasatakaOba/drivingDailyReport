function geoFindMe() {
  var output = document.getElementById("out");
  var latitude = document.getElementById("latitude");
  var longitude = document.getElementById("longitude");

  if (!navigator.geolocation){
    output.innerHTML = "Geolocationはこのブラウザでは使用できません。";
    return;
  }
  //座標取得成功時の処理
  function success(position) {

    //座標の取得・定義
    var latitude_js  = position.coords.latitude;
    var longitude_js = position.coords.longitude;

    //座標の表示　（変更点：緯度経度の表示をなくしました）
    output.innerHTML = /*'緯度: ' + latitude_js + '° <br>経度: ' + longitude_js + '°<br>'+*/"<li class='small'>※現在地と違う場合は「現在地登録」を再度押して下さい。</li>";

    //画像の取得
    var img = new Image();
    img.src = "https://maps.googleapis.com/maps/api/staticmap?center=" + latitude_js + "," + longitude_js + "&zoom=16&size=300x180&markers=" + latitude_js + "," + longitude_js + "&key=AIzaSyCagtcw0f_Wd5siCcs63iss_Ld8rs6wcX0";

    //画像の表示
    output.appendChild(img);

    //取得した座標を値としてidのvalueに渡す
    document.form.latitude.value = position.coords.latitude;
    document.form.longitude.value = position.coords.longitude;

  };
　//座標取得失敗時の処理
  function error() {
    output.innerHTML = "位置情報が取得できません。";
  };
  //座標取得中の処理
  output.innerHTML = "位置情報取得中です。";
  //現在地取得の関数
  navigator.geolocation.getCurrentPosition(success, error);
}