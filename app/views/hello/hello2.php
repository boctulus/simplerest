<?php

use simplerest\core\libs\Form;
use simplerest\core\libs\Bt5Form; 
use simplerest\core\libs\Tag;

Form::macro('salutor', function($name, $adj)
{
    return "<span/>Hello $adj $name</span>";
});

?>

<div class = "row">
    <div class = "col-6 offset-3">
    <?php   

    $f = new Bt5Form();

    $f
    ->setIdAsName()

    ->h(3, "Datos")

    ->tag(type:'hr', style:'color:grey')

    ->p("Hola mundo cruel")

    // ->insert(
	// 	tag('color')->name('my_color')->text('Color')->id('c1')
	// )

    ->div(function($form){
        $form->span('@', [
            'id'    => 'basic-addon',
            'class' => 'input-group-text'
        ]);
        $form->text('nombre', null, [
            "placeholder" => "Username"
        ]);
    }, [
        "class" => "input-group mb-3"
    ])

    ->select(name:'sexo', options:[
        'varon' => 1,
        'mujer' => 2
    ], defult:1, placeholder:'Su sexo', attributes:['class' => 'my-3'])

    ->label("edad", "Edad")
    ->range(name:'edad', min:0, max:99, default:10, class:'my-3')

    ->checkGroup(function($h){
        $h->radio("civil", "soltero", true, ['id' => 'soltero']);
    }, ['class' => 'my-3'])

    ->checkGroup(function($h){
        $h->radio("civil", "casado", false, ['id' => 'casado']);
    }, ['class' => 'my-3'])


    ->switch(name:"hijos", text:"Hijos", checked:true)

    ->url("Linkedin")

    ->label(id:"comment", placeholder:"Algo que desea agregar:", attributes:['class' => 'mt-3'])
    ->area(id:'comment', default:'bla bla', attributes:['class' => 'my-3'])

    ->inputButton(id:"comprar", value:"Comprar")

    ->reset(id:"limpiar", value:"Limpiar")
    ->submit(id:"enviar", value:"enviar")

    ->br()

    ->img(assets('img/personal_data.png'), ['id' => 'i1', 'class' => 'img-fluid'])

    ->br()

    ->insert(Form::salutor("Isabel", "bella"))

    ->link_to("www.solucionbinaria.com", 'SolucionBinaria .com', [
        'class' => 'mb-3 text-success'
    ]);

    echo $f->render();
    ?>
    </div>
</div>