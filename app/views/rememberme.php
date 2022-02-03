<!-- Rememberme -->
    
<style type="text/css">
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
        margin: 20px 0 10px;
        text-align: center;
        border-top: 1px solid #ccc;
    }
    .or-seperator i {
        padding: 0 10px;
        background: #f7f7f7;
        position: relative;
        top: -11px;
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
	}
</style>

<div class="row vh-100 d-flex  align-items-center">
	<div class="col-xs-12 col-sm-6 offset-sm-3 col-md-4 offset-md-4">
		<h1 style="font-size: 3em; padding-bottom: 0.5em;">Recuérdame</h1>

		<?php

		use simplerest\core\libs\Bt5Form;

		$f = new Bt5Form();

		echo $f

        ->insert('
            <div style="text-align:right; margin-bottom:1em;">
                Tiene cuenta? <a href="login">Ingresar</a>
            </div>
        ')
        
		->div(function($form){
			$form->span('<i class="fas fa-envelope"></i>', [
				'class' => 'input-group-text'
			]);
			$form->email(
				id:"email",
				placeholder:"E-mail",
				required:"required"
			);
		}, [
			"class" => "input-group mb-3"
		])

		

		->render(action:"#", onsubmit:"return false;");
		?>		

		<div class="form-group mb-3">
				<button type="submit" class="btn btn-primary btn-lg btn-block login-btn w-100" onClick="rememberme()">Recuérdame</button>
        </div>

        No registrado? <a href="login/register">regístrese</a>
	</div>
</div>
