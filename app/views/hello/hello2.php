<?php

use simplerest\core\libs\Bt5Form;
use simplerest\core\libs\Tag;


Tag::registerBuilder(\simplerest\core\libs\Bt5Form::class);

Bt5Form::macro('salutor', function ($name, $adj, Array $att = []) {
    $str_att = Bt5Form::attributes($att);
    return "<span $str_att>Hello $adj $name</span>";
});

Bt5Form::setIdAsName();

?>

<div class="row mt-5">
    <div class="col-6 offset-3">

        <?php

        //Nav

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
        ])->class('mb-3')->justifyRight();     

        // Breadcrumb

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

        //echo tag('link')->href('#')->anchor('The Link')->title('Hey!')->tooltip();

        echo tag('dropdown')->content(
            tag('dropdownButton')->id('dropdownMenuButton1')->content('Dropdown button')->danger() .

            tag('dropdownMenu')->ariaLabel('dropdownMenuButton1')->content(
                tag('dropdownItem')->href('#')->anchor('Action') .
                tag('dropdownItem')->href('#')->anchor('Another action') .
                tag('dropdownDivider') .
                tag('dropdownItem')->href('#')->anchor('Something else here')
            )
        );

        echo '<br/>';

        /* Carrousel */

        echo tag('carousel')->content([
            tag('carouselItem')->content(
                tag('carouselImg')->src(assets('img/slide-1.jpeg'))
            )->caption(
                '<h5>First slide label</h5>
                <p>Some representative placeholder content for the first slide.</p>'
            ),

            tag('carouselItem')->content(
                tag('carouselImg')->src(assets('img/slide-2.jpeg'))
            ),

            tag('carouselItem')->content(
                tag('carouselImg')->src(assets('img/slide-3.jpeg'))
            )
        ])->id("carouselExampleControls")->withControls()->withIndicators()
        // ->dark()
        ;

        // Modal

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
        ->options([
            //'fullscreen',
            //'center',
            //'scrollable'
        ])
        ->id('exampleModal');

        echo tag('openButton')->target("exampleModal")->content('Launch demo modal')->class('my-3');

        // Cards

        echo '<br/>';

        echo tag('card')
        ->header('Quote') 
        ->body(
            tag('blockquote')->content(
                tag('p')->text(
                    'A well-known quote, contained in a blockquote element.'
                ) .
                    tag('blockquoteFooter')->content('Someone famous in ' . tag('cite')->title("Source Title")->content('Source Title'))
            )->class('mb-0')
        ) 
        ->footer('Some footer')
        ->class('mb-4');


        echo tag('card')
        ->body(
                tag('cardTitle')->text('Some title') .
                tag('cardSubtitle')->text('Some subtitle')->class('mb-2')->textMuted()
        )
        ->class('mb-4');

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


        echo tag('buttonGroup')->content([
            tag('inputButton')->value('Un botón')->info()->class('rounded-pill'),
            tag('inputButton')->value('Otro botón')->warning()->class('rounded-pill')->large(),
            tag('inputButton')->value('Peque')->info()->class('rounded-pill mx-3')->small()
        ])->class('my-3');

        echo tag('br');


        /* Collapse */

        echo tag('p')->text(
            tag('collapseLink')->href("#collapseExample")->anchor('Link with href')->class('me-1') .            
            tag('collapseButton')->dataBsTarget("#collapseExample")->content('Button with data-bs-target')
        );

        echo tag('collapse')->id("collapseExample")->content(
            tag('cardBody')->content('Some placeholder content for the collapse component. This panel is hidden by default but revealed when the user activates the relevant trigger.')
        );




        echo tag('alert')->content('OK !')->success();
        echo Bt5Form::alert(content: 'Some content', attributes: ['warning', 'dismissible']);
        echo tag('alert')->content(tag('alertLink')->href('#')->anchor('A danger content'))->color('danger')->dismissible(true);

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
                tag('inputText')->name('nombre')->placeholder("Username")
            ],
            class: "input-group mb-3"
        );

        //

        echo tag('label')->name("edad")->text("Edad");
        echo Bt5Form::range(name: 'edad', min: 0, max: 99, default: 10, class: 'my-3');

        echo tag('checkGroup')->content([
            Bt5Form::radio(name: 'civil', text: "soltero", checked: true, id: 'soltero')
        ])->class('mt-3');

        echo tag('checkGroup')->content([
            Bt5Form::radio(name: 'civil', text: "casado", checked: true, id: 'casado')
        ])->class('mb-3');

        echo Bt5Form::switch(id: "hijos", text: "Hijos", checked: true);


        // Stack de checkbox / radios

        echo tag('formCheck')->content(
            tag('checkbox')->id("defaultCheck1")->class('me-2').
            tag('formCheckLabel')->for("defaultCheck1")->text('Default checkbox')
        )->class('mt-3');
    
        echo tag('formCheck')->content(
            tag('checkbox')->id("defaultCheck2")->class('me-2')->disabled() .
            tag('formCheckLabel')->for("defaultCheck2")->text('Disabled checkbox')
        );

        echo Bt5Form::url(default: "https://www.linkedin.com/in/pablo-bozzolo/", class: "mt-3");

        echo Bt5Form::label(id: "comment", placeholder: "Algo que desea agregar:", class: 'mt-3');
        echo Bt5Form::area(id: 'comment', default: 'bla bla', class: 'my-3');


        echo tag('buttonGroup')->content(
            tag('inputButton')->id("comprar")->value('Comprar')->danger()->class('rounded-pill') .
                tag('reset')->id("limpiar")->value("Limpiar")->warning() .
                tag('submit')->id("enviar")->value("Enviar")->success()->disabled()
        )->aria_label("Basic example");

        /*  List groups */

        echo tag('listGroup')->content([
            tag('listGroupItem')->text('An item')->active(),
            tag('listGroupItem')->text('An item #2')->warning(),
            tag('listGroupItem')->text('An item #3')->color('success')
        ])->class('mt-5')->horizontal();

    
        echo Bt5Form::br();

        echo tag('img')->src(assets('img/personal_data.png'))->id('i1')->class('img-fluid')->alt("Some alternative text");

        echo Bt5Form::br();
        echo Bt5Form::salutor("Isabel", "bella", ['class' => 'my-3 me-1', 'style' => 'color: red']); 
        echo ' ~ '; 
        echo Bt5Form::link(href: "www.solucionbinaria.com", anchor: 'SolucionBinaria .com', class: 'mb-3 text-success');

        ?>

        <p><p>
    </div>
</div>