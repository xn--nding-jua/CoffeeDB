var countDownSeconds = 120;

var x = setInterval(function() {
  countDownSeconds-=1;
  var min = Math.floor(countDownSeconds/(60));
  var sec = Math.floor(countDownSeconds % 60);
  document.getElementById("countdowncounter").innerHTML = "Refresh in " + min + ":" + sec;
 
  if (countDownSeconds < 0) {
    clearInterval(x);
    document.getElementById("countdowncounter").innerHTML = "Refresh";
  }
}, 1000);
