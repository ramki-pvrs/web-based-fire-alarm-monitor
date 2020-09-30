<!DOCTYPE html>
<html>
<head>
	<title></title>
	<style type="text/css">
		.card {
 			 box-shadow: 0 10px 20px rgba(0,0,0,0.19), 0 6px 6px rgba(0,0,0,0.23);
		}
	</style>
  <?php include ("head.php"); ?>
</head>
<body>
<h1 class="text-center mt-5">Fire Alarm System Login</h1>
<div class="container pt-3">
  <div class="row justify-content-sm-center">
    <div class="col-sm-10 col-md-6">
      <div class="card border-info">
        <div class="card-header">Login to continue</div>
        <div class="card-body">
          <div class="row">
            <div class="col-md-8">
              <form class="form-signin">
                <input type="text" class="form-control mb-2" placeholder="Email" required autofocus>
                <input type="password" class="form-control mb-2" placeholder="Password" required>
                <button class="btn btn-lg btn-primary btn-block mb-1" type="submit">Sign in</button>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
</body>
</html>