function increaseFont(){
	// get the current value from the cookie
	var cv1 = readCookie("ihtuserdata");
	if (cv1){
		cookieValues = parseCookie(cv1);
	} else {
		cookieValues[1] = "12";
	}
	var currentFontSize = parseInt(cookieValues[1]);
	var newFontSize = currentFontSize + 2;
	if (newFontSize > 18){
		newFontSize = 18;
	}
	// save new value in the cookie
	var cv = "columnMode:" + cookieValues[0] + "&fontSize:" + newFontSize + "&clippings:" + cookieValues[2];
	createCookie("ihtuserdata",cv,7);
	
	// re-draw article
	lineHeight = newFontSize + Math.round(.3 * newFontSize);
	for (var i = 0; i < 3; i++){
		obj = document.getElementById("at" + i);
		obj.style.fontSize = newFontSize + "px";
		obj.style.lineHeight = lineHeight + "px";
	}			
}

function decreaseFont(){
	// get the current value from the cookie
	var cv1 = readCookie("ihtuserdata");
	if (cv1){
		cookieValues = parseCookie(cv1);
	} else {
		cookieValues[1] = "12";
	}
	var currentFontSize = parseInt(cookieValues[1]);
	var newFontSize = currentFontSize - 2;
	if (newFontSize < 12){
		newFontSize = 12;
	}
	// save new value in the cookie
	var cv = "columnMode:" + cookieValues[0] + "&fontSize:" + newFontSize + "&clippings:" + cookieValues[2];
	createCookie("ihtuserdata",cv,7);
	// re-draw article
	lineHeight = newFontSize + Math.round(.3 * newFontSize);
	for (var i = 0; i < 3; i++){
		obj = document.getElementById("at" + i);
		obj.style.fontSize = newFontSize + "px";
		obj.style.lineHeight = lineHeight + "px";
	}			
}

var doAlerts=true;
function changeSheets(whichSheet){
  whichSheet=whichSheet-1;
  if(document.styleSheets){
    var c = document.styleSheets.length;
    for(var i=1;i<c;i++){
      if(i!=whichSheet){
        document.styleSheets[i].disabled=true;
      }else{
        document.styleSheets[i].disabled=false;
      }
    }
  }
}

function ll(){
	alert(document.styleSheets[0].href);
	alert(document.styleSheets[1].href);
	alert(document.styleSheets[2].href);
	alert(document.styleSheets[3].href);
}
