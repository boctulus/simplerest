# HTML & Form builder

El framework provee de las clases HTML, Form y Bt5Form para la construcción programática de HTML. 

Ej:

	echo Bt5Form::p(text:"Hola mundo cruel");
	echo Bt5Form::tag(type:'hr', style:'color:cyan');
	echo Bt5Form::inputColor(name:'my_color', text:'Color', id:'c1');

O usando el helper tag(),

    echo tag('p')->text("Hola mundo cruel");
	echo tag('hr')->style('color:cyan');
	echo tag('inputColor')->name('my_color')->text('Color')->id('c1');
	
Se puede ver que para renderizar el tag <hr> en el primer caso se usa una sintáxis un poco distinta y esto es porque se da el caso de que no existe el método correspondiente en la clase Bt5Form o las clases de las que hereda. En estos casos siempre que el tag no deba anidar otros se puede usar Form::tag() o en este caso Bt5Form::tag()

Lo anterior renderizará algo como:

	<p>Hola mundo cruel</p>
	<hr style="color:cyan">

	<input name="my_color" id="my_color" type="color" class="form-control form-control-color" colorpick-eyedropper-active="true">
	<label for="c1" class="form-label">Color</label>


# Compatibilidad con PHP 7.4 y 8.1+

En PHP 8.1+ es posible hacer lo siguiente:

	Tag::registerBuilder(\Boctulus\Simplerest\Core\Libs\HtmlBuilder\Bt5Form::class);

	$html = tag('note')
	->text('<strong>!!! Note secondary:</strong> Lorem, ipsum dolor sit ...	soluta porro?')
	->color('secondary')
	->class('mb-5');

Sin embargo en PHP 7.4, no es posible usar "named arguments" y por tanto debe usarse asi:

	$html = Bt5Form::note(
	'<strong>!!! Note secondary:</strong> Lorem, ipsum dolor sit ...	soluta porro?', 
	[
		'color' => 'secondary',
		'class' => 'mb-5'
	]); 


# Anidamiento de etiquetas

Como cada etiqueta renderiza a un string, siempre podrá anidarlas pero además si utiliza Form::group() o los tags que lo implementan como Form:div y Form:picture entre otros, podrá enviar un Array en vez de un string. 

Ej:

	echo Bt5Form::group(content:[
        tag('span')->text('@')->id('basic-addon')->class('input-group-text'),
        tag('inputText')->name('nombre')->placeholder("Username")
    ],
		tag:"div"
        class:"input-group mb-3"
    );

o

	echo Bt5Form::group(content:
        tag('span')->text('@')->id('basic-addon')->class('input-group-text') .
        tag('inputText')->name('nombre')->placeholder("Username")
    ,
		tag:"div"
        class:"input-group mb-3"
    );

o

	echo Bt5Form::div(content:[
        tag('span')->text('@')->id('basic-addon')->class('input-group-text'),
        tag('inputText')->name('nombre')->placeholder("Username")
    ],
        class:"input-group mb-3"
    );

o

	echo tag('div')->content([
        tag('span')->text('@')->id('basic-addon')->class('input-group-text'),
        tag('inputText')->name('nombre')->placeholder("Username")
    ])->class("input-group mb-3")

o

	echo tag('inputGroup')->content([
        tag('span')->text('@')->id('basic-addon')->class('input-group-text'),
        tag('inputText')->name('nombre')->placeholder("Username")
    ])->class("mb-3")

o

	echo tag('inputGroup')->content(
        tag('span')->text('@')->id('basic-addon')->class('input-group-text') .
        tag('inputText')->name('nombre')->placeholder("Username")
    )->class("mb-3")


# Atributos válidos

Si un atributo en su nombre contiene un guión medio al momento de referenciarlo sino es dentro de $attributes entonces tendrá que hacerlo reemplazando los guiones medios (-) por guiones bajos (_).

Ej:

	text:static::button(
		class:"accordion-button collapsed",
		data_bs_toggle:"collapse",
		dataBsTarget:"#{$arr['id']}",
		aria_expanded:"false",
		aria_controls:$arr['id'],
		content:[ $arr['title'] ]
	)

# Select

Ej:

	echo Bt5Form::select(name:'size', options:[
		'L' => 'Large', 
		'S' => 'Small'
	], placeholder:'Pick a size...', default:'Large');	

o

	tag('select')->name('size')->options([
		'L' => 'Large', 
		'S' => 'Small'
	])->placeholder('Pick a size...')->default('Large');


Además acepta un agrupamiento de opciones enviando un array asociativo.

Ej:

	echo Bt5Form::select(name:'comidas', options:[
        'platos' => [
            'Pasta' => 'pasta',
            'Pizza' => 'pizza',
            'Asado' => 'asado' 
        ],
    
        'frutas' => [
            'Banana' => 'banana',
            'Frutilla' => 'frutilla'
        ]
    ],         
    placeholder:'Tu comida favorita',
	class:'my-3');

o

	echo tag('select')
    ->name('comidas')
    ->placeholder('Tu comida favorita') 
    ->options([
        'platos' => [
            'Pasta' => 'pasta',
            'Pizza' => 'pizza',
            'Asado' => 'asado' 
        ],

        'frutas' => [
            'Banana' => 'banana',
            'Frutilla' => 'frutilla'
        ]
    ])->class('my-3');


Para "select múltiple" solo agregue 

	->multiple('multiple')

o simplemente

	->multiple()


# El tag input

Hay distintos tipos de <input> y cada uno tiene su alias que corresponde al type donde por ejemplo <input type="password"> es password()

Ej:

	Bt5Form::password(id:'pass', placeholder:"Contraseña");

o

	tag('password')->id('pass')->placeholder('Contraseña');

Hay un algunas de exceptiones:

	type="text"				es inputText()
	type="button"			es inputButton()
	type="time" 			es inputTime() 
	type="datetime_local"	es datetimeLocal()
	type="color"		    es inputColor()

La razón de las excepciones son colisiones de nombres como sucede con button() ya que hace referencia a <button> y por tanto no puede usarse para <input type="button">

Igualmente "text" puede hacer referencia a un atributo de otra etiqueta así que <input type="text"> es inputText().


# File

El input file permite subir archivos ya sea solo uno o varios.

Ej:

	echo tag('file')->multiple();


Size

A los controles de tipo form-control se les puede cambiar el tamaño aplicándoles large() o size()

Se verificó su funcionamiento para el caso de los <input> aunque se implemetó también para los <select>


# Id o name

Se puede generar una copia del valor del atributo "name" en "id" para evitar repetir:

	Bt5Form::setIdAsName();


# Cuando usar content()

Para algunas etiquetas se debe utilizar content() y para otras no y en esos casos dependiendo de la etiqueta es value() como para los INPUT pero bien podría ser text(). 

Cuál es el criterio?

Ej:

	echo tag('inputButton')
	->id("comprar")
	->value('Comprar')
	->attributes([ 'onClick' => "alert('Me presionaste')" ]);
	
	echo tag('button')
	->content('Notifications')
	->onclick("alert('Me presionaste')");

Sucede que hay dos tipos de etiquetas, las que se cierran (encerrando un contenido) y las que no. Dentro de las primeras tenemos los button, div y derivados. Entre las que no se cierran, input y hr.


# Métodos genéricos con tag()

Hay muchísimos tags de HTML y si alguno no está contemplado siempre es posible usar el métodos tag() para conseguir el resultado deseado.

Ej:

	Bt5Form::tag('hr')

o

	tag('hr');


La salida de los builders de HTML se puede perfectamente exportar porque se puede recuperar como string.

Ej:

	$content = tag('accordion')
	->items([
		[
			'id' => "flush-collapseOne",
			'title' => "Accordion Item #1",
			'body' => 'Placeholder content for this accordion, which is intended to demonstrate the <code>.accordion-flush</code> class. This is the first items accordion body.'
		],
		[
			'id' => "flush-collapseTwo",
			'title' => "Accordion Item #2",
			'body' => 'Placeholder 2'
		],
		[
			'id' => "flush-collapseThree",
			'title' => "Accordion Item #3",
			'body' =>  'Placeholder 3'
		]
	])
	->id('accordionExample')
	->always_open(true)
	->attributes(['class' => 'accordion-flush'])
	;


# Macros

La clase Html y Form soportan macros, es decir se pueden definir tags propios y estos son luego renderizados al llamar a un tag con ese nombre. 

Ej:

	Html::macro('salutor', function($name, $adj)
	{
		return "<span/>Hello $adj $name</span>";
	});

	echo Html::salutor("Isabel", "bella");

Inclusive puede aceptar atributos:

Ej:

	Html::macro('salutor', function ($name, $adj, Array $att = []) {
		$str_att = Bt5Form::attributes($att);
		return "<span $str_att>Hello $adj $name</span>";
	});

	echo Bt5Form::salutor("Isabel", "bella", ['class' => 'my-3 me-1', 'style' => 'color: red']); 


# La clase Tag

Es posible usar métodos encadenados para definir los atributos en vez de los parámetros de los métodos de las clases Html y derivadas (Form, Bt5Form, etc).

	echo tag('span')->text('some text')->style('color:red');

Renderiza algo como

	<span style="color:red">some text</span>


Lo que sigue son equivalentes:

	Bt5Form::range('edad', 0, 99, 10, ['class' => 'my-3'])

	Bt5Form:range(name:'edad', min:0, max:99, default:10, class:'my-3')

    tag('range')->name('edad')->min(0)->max(99)->default(10)->class('my-3')


Importante:

	Para poder hacer uso de la clase Tag se debe primero "registrar" la clase del Html Builder.

Ej:

	Tag::registerBuilder(\Boctulus\Simplerest\Core\Libs\Bt5Form::class);

Nota:

	Las distintas formas de llamar al form builder se pueden combinar.

Ej:

	<?php

	use Boctulus\Simplerest\Core\Libs\Bt5Form; 
	use Boctulus\Simplerest\Core\Libs\Tag;
	?>

	<div class = "row mt-5">
		<div class = "col-6 offset-3">

		<?php 

		echo tag('h3')->text("Datos")->class('mb-3');

		echo Bt5Form::dataList(listName:'datalistOptions', id:'occupation', options:[
			'programador',
			'software engenierer'
		], placeholder:'Escriba aquí', label:'Ocupación');

		/*
			El tag 'hr' ni siquiera está definido en la clase Html
		*/
		echo tag('hr')->style('color:cyan');

		echo tag('p')->text("Hola mundo cruel");

		?>
		</div>
	</div>


# When

Al igual que en el QueryBuilder disponemos de when()

$req = true;

$html = tag('inputText')
->name('nombre')
->when($req, function($o){
    $o->required('required');
});


# Mostrar / ocultar

Para ocultar o mostrar un elemento se pueden utilizar los metodos show() y hide()

Ej:

    echo tag('modal')->content(
        tag('modalDialog')->content(
            tag('modalContent')->content(
                tag('modalHeader')->content(
                    tag('modalTitle')->text('Nuevo') . 
                    tag('closeButton')->dataBsDismiss('modal')
                ) .
                tag('modalBody')->content(
                    $modal_body                    
                ) . 
                tag('modalFooter')->content(
                    tag('closeModal')->content("Cancelar")->attributes([ 'onClick' => "hide_elem_by_id('row-form-modal');" ]) .
                    tag('button')->id("save_row")->text('Guardar')
                ) 
            ) 
        )
    )
    ->show()  // <-------------------- HERE
    ->id('row-form-modal');

En el ejemplo anterior el modal seria renderizado ya abierto.

Nota:

Tambien se agrego el metodo display() pero no es compatible con modales de BT5 !!!

    ->display('block')
O
    ->display('none')

De todas formas, hay más clases que no es están considerando:

¨To hide elements simply use the .d-none class or one of the .d-{sm,md,lg,xl,xxl}-none classes for any responsive screen variation.¨


# Atributos data-*

Los atributos data-* en caso de usarse como métodos encadenados con la clase Tag deben pasarse a camelCase.

Ej:

	tag('closeButton')->dataBsDismiss('modal')

Donde "dataBsDismiss" hace referencia al atributo data-bs-dismiss renderizando dataBsDismiss('modal') en data-bs-dismiss="modal"


# Text utilities

Silenciar un texto (aclarar el color de la fuente) es tan simple como llamar a textMuted() sobre un p(), div(), span(), etc.

Ej:

	echo tag('p')->text('Some paragraph')->class('mt-3')->textMuted();
    echo tag('div')->content('Some content')->class('mt-3')->textMuted();

En general se puede aplicar cualquier clase de css sobre si se define en $classes y tal es el caso para textMuted a la que se le aplica para Boostrap 5 la clase "text-muted".


# Buttons

A un botón se le puede setear el color muy fácil mediante métodos Form::{color}()

Ej:

	echo tag('button')->content('Notifications')->primary();

Que es lo mismo que:

	echo tag('button')->content(Notifications')->class('primary');

Colores y otras clases sobre botones 

Sobre colores y derivados (como dropdownButton) se pueden aplicar:

	info()
	warning()
	danger()
	etc

Y por compatibilidad con AdminLTE también default()

Ej:

	echo tag('inputButton')->id("comprar")->value('Comprar')->danger()->class('rounded-pill');
    echo tag('reset')->id("limpiar")->value("Limpiar")->warning()->class('mx-3');
    echo tag('submit')->id("enviar")->value("Enviar")->success();


Flat

Se puede conseguir botones planos (sin bordes redondeados) aplicando flat()

Ej:

	echo tag('button')->content('Un botón')->large()->bg('warning')->flat();

Outline

Se puede conseguir botones sin background aplicando outline() 

Ej:
	echo tag('button')->content('Botón rojo')->danger()->class('rounded-pill')->outline()

Nota: solo funciona con <button> y no <input type="button">

Tamaño 

Llamando a Form::small() o Form::large() se obtienen botones más grande o pequeños.

Ej:
	echo tag('inputButton')->value('Un botón')->info()->class('rounded-pill');
    echo tag('inputButton')->value('Otro botón')->warning()->class('rounded-pill')->large();
    echo tag('inputButton')->value('Peque')->info()->class('rounded-pill mx-3')->small();

En general cualquier control que implemente la clase form-control es suceptible de ser agrandado o achicado con large() y small()

Ej:

	echo tag('file')
	->class('mt-3 mb-5');

	echo tag('file')->large()
	->class('mt-3 mb-5');

	echo tag('file')->small()
	->class('mt-3 mb-5');

Deshabilitar botones

Solo agregue una llamada a Form::disabled()

	echo tag('submit')->id("enviar")->value("Enviar")->success()->disabled()


Botones de ancho completo

Aplicando block() se consigue que un button ocupe todo el ancho de su parent.

Ej:

	echo tag('button')->content(
		'<i class="fa fa-bell"></i> .btn-block'
	)
	->block(); 

Botones con íconos

Ej:

	echo tag('button')->content(
		'<i class="fa fa-bell"></i> .btn-block'
	)
	->block(); 

O si son fonts de FontAwesome,...

	echo tag('button')->content("btn-block")
	->block()
	->icon('bell'); 

O con outline

Ej:

	echo tag('button')->content("btn-block")
	->block()
	->icon('bell')
	->outline(); 


# App buttons

Admin LTE viene con App buttons (que recuerdan los íconos de Windows 3.1). Se respeta el nombre de "app buttons" que le da la plantilla aunque son links.

Ej:

	echo tag('appButton')
	->content("Edit")
	->icon('edit')
	->href('#edit'); 

Se puede setear una cantidad a ser mostrada como notificación en el márgen superior derecho como badge.

Ej:

	echo tag('appButton')
	->content("Edit")
	->icon('edit')
	->href('#edit')
	->badgeQty(5); 

Se puede colorear tanto el "botón" como el badge. 

Ej:

	echo tag('appButton')
	->content("Edit")
	->icon('edit')
	->href('#edit')
	->bg('danger')
	->badgeQty(5)
	->badgeColor('warning'); 

# Input Groups

Los tag input* como inputText, file (o inputFile), etc pueden contener otros elementos como botones o dropdowns tanto en el frente como en su parte final para lo que se utiliza prepend() y append() respectivamente.

Ej:

Delante

	echo tag('inputGroup')
	->content(
		tag('inputText')
	)
	->prepend(
		tag('button')->danger()->content('Action')
	)->class('mb-3');

Ej:

Detrás

	echo tag('inputGroup')
	->content(
		tag('inputText')
	)
	->append(
		tag('button')->info()->content('Go!')
	)->class('mb-3');

Ej:

Delante y detrás

	echo tag('inputGroup')
	->content(
		tag('inputText')
	)
	->prepend(
		tag('button')->danger()->content('Action')
	)
	->append(
		tag('button')->info()->content('Go!')
	)->class('mb-3');

Ej:

Varios elementos delante	

	echo tag('inputGroup')
	->content(
		tag('inputText')
	)
	->prepend([
		tag('button')->danger()->content('Action'),
		tag('button')->warning()->content('Other action') 
	])
	->append(
		tag('button')->info()->content('Go!')
	)->class('mb-3');


Así es muy fácil crear input con un ícono de búsqueda por ejemplo:

	echo tag('inputGroup')
	->content(
		tag('inputText')
		->placeholder('Search')
	)
	->append(
		tag('button')->info()->icon('search')
	);


# Button groups and button toolbars

Con buttonGroup() se puede "agrupar" botones.

Ej:

	echo tag('buttonGroup')->content([
		tag('button')->content('Left')->info(),
		tag('button')->content('Middle')->info(),
		tag('button')->content('Right')->info()
	])->class('my-3');

y vericalmente con vertical()

	echo tag('buttonGroup')->content([
		tag('button')->content('Top')->warning(),
		tag('button')->content('Middle')->warning(),
		tag('button')->content('Bottom')->warning()
	])->vertical();

Con buttonToolbar() se grupan conjuntos de botones o sea es un wrapper sobre varios "button groups". También puede agrupar de forma mezclada "button groups" con "input groups".

Ej:

	echo tag('buttonToolbar')->content([
        tag('buttonGroup')->content(
            tag('button')->content('Botón rojo')->danger()->class('rounded-pill')->outline() .
            tag('button')->content('Botón verde')->success()->class('rounded-pill')->outline()
        )->aria_label("Basic example")->class('mx-3'),
    
        tag('buttonGroup')->content(
            tag('button')->content('Botón azul')->info()->class('rounded-pill')->outline() .
            tag('button')->content('Botón amarillo')->warning()->class('rounded-pill')->outline()
        )->aria_label("Another group")->class('mx-3')
    ]);
	

Tamaño de botones en "button groups"

Se puede aplicar large() o small() a los grupos.

Ej:

	echo tag('buttonGroup')->content(
        tag('button')->content('E')->danger()->class('rounded-pill')->outline() .
        tag('button')->content('F')->success()->class('rounded-pill')->outline()
    )->aria_label("Basic example")->class('mx-3')->large();


Botones agrupados en vertical

Aplique Form::vertical() al button group para lograr el alineamiento vertical.

Ej:

	echo tag('buttonGroup')->content(
        tag('button')->content('E')->danger()->class('rounded-pill')->outline() .
        tag('button')->content('F')->success()->class('rounded-pill')->outline()
    )->aria_label("Basic example")->class('mx-3')->large()->vertical();


# Acordión

Ej:

 	echo tag('accordion')->items([
		[
			'id' => "flush-collapseOne",
			'title' => "Accordion Item #1",
			'body' => 'Placeholder content for this accordion, which is intended to ....'
		],
		[
			'id' => "flush-collapseTwo",
			'title' => "Accordion Item #2",
			'body' => 'Placeholder 2'
		],
		[
			'id' => "flush-collapseThree",
			'title' => "Accordion Item #3",
			'body' =>  'Placeholder 3'
		]
	])->id('accordionExample');

Para mantener abierta una sección cuando se abra otra, use always_open

Ej:

	echo tag('accordion')->items([
		[
			'id' => "flush-collapseOne",
			'title' => "Accordion Item #1",
			'body' => 'Placeholder content for this accordion, which is intended to demonstrate the <code>.accordion-flush</code> class. This is the first items accordion body.'
		],
		[
			'id' => "flush-collapseTwo",
			'title' => "Accordion Item #2",
			'body' => 'Placeholder 2'
		],
		[
			'id' => "flush-collapseThree",
			'title' => "Accordion Item #3",
			'body' =>  'Placeholder 3'
		]
	])
	->id('accordionExample')
	->always_open(true)
	->attributes(['class' => 'accordion-flush']);



Atributos deben pasarse como ->attributes($array)

Ej:

	echo tag('accordion')->items([
		/*
			Items
		*/
	])
	->id('accordionExample')
	->attributes(['class' => 'accordion-flush']);


# Input range

	echo tag('label')->for("exp")->text("Experiencia");
    echo tag('range')->name('exp')->min(0)->max(99)->default(30)->class('my-3');

Con Admin LTE se puede personalizar cambiando el color de la "bolita".

Ej:

	echo tag('label')->for("exp")->text("Experiencia");
    echo tag('range')->name('exp')->min(0)->max(99)->default(30)->class('my-3')->color('teal');


# Alerts

A Simple alert. 

Ej:

    echo tag('alert')->content('Some content')->warning();

An alert with some link. 

Ej:

    echo tag('alert')->content(
		tag('alertLink')->href('#')->anchor('A danger content')
	)->danger();

Dismissable alerts

Just add ->dismissible(true) to become the alert dismissable.

Ej:

	echo tag('alert')->content('Some content')->warning()->dismissible(true);
o
	echo Bt5Form::alert(content:'Some content', attributes:['warning', 'dismissible']);


# Badges

Ej:

	echo tag('badge')->content('barato')->success();
	echo tag('badge')->content('casi agotado')->warning();
	echo tag('badge')->content('agotado')->danger();

Modificar el tamaño

Ej:

	echo tag('h3')->text(
        tag('badge')->content('barato')->success()
    );

Bages como pílodoras

Es solo de aplicar la clase rounded-pill.

Ej:
	echo tag('badge')->content('barato')->class('rounded-pill')->success();


A los botones es posible aplicarles badges

Ej:

	echo tag('button')->content([
        'Notifications', 
        tag('badge')->content('4')->secondary()
    ])
    ->class('rounded')
    ->primary();

Posicionando el badge 

Ej:

	echo tag('button')->content([
        'Inbox', 
        tag('badge')->content('99+')->danger()->class('position-absolute top-0 start-100 translate-middle rounded-pill')
    ])
    ->class('rounded position-relative')
    ->primary();


# Notification buttons

Un botón que muestra en su márgen superior derecho un número.

Ej:

	echo tag('notificationButton')->text('Inbox')->qty(101);


# Breadcrumbs

Un breadcrumb renderiza un elemento de navegación como:

	Home > Library > Data

Ej:

	echo tag('breadcrumb')->content([
        [   
            'href' => '#', 
            'anchor' => 'Home'
        ],
        
        [
            'href' => '#library',
            'anchor' => 'Library'
        ],

        [
            'anchor' => 'Data'
        ]
    ]);


# Cards

Se implementan varios métodos para la creación de tarjetas aunque las cards son tan flexibles que no hay un diseño fijo.

Ej:

	echo tag('card')
        ->header('Quote') 
        ->body(

            tag('blockquote')->content(
                tag('p')->text(
                    'A well-known quote, contained in a blockquote element.'
                ) .
                tag('blockquoteFooter')->content('Someone famous in ' . 
                tag('cite')->title("Source Title")->content('Source Title'))

            )->class('mb-0')
        
        ) 
        ->footer('Some footer')
        ->class('mb-4');

Tanto header como footer son opcionales.

Ej:

	echo tag('card')->content(
		tag('cardHeader')->content(
			tag('cardTitle')->text('Some title')
		)
        tag('cardBody')->content(
            tag('cardSubtitle')->text('Some subtitle')->class('mb-2')->textMuted()
        )
    )->class('mb-4');

Sintaxis simplificada

	echo tag('card')
	->header('Some header') 
	->body(
		'Some body'
	) 
	->footer('Some footer')
	->class('mb-4');


Cards con imágenes

Cuando hay una imágen dentro de una card es imprescindible setear el ancho de la tarjeta para evitar que crezca hasta el 100% del width del padre. Por ejemplo con style('width: 18rem;')

Notar que la imágen se coloca en el content() y no en el body.

Ej:

	echo tag('card')->style('width: 18rem;')->class('my-3')
	->content(
		tag('cardImgTop')->src(asset('img/mail.png'))
	)
	->body([   
		tag('cardTitle')->text('Some title'),        
		tag('cardText')->text('Some quick example text to build on the card title and make up the bulk of the cards content.'),
		tag('inputButton')->value('Go somewhere')
	]);


Sizing

	Se pueden aplicar distintas utilidades como w-75, w-50, etc sobre la card o usando style(), ej: style('width: 18rem;')

Colores 

Se puede aplicar textColor() y bg() sobre el body() y por supuesto también colorear los botones.

Ej:

	echo tag('card')->style('width: 18rem;')
	->header(tag('cardTitle')->text('Some title'))
	->body([            
		tag('cardText')->text('Some quick example text to build on the card title and make up the bulk of the cards content.'),
		tag('inputButton')->value('Go somewhere')->info()->textColor('white')
	])
	->class('my-3')
	->bg('primary')
	->textColor('white');

Card title

Tanto title como subTitle pueden ir dentro o fuera del header() pero la diferencia es que dentro del header quedan "remarcados" con un border inferior y se ubicarían delante del content(). Para cards que contienen imágenes queda anti-estético que se use con header.

Ej:

	/*
      Sin header
    */
    echo tag('card')->style('width: 18rem;')->class('my-3')
    // ->content(
    //   tag('cardImgTop')->src(asset('img/mail.png'))
    // )
    ->body([   
      tag('cardTitle')->text('Some title'),        
      tag('cardText')->text('Some quick example text to build on the card title and make up the bulk of the cards content.'),
      tag('inputButton')->value('Go somewhere')
    ]);


    /*
      Con header
    */
    echo tag('card')->style('width: 18rem;')->class('my-3')
    // ->content(
    //   tag('cardImgTop')->src(asset('img/mail.png'))
    // )
    ->header(tag('cardTitle')->text('Some title'))
    ->body([           
      tag('cardText')->text('Some quick example text to build on the card title and make up the bulk of the cards content.'),
      tag('inputButton')->value('Go somewhere')
    ]);

Notas:

Debería ser posible capturar la salida un "componente" a fin de que sea utilizado como template para un "widget". Notar el uso de elementos placeholders para templates {}

Ej:

	echo tag('card')
		->header(tag('cardTitle')->text('{{cartTitle:text}}'))
		->content(
        tag('cardBody')->content(
            tag('cardSubtitle')->text('{{cartsubtitle:text}}')->textMuted()
        )
    );


# Flip cards

Están basadas en ejemplo de w3schools.com y quizás no sean 100% responsivas. Toca ajustar el ancho de la imágen según como sea ésta y también los paddings.

Toca escribir el componente que use las clases flip-card, flip-card-inner, flip-card-front y flip-card-back aunque primero debería probarse su utilidad / responsividad.

Ej:
	/*
		Esto esta mal, en todo caso definir el tag 'flip_card' que directamente
		incluya el css para un 'flip_card'

		El css para Bt5Form deberia estar dentro de una carpeta assets/css del "package" o sea.. 
		Bt5Form deberia ser un package.
	*/

	include_widget_css('flip_card');

    echo tag('card')->class('my-3 flip-card')
	->content([
		tag('div')
		->class('flip-card-inner')
		->content([			
			tag('div')->content([
				tag('h3')->text('Tienes un correo')->bg('primary')->class('py-2'),
				tag('img')->src(asset('img/mail.png'))->w(65)
			])
			->class('flip-card-front bg-transparent'),
			
			tag('div')->content([
				tag('h3')->text('Some title'),
				tag('cardText')->text('Some quick example text to build on the card title and make up the bulk of the cards content.'),
				tag('inputButton')->value('Go somewhere')
			]) 
			->class("flip-card-back py-2") 
		])  
	]);


# Citas

Para citas use el tag blockquote

Ej:

	echo tag('card')
	->header('Quote') 
	->body(

		tag('blockquote')->content(
			tag('p')->text(
				'A well-known quote, contained in a blockquote element.'
			) .
			tag('blockquoteFooter')->content('Someone famous in ' . 
			tag('cite')->title("Source Title")->content('Source Title'))

		)
	
	) 
	->footer('Some footer')
	->class('mb-4');

Con AdminLTE cambie el color del marco con color()

	echo tag('card')
	->header('Quote') 
	->body(

		tag('blockquote')->content(
			tag('p')->text(
				'A well-known quote, contained in a blockquote element.'
			) .
			tag('blockquoteFooter')->content('Someone famous in ' . 
			tag('cite')->title("Source Title")->content('Source Title'))

		)->color('secondary')
	
	) 
	->footer('Some footer')
	->class('mb-4');


# Carousels

Los carruseles son de los componentes más complejos y con los métodos "base" es una ardua tarea poder crear un uno. Es por esto que se ha eliminado el boilerplate y la creación es algo trivial.

Ej:

	echo tag('carousel')->content(
		tag('carouselItem')->content(
			tag('carouselImg')->src(asset('img/slide-1.jpeg'))
		)->caption(
			'<h5>First slide label</h5>
			<p>Some representative placeholder content for the first slide.</p>'
		),

		tag('carouselItem')->content(
			tag('carouselImg')->src(asset('img/slide-2.jpeg'))
		),

		tag('carouselItem')->content(
			tag('carouselImg')->src(asset('img/slide-3.jpeg'))
		),
	)->id("carouselExampleControls")->withControls()->withIndicators();

Componentes opcionales:

	withControls			-- permiten navegar hacia adelante y atrás
	withIndicators			-- están en el "footer" del componente e indican el "avance" 


Para transacción con efecto "fade" llamando a carouselFade() o fade() y para tema obscuro es con carouselDark() o dark()

Ej:

	echo tag('carousel')->content(
		// Items
	)->id("carouselExampleControls")->dark()->fade();

Para especificar el tiempo en milisegundos para cada transición es con interval()

	tag('carouselItem')->content(
		// ...
	)->interval(1500)


En vez de 

	tag('img')->class("d-block w-100")->src('...')

puede usar:

	tag('carouselImg')->src('...')

El uso "captions" para aclarar una diapositiva se puede hacer mediante caption()

Ej:

 	tag('carouselItem')->content(
		tag('carouselImg')->src('...')
	)->caption(
		'<h5>Second slide label</h5>
		<p>Some representative placeholder content for the second slide.</p>'
	),

Altura de las diapositivas

Use height() sobre el tag carousel para normalizar el height de las diapositivas. El valor puede tener cualquier escala pero por defecto son pixeles.

Ej:

	echo tag('carousel')->content([
		tag('carouselItem')->content(
			tag('carouselImg')->src(asset('img/slide-1.jpeg'))
		)->caption(
			'<h5>First slide label</h5>
			<p>Some representative placeholder content for the first slide.</p>'
		),

		tag('carouselItem')->content(
			tag('carouselImg')->src(asset('img/slide-2.jpeg'))
		),

		tag('carouselItem')->content(
			tag('carouselImg')->src(asset('img/slide-3.jpeg'))
		)
	])->id("carouselExampleControls")->withControls()->withIndicators()
	->height('400px');
	

# Eventos

Usando attributes() en principio es posible manejar eventos aunque logicamente tambien se puede utilizar addEventListener

	tag('button')
	->content("Hacer algo")
	->attributes([ 'onClick' => "console.log('hago algo')" ]) 

Notas:

Si para abrir / cerrar una ventana modal lo que deben usarse son los tags tag('openButton') y tag('closeButton')

# Ventanas modales

La estructura de un modal es más o menos la siguiente:

	echo tag('modal')->content(
		tag('modalDialog')->content(
			tag('modalContent')->content(
				tag('modalHeader')->content(
					tag('modalTitle')->text('Modal title') . 
					tag('closeButton')->dataBsDismiss('modal')
				) .
				tag('modalBody')->content(
					tag('p')->text('Modal body text goes here.')
				) . 
				tag('modalFooter')->content(
					tag('closeModal') .
					tag('button')->text('Save changes')
				) 
			) 
		)
	)->id('exampleModal');


El texto del boton en closeModal se puede personalizar como el de cualquier otro boton:

	tag('closeModal')->content("Cerrar")


Ejemplo completo:

	echo tag('modal')->content(
        tag('modalDialog')->content(
            tag('modalContent')->content(
                tag('modalHeader')->content(
                    tag('modalTitle')->text('Nuevo / Editar') . 
                    tag('closeButton')->dataBsDismiss('modal')
                ) .
                tag('modalBody')->content(
                    tag('p')->text('El contenido')
                ) . 
                tag('modalFooter')->content(
                    tag('closeModal')->content("Cancelar") .
                    tag('button')->text('Guardar')
                ) 
            ) 
        )
    )->id('row-form-modal');

	echo tag('openButton')->target("row-form-modal")->content('Launch demo modal')->class('my-3');

O...

	echo tag('modal')
	->header(
		tag('modalTitle')->text('Nuevo / Editar') . 
		tag('closeButton')->dataBsDismiss('modal')
	)
	->body(
		$modal_body
	)
	->footer(
		tag('closeModal') .
		tag('button')->id("save_row")->text('Guardar')
	)
	->options([
		//'fullscreen',
		//'center',
		//'scrollable'
	])
	//->show() ///
	->id('row-form-modal');

	echo tag('openButton')->target("row-form-modal")->content('Launch demo modal')->class('my-3');

Nota:

Para abrir / cerrar una ventana modal lo que deben usarse son los tags tag('openButton') y tag('closeButton')


Static

Para que la ventana modal no se cierre al hacer click fuera de ella se puede usar static()

Ej:

	echo tag('modal')->content(
		// ...
	)->id('exampleModal')->static();

Scrollable 

Cuando son textos largos se puede aplicar scrollable()

Ej:

 	echo tag('modal')->content(
		tag('modalDialog')->content(
			//  ....
		)->scrollable()
	)->id('exampleModal');


Ancho del modal

En teoría es posible cambiar el ancho con small(), large() y extraLarge() sobre el tag('modalDialog')


Pantalla completa

Llamar a fullscreen() sobre tag('modalDialog')


Forma simplificada

Usando algo de "azucar sintáctico" es posible simplificar notablemente la creación de una ventana modal.

Ej:

	echo tag('modal')
	->header(
		tag('modalTitle')->text('Modal title') . 
		tag('closeButton')->dataBsDismiss('modal')
	)
	->body(
		tag('p')->text('Modal body text goes here!')
	)
	->footer(
		tag('closeModal') .
		tag('button')->text('Save changes')
	)
	->options(['fullscreen'])
	->id('exampleModal');

Como puede observarse en options() se envian las opciones para el "modelDialog" que ya no es necesario declararlo.


# Collapse 

Elementos como div pueden colapsarse mediante el "componente" de collapse de Boostrap.

Los eventos para colapsar y des-colapsar se pueden asociar a "links" y "buttons".

Ej:

	echo tag('p')->text(
		tag('collapseLink')->href("#collapseExample")->anchor('Link with href')->class('me-1') .            
		tag('collapseButton')->dataBsTarget("#collapseExample")->content('Button with data-bs-target')
	);

	echo tag('collapse')->id("collapseExample")->content(
		tag('cardBody')->content('Some placeholder content for the collapse component. This panel is hidden by default but revealed when the user activates the relevant trigger.')
	);

Multiple targets

"A <button> or <a> can show and hide multiple elements by referencing them with a selector in its href or data-bs-target attribute. Multiple <button> or <a> can show and hide an element if they each reference it with their href or data-bs-target attribute"

Usando el método multiple() sobre collapse() se agrega la clase "multi-collapse".


# Dropdown

La estructura básica es la siguiente:

	echo tag('dropdown')->content(
		tag('dropdownButton')->id('dropdownMenuButton1')->content('Dropdown button') .
		tag('dropdownMenu')->ariaLabel('dropdownMenuButton1')->content(
			tag('dropdownItem')->href('#')->anchor('Action') .
			tag('dropdownItem')->href('#')->anchor('Another action') .
			tag('dropdownItem')->href('#')->anchor('Something else here')
		)
	);

En vez de un button puede usarse un link:

	echo tag('dropdown')->content(
		tag('dropdownLink')->id('dropdownMenuButton1')->href('#')->anchor('Dropdown button') .
		tag('dropdownMenu')->ariaLabel('dropdownMenuButton1')->content(
			tag('dropdownItem')->href('#')->anchor('Action') .
			tag('dropdownItem')->href('#')->anchor('Another action') .
			tag('dropdownItem')->href('#')->anchor('Something else here')
		)
	);

Variantes de los botones

Ej:

Se puede aplicar danger(), warning(), success() ... outline() a los botones.

Ej:

	echo tag('dropdown')->content(
		tag('dropdownButton')->id('dropdownMenuButton1')->content('Dropdown button')->danger() .

		tag('dropdownMenu')->ariaLabel('dropdownMenuButton1')->content(
			tag('dropdownItem')->href('#')->anchor('Action') .
			tag('dropdownItem')->href('#')->anchor('Another action') .
			tag('dropdownItem')->href('#')->anchor('Something else here')
		)
	);

Divisor 

Se pueden agregar divisores entre los items del menú con dropdownDivider()

	echo tag('dropdown')->content(
		tag('dropdownButton')->id('dropdownMenuButton1')->content('Dropdown button')->danger()->outline() .
		tag('dropdownMenu')->ariaLabel('dropdownMenuButton1')->content(
			tag('dropdownItem')->href('#')->anchor('Action') .
			tag('dropdownItem')->href('#')->anchor('Another action') .
			tag('dropdownDivider') .
			tag('dropdownItem')->href('#')->anchor('Something else here')
		)
	);

Tamaño del botón

Se ajusta con small() o large() sobre dropdown()

Ej:

	echo tag('dropdown')->content(
		tag('dropdownButton')->id('dropdownMenuButton1')->content('Dropdown button')->danger()->outline()->large() .
		tag('dropdownMenu')->ariaLabel('dropdownMenuButton1')->content(
			tag('dropdownItem')->href('#')->anchor('Action') .
			tag('dropdownItem')->href('#')->anchor('Another action') .
			tag('dropdownDivider') .
			tag('dropdownItem')->href('#')->anchor('Something else here')
		)
	);

Disabled

Para des-habilitar solo aplicar disabled() sobre el botón


# Split buttons

Son una variación de los dropdowns donde lo que cambia es el tag split por dropdown y dos botones en vez del dropdownButton.

Ej:

	echo tag('split')->content([
      tag('button')->content('Split button')->default(),

      tag('splitButton')->id('dropdownMenuButton33')->content('Toggle Dropdown'),

      tag('dropdownMenu')->ariaLabel('dropdownMenuButton33')->content(
        tag('dropdownItem')->href('#ln1')->anchor('Action #1') .
          tag('dropdownItem')->href('#ln2')->anchor('Another action') .
          tag('dropdownDivider') .
          tag('dropdownItem')->href('#ln3')->anchor('Something else here')
      )
    ]);

A diferencia del dropdown despliega las opciones hacia la derecha y con el color default se ve una línea divisoria en el botón.


# List group

Ej:

	echo tag('listGroup')->content([
		tag('listGroupItem')->text('An item'),
		tag('listGroupItem')->text('An item #2'),
		tag('listGroupItem')->text('An item #3')
	])->class('mt-3');

Item "activo"

	echo tag('listGroup')->content([
		tag('listGroupItem')->text('An item')->active(),
		tag('listGroupItem')->text('An item #2'),
		tag('listGroupItem')->text('An item #3')
	])->class('mt-3');

En caso de querer usar la clase Bt5Form en vez de Tag() sería:

    Bt5Form::listGroupItem('An item', ['active' => true]),
o
	Bt5Form::listGroupItem(text:'An item', active:true),


Actionable

Solo aplique actionable() sobre los items.

	echo tag('listGroup')->content([
		tag('listGroupItem')->text('An item #1')->actionable()->active(),
		tag('listGroupItem')->text('An item #2')->actionable(),
		tag('listGroupItem')->text('An item #3')->actionable()
	])->class('mt-5');


Removoción de bordes

Para hacer un "flush" solo aplique flush() al listGroup.

Ej:

	echo tag('listGroup')->content([
		// ...
	])->flush();

Numerados

Se puede aplicar un numerado sobre el listGroup con numbered() aunque en principio no es compatible con actionable.

Ej:

	echo tag('listGroup')->content([
		tag('listGroupItem')->text('An item')->active(),
		tag('listGroupItem')->text('An item #2'),
		tag('listGroupItem')->text('An item #3')
	])->class('mt-5')->numbered();


Si se quisiera usar la clase Bt5Form directamente para el caso de numbered sería así:

	echo Bt5Form::listGroup([
		// Items
	], ['class' => 'mt-5', 'numbered' => true] );


Arreglo horizontal

Se logra aplicando horizontal() sobre el listGroup.

Ej:

	echo tag('listGroup')->content([
		tag('listGroupItem')->text('An item')->active(),
		tag('listGroupItem')->text('An item #2'),
		tag('listGroupItem')->text('An item #3')
	])->class('mt-5')->horizontal();

Colores

Se pueden aplicar colores sobre cada item con color($color). No intente con $color() como success() porque NO es lo que desea.

Ej:

	echo tag('listGroup')->content([
		tag('listGroupItem')->text('An item #1')->color('info'),
		tag('listGroupItem')->text('An item #2')->warning(),  // mal
		tag('listGroupItem')->text('An item #3')->color('success')  // ok
	])->class('mt-5')->horizontal();


# Navs

	echo tag('nav')->content([
		tag('navLink')->anchor('Active')->active(),
		tag('navLink')->anchor('Link'),
		tag('navLink')->anchor('Link'),
		tag('navLink')->anchor('Disabled')->disabled()
	])->class('mb-3')
	//->vertical()
	->justifyRight()
	//->justify()
	->pills()
	//->fill()
	//->tabs();    

Vertical

Solo aplique vertical() a tag('nav')

Ej:

	echo tag('nav')->content([
		tag('navLink')->anchor('Active')->active(),
		tag('navLink')->anchor('Link'),
		tag('navLink')->anchor('Link'),
		tag('navLink')->anchor('Disabled')->disabled()
	])->class('mb-3')
	->vertical();    

Justify right

Solo aplique justifyRight() a tag('nav')


Pills

Solo aplique pills() a tag('nav')


Tabs

Solo aplique tabs() a tag('nav')

Ej:

	echo tag('nav')->content([
		tag('navLink')->anchor('Active')->active(),
		tag('navLink')->anchor('Link'),
		tag('navLink')->anchor('Link'),
		tag('navLink')->anchor('Disabled')->disabled()
	])
	->tabs();    

Fill

Para que el nav ocupe todo el espacio disponible en ancho use fill()

Ej:

	echo tag('nav')->content([
		tag('navLink')->anchor('Active')->active(),
		tag('navLink')->anchor('Link'),
		tag('navLink')->anchor('Link'),
		tag('navLink')->anchor('Disabled')->disabled()
	])
	->fill()
	->tabs();


Justify

	Para que el nav ocupe todo el espacio disponible y el ancho de cada botón sea el mismo use justify() en vez de fill()


Si por alguna razón deseara usar "navItems" como wrapper sobre los navLinks haga aplique as('ul') sobre tag('nav') 

Ej:

	echo tag('nav')->content([
		tag('navItem')->content(
			tag('navLink')->anchor('Active')->active()
		),
		tag('navItem')->content(
			tag('navLink')->anchor('Link')
		),
		tag('navItem')->content(
			tag('navLink')->anchor('Link')
		),
		tag('navItem')->content(
			tag('navLink')->anchor('Disabled')->disabled()
		)
	])->as('ul')->justifyRight();     


Working with flex utilities

"If you need responsive nav variations, consider using a series of flexbox utilities. 
While more verbose, these utilities offer greater customization across responsive breakpoints."

https://getbootstrap.com/docs/5.0/components/navs-tabs/#working-with-flex-utilities


Nav con Dropdowns

Add dropdown menus with a little extra HTML and the dropdowns JavaScript plugin.

Ej:

	echo tag('nav')->content([             
		tag('navLink')->anchor('Active')->active(),    

		tag('dropdown')->content(
			tag('dropdownButton')->id('dropdownMenuButton1')->content('Dropdown button') .

			tag('dropdownMenu')->ariaLabel('dropdownMenuButton1')->content(
				tag('dropdownItem')->href('#')->anchor('Action') .
				tag('dropdownItem')->href('#')->anchor('Another action') .
				tag('dropdownDivider') .
				tag('dropdownItem')->href('#')->anchor('Something else here')
			)
		),

		tag('navLink')->anchor('Link'),        
		tag('navLink')->anchor('Disabled')->disabled()

	])	
	->justifyRight()	
	->tabs();     

O con navItems (aunque el resultado es el mismo),

	echo tag('nav')->content([     
		tag('navItem')->content(        
			tag('navLink')->anchor('Active')->active(),    
		),    
		tag('navItem')->content(
			tag('dropdown')->content(
				tag('dropdownButton')->id('dropdownMenuButton1')->content('Dropdown button') .    
				tag('dropdownMenu')->ariaLabel('dropdownMenuButton1')->content(
					tag('dropdownItem')->href('#')->anchor('Action') .
					tag('dropdownItem')->href('#')->anchor('Another action') .
					tag('dropdownDivider') .
					tag('dropdownItem')->href('#')->anchor('Something else here')
				)
			)
		),

		tag('navItem')->content(
			tag('navLink')->anchor('Link'), 
		),     
		
		tag('navItem')->content(
			tag('navLink')->anchor('Disabled')->disabled()
		)    
	])


Simplificación

En muchos casos se puede simplificar enviando un array a tag('nav') de estructura similar al que recibe breadcrumb.

	echo tag('nav')->content([  
		[
			'href'   => '#',
			'anchor' => 'Home'
		],

		[
			'href' => '#library',
			'anchor' => 'Library'
		],

		[
			'anchor' => 'Data'
		]
	])
	->justifyRight()
	->tabs();     

Casting 

Un navLink es implementado con un el tag <a> pero se lo puede "castear" a <button> mediante as()

Ej:

	tag('navLink')->anchor('Click Me')->as('button')


Tab list

De momento a medio-implementar pero la idea es pasar el array de "links", aplicar tabs() o pills() y role('tablist') así como un array con los "textos" (html) a renderizar para cada tab.

Ej:

	// Nav como tab-list
	echo tag('nav')->content([  
		[
			'anchor' => 'Uno',
			'href'   => '#uno'
		],

		[
			'anchor' => 'Dos',
			'href' => '#dos'
		],

		[
			'anchor' => 'Tres',
			'href'   => '#tres'
		]
	])->class('mb-3')
	->justifyRight()
	->tabs()
	->role('tablist')
	->panes([
		'Textoooooooooo oo',
		'otroooooo',
		'y otro más'            
	]); 

Inclusive es posible combinar "panes" con un Dropdown

Ej:

	// Nav como tab-list
	echo tag('nav')->content([  
		[
			'anchor' => 'Uno',
			'href'   => '#uno'
		],

		[
			'anchor' => 'Dos',
			'href' => '#dos'
		],

		[
			'anchor' => 'Tres',
			'href'   => '#tres'
		],
		tag('dropdown')->content(
			tag('dropdownButton')->id('dropdownMenuButton')->content('Dropdown button') .    
			tag('dropdownMenu')->ariaLabel('dropdownMenuButton')->content(
				tag('dropdownItem')->href('#')->anchor('Action 1') .
				tag('dropdownItem')->href('#')->anchor('Another action') .
				tag('dropdownDivider') .
				tag('dropdownItem')->href('#')->anchor('Something else here')
			)
		),
	])->class('mb-3')
	->justifyRight()
	->tabs()
	->role('tablist')
	->panes([
		'Textoooooooooo oo',
		'otroooooo',
		'y otro más'            
	]); 

Lo importante en este último caso es que tanto el array de "links" como los "panes" apuntados por los primeros conserven los mísmos índices.

También puede hacerse de píldoras en vertical aplicando pills() y vertical() al nav.

Ej:

	// Nav como tab-list
	echo tag('nav')->content([  
		[
			'anchor' => 'Uno',
			'href'   => '#uno'
		],

		[
			'anchor' => 'Dos',
			'href' => '#dos'
		],

		[
			'anchor' => 'Tres',
			'href'   => '#tres'
		]
	])->class('mb-3')
	->vertical()
	->pills()
	->role('tablist')
	->panes([
		'Textoooooooooo oo',
		'otroooooo',
		'y otro más'            
	]);  


# Navbar

Hay varios sub-componentes y utilidades para armar un navbar entre ellos navbarBrand, navbarToggler,...

Brand

Para mostrar el logo en el navbar ya sea como texto o imágen con o sin enlace la estructura básica incluye al navbar, un contenedor y un navbarBrand.

Ej:

	echo tag('navbar')->content(
		tag('container')->fluid()->content([
			tag('navbarBrand')->anchor('Navbar')
		])
	);  

con enlace:

	echo tag('navbar')->content(
		tag('container')->fluid()->content([
			tag('navbarBrand')->anchor('Navbar')->href('#')
		])
	);  

Con imágen es solo pasar la imágen dentro del anchor del navbarBrand:

	echo tag('navbar')->content(
		tag('container')->fluid()->content([
			tag('navbarBrand')->anchor(
				tag('img')->src(asset('img/ai_logo.png'))->witdh(24)->height(24)
			)->href('#')
		])
	)

Imágen + texto

	echo tag('navbar')->content(
		tag('container')->fluid()->content([
			tag('navbarBrand')->anchor(
				tag('img')->src(asset('img/ai_logo.png'))->witdh(24)->height(24)
				->class("d-inline-block align-text-top") . '&nbsp;&nbsp; Some text'
			)->href('#') 
		])
	)


Tema / estilos

De mínima puede cambiarse a tema oscuro aplicando dark() sobre el navbar.

Ej:

	echo tag('navbar')->content(
		tag('container')->fluid()->content([
			tag('navbarBrand')->anchor(
				tag('img')->src(asset('img/ai_logo.png'))->witdh(24)->height(24)
				->class("d-inline-block align-text-top") . '&nbsp;&nbsp; Some text'
			)->href('#') 
		])
	)->class('mb-3')
	->dark()


Navbar collapsable

Cuando hay varios elementos en el navbar entonces éste debe colapsarse en dispositivos con poco ancho renderizando en su lugar el ícono de hamburguesa conocido como "toggler" (elemento navbarToggler) ya que al ser clickeado se alterna entre el colapso y descolapso de elementos.

Use expand() para que los elementos dentro del navbar se muestren en vez de estar colapsados.

Ej:

	echo tag('navbar')->content(
		tag('container')->fluid()->content([
			tag('navbarBrand')->anchor(
				tag('img')->src(asset('img/ai_logo.png'))->witdh(24)->height(24)
				->class("d-inline-block align-text-top") . '&nbsp;&nbsp; Some text'
			)->href('#'),
			
			tag('navbarToggler')->target("#navbarNavAltMarkup"),

			tag('navbarCollapse')->content([
				tag('navbarNav')->content([
					tag('navLink')->anchor('Home')->active(),
					tag('navLink')->anchor('Features')->href('#features'),
					tag('navLink')->anchor('Pricing')->href('#pricing'),
					tag('navLink')->anchor('Disabled')->disabled()->attributes(["tabindex" => "-1"])
				])
			])->id("navbarNavAltMarkup")
		])
	)->expand();

O enviando un array más simple a navbarNav.

Ej:

	echo tag('navbar')->content(
		tag('container')->fluid()->content([
			tag('navbarBrand')->anchor(
				tag('img')->src(asset('img/ai_logo.png'))->witdh(24)->height(24)
				->class("d-inline-block align-text-top") . '&nbsp;&nbsp; Some text'
			)->href('#'),
			
			tag('navbarToggler')->target("#navbarNavAltMarkup"),

			tag('navbarCollapse')->content([
				tag('navbarNav')->content([
					[
						'anchor'   => 'Home'
					],
					[
						'anchor'   => 'Features',
						'href'     => '#features'
					],
					[
						'anchor'   => 'Pricing',
						'href'     => '#pricing'
					],
					[
						'anchor'   => 'Disabled',
						'class'    => 'disabled',
						'aria-disabled' => "true"                        
					],
				])
			])->id("navbarNavAltMarkup")
		])
	)->expand()

Se puede agregar dropDown como un elemento más al navbar.

Ej:

	echo tag('navbar')->content(
		tag('container')->fluid()->content([
			tag('navbarBrand')->anchor(
				tag('img')->src(asset('img/ai_logo.png'))->witdh(24)->height(24)
				->class("d-inline-block align-text-top") . '&nbsp;&nbsp; Some text'
			)->href('#'),
			
			tag('navbarToggler')->target("#navbarNavAltMarkup"),

			tag('navbarCollapse')->content([
				tag('navbarNav')->content([
					[
						'anchor'   => 'Home'
					],
					[
						'anchor'   => 'Features',
						'href'     => '#features'
					],
					[
						'anchor'   => 'Pricing',
						'href'     => '#pricing'
					],
					[
						'anchor'   => 'Disabled',
						'class'    => 'disabled',
						'aria-disabled' => "true"                        
					],

					/*
						Dropdown
					*/
					tag('dropdown')->content(
						tag('dropdownButton')->id('dropdownMenuButton33')->content('Dropdown button') .    
						tag('dropdownMenu')->ariaLabel('dropdownMenuButton33')->content(
							tag('dropdownItem')->href('#')->anchor('Action') .
							tag('dropdownItem')->href('#')->anchor('Another action') .
							tag('dropdownDivider') .
							tag('dropdownItem')->href('#')->anchor('Something else here')
						)
					),
				])
			])->id("navbarNavAltMarkup")
		])
	)->class('mb-3')->expand()


Para que el <form> barra de búsqueda quede del lado derecho y el navbarBrand del lado izquierdo debe sacarse fuera del form. La distribución de estos elementos sería la de un flexbox.	

Ej:

	echo tag('navbar')->content(
		tag('container')->fluid()->content([
			tag('navbarBrand')->anchor(
				tag('img')->src(asset('img/ai_logo.png'))->witdh(24)->height(24)
				->class("d-inline-block align-text-top") . '&nbsp;&nbsp; Some text'
			)->href('#'),

			tag('form')->class("d-flex")->content([
				tag('search')->placeholder("Search")->class('me-2'),
				tag('button')->outline()->success()->value('Search')
			])
		])->class('mb-3')
	)

Posicionamiento

Para fijar el navbar al márgen 

...	superior: 			fixed-top
...	superior flotando:  sticky-top
...	inferior: 			fixed-bottom

Ej:

	echo tag('navbar')->content(
		tag('container')->fluid()->content([
			tag('navbarBrand')->anchor(
				tag('img')->src(asset('img/ai_logo.png'))->witdh(24)->height(24)
				->class("d-inline-block align-text-top") . '&nbsp;&nbsp; Some text'
			)->href('#'),
			
			tag('navbarToggler')->target("#navbarNavAltMarkup"),

			tag('navbarCollapse')->content([
				tag('navbarNav')->content([
					[
						'anchor'   => 'Home'
					],
					[
						'anchor'   => 'Features',
						'href'     => '#features'
					],
					[
						'anchor'   => 'Pricing',
						'href'     => '#pricing'
					],
					[
						'anchor'   => 'Disabled',
						'class'    => 'disabled',
						'aria-disabled' => "true"                        
					]
				])
			])->id("navbarNavAltMarkup")
		])
	)->class('mb-3 fixed-top');


Scrolling

Es posible habilitar el scrolling vertical de los elementos colapsados:

https://getbootstrap.com/docs/5.0/components/navbar/#scrolling


Responsive behaviors

Navbars can use .navbar-toggler, .navbar-collapse, and .navbar-expand{-sm|-md|-lg|-xl|-xxl} classes to determine when their content collapses behind a button. In combination with other utilities, you can easily choose when to show or hide particular elements.

For navbars that never collapse, add the .navbar-expand class on the navbar. For navbars that always collapse, don’t add any .navbar-expand class.


# Offcanvas

Son paneles laterales ocultos que se desocultan ante un evento y presentan generalmente opciones.

El panel se puede desocultar con un offcanvasLink o bien un offcanvasOpenButton y se cierra con un offcanvasCloseButton.

La estructura básica es:

	<button | link href="#id">

	<offcanvas id="">
		<offcanvasHeader>
			<offcanvasTitle>
			<offcanvasCloseButton>

		<offcanvasBody>

Ej:

	echo tag('div')->content([
		tag('offcanvasLink')->anchor('Link with href')->href('#offcanvasExample')->class('btn btn-primary'),
		tag('offcanvasOpenButton')->anchor('Button with data-bs-target')->href('#offcanvasExample')
	])->class('vstack gap-2 col-md-5 mx-auto my-3');

	echo tag('offcanvas')->id("offcanvasExample")->content([
		tag('offcanvasHeader')->content([
			tag('offcanvasTitle')->text('Offcanvas'),
			tag('offcanvasCloseButton')
		]),

		tag('offcanvasBody')->content([
			/*
				Body example
			*/
			
			'Some text as placeholder. In real life you can have the elements you have chosen. Like, text, images, lists, etc.',

			tag('dropdown')->content(
				tag('dropdownButton')->id('dropdownMenuButtonX')->content('Dropdown button')
				->success() .
	
				tag('dropdownMenu')->ariaLabel('dropdownMenuButtonX')->content(
					tag('dropdownItem')->href('#')->anchor('Action') .
					tag('dropdownItem')->href('#')->anchor('Another action') .
					tag('dropdownDivider') .
					tag('dropdownItem')->href('#')->anchor('Something else here')
				)
			)

		])
	]);


Se puede simplificar haciendo que <offcanvas> aceptara el Title y el Body (como content) 

	<offcanvas id="">
		<offcanvasHeader>
			<offcanvasTitle>
			<offcanvasCloseButton>

		<offcanvasBody>

A ...

	<offcanvas id="" title="" body="">

Ej:

		echo tag('div')->content([
            tag('offcanvasLink')->anchor('Link with href')->href('#offcanvasExample')->class('btn btn-primary'),
        ])->class('vstack gap-2 col-md-5 mx-auto my-3');

        echo tag('offcanvas')->id("offcanvasExample")->title('Offcanvas')->body([
            // The body
            )
        ]);

Placement

There’s no default placement for offcanvas components, so you must add one of the modifier classes below;

.offcanvas-start 	places offcanvas on the left of the viewport (shown above)
.offcanvas-end 		places offcanvas on the right of the viewport
.offcanvas-top 		places offcanvas on the top of the viewport
.offcanvas-bottom 	places offcanvas on the bottom of the viewport

En si es aplicar pos() con {'left', 'right', 'top' o 'bottom'} a offcanvas.

Ej:

	echo tag('div')->content([
		tag('offcanvasLink')->anchor('Link with href')->href('#offcanvasExample')->class('btn btn-primary'),
	])->class('vstack gap-2 col-md-5 mx-auto my-3');

	echo tag('offcanvas')->id("offcanvasExample")->title('Offcanvas')->body([
		// The body
		)
	])->bottom();

Scroll

Para habilitar el scroll del resto de la página se debe setear el atributo

	data-bs-scroll="true"


Oscurecer el fondo

Para oscurecer el resto de la página (que se verá como en segundo plano) es seteando el atributo

	data-bs-backdrop="true" 

que se puede usar en conjunto con data-bs-scroll="true"


Se implementaron backdrop() y scroll() para oscurecer el fondo y habilitar scroll respectivamente.

Ej:

		echo tag('offcanvas')->id("offcanvasExample")->title('Offcanvas')->body(
            'Some text as placeholder. In real life you can have the elements you have chosen. Like, text, images, lists, etc.'
        ])
        ->pos('left')
        ->backdrop()
        ->scroll();

Nota: recordar que es necesario un enlace o botón o algún evento para visibilizar el offcanvas.


# Pagination

Para crear un paginador es con paginator.

Ej:

	echo tag('paginator')->content([
		[
			'href'   => '#?page=1',
			'active' => true
		],
		[
			'href' => '#?page=2'
		],
		[
			'href' => '#?page=3'
		]
	]);

Sizing

Se pueden aplicar small() o larg() a paginator.

	echo tag('paginator')->content([
		[
			'href'   => '#?page=1',
			'active' => true
		],
		[
			'href' => '#?page=2'
		],
		[
			'href' => '#?page=3'
		]
	])->class('mt-5')->large();

Alignment

En si el paginador es una "lista" desordenada (ul) y se pueden aplicar flexbox utilities sobre el <ul class="pagination"> mediante options() 

Ej:

	echo tag('paginator')->content([
		[
			'href'   => '#?page=1',
			'active' => true
		],
		[
			'href' => '#?page=2'
		],
		[
			'href' => '#?page=3'
		]
	])->class('mt-5')->options(['justify-content-center']);


Next & Previous

Para renderizar los botones de atrás y adelante es aplicando withPrev() y withNext()

Ej:

	echo tag('paginator')->content([
		[
			'href'   => '#?page=1',
			'active' => true
		],
		[
			'href' => '#?page=2'
		],
		[
			'href' => '#?page=3'
		]
	])
	->withPrev([
		'href'   => '#?page=1',
		'anchor' => 'Previous',
		'disabled' => true
	])
	->withNext([
		'href'   => '#?page=4',
		'anchor' => 'Next'
	]);


Mostrar flechas

Si se desea mostrar íconos de flechas en vez las leyendas Next | Previous, solo reemplace por '&laquo;' y '&raquo;' respectivamente.

	->withPrev([
		'href'   => '#?page=1',
		'anchor' => '&laquo;',
		'disabled' => true
	])
	->withNext([
		'href'   => '#?page=11',
		'anchor' => '&raquo;'
	])


Personalización

Ej:

	/*
		Previous | 1 | 2 | 3 | .. | 10 | Next
	*/
	echo tag('paginator')->content([
		[
			'href'   => '#?page=1',
			'active' => true
		],
		[
			'href' => '#?page=2'
		],
		[
			'href' => '#?page=3'
		],
		[
			'href' => '#',
			'anchor' => '..',
			'disabled' => true
		],
		9 => [
			'href' => '#?page=10'
		]
	])
	->class('mt-5')
	->large()
	->options(['justify-content-center'])
	->withPrev([
		'href'   => '#?page=1',
		'anchor' => 'Previous',
		'disabled' => true
	])
	->withNext([
		'href'   => '#?page=11',
		'anchor' => 'Next'
	]);

El truco está en:

	[
		'href' => '#',
		'anchor' => '..',
		'disabled' => true
	],
	9 => [
		'href' => '#?page=10'
	]

Se especifica un anchor '..' donde normalmente va el número de página en automático y se agrega en la secuencia la página 10 que sería la #4 ya que es un array de 5 elementos comenzando por 0 pero se fuerza se vea como #10 agregándo manualmente el índice como 9.

Lógica incluida

Si la página activa es la primera, entonces se auto-desabilita el botón prev y no es necesario pasar 'disabled' => true

Ej:

	echo tag('paginator')->content([
		[
			'href'   => '#?page=1',
			'active' => true              
		],
		[
			'href' => '#?page=2'                
		],
		[
			'href' => '#?page=3'
		]
	])
	->class('mt-5')
	->large()
	->options(['justify-content-center'])
	->withPrev([
		'href'   => '#?page=1',
		'anchor' => 'Previous'
	])
	->withNext([
		'href'   => '#?page=4',
		'anchor' => 'Next'
	]);
	

Podría incluirse más lógica como que:

- Si hay una sola página que se deshabiliten tanto Prev como Next.

- Que si se recibe como parámetro del número total de páginas y está activa la última, entonces se desabilite Next.


NOTA:

Veanse tambien las clases BootstrapPaginator de PHP y JavaScript


Paginador de meses

Debe aplicarse la clase de css "pagination-month" al paginator e incluir en principio cierto html para cada mes-año.

Ej:

	/*
		Previous | Ene | Feb | Mar | Next
	*/
	echo tag('paginator')
	->class('pagination-month')
	->content([
		[
		'href'   => '#?page=1',
		'anchor' => '
					<p class="page-month">Ene</p>
					<p class="page-year">2021</p>'
		],
		[
			'href'   => '#?page=2',
			'anchor' => '
						<p class="page-month">Feb</p>
						<p class="page-year">2021</p>',
			'active' => true
		],
		[
		'href'   => '#?page=3',
		'anchor' => '
					<p class="page-month">Mar</p>
					<p class="page-year">2021</p>'
		],
		// ...
	])
	->options(['justify-content-center'])
	->withPrev([
		'href'   => '#?page=1',
		'anchor' => '&laquo;',
	])
	->withNext([
		'href'   => '#?page=11',
		'anchor' => '&raquo;',
	]);


# Placeholders

Use loading placeholders for your components or pages to indicate something may still be loading.

En particular se implemetó para cardTitle, cardSubtitle, cardText e inputButton.

Ej:

	echo tag('card')->style('width: 18rem;')->class('my-3')
	->content(
		tag('cardImgTop')->src(asset('img/mail.png'))
	)
	->body([            
		tag('cardTitle')
		->placeholder(),
		
		tag('cardSubtitle')
		->placeholder(),

		tag('cardText')
		->placeholder(),
		
		tag('inputButton')->value('Go somewhere')
		->placeholder()
	]);

Color

Se pueden aplicar los colores de background bg-* con bg()

Ej:

	echo tag('card')->style('width: 18rem;')->class('my-3')
	->content(
		tag('cardImgTop')->src(asset('img/mail.png'))
	)
	->body([            
		tag('cardTitle')
		->placeholder()->bg('danger'),
		
		tag('cardSubtitle')
		->placeholder()->bg('warning'),

		tag('cardText')
		->placeholder()->bg('success'),
		
		tag('inputButton')->value('Go somewhere')
		->placeholder()
	]);


# Popover

Un popover debe ser habilitado via JavaScript. La forma más sencilla es habilitar todos referenciándolos via clase css. Se utlizará la clase "popovers" para hacer esa referencia.

	<button type="button" 
	class="btn btn-lg btn-danger popovers" 
	data-bs-toggle="popover" 
	title="Popover title" 
	data-bs-content="And here's some amazing content. It's very engaging. Right?">Click to toggle popover</button>

O con links

	<a role="button" 
	class="btn btn-lg btn-danger" 
	data-bs-toggle="popover" 
	data-bs-trigger="focus" 
	title="Dismissible popover" 
	data-bs-content="And here's some amazing content. It's very engaging. Right?">Dismissible popover</a>

El JavaScript habilitante para los popovers es:

	var popover = new bootstrap.Popover(document.querySelector('.popovers'), {
		container: 'body'
	})

Ej:

	echo tag('popoverButton')
	->content('Click to toggle popover')
	->title('Popover title')
	->body("And here's some amazing content. It's very engaging. Right?")
	->class('btn-lg')->danger();

Four directions

Aplique pos() con top, bottom, left o right.

	echo tag('popoverButton')
	->content('Click to toggle popover')
	->title('Popover title')
	->body("And here's some amazing content. It's very engaging. Right?")
	->class('btn-lg')->danger()->pos('top');

Es importante notar que sino hay espacio el popover no se materializa aunque se intentará hacerlo aparecer del lado derecho (default).

Dismiss on next click

Se logra aplicando dismissible()

Ej:
	echo tag('popover')
	->content('Click to toggle popover')
	->title('Popover title')
	->body("And here's some amazing content. It's very engaging. Right?")
	->as('button')
	->class('btn-lg mt-5')->danger()->pos('top')
	->dismissible();

Casting

En caso de querer usar un <button> haga el casting correspondiente con as()

	echo tag('popover')
	->content('Click to toggle popover')
	->title('Popover title')
	->body("And here's some amazing content. It's very engaging. Right?")
	->as('button')
	->class('btn-lg mt-5')->danger()->pos('top');

De momento solo se puede cambiar el color del botón cuando se hace el casting a button. A corregir moviendo el casting al método tag.


# Progress bars

Indican el avance de un proceso, generalmente en porcentaje (%) y por ello por defecto se asume:

	min = 0
	max = 100

Ej:

	echo tag('progress')->content(
		tag('progressBar')->current(80)
	);

También claro puede especificarse el rango.

Ej:

	echo tag('progress')->content(
		tag('progressBar')
		->min(5)
		->max(25)
		->current(15)
	);

Labels

Para mostrar una label con el % de avance es con withLabel() sobre el progresBar.

Ej:

	echo tag('progress')->content(
		tag('progressBar')
		->min(5)
		->max(25)
		->current(15)->withLabel()
	)

Height

Se logra seteando la altura en progress.

	echo tag('progress')->content(
		tag('progressBar')
		->current(25)->withLabel()
	)->class('my-5')->style("height: 50px");


Background colors

Aplicando bg($color) sobre el progressBar.

Ej:

	echo tag('progress')->content(
		tag('progressBar')
		->current(25)->withLabel()->bg('danger')
	)->class('my-5')->style("height: 50px;");


Multiple bars

Include multiple progress bars in a progress component if you need.

	echo tag('progress')->content([
		tag('progressBar')
		->current(15)->withLabel()->bg('primary'),

		tag('progressBar')
		->current(30)->withLabel()->bg('success'),

		tag('progressBar')
		->current(25)->withLabel()->bg('info')
	])->class('mt-3');

Striped

Se puede aplicar un gradiente de color con striped() sobre el progresBar.

Ej:

	echo tag('progress')->content(
		tag('progressBar')
		->min(5)
		->max(25)
		->current(15)->withLabel()->striped()
	)->class('my-5');

Animated

Aplicando animated() una progresBar se hace striped y se "anima".

Ej:

	echo tag('progress')->content(
		tag('progressBar')
		->current(25)->withLabel()->bg('danger')->animated()
	)->class('my-5')->style("height: 50px;");


Sería ideal poder encapsular en un componente o en varios (por el tema de las progresBar múltiples).


Con AdminLTE al menos,... se definen clases de css para los tamaños xxs, xs y sm que se pueden aplicar con size()

	echo tag('progress')->content(
	tag('progressBar')
	->current(25)->bg('danger')->animated()
	->size('xxs');  

Barras de progreso verticales

Aplicar vertical() 

	echo tag('progress')->content(
		tag('progressBar')
		->current(20)->bg('primary')->striped()
	)
	->size('xs')
	->vertical();  


# Scrollspy

To easily add scrollspy behavior to your topbar navigation, add data-bs-spy="scroll" to the element you want to spy on (most typically this would be the <body>). Then add the data-bs-target attribute with the ID or class of the parent element of any Bootstrap .nav component.


	body {
		position: relative;
	}

	<body data-bs-spy="scroll" data-bs-target="#navbar-example">
		...
		<div id="navbar-example">
			<ul class="nav nav-tabs" role="tablist">
			...
			</ul>
		</div>
		...
	</body>

<-- no logré funcionara. Queda pendiente ! problema: recarga la página en vez de ir al #id.

# Spinners

Ej:

	echo tag('spinner');

Colors

Ej:

	echo tag('spinner')->textColor('danger')

Growing spinner

	echo tag('spinner')->textColor('danger')->grow();
	
Placement

Solo utilice un wrapper y utilidades como flex. Ej:

	<div class="d-flex justify-content-center">
	<?php
		echo tag('spinner')->textColor('danger')->grow();
	?>
	</div>

Sizing

Use size() con el tamaño expresado en rems. El valor por defecto parecer ser 2 rems.

Ej:

	echo tag('spinner')->class('my-3')->color('danger')->grow()->size(5);


En botones

Ej:

	echo tag('spinner')->class('my-3')->as('button');
	echo tag('p');
	echo tag('spinner')->class('my-3')->grow()->as('button');

Con texto como "Loading..."

Ej:

	echo tag('spinner')->class('my-3')->as('button')->unhide();
	echo tag('p');
	echo tag('spinner')->class('my-3')->grow()->as('button')->unhide();

Para cambiar el texto "Loading.." por otro solo envíelo en content()

Ej:

echo tag('spinner')->class('my-3')->grow()->as('button')->unhide()->content('Cargando..');


# Toasts

Sirven para notificaciones en tiempo real (del lado del front) y por ello no tiene mucho sentido implementarlas en el backend (con PHP)

Importante:

- Toasts are opt-in for performance reasons, so you must initialize them yourself.
- Toasts will automatically hide if you do not specify autohide: false.


To encourage extensible and predictable toasts, we recommend a header and body.

At a minimum, we require a single element to contain your “toasted” content and strongly encourage a dismiss button.

El código HTML mínimo recomendado es el siguiente:

	<!-- button to initialize toast -->
	<button type="button" class="btn btn-primary" id="toastbtn">Initialize toast</button>

	<!-- positioning ->
	<div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">

		<!-- Toast -->
		<div class="toast" id="toastNotice">
			
			<div class="toast-header">  
			<svg class="bd-placeholder-img rounded me-2" width="20" height="20" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" preserveAspectRatio="xMidYMid slice" focusable="false"><rect width="100%" height="100%" fill="#007aff"></rect></svg>
			<strong class="me-auto">Bootstrap</strong>
			<small>11 mins ago</small>
			<button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
			</div>

			<div class="toast-body">
			Hello, world! This is a toast message.
			</div>

		</div>

	</div>

Con el builder:

	echo tag('button')->id("toastbtn")->value("Abrir toast");

	echo tag('div')->class("position-fixed bottom-0 end-0 p-3")->style("z-index: 11")->content(
			tag('toast')->content([
				tag('toastHeader')->content([
					'<svg class="bd-placeholder-img rounded me-2" width="20" height="20" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" preserveAspectRatio="xMidYMid slice" focusable="false"><rect width="100%" height="100%" fill="#007aff"></rect></svg>
					<strong class="me-auto">Bootstrap</strong>
					<small>11 mins ago</small>',

					tag('toastCloseButton')
				]),
				tag('toastBody')->content(
					'Hello, world! This is a toast message.'
				),
			])
	);

Los toast requieren inicialización via JavaScript:

	document.getElementById("toastbtn").onclick = function() {
		var myAlert =document.querySelector('.toast');
		var bsAlert = new bootstrap.Toast(myAlert);//inizialize it
		bsAlert.show();//show it
	};

Notas:

Las "toast" sirven para notificaciones en tiempo real (del lado del front) y por ello no tiene mucho sentido implementarlas en el backend (con PHP)

Boostrap 5 tiene varias opciones más para toast de las descritas aquí.


# Tooltips

Tooltips y popovers son componentes similares siendo lo tooltips para mostrar mucho menos texto y son más simples.

inicialización

Los tooltips deben ser inicializados por motivos de performance:

	var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
		var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
		return new bootstrap.Tooltip(tooltipTriggerEl)
	})

Ej:

	echo tag('tooltip')->title('Some title')->content('Tooltip on top');

Positioning

	echo tag('tooltip')->title('Some title')->content('Tooltip on bottom')->pos('bottom');


# Colors

Para los botones y alertas en particular existen "temas" que ajustan tanto el color del texto como el background de los botones manejados por las clases btn-{color}, btn-outline-{color} y alert-{color}.

Estos se aplican con los "métodos"

	info()
	success()
	warning()
	danger()
	etc

En general existen los "text colors" y "background colors" que se aplican respectivamente con textColor() y bg()

Ej:

	echo tag('card')->style('width: 18rem;')
	->body([            
		tag('cardTitle')->text('Some title'),
		tag('cardText')->text('Some quick example text to build on the card title and make up the bulk of the cards content.'),
		tag('inputButton')->value('Go somewhere')->info()->textColor('white')
	])
	->class('my-3')
	->bg('primary')
	->textColor('white');

Para el caso particular de los botones donde se puede aplicar el outline es con outline() 

Ej:

	tag('button')->content('Botón verde')->class('rounded-pill')->success()->outline()

En algunos componentes se permitió el uso de color() como alternativa a $color()

Ej:

	echo tag('alert')->content(
        tag('alertLink')->href('#')->anchor('A danger content')
    )->color('danger')->dismissible(true);

Equivale a,...

	echo tag('alert')->content(
        tag('alertLink')->href('#')->anchor('A danger content')
    )->danger()->dismissible(true);


Se está transicionando de la forma success() a bg('success') o lo que corresponda por cuestiones de eficiencia. 

Sin embargo hay casos donde no se puede evitar el uso de color()

Ej:

	// incorrecto: bg-{$color}
	tag('listGroupItem')->text('An item #3')->bg('success')

No es lo mismo que 

	// correcto: list-group-item-{$color}
	tag('listGroupItem')->text('An item #3')->color('success')


Opacidad

La opacidad es una propiedad que tiene un rango de 0 a 1 y se aplica con opacity()

Ej:

	echo tag('div')->content('Some content')->textColor('primary')->opacity(0.5);


Gradiente de color

Ej:

  echo tag('div')->class('mt-5 p-3 py-5 mb-2')
  ->content('Gradiente?')
  ->textColor('white')
  ->bg('success')
  ->gradient();


Link colors

En teoría (según la doc oficial), para los links deben aplicarse clases css link-{color} sin embargo,
en principio producen el mismo efecto que los text-{color}

Usar color() para aplicar "link colors".

Ej:

	echo tag('link')
	->href("www.solucionbinaria.com")
	->anchor('SolucionBinaria .com')
	->color('success');


# Borders

Con border() se aplican borders sobre todos los lados.

Ej:

	echo tag('div')->width(100)->content('
		Some content,...
		<p>
		<p>
	')
	->border();

Bordes sobre lados (aditivos)

Ej:

	echo tag('div')->width(100)->content('
      Some content,...
      <p>
      <p>
    ')
    ->border()
    ->border('top bottom');

Bordes sobre lados (substractivo)

Ej:

	echo tag('div')->width(100)->content('
      Some content,...
      <p>
      <p>
    ')
    ->border()
    ->borderSub('top bottom');  // solo a los lados

Border color

Ej:

	echo tag('div')->width(100)->content('
      Some content,...
      <p>
      <p>
    ')
    ->border()
    ->borderSub('top bottom')
	->borderColor('success');

Border width

Ej:

	echo tag('div')->width(100)->content('
      Some content,...
      <p>
      <p>
    ')
    ->border('top left right')
    ->borderColor('success')
	->borderWidth(5);

Border radius

Con borderRad() se especifica la redondez de 0 a 3 donde 0 es sin redondez alguna.

Ej:

	echo tag('div')->width(100)->content('
      Some content,...
      <p>
      <p>
    ')
    ->border('top left right')
    ->borderWidth(5)
    ->borderColor('success')
	->borderRad(3);

Un buen ejemplo de uso es dentro de un buttonGroup.

Ej:

	tag('buttonGroup')
	->content([
		tag('closeModal')
		->text('Cerrar')
		->borderRad(0)
		->style('margin-right:0.25em'),
		
		tag('submit')
		->id("save_row")
		->text('Guardar')
		->borderRad(0)
	])   


Bordes como "píldora"

Con borderPill() se consigue la forma de píldora.

Ej:

	echo tag('div')->width(100)->content('
      Some content,...
      <p>
      <p>
    ')
    ->borderWidth(5)
    ->borderColor('warning')
	->borderRad(3)
	->borderPill();


Bordes circulares

Con bordeCircle() se consiguen borders circulares.

Ej:

	echo tag('div')->width(100)->content('
      Some content,...
      <p>
      <p>
    ')
    ->borderWidth(5)
    ->borderColor('warning')
	->borderRad(3)
	->borderCircle();


Redondear corners específicos

Con borderCorner() se pueden redondear solo determinados corners y en particular el efecto es visible cuando se aplica borderPill() o borderCircle()

Ej:

	echo tag('div')->width(100)->content('
      Some content,...
      <p>
      <p>
    ')
    //->border()
    ->borderWidth(5)
    ->borderRad(3)
    ->borderColor('warning')
    ->borderPill()
	->borderCorner('left top');

# Size

- Relative to the parent

Width

Valores posibles: cualquier entero de 0 a 100 y 'auto'

Ej:

	echo tag('div')->content(
      'Width 75%'
    )
    ->w(75)
    ->bg('warning')
    ->class('p-3');

Height

Valores posibles: cualquier entero de 25, 50, 75, 100 y 'auto'.

	echo tag('div')->content(
	'Height 75%'
	)
	->w(75)
	->h(75)
	->bg('warning')
	->class('d-inline-block');


- Relative to the viewport

You can also use utilities to set the width and height relative to the viewport.

	<div class="min-vw-100">Min-width 100vw</div>
	<div class="min-vh-100">Min-height 100vh</div>
	<div class="vw-100">Width 100vw</div>
	<div class="vh-100">Height 100vh</div>


# Text utilities

Para justificar texto (o como si texto se tratara) use:

	left()
	center()
	right()

Ej:

	echo tag('h4')
	->text('Indigo!')
	->bg('indigo')
	->center();

La justificación (centrado, a izquierda o a derecha) se puede aplicar con un breakpoint (solo cuando lo supere).

Ej:

	echo tag('h4')
	->text('Orange!')
	->bg('orange')
	->lgCenter();

Los breakpoints en BT5 son sm, md, lg y xl.

Nota: las clases bg- para colores como "indigo" u "orange" no están disponibles con Bootstrap pero si en templates como AdminLTE. 


# Tables

Las tablas se crean con tag('table') que recibe arrays de rows y cols.

NOTA: rows y cols está alrevés !!! tocaría intercambiar los nombres.

Ej:

	echo tag('table')
    ->rows([
      '#',
      'First',
      'Last',
      'Handle'
    ])
    ->cols([
      [
        1,
        'Mark',
        'Otto',
        '@mmd'
      ],
      [
        2,
        'Lara',
        'Cruz',
        '@fat'
      ],
      [
        3,
        Juan',
        'Cruz',
        '@fat'
      ],
      [
        4,
        'Feli',
        'Bozzolo',
        '@facebook'
      ]
    ]);

Colores

Se aplican colores con color()

Ej:

	echo tag('table')
    ->rows([
      '#',
      'First',
      'Last',
      'Handle'
    ])
    ->cols([
      [
        1,
        'Mark',
        'Otto',
        '@mmd'
      ],
      [
        2,
        'Lara',
        'Cruz',
        '@fat'
      ],
      [
        3,
        'Juan',
        'Cruz',
        '@fat'
      ],
      [
        4,
        'Feli',
        'Bozzolo',
        '@facebook'
      ]
    ])
    ->color('primary');


Aplicar color al head

Mediante headOptions() se puede pasar un array de atributos al head de la tabla.

	echo tag('table')
    ->rows([
      '#',
      'First',
      'Last',
      'Handle'
    ])
    ->cols([
      [
        1,
        'Mark',
        'Otto',
        '@mmd'
      ],
      [
        2,
        'Lara',
        'Cruz',
        '@fat'
      ],
      [
        3,
        'Juan',
        'Cruz',
        '@fat'
      ],
      [
        4,
        'Feli',
        'Bozzolo',
        '@facebook'
      ]
    ])
    ->color('light')
    ->headOptions([
      'color' => 'dark'
    ]);


Aplicar color a una columna 

Se implementó en principio para colorear una sola columna (que es el caso típico) para desctacarla aplicando colorCol($index, $color) 

 	->colorCol([
        'pos'   => 1, 
        'color' => 'primary'
      ])

Ej:

	echo tag('table')
      ->rows([
      '#',
      'First',
      'Last',
      'Handle'
      ])
      ->cols([
      [
          1,
          'Mark',
          'Otto',
          '@mmd'
      ],
      [
          2,
          'Lara',
          'Cruz',
          '@fat'
      ],
      [
          3,
          'Juan',
          'Cruz',
          '@fat'
      ],
      [
          4,
          'Feli',
          'Bozzolo',
          '@facebook'
      ]
      ])
      ->color('light')
      ->headOptions([
        'color' => 'dark'
      ])
      ->colorCol([
        'pos'   => 1, 
        'color' => 'primary'
      ]);


# Animations

Parecen faltar en BT5 pero están en "MDB Pro Essential package" 

Más
https://dev.to/mdbootstrap/bootstrap-5-animations-1kf0


~ Widgets

Basados en elementos de formulario se han construido varios Widgets. 


# Milestone / Timeline

El siguiente componente puede usare como milestone o timeline. 

Ej:
	
	echo tag("h_timeline")->content([
		[
			'title' => 2010,
			'subTitle' => 'algo remoto'
		],
		[
			'title' => 2011,
			'subTitle' => 'algo hermoso'
		],
		[
			'title' => 2016,
			'subTitle' => 'algo horrible'
		],        
	]); 


# Search tool

Este componente consciste en un inputSeach + icon

Ej:

	echo tag('searchTool')->id('my_search');


# Mask

Son overlays sobre imágenes con posibilidad de texto superpuesto.

Ej:

	echo tag('mask')->content(
		'<img src="'. asset('img/slide-1.jpeg') .'" class="w-100" alt="Louvre Museum">'
	);   

o

	echo tag('mask')
	->img([
		'src' => asset('img/slide-3.jpeg'), 
		'class' => "w-100",
		'alt' => "Louvre Museum"
	]);

Para superponer texo use text()

Ej:

	echo tag('mask')
	->img([
		'src' => asset('img/slide-3.jpeg'), 
		'class' => "w-100",
		'alt' => "Louvre Museum"
	])
	->text('Puedes verme?')->style('font-size:130%;');');


# Shadow

Con el tag shadow se obtienen sombras para contenedores.

	echo tag('shdow')
    ->content('Some content')
    ->class("p-3");

Hay varias clases para sombras en shadows.css del widget "shadow"

	echo tag('shadow')
	->content('Some content')
	->class("p-3 shadow-lg");


# Note

Con el tag note se pueden crear notas y opcionalmente especificar color con color()

Ej:

	echo tag('note')
	->text('<strong>!!! Note secondary:</strong> Lorem, ipsum dolor sit amet consectetur adipisicing
      elit. Cum doloremque officia laboriosam. Itaque ex obcaecati architecto! Qui
      necessitatibus delectus placeat illo rem id nisi consequatur esse, sint perspiciatis
      soluta porro?')
	->color('secondary');


# Steps

El tag step permite crear milestones minimalistas para formularios en varios pasos.

Ej:

	echo tag('steps')->max(4)->current(3);


Otro componente es wizard_steps

Ej:
	echo tag('wizard_steps')
	->content([
		[
			'href'        => "#s1",
			'title'       => 'Step 1',
			'description' => 'Step 1 description'
		],
		[
			'href'        => "#s2",
			'title'       => 'Step 2',
			'description' => 'Step 2 description'
		],
		[
			'href'        => "#s3",
			'title'       => 'Step 3',
			'description' => 'Step 3 description'
		],       
	])
	->current(2);

También pueden setearse solo algunos campos.

Ej:

	echo tag('wizard_steps')
	->content([
		[
			'href'        => "#s1"
		],
		[
			'href'        => "#s2"
		],
		[
			'href'        => "#s3"
		],    
		[
			'href'        => "#s4",
		],    
	])
	->current(3);

O incluso solo pasar la cantidad de pasos y el paso actual.

Ej:

	echo tag('wizard_steps')
	->max(5)
	->current(3);

Con vertical() debería verse vertical pero parece que faltó importar CSS.
	

# Tags que no cierran

Hay ciertos tag que la W3 ha determinado que deben abrirse pero no cerrarse como el hr. En su implementación eso es demarcado con 

	$attributes['close_tag'] = false

Ej:

	static function hr(Array $attributes = [], ...$args){
		$attributes['close_tag'] = false;

		return static::tag('hr', '', $attributes, null,...$args);
	}

Logicamente un tag que no cierra tampoco puede tener "contenido" entre apertura y cierre porque hay un solo tag.

# Select2

Select2 actua sobre un select normal y solo requiere incluir CSS y JS e inicializar el componente:

	<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
	<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>	

Ej:

	 echo tag('select')
	->id('sexo')
	->options([
		'varon' => 1,
		'mujer' => 2
	])
	->default(1)
	->placeholder('Su sexo')
	->class('my-3');


inicialización (requerida) del componente:

	$(function () {
      $( '#sexo' ).select2( {
          theme: 'bootstrap-5'
      } );
    })

También funciona con select con "option groups".

Ej:

	echo tag('select')
	->id('comidas')
	->placeholder('Tu comida favorita')
	->options([
		'platos' => [
			'Pasta' => 'pasta',
			'Pizza' => 'pizza',
			'Asado' => 'asado'
		],

		'frutas' => [
			'Banana' => 'banana',
			'Frutilla' => 'frutilla'
		]
	])
	//->multiple()   
	->class('my-3');


En cualquier caso si se habilita el modo "múltiple" entonces las opciones seleccionadas se mostrarán como "tags" y podrán entonces removerse también fácilmente.

La forma fácil

En vez de usar tag("select"), use tag("select2") que ya incorpora la clase '.select2' con lo cual se pueden inicializar todos los select2 de forma sencilla así:

Ej:

	echo tag('select2')
	->id('comidas_duallistbox')
	->placeholder('Tu comida favorita')
	->options([
		'Pasta' => 'pasta',
		'Pizza' => 'pizza',
		'Asado' => 'asado',
		'Banana' => 'banana',
		'Frutilla' => 'frutilla'
	])
	->multiple()   
	->class('my-3');


	$(function () {
      $( '.select2' ).select2( {
          theme: 'bootstrap-5'
      } );
    })


PD: Es posible elegir el color de los tags aunque no se implementó esta funcionalidad.

Más
https://select2.github.io/


# Bootstrap Duallistbox

Plugin que debe instalarse e inicializarse:

	<script src="dist/jquery.bootstrap-duallistbox.min.js"></script>
	<link rel="stylesheet" type="text/css" href="../src/bootstrap-duallistbox.css">

Requiere también de JQuery y PopperJs.

inicialización:

	//Bootstrap Duallistbox
	$("#element").bootstrapDualListbox({
		// see next for specifications
	});

O sin parámetros y aplicándolo a cualquier en general via clase '.duallistbox'

	//Bootstrap Duallistbox
    $('.duallistbox').bootstrapDualListbox()

Pero es altamente configurable.

Ej:

	var demo1 = $('select[name="duallistbox_demo1[]"]').bootstrapDualListbox({
      nonSelectedListLabel: 'Available Payees',
      selectedListLabel: 'Selected Payees',
      preserveSelectionOnMove: 'moved',
      moveAllLabel: 'Move all',
      removeAllLabel: 'Remove all'
    });

En si el ÚNICO requisito del componente es que sea un select *múltiple*

Ej:

	<?php

	echo tag('select')
      ->id('comidas_duallistbox')
      ->placeholder('Tu comida favorita')
      ->options([
          'Pasta' => 'pasta',
          'Pizza' => 'pizza',
          'Asado' => 'asado',
          'Banana' => 'banana',
          'Frutilla' => 'frutilla'
      ])
      ->multiple()   
      ->class('my-3');

	?>

	<script>    
    	$('#comidas_duallistbox').bootstrapDualListbox()
  	</script>

La forma fácil

En vez de usar select use el componente duallistbox que ya incorpora la clase .duallistbox

Ej:

	echo tag('duallistbox')
	->id('comidas_duallistbox')
	->placeholder('Tu comida favorita')
	->options([
		'Pasta' => 'pasta',
		'Pizza' => 'pizza',
		'Asado' => 'asado',
		'Banana' => 'banana',
		'Frutilla' => 'frutilla'
	])
	->multiple()   
	->class('my-3');

Entonces puede inicializar todos los duallistbox de una vez así:

	$('.duallistbox').bootstrapDualListbox()

Más
https://github.com/istvan-ujjmeszaros/bootstrap-duallistbox


# Input mask

Requiere instalar el plugin:

	<!-- InputMask -->
    <script src="<?= asset('adminlte/plugins/moment/moment.min.js') ?>"></script>
    <script src="<?= asset('adminlte/plugins/inputmask/jquery.inputmask.min.js') ?>"></script>

Fecha 

Por defecto es 'dd/mm/yyyy'

	<?php
		echo tag('inputMask')->id('mydate');
	?>

O especificando el formato:

	<?php
		echo tag('inputMask')->id('mydate')->format('mm/dd/yyyy');
	?>

Telefono

	<?php
		echo tag('phoneMask')->id('myphone')->format('(01) 999-9999');
	?>

Hay otras máscaras como para IP.

# Date picker

Presenta interferencia con el js de jQuery UI por lo cual *no* pueden usarse simultaneamente.

Requisitos:

	<!-- datepicker -->
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css" />
	<script src="http://simplerest.lan:8082/public/assets/js/plugins/bootstrap-datepicker/bootstrap-datepicker.min.js"></script>

Ej:

	<div class="input-group date" id="reservationdatetime">
		<input type="text" class="form-control" id="date"/>
		<span class="input-group-append">
		<span class="input-group-text bg-light d-block">
			<i class="fa fa-calendar"></i>
		</span>
		</span>
	</div>

Y debe inicializarse:

	<script>
		$('#reservationdatetime').datepicker();
	</script>

Con el builder podría hacerse algo así:

 	echo tag('inputGroup')
	->content(
		tag('inputText')
	)
	->append(
		tag('button')->info()->icon('calendar')
	)->class('mb-3 date')->id("reservationdatetime");


En este caso es posible personalizar el color del botón aplicando info(), warning(), etc.

También es posible cambiar de lugar el botón con el ícono de calendario cambiando append() por prepend().

	echo tag('inputGroup')
	->content(
		tag('inputText')
	)
	->prepend(
		tag('button')->info()->icon('calendar')
	)->class('mb-3 date')->id("reservationdatetime");

