<?php

use simplerest\core\libs\Bt5Form;
use simplerest\core\libs\Tag;


Tag::registerBuilder(\simplerest\core\libs\Bt5Form::class);

Bt5Form::macro('salutor', function ($name, $adj) {
    return "<span/>Hello $adj $name</span>";
});

Bt5Form::setIdAsName();

?>

<div class="row mt-5">
    <div class="col-6 offset-3">

        <?php


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

        echo tag('h3')->text("Datos")->class('mb-3');

        /* Carrousel */

        echo tag('carousel')->content(
            // with Indicators
            tag('carouselIndicators')->content([
                tag('button')->data_bs_target("#carouselExampleIndicators")->data_bs_slide_to("0")->aria_current("true")
                ->content()->active(),
                tag('button')->data_bs_target("#carouselExampleIndicators")->data_bs_slide_to("1")->aria_current("true")
                ->content(),
                tag('button')->data_bs_target("#carouselExampleIndicators")->data_bs_slide_to("2")->aria_current("true")
                ->content(),
            ]) .

            tag('carouselInner')->content([
                tag('carouselItem')->content(
                    tag('img')->class("d-block w-100")->src('https://solucionbinaria.com/assets/images/porfolio/elgrove.png') .
                    // withCaptions
                    tag('carouselCaption')->content(
                    '<h5>First slide label</h5>
                    <p>Some representative placeholder content for the first slide.</p>'
                    )->class("d-none d-md-block")
                )->active(),

                tag('carouselItem')->content(
                    tag('img')->class("d-block w-100")->src('https://solucionbinaria.com/assets/images/porfolio/drivingcars-cl2.png') .
                    tag('carouselCaption')->content(
                        '<h5>Second slide label</h5>
                        <p>Some representative placeholder content for the second slide.</p>'
                    )->class("d-none d-md-block")
                ),

                tag('carouselItem')->content(
                    tag('img')->class("d-block w-100")->src('https://solucionbinaria.com/assets/images/porfolio/acrilicosxtchile-cl.png') .
                    tag('carouselCaption')->content(
                        '<h5>Third slide label</h5>
                        <p>Some representative placeholder content for the third slide.</p>'
                    )->class("d-none d-md-block")
                ),
            ]) .

            // with Controls
            tag('carouselControlPrev')->content(
                tag('carouselControlPrevIcon')->text() .
                tag('span')->hidden()->text('Previous')
            )->data_bs_target("#carouselExampleControls") .

            tag('carouselControlNext')->content(
                tag('carouselControlNextIcon')->text() .
                tag('span')->hidden()->text('Next')
            )->data_bs_target("#carouselExampleControls")

        )->id("carouselExampleControls");


        echo tag('card')->content(
            tag('cardHeader')->content('Quote') .
                tag('cardBody')->content(
                    tag('blockquote')->content(
                        tag('p')->text(
                            'A well-known quote, contained in a blockquote element.'
                        ) .
                            tag('blockquoteFooter')->content('Someone famous in ' . tag('cite')->title("Source Title")->content('Source Title'))
                    )->class('mb-0')
                )
        )->class('mb-4');


        echo tag('card')->content(
            tag('cardBody')->content(
                tag('cardTitle')->text('Some title') .
                    tag('cardSubtitle')->text('Some subtitle')->class('mb-2')->textMuted()
            )
        )->class('mb-4');

        echo tag('badge')->content('barato')->class('mb-3 me-3 rounded-pill')->success();

        echo tag('button')->content([
            'Inbox',
            tag('badge')->content('99+')->danger()->class('position-absolute top-0 start-100 translate-middle rounded-pill')
        ])
            ->class('rounded position-relative')
            ->primary();

        echo tag('buttonToolbar')->content([
            tag('buttonGroup')->content(
                tag('button')->content('Botón rojo')->danger()->class('rounded-pill')->outline() .
                    tag('button')->content('Botón verde')->success()->class('rounded-pill')->outline()
            )->aria_label("Basic example")->class('mx-3'),

            tag('buttonGroup')->content(
                tag('button')->content('Botón azul')->info()->class('rounded-pill')->outline() .
                    tag('button')->content('Botón amarillo')->warning()->class('rounded-pill')->outline()
            )->aria_label("Another group")->class('mx-3')
        ])->class('my-3');

        echo tag('buttonGroup')->content(
            tag('button')->content('A')->danger()->class('rounded-pill')->outline() .
                tag('button')->content('B')->success()->class('rounded-pill')->outline()
        )->aria_label("Basic example")->class('mx-3')->small();

        echo tag('buttonGroup')->content(
            tag('button')->content('C')->danger()->class('rounded-pill')->outline() .
                tag('button')->content('D')->success()->class('rounded-pill')->outline()
        )->aria_label("Basic example")->class('mx-3');

        echo tag('buttonGroup')->content(
            tag('button')->content('E')->danger()->class('rounded-pill')->outline() .
                tag('button')->content('F')->success()->class('rounded-pill')->outline()
        )->aria_label("Basic example")->class('mx-3')->large()->vertical();

        echo '<br/>';


        echo tag('inputButton')->value('Un botón')->info()->class('rounded-pill');
        echo tag('inputButton')->value('Otro botón')->warning()->class('rounded-pill')->large();
        echo tag('inputButton')->value('Peque')->info()->class('rounded-pill mx-3')->small();

        echo tag('br');

        echo tag('alert')->content('OK !')->success();
        echo Bt5Form::alert(content: 'Some content', attributes: ['warning', 'dismissible']);
        echo tag('alert')->content(tag('alertLink')->href('#')->anchor('A danger content'))->danger()->dismissible(true);

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

        echo Bt5Form::select(name: 'sexo', options: [
            'varon' => 1,
            'mujer' => 2
        ], default: 1, placeholder: 'Su sexo', attributes: ['class' => 'my-3']);

        echo Bt5Form::dataList(listName: 'datalistOptions', id: 'occupation', options: [
            'programador',
            'software engenierer'
        ], placeholder: 'Escriba aquí', label: 'Ocupación');

        /*
        El tag 'hr' ni siquiera está definido en la clase Html
    */
        echo tag('hr')->style('color:cyan');

        echo tag('p')->text("Hola mundo cruel");

        echo tag('color')->name('my_color')->text('Color')->id('c1');


        echo Bt5Form::div(
            content: [
                tag('span')->text('@')->id('basic-addon')->class('input-group-text'),
                tag('text')->name('nombre')->placeholder("Username")
            ],
            class: "input-group mb-3"
        );

        //

        echo tag('label')->name("edad")->text("Edad");
        echo Bt5Form::range(name: 'edad', min: 0, max: 99, default: 10, class: 'my-3');

        echo tag('checkGroup')->content([
            Bt5Form::radio(name: 'civil', text: "soltero", checked: true, id: 'soltero')
        ])->class('my-3');

        echo tag('checkGroup')->content([
            Bt5Form::radio(name: 'civil', text: "casado", checked: true, id: 'casado')
        ])->class('my-3');


        echo Bt5Form::switch(id: "hijos", text: "Hijos", checked: true);

        echo Bt5Form::url(default: "https://www.linkedin.com/in/pablo-bozzolo/", class: "mt-3");

        echo Bt5Form::label(id: "comment", placeholder: "Algo que desea agregar:", class: 'mt-3');
        echo Bt5Form::area(id: 'comment', default: 'bla bla', class: 'my-3');


        echo tag('buttonGroup')->content(
            tag('inputButton')->id("comprar")->value('Comprar')->danger()->class('rounded-pill') .
                tag('reset')->id("limpiar")->value("Limpiar")->warning() .
                tag('submit')->id("enviar")->value("Enviar")->success()->disabled()
        )->aria_label("Basic example");

        echo Bt5Form::br();

        echo tag('img')->src(assets('img/personal_data.png'))->id('i1')->class('img-fluid')->alt("Some alternative text");

        echo Bt5Form::br();

        echo Bt5Form::salutor("Isabel", "bella");
        echo ' ';
        echo Bt5Form::link_to(href: "www.solucionbinaria.com", anchor: 'SolucionBinaria .com', class: 'mb-3 text-success');

        ?>
    </div>
</div>