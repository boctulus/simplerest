AdminLTE
========

Con este tema es necesario re-implementar algunos componentes y otros como el accordion no funcionan bien pero en tal caso simplemente no incluir los estilos si son componentes del frontoffice (home).

# Alert

Los alerts para Admin LTE aceptan de forma opcional un "title".

Ej:

	echo tag('alert')
	->content('OK')
	->title('Perfecto!')
	->success()
	->dismissible(true);

	echo tag('alert')
	->content('Algo para tener en cuenta.')
	->title('Cuidado!')
	->warning()
	->dismissible(true);

	echo tag('alert')
	->content('Algo salió mal.')
	->title('Error!')
	->danger()
	->dismissible(true);

	// Sin título
	echo tag('alert')
	->content('Algo salió mal.')
	->danger()
	->dismissible(true);

	echo AdminLte::alert(content: 'Some content', attributes: ['warning', 'dismissible']);


# Callout

Son el equivalente "light" de las cards. Color y title son opcionales.

Ej:

	echo tag('callout')->color('danger')
	->content('There is a problem that we need to fix. A wonderful serenity has taken possession of my entire
	soul, like these sweet mornings of spring which I enjoy with my whole heart.');

O con title.

Ej:

	echo tag('callout')->color('danger')
	->content('There is a problem that we need to fix. A wonderful serenity has taken possession of my entire
	soul, like these sweet mornings of spring which I enjoy with my whole heart.')
	->title('Some title');


# Ion Sliders

Estos sliders utilizan un plugin y básicamente se diferencian de un inputRange en que tienen un máximo, un mínimo y dentro de este rango el usuario puede elegir el suyo.

Hay de dos tipos: "single" y "double". Por defecto son "single" siendo similares a un inputRange o sea sin sub-rango.

Ej:

	<div class="col-sm-6">
		<?= tag('ionSlider')->id('range_1') ?>
	</div>

El código JavaScript mínimo es:

	$('#range_1').ionRangeSlider({
    })

Sino se especificara mínimo o máximo, los valores por defecto son 10 y 100. Se pueden especificar más paramátros.

Ej:

	$('#range_1').ionRangeSlider({
		min     : 0,
		max     : 10,
		type    : 'single',
		step    : 0.1,
		postfix : ' mm',
		prettify: false,
		hasGrid : true
	})

Para el caso de los tipo "double" se pasa también un "from" y "to".

Ej:

	$('#range_1').ionRangeSlider({
		min     : 0,
		max     : 5000,
		from    : 1000,
		to      : 4000,
		type    : 'double',
		step    : 1,
		prefix  : '$',
		prettify: false,
		hasGrid : true
	})

Es posible fijar el valor de inicio ("from") con el "value" en el ionSlider (ya que hereda de un inputText) cuando es de tipo "single". Para los tipo "double" y en general sería con el "from".

Ej:

	<!--
		Solo tiene sentido si es de tipo "single". Sino usar from.
	-->
	<div class="col-sm-6">
		<?= tag('ionSlider')->id('range_1')->value(1400) ?>
	</div>


Con el código JavaScript mínimo es posible inicializar todo desde el Builder en el backend.

Ej:

	<?=
		/*
			Slider "simple"
		*/

		tag('ionSlider')->id('range_1')
          ->min(0)
          ->max(6000)
          ->value(50) 
          ->postfix(" &euro;")
          ->step(10)
	?>

Recordemos que value() y from() para el caso de los sliders "simples" son equivalentes:

	<?=
		/*
			Slider "simple"
		*/

		tag('ionSlider')->id('range_1')
          ->min(0)
          ->max(6000)
          ->from(50) 
          ->postfix(" &euro;")
          ->step(10)
	?>

Para el caso de los sliders "dobles", especificaremos un sub-rango inicial con from y to.

Ej:

	<?= 
		/*
			Slider "doble"
		*/
          
		tag('ionSlider')->id('range_2')
		->min(-100)
		->max(400)          
		->postfix(" C")
		->step(1)

		->type('double')
		->from(100)
		->to(200)
		
    ?>

Se implementó de forma muy primitiva la inclusión del código JS mínimo de inicialización del componente.

Recordar que debe incluirse el JS del plugin:

	<?php
    	View::js_file(ASSETS_PATH . 'adminlte/plugins/ion-rangeslider/js/ion.rangeSlider.min.js');
    ?>


# Ribbons

Admin LTE ofrece "cintas" o ribbons que se pueden aplicar a un contenedor.

Código base:

	<div class="position-relative bg-gray"> 
	<!-- body -->
	<div class="ribbon-wrapper">
		<div class="ribbon bg-primary">
		Ribbon
		</div>
	</div>
	
	<!-- footer -->
	Ribbon Default <br />
	<small>.ribbon-wrapper.ribbon-lg .ribbon</small>
	</div>


Se pueden agrandar: ribbon-lg, ribbon-xl

  <div class="ribbon-wrapper ribbon-lg">
    <div class="ribbon bg-info">
      Ribbon Large
    </div>
  </div>

El texto del ribbon también se puede agrandar: text-lg, text-xl

	<div class="ribbon-wrapper ribbon-lg">
	<div class="ribbon bg-success text-lg">
		Ribbon
	</div>
	</div>

Sobre imágenes

	<div class="position-relative">
	<!-- header -->
	<img src="../../dist/img/photo1.png" alt="Photo 1" class="img-fluid">
	
	<!-- body -->
	<div class="ribbon-wrapper ribbon-lg">
		<div class="ribbon bg-success text-lg">
		Ribbon
		</div>
	</div>
	</div>


Implementando ribbons con el builder

De mínima se necesita un texto para la cinta (title) y un contenido (body).

Como punto de partida:

	<?=

		tag('ribbon')
		->bg('gray')
		->style('height: 100px')
		->title(
		tag('ribbonTitle')->content('Ribbon')->bg('primary')
		)
		->body(
		'Ribbon Default <br />
		<small>.ribbon-wrapper.ribbon-lg .ribbon</small>'
		)

	?>

O con imágenes

En este caso la imágen va en el *header* habiendo header, title y body.

Ej:
	
	<?=

		tag('ribbon')
		->bg('gray')
		//->style('min-height: 300px')
		->class('mt-3')  
		->header(
			tag('img')->src(asset('img/photo2.png'))->class('img-fluid py-3')
		)                  
		->title(
			tag('ribbonTitle')->content('Ribbon')->bg('danger')
		)
		->body(
			'Ribbon Default <br />
			<small>.ribbon-wrapper.ribbon-lg .ribbon</small>'
		)

	?>

Para alargar la cinta o ensancharla conjuntamente con el tamaño del texto use size() y textSize() respectivamente con 'lg' o 'xl' sobre el ribbonTitle.

Ej:

	<?=

		tag('ribbon')
		->bg('gray')
		->style('min-height: 300px')
		->class('mt-3')  
		->header(
			tag('img')->src(asset('img/photo2.png'))->class('img-fluid py-3')
		)                  
		->title(
			tag('ribbonTitle')->content('Ribbon')->bg('danger')->size('xl')->textSize('xl')
		)
		->body(
			'Ribbon Default <br />
			<small>.ribbon-wrapper.ribbon-lg .ribbon</small>'
		)

	?>


# Custom File

Ej:

	<div class="form-group">
		<?php
		echo tag('customFile')
		->id('my_file')
		->placeholder('Elija los archivos');
		?>
	</div>

Se requiere incluir el plugin:

    js_file('vendors/adminlte/plugins/bs-custom-file-input/bs-custom-file-input.min.js')

Más
https://github.com/Johann-S/bs-custom-file-input


# Input validation

Con Admin LTE las clases para marcar un inputText como valid, invalid o con un warning son is_valid, is_invalid e is_warning respectivamente.

	<?= tag('inputText')->class("is-valid")->placeholder("Enter ...")->id("id1");   ?>

	<?= tag('inputText')->class("is-warning")->placeholder("Enter ...")->id("id2"); ?>

	<?= tag('inputText')->class("is-invalid")->placeholder("Enter ...")->id("id3"); ?>


