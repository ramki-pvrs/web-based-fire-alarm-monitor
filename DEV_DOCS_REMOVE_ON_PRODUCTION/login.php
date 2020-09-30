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
<h1 class="text-center mt-5">Horizontal layout</h1>
<div class="container pt-3">
  <div class="row justify-content-sm-center">
    <div class="col-sm-10 col-md-6">
      <div class="card border-info">
        <div class="card-header">Sign in to continue</div>
        <div class="card-body">
          <div class="row">
            <div class="col-md-4 text-center">
              <img src="https://placeimg.com/128/128/tech/sepia">
              <h4 class="text-center">Hunger & Debt Ltd</h4>
            </div>
            <div class="col-md-8">
              <form class="form-signin">
                <input type="text" class="form-control mb-2" placeholder="Email" required autofocus>
                <input type="password" class="form-control mb-2" placeholder="Password" required>
                <button class="btn btn-lg btn-primary btn-block mb-1" type="submit">Sign in</button>
                <label class="checkbox float-left">
              <input type="checkbox" value="remember-me">
              Remember me
            </label>
                <a href="#" class="float-right">Need help?</a>
              </form>
            </div>
          </div>
        </div>
      </div>
      <a href="#" class="float-right">Create an account </a>
    </div>
  </div>
</div>
</body>
</html>