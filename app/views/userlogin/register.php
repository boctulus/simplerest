
<!-- Sign up -->
    
<?php
	include_css(WIDGETS_PATH . 'login/login.css');
?>

<div class="row vh-100 d-flex  align-items-center">
	<div class="col-xs-12   col-sm-8 offset-sm-2    col-md-6 offset-md-3    col-lg-4 offset-lg-4">	

		<?php
 
		use simplerest\core\libs\HtmlBuilder\Bt5Form;
		use simplerest\core\libs\HtmlBuilder\Tag;

		Tag::registerBuilder(\simplerest\core\libs\HtmlBuilder\Bt5Form::class);

		Bt5Form::setIdAsName();

		echo tag('card')
		->header(tag('cardTitle')->text('Registro'))
		->body([
			tag('inputGroup')->content([
				Bt5Form::span('<i class="fas fa-at"></i>', [
					'class' => 'input-group-text'
				]),
	
				tag('email')
				->id("email")	
				->placeholder("E-mail")
				->required("required")
				->style("font-size:1rem")

			])->class("mb-3"),
	
			tag('inputGroup')->content([
				Bt5Form::span('<i class="fas fa-user"></i>', [
					'class' => 'input-group-text'
				]),

				tag('inputText')
				->id("username")	
				->placeholder("Nombre de usuario")
				->required("required")
				->style("font-size:1rem")

			])->class("mb-3"),
			
			tag('inputGroup')->content([
				Bt5Form::span('<i class="fas fa-key"></i>', [
					'class' => 'input-group-text'
				]),
	
				tag('password')
				->id("password")	
				->placeholder("Password")
				->required("required")
				->style("font-size:1rem")

			])->class("mb-3"),
	
			tag('inputGroup')->content([
				Bt5Form::span('<i class="fas fa-key"></i>', [
					'class' => 'input-group-text'
				]),
				
				tag('password')
				->id("password_confirmation")	
				->placeholder("Password confirmación")
				->required("required")
				->style("font-size:1rem")

			])->class("mb-3"),
			'<div style="margin-bottom:1em;">
			<a href="login/rememberme">Recordar contraseña</a>
			</div>	

			<div class="form-group">
				<button type="submit" class="btn btn-primary btn-lg btn-block login-btn w-100" onClick="register()">Enviar</button>
			</div>
			
			<div class="mt-3" style="text-align:right;">
				Ya registrado? <a href="login">Ingrese</a>
			</div>',
			
			'<!-- en realidad probablemente sea un pesimo lugar para poner errores -->
			<span id="registerError" class="mt-3"></span>'		
		])->class('card-primary card-outline');

		?>

		
	</div>
</div>
