<!-- Rememberme -->
    
<?php	
	css_file('css/login/login.css');
?>

<div class="row vh-100 d-flex  align-items-center">
	<div class="col-xs-12   col-sm-8 offset-sm-2    col-md-6 offset-md-3    col-lg-4 offset-lg-4">
		<?php

        use simplerest\core\libs\HtmlBuilder\Bt5Form;
        use simplerest\core\libs\HtmlBuilder\Tag;

        Tag::registerBuilder(\simplerest\core\libs\HtmlBuilder\Bt5Form::class);

        Bt5Form::setIdAsName();
        
        echo tag('card')
        ->header(tag('cardTitle')->text('Recordar contraseña'))
        ->body([
            '<div style="text-align:right; margin-bottom:1em;">
                Tiene cuenta? <a href="login">Ingresar</a>
            </div>',

            tag('inputGroup')->content([
                Bt5Form::span('<i class="fas fa-envelope"></i>', [
                    'class' => 'input-group-text'
                ]),

                tag('email')
				->id("email")	
				->placeholder("E-mail")
				->required("required")
				->style("font-size:1rem")

            ])->class("input-group mb-3"),		        

            '<div class="form-group mb-3">
                <button type="submit" class="btn btn-primary btn-lg btn-block login-btn w-100" onClick="rememberme()">Recuérdame</button>
            </div>

            No registrado? <a href="login/register">regístrese</a>'            

        ])->class('card-primary card-outline');
        
        ?>
        
        
	</div>
</div>
