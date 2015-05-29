console.log('WebExcess.Notifications Start');

if (typeof document.addEventListener === 'function') {
	document.addEventListener('Neos.ContentModuleLoaded', function(event) {

		function webExcessNotificationsCheck() {
			var xmlhttp;
			if (window.XMLHttpRequest) {// code for IE7+, Firefox, Chrome, Opera, Safari
				xmlhttp=new XMLHttpRequest();
			} else {// code for IE6, IE5
				xmlhttp=new ActiveXObject('Microsoft.XMLHTTP');
			}
			xmlhttp.onreadystatechange=function() {
				if (xmlhttp.readyState==4 && xmlhttp.status==200) {
					eval(xmlhttp.responseText);
				}
			}
			xmlhttp.open('GET', '/webexcess/notifications/get',true);
			xmlhttp.send();
		}
		setInterval(webExcessNotificationsCheck, 8000);

	}, false);
}
