<?php

use simplerest\core\libs\Bt5Form; 
use simplerest\core\libs\Tag;


Tag::registerBuilder(\simplerest\core\libs\Bt5Form::class);

Bt5Form::macro('salutor', function($name, $adj)
{
    return "<span/>Hello $adj $name</span>";
});

Bt5Form::setIdAsName();

?>

<div class = "row mt-5">
    <div class = "col-6 offset-3">

    <?php 

    echo tag('h3')->text("Datos")->class('mb-3');

    echo tag('alert')->content('Some content')->danger();

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
    ])
    ->multiple()
    ->class('my-3');

    echo Bt5Form::select(name:'sexo', options:[
        'varon' => 1,
        'mujer' => 2
    ], default:1, placeholder:'Su sexo', attributes:['class' => 'my-3']);

    echo Bt5Form::dataList(listName:'datalistOptions', id:'occupation', options:[
        'programador',
        'software engenierer'
    ], placeholder:'Escriba aquí', label:'Ocupación');

    /*
        El tag 'hr' ni siquiera está definido en la clase Html
    */
    echo tag('hr')->style('color:cyan');

    echo tag('p')->text("Hola mundo cruel");

	echo tag('color')->name('my_color')->text('Color')->id('c1');
	

    echo Bt5Form::div(content:[
        tag('span')->text('@')->id('basic-addon')->class('input-group-text'),
        tag('text')->name('nombre')->placeholder("Username")
    ],
        class:"input-group mb-3"
    );

    //

    echo tag('label')->name("edad")->text("Edad");
    echo Bt5Form::range(name:'edad', min:0, max:99, default:10, class:'my-3');

    echo tag('checkGroup')->content([
        Bt5Form::radio(name:'civil', text:"soltero", checked:true, id:'soltero')
    ])->class('my-3');

    echo tag('checkGroup')->content([
        Bt5Form::radio(name:'civil', text:"casado", checked:true, id:'casado')
    ])->class('my-3');

    
    echo Bt5Form::switch(id:"hijos", text:"Hijos", checked:true);

    echo Bt5Form::url(default:"https://www.linkedin.com/in/pablo-bozzolo/", class:"mt-3");

    echo Bt5Form::label(id:"comment", placeholder:"Algo que desea agregar:", class:'mt-3');
    echo Bt5Form::area(id:'comment', default:'bla bla', class:'my-3');

    echo Bt5Form::inputButton(id:"comprar", value:"Comprar");
    echo Bt5Form::reset(id:"limpiar", value:"Limpiar", class:'mx-3');
    echo Bt5Form::submit(id:"enviar", value:"enviar");

    echo Bt5Form::br();

    echo tag('img')->src(assets('img/personal_data.png'))->id('i1')->class('img-fluid');

    echo Bt5Form::br();

    echo Bt5Form::salutor("Isabel", "bella"); 
    echo ' ';
    echo Bt5Form::link_to(href:"www.solucionbinaria.com", anchor:'SolucionBinaria .com', class:'mb-3 text-success');

    ?>
    </div>
</div>