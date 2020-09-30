<?php
//http://www.formget.com/login-form-in-php/
//trial in users_page.php and users.js; look for session_logout.php in users.js
session_start();
include_once __DIR__ .'/static/public/CSRF-Protector-PHP/libs/csrf/csrfprotector.php';
//Initialise CSRFGuard library
csrfProtector::init();

if(isset($_REQUEST['dbData']['actionfunction']) && $_REQUEST['dbData']['actionfunction']!=''){
  $actionfunction=$_REQUEST['dbData']['actionfunction'];
  call_user_func($actionfunction,$_REQUEST,$con);
}

function logout($data,$con) {
	if($_SESSION['admin_user'] == 'rsrinivasan') // Destroying All Sessions
	{
		if (session_destroy()) {
			header("Location: ../index.php"); // Redirecting To Home Page
		}
	} else {
		if (session_destroy()) {
			//you are in php context; the next two lines are in javascript world
			// change the logout as button, take a event on click, close the windows, make an ajax call to this php file destroy the session and 
			// you can exit

			//you can get the opened window object if you know the namell window.open with empty link and name passed to it will get you that window object
			//var userlistWindow = window.open('', 'userlist');
			//userlistWindow.close();

			//https://medium.com/@bluepnume/every-known-way-to-get-references-to-windows-in-javascript-223778bede2d
			//https://stackoverflow.com/questions/6375897/call-to-a-php-logout-script-using-jquery
			//https://www.sitepoint.com/community/t/logout-with-jquery/39836

			//also learn session_unset(); session_destroy();
			header("Location: ../index.php"); // Redirecting To Home Page
		}
		
	}
}
?>