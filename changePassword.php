<?php
session_start();
include_once dirname(__FILE__).'/static/public/CSRF-Protector-PHP/libs/csrf/csrfprotector.php';
//Initialise CSRFGuard library
csrfProtector::init();
//session_start();
//echo(__DIR__);
include dirname(__FILE__).'/dbconnect.php';
?>

<?php
$error=''; 
if(isset($_GET['loggedInUser'])){
    $loggedInUser = $_GET['loggedInUser'];
}
if (isset($_POST['submit'])) {
	if (empty($_POST['username']) || empty($_POST['oldpassword'])) {
		$error = "Username or Password is empty";
	} else {
		$selectUserQuery="SELECT password,role, status
		                      FROM hootus.user AS u
		                      LEFT JOIN role AS r ON r.id = u.role_id
		                      WHERE userID = :userID;";
	    $selectUser=$con->prepare($selectUserQuery);
	    $selectUser->bindParam(':userID', $_POST['username'], PDO::PARAM_STR);
	    $selectUser->execute();
	    $userData=$selectUser->fetchAll(PDO::FETCH_ASSOC);
	    $stored_password = $userData[0]["password"];
	    $logged_user_role = $userData[0]["role"];
	    $user_status = $userData[0]["status"];
        //echo($_POST['oldpassword'].':::'.$stored_password);
	    if(password_verify($_POST['oldpassword'], $stored_password)) {
            //echo($_POST['oldpassword'].':::'.$stored_password);
	    	$newpassword=password_hash($_POST['newpassword'], PASSWORD_BCRYPT);
	    	$sql="UPDATE hootus.user SET password=:newpassword, status=1
		          WHERE userID = :userID;";
		    $sql=$con->prepare($sql);
		    $sql->bindParam(':newpassword', $newpassword);
		    $sql->bindParam(':userID', $_POST['username'], PDO::PARAM_STR);
		    $con->beginTransaction(); 
		    $sql->execute();
		    $con->commit();
			header("location: ../index.php?errorMssg=".urlencode("Login again with changed password!"));
			exit;
		} else {
			$error = "Either Username or Old Password is invalid";
		}
	}
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>FIRE-PANEL</title>
    <!-- <link href="static/appspecific/css/login_style.css" rel="stylesheet" type="text/css"> -->
    <script type="text/javascript" src="static/public/js/jquery-3.3.1.min.js"></script>
    <link rel="stylesheet" href="static/public/Bootstrap/css/bootstrap.min.css">
    <script type="text/javascript" src="static/public/Bootstrap/js/bootstrap.min.js"></script>
    <style type="text/css">
    .card {
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.19), 0 6px 6px rgba(0, 0, 0, 0.23);
    }
    </style>
</head>

<body>
    <div id="main">
        <h1 class="text-center mt-5">Fire Alarm System Change Password</h1>
        <div id="login">
            <div class="container pt-3">
                <div class="row justify-content-sm-center">
                    <div class="col-sm-10 col-md-6">
                        <div class="card border-info">
                            <div class="card-header">Change Password:</div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-8">
                                        <form action="" method="post">
                                            <!-- <label>Login Name:</label> -->
                                            <input type="text" class="form-control mb-2" value='<?php echo $loggedInUser?>' id="name" name="username" required readonly="readonly">
                                            <!-- <label>Old Password:</label>  -->
                                            <input type="password" class="form-control mb-2" placeholder="Enter Old Password" id="oldpassword" name="oldpassword" required>
                                            <!--  <label>New Password:</label>  -->
                                            <input type="password" class="form-control mb-2" placeholder="Enter New Password" id="newpassword" name="newpassword" required>
                                            <!-- <label>Confirm Password:</label>  -->
                                            <input type="password" class="form-control mb-2" placeholder="Confirm New Password" id="confirm_password" name="confirmpassword" required>
                                            <input name="submit" class="btn btn-lg btn-primary btn-block mb-1" type="submit" value="Change Password">
                                        </form>
                                        <span><?php echo $error; ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        var newpassword = document.getElementById("newpassword");
        var confirm_password = document.getElementById("confirm_password");

        function validatePassword() {
            if (newpassword.value != confirm_password.value) {
                confirm_password.setCustomValidity("Passwords Don't Match");
            } else {
                confirm_password.setCustomValidity('');
            }
        }
        newpassword.onchange = validatePassword;
        confirm_password.onkeyup = validatePassword;
    </script>
</body>

</html>