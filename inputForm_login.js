function checkForm(){

  var flag = 0;
  var getText = document.form.shimeicd.value;

    if(document.form.shimeicd.value == ""){
      flag = 1;
      }
    if(flag){
  		window.alert('必須項目に未入力がありました'); // 入力漏れがあれば警告ダイアログを表示
  		return false; // 送信を中止
    	}
    if (getText.length > 7) {
        alert("文字数が制限を越えています");
  		  return false; // 送信を中止
      }
    if (getText.length < 7) {
        alert("文字数が足りません");
  		  return false; // 送信を中止
      }
    else{
    	return true; // 送信を実行
      }
    }
