<!-- Login -->
    
    <style>
    .login-form {
	width: 340px;
	margin: 30px auto;
}

.login-form form {
	margin-bottom: 15px;
    background: #f7f7f7;
    box-shadow: 0px 2px 2px rgba(0, 0, 0, 0.3);
    padding: 30px;
}

.login-form h2 {
    margin: 0 0 15px;
}

.login-form .hint-text {
	color: #777;
	padding-bottom: 15px;
	text-align: center;
}

.form-control, .btn {
    min-height: 50px;
    border-radius: 2px;
	font-size: 18px;
}

.login-btn {        
    font-weight: bold;
}

.or-seperator {
	font-size: 18px;
    margin: 20px 0 10px;
    text-align: center;
    border-top: 1px solid #ccc;
}

.or-seperator i {
    padding: 0 10px;
    background: #fff;
    position: relative;
    top: -15px;
    z-index: 1;
}

.social-btn .btn {
    margin: 12px 0;
    font-size: 18px;
    text-align: left; 
    line-height: 40px;       
}

.social-btn .btn i {
	float: left;
	margin: 11px 15px  0 5px;
    min-width: 15px;
}

.input-group-addon .fa{
	font-size: 20px;
}    </style>
    
<div class="row vh-100 d-flex  align-items-center">
	<div class="col-xs-12 col-sm-6 offset-sm-3 col-md-4 offset-md-4">

		    <style>
    <br />
<b>Warning</b>:  include(D:\www\simplerest\public\assets\vendors/adminlte/css/adminlte.css): Failed to open stream: No such file or directory in <b>D:\www\simplerest\app\core\helpers\view.php</b> on line <b>205</b><br />
<br />
<b>Warning</b>:  include(): Failed opening 'D:\www\simplerest\public\assets\vendors/adminlte/css/adminlte.css' for inclusion (include_path='.;C:\php\pear') in <b>D:\www\simplerest\app\core\helpers\view.php</b> on line <b>205</b><br />
    </style>
    <div class="card card-primary card-outline"><div class="card-header"><h5 class="card-title" style="font-size: 300%;">Login</h5></div> <div class="card-body"><div class="social-btn">
				<a href="facebook/login" class="btn btn-primary w-100"><i class="fa fa-facebook"></i> Sign in with <b>Facebook</b></a>
				<a href="google/login" class="btn btn-danger w-100"><i class="fa fa-google"></i> Sign in with <b>Google</b></a></div> <div class="or-seperator"><i>or</i></div> <div class="input-group mb-3"><span class="input-group-text"><i class="fas fa-user"></i></span><input class="form-control" type="text" id="email_username" placeholder="email o username" required="required"></input></div> <div class="input-group mb-3"><span class="input-group-text"><i class="fas fa-key"></i></span><input class="form-control" type="password" id="password" placeholder="Password" required="required"></input><span class="input-group-text" onclick="password_show_hide();">
				<i class="fas fa-eye" id="show_eye"></i>
				<i class="fas fa-eye-slash d-none" id="hide_eye"></i>
				</span></div> <div style="margin-bottom:1em;">
				<a href="login/rememberme">Recordar contraseña</a>
			</div>	

			<div class="form-group">
				<button type="submit" class="btn btn-primary btn-lg btn-block login-btn w-100" onClick="login()">Login</button>
			</div>
			
			<div class="mt-3" style="text-align:right;">
				No registrado? <a href="login/register">regístrese</a>
			</div></div> </div>
	</div>
</div>
