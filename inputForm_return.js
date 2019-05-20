function checkForm(){

  var flag = 0;

      //以下、フォーム入力が空欄の場合の条件
    if(document.form.odometer.value == ""){
      flag = 1;
      }
    else if(document.form.place.value == ""){
      flag = 1;
      }
      else if(document.form.highway_fee.value == ""){
        flag = 1;
        }

    if(flag){
  		window.alert('必須項目に未入力がありました'); // 入力漏れがあれば警告ダイアログを表示
  		return false; // 送信を中止
    	}
    else{
    	return true; // 送信を実行
      }
    }
