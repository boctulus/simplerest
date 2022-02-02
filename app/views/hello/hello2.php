<?php

use simplerest\core\libs\Form;
use simplerest\libs\Bt5Form;

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

    ->tag('hr')

    ->p("Hola mundo cruel")

    // ->color('my_color', 'Color', [
    //     'id' => 'my_color'
    // ])

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

    ->select('sexo', [
        'varon' => 1,
        'mujer' => 2
    ], 1, 'Su sexo', ['class' => 'my-3'])

    ->label("edad", "Edad")
    ->range('edad', 0, 99, 10, ['class' => 'my-3'])

    ->checkGroup(function($h){
        $h->radio("civil", "soltero", true, ['id' => 'soltero']);
    }, ['class' => 'my-3'])

    ->checkGroup(function($h){
        $h->radio("civil", "casado", false, ['id' => 'casado']);
    }, ['class' => 'my-3'])


    ->switch("hijos", "Hijos", true)

    ->url("Linkedin")

    ->label("comment", "Algo que desea agregar:", ['class' => 'mt-3'])
    ->area('comment', 'bla bla', ['class' => 'my-3'])

    ->inputButton("comprar", "Comprar")

    ->reset("limpiar", "limpiar")
    ->submit("enviar", "enviar")

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