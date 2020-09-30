<?php
session_start();
include_once dirname(__FILE__).'/hootus/static/public/CSRF-Protector-PHP/libs/csrf/csrfprotector.php';
//Initialise CSRFGuard library
csrfProtector::init();
//session_start();
//echo(__DIR__);

if(isset($_SESSION['logged_user'])){
	header("location: hootus/alarms_page.php");
	exit; //if session has logged in user, it goes to alarms_page.php and exits here
	//if logged in user is not found, it displays the page below and submit on the same page is taken by the php part after html content
}
include dirname(__FILE__).'/hootus/dbconnect.php';
?>

<?php
$error=''; 
if(isset($_GET['errorMssg'])){
	$error = $_GET['errorMssg'];
}
if (isset($_POST['submit'])) {
	if (empty($_POST['username']) || empty($_POST['password'])) {
		$error = "Username or Password is empty";
	} else {
		$selectUserQuery="SELECT u.id AS uid, u.firstName, CONCAT(u.firstName, ' ', u.lastName) AS userName, r.id AS rid, password, role, status
		                      FROM hootus.user AS u
		                      LEFT JOIN role AS r ON r.id = u.role_id
		                      WHERE userID = :userID";
	    $selectUser=$con->prepare($selectUserQuery);
	    $selectUser->bindParam(':userID', $_POST['username'], PDO::PARAM_STR);
	    $selectUser->execute();
	    $userData=$selectUser->fetchAll(PDO::FETCH_ASSOC);
	    $stored_password = $userData[0]["password"];
	    $logged_user_role = $userData[0]["role"];
	    $logged_user_role_id = $userData[0]["rid"];
	    $logged_user_id = $userData[0]["uid"];
	    $logged_userName = $userData[0]["userName"];
	    $logged_userFirstName = $userData[0]["firstName"];
	    $user_status = $userData[0]["status"];

	    if($user_status == 1) {
			if(password_verify($_POST['password'], $stored_password)) {
				$_SESSION['logged_user']=filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
				$_SESSION['logged_user_role']=$logged_user_role; // Initializing Session
				$_SESSION['logged_user_id']=$logged_user_id; // Initializing Session
				$_SESSION['logged_userName']=$logged_userName;
				$_SESSION['logged_userFirstName']=$logged_userFirstName;
				$_SESSION['logged_user_role_id']=$logged_user_role_id; // Initializing Session
				header("location: hootus/alarms_page.php"); // Redirecting to landing page on successful login
				exit;
			} else {
				$_SESSION['logged_user']=NULL;
				$_SESSION['logged_user_role']=NULL;
				$_POST['username'] = NULL;
				$_POST['password'] = NULL;
				$error = "Either Username or Password is invalid";
			}
		} elseif($user_status == 0) {
			//echo("<b>Access restricted! Check with your administrator for your access permissions!</b>");
			$_SESSION['logged_user']=NULL;
			$_SESSION['logged_user_role']=NULL;
			$_POST['username'] = NULL;
			$_POST['firstname'] = NULL;
			$_POST['password'] = NULL;
			$_POST['submit'] = NULL;
			header("Location:index.php?errorMssg=".urlencode("Access restricted! Check with administrator for your access permissions!"));
			exit;
		} else {
			header("location: hootus/changePassword.php?loggedInUser=".urlencode($_POST['username'])); // Redirecting to changePassword page if first time login by user
			exit;
		}
	}
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>FIRE-PANEL</title>
    <!-- <link href="hootus/static/appspecific/css/login_style.css" rel="stylesheet" type="text/css"> -->
    <script type="text/javascript" src="hootus/static/public/js/jquery-3.3.1.min.js"></script>
    <link rel="stylesheet" href="hootus/static/public/Bootstrap/css/bootstrap.min.css">
    <script type="text/javascript" src="hootus/static/public/Bootstrap/js/bootstrap.min.js"></script>
    <style type="text/css">
		.card {
 			 box-shadow: 0 10px 20px rgba(0,0,0,0.19), 0 6px 6px rgba(0,0,0,0.23);
		}
	</style>
</head>

<body>
    <div id="main">
        <div id="login">
            <h1 class="text-center mt-5">Fire Alarm System Login</h1>
            <div class="container pt-3">
                <div class="row justify-content-sm-center">
                    <div class="col-sm-10 col-md-6">
                        <div class="card border-info">
                            <div class="card-header">Login to continue</div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-8">
                                        <form action="" method="post">
                                            <!-- <label>Login Name :</label> -->
                                            <input id="name" class="form-control mb-2" name="username" placeholder="username" type="text" required autofocus>
                                           <!--  <label>Password :</label> -->
                                            <input id="password" class="form-control mb-2" name="password" placeholder="**********" type="password" required>
                                            <input name="submit" class="btn btn-lg btn-primary btn-block mb-1" type="submit" value="Login ">
                                            <!-- <button class="btn btn-lg btn-primary btn-block mb-1" type="submit">Sign in</button> -->
                                        </form>
                                        <span style="color:red;"><strong><?php echo $error; ?></strong></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>