// autoload
$(document).ready(function(){
	timeZone();
});

// set users timezone
function timeZone() {
	var usertime = new Date();
	usertime.setFullYear(usertime.getFullYear() + 1);
	var dateofcookie = usertime.toUTCString();
	var usertimezone = usertime.getTimezoneOffset()/60;
	document.cookie = "timezone = " + usertimezone + ";path=/;expires=" + dateofcookie + "";	
}