<?php

use Boctulus\Simplerest\Core\View;
use Boctulus\Simplerest\Core\Libs\HtmlBuilder\Tag;
use Boctulus\Simplerest\Core\Libs\HtmlBuilder\AdminLte;


Tag::registerBuilder(\Boctulus\Simplerest\Core\Libs\HtmlBuilder\AdminLte::class);

AdminLte::macro('salutor', function ($name, $adj, Array $att = []) {
    $str_att = AdminLte::attributes($att);
    return "<span $str_att>Hello $adj $name</span>";
});

AdminLte::setIdAsName();

View::css_file(ASSETS_PATH . 'third_party/adminlte/dist/css/adminlte.css');

?>

<div class="row mt-5">
    <div class="col-6 offset-3">

        <?php

        /*  
            Navbar
        */

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
                        // tag('dropdown')->content(
                        //     tag('dropdownButton')->id('dropdownMenuButton33')->content('Dropdown button') .    
                        //     tag('dropdownMenu')->ariaLabel('dropdownMenuButton33')->content(
                        //         tag('dropdownItem')->href('#')->anchor('Action') .
                        //         tag('dropdownItem')->href('#')->anchor('Another action') .
                        //         tag('dropdownDivider') .
                        //         tag('dropdownItem')->href('#')->anchor('Something else here')
                        //     )
                        // ),
                    ])
                ])->id("navbarNavAltMarkup")
            ])
        )->class('mb-3 fixed-top')->expand()
        //->dark()
        ; 

        echo tag('p')->class('mt-5');

        echo tag('tooltip')->title('Some title')->content('Tooltip on bottom')->pos('bottom')
        ->class('me-3');

        // Toasts
        echo tag('button')->id("toastbtn")->value("Abrir toast");

        echo tag('div')->content([
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
        ])->class("position-fixed bottom-0 end-0 p-3")->style("z-index: 11");

        echo tag('p');

        echo tag('popover')
        ->content('Click to toggle popover')
        ->title('Popover title')
        ->body("And here's some amazing content. It's very engaging. Right?")
        ->as('button')
        ->class('btn-lg my-3')->danger()->pos('top')
        ->dismissible();                   

         
        echo tag('searchTool')->id('my_search')->class('my-3');
        

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


        /*
            Previous | 1 | 2 | 3 | .. | 10 | Next
        */
        echo tag('paginator')->content([
            [
                'href'   => '#?page=1',
            ],
            [
                'href' => '#?page=2'
            ],
            [
                'href' => '#?page=3',
                'active' => true
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
        ->class('mt-3')
        //->large()
        ->options(['justify-content-center'])
        ->withPrev([
            'href'   => '#?page=1',
            'anchor' => '&laquo;',
            //'disabled' => true
        ])
        ->withNext([
            'href'   => '#?page=11',
            'anchor' => '&raquo;',
            //'disabled' => true
        ])
        ;


        echo tag('paginator')->content([
            [
                'href'   => '#?page=1',
                'active' => true              
            ],
            [
                'href' => '#?page=2',
                //'active' => true             
            ],
            [
                'href' => '#?page=3'
            ]
        ])
        ->class('mt-3')
        ->large()
        ->options(['justify-content-center'])
        ->withPrev([
            'href'   => '#?page=1',
            'anchor' => 'Previous'
        ])
        ->withNext([
            'href'   => '#?page=4',
            'anchor' => 'Next',
            //'disabled' => true
        ]);

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
        

        /*
            Spinners
        */

        echo tag('spinner')->class('my-3')->bg('danger')->grow()->size(5);
        echo tag('p');

        echo tag('spinner')->class('my-3')->as('button');
        echo tag('p');
        echo tag('spinner')->class('my-3')->grow()->as('button');

        echo tag('p');

        echo tag('spinner')->class('my-3')->as('button')->unhide();
        echo tag('p');
        echo tag('spinner')->class('my-3')->grow()->as('button')->unhide()->content('Cargando..');


        echo tag('h2')->text('Progress bars')->class('mt-3');

        echo tag('progress')->content(
            tag('progressBar')->current(80)
        )->class('mt-3');

        echo tag('progress')->content(
            tag('progressBar')
            ->min(5)
            ->max(25)
            ->current(15)->withLabel()->striped()
        )->class('my-5');

        echo tag('progress')->content(
            tag('progressBar')
            ->current(25)->withLabel()->bg('danger')->animated()
        )->class('my-5')->style("height: 50px;");

        echo tag('progress')->content([
            tag('progressBar')
            ->current(15)->withLabel()->bg('primary'),
    
            tag('progressBar')
            ->current(30)->withLabel()->bg('success'),
    
            tag('progressBar')
            ->current(25)->withLabel()->bg('info')
        ])->class('mt-3');

        
        echo tag('h2')->text('Progress bars | verticales')->class('my-3');

        echo tag('progress')->content(
            tag('progressBar')
            ->current(20)->bg('primary')->striped()
        )
        ->size('xs')
        ->vertical();  


        // echo tag('navbar')->content(
        //     tag('container')->fluid()->content([
        //         tag('navbarBrand')->anchor(
        //             tag('img')->src(asset('img/ai_logo.png'))->witdh(24)->height(24)
        //             ->class("d-inline-block align-text-top") . '&nbsp;&nbsp; Some text'
        //         )->href('#'),

        //         tag('form')->class("d-flex")->content([
        //             tag('search')->placeholder("Search")->class('me-2'),
        //             tag('button')->outline()->success()->value('Search')
        //         ])
        //     ])->class('mb-3')
        // )
        // //->dark()       
        // ;


        // echo tag('navbar')->content(
        //     tag('container')->fluid()->content([
        //         tag('navbarBrand')->anchor(
        //             tag('img')->src(asset('img/ai_logo.png'))->witdh(24)->height(24)
        //             ->class("d-inline-block align-text-top") . '&nbsp;&nbsp; Some text'
        //         )->href('#') 
        //     ])
        // )->class('mb-3')
        // //->dark()       ;  


        /*
            Navs 
        */

        echo '<br/>';

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
            // tag('dropdown')->content(
            //     tag('dropdownButton')->id('dropdownMenuButton')->content('Dropdown button') .    
            //     tag('dropdownMenu')->ariaLabel('dropdownMenuButton')->content(
            //         tag('dropdownItem')->href('#')->anchor('Action 1') .
            //         tag('dropdownItem')->href('#')->anchor('Another action') .
            //         tag('dropdownDivider') .
            //         tag('dropdownItem')->href('#')->anchor('Something else here')
            //     )
            // ),
        ])->class('mb-3')
        ->justifyRight()
        ->tabs()
        ->role('tablist')
        ->panes([
            'Textoooooooooo oo',
            'otroooooo',
            'y otro más'            
        ]);     

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
        ])->class('mb-3')
        //->vertical()
        ->justifyRight()
        //->justify()
        //->pills()
        //->fill()
        ->tabs()
        ;     

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
    
            tag('navLink')->anchor('Link')->as('button'),        
            tag('navLink')->anchor('Disabled')->disabled()
 
        ])->class('mb-3')
        //->vertical()
        ->justifyRight()
        //->justify()
        //->pills()
        //->fill()
        ->tabs()
        ;     

        // Offcanvas
        
        echo tag('div')->content([
            tag('offcanvasLink')->anchor('Link with href')->href('#offcanvasExample')->class('btn btn-primary'),
            tag('offcanvasOpenButton')->anchor('Button with data-bs-target')->href('#offcanvasExample')
        ])->class('vstack gap-2 col-md-5 mx-auto my-3');

        echo tag('offcanvas')->id("offcanvasExample")->title('Offcanvas')->body([
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
                )->class('mt-3')
        ])
        ->pos('right')
        ->backdrop()
        ->scroll();


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
            tag('dropdownButton')->id('dropdownMenuButton2')->content('Dropdown button')
            ->danger() .

            tag('dropdownMenu')->ariaLabel('dropdownMenuButton2')->content(
                tag('dropdownItem')->href('#')->anchor('Action') .
                tag('dropdownItem')->href('#')->anchor('Another action') .
                tag('dropdownDivider') .
                tag('dropdownItem')->href('#')->anchor('Something else here')
            )->class('animated--grow-in')
        );

        echo tag('split')->content([
            tag('button')->content('Split button')->default(),
      
            tag('splitButton')->id('dropdownMenuButton33')->content('Toggle Dropdown'),
      
            tag('dropdownMenu')->ariaLabel('dropdownMenuButton33')->content(
              tag('dropdownItem')->href('#ln1')->anchor('Action #1') .
                tag('dropdownItem')->href('#ln2')->anchor('Another action') .
                tag('dropdownDivider') .
                tag('dropdownItem')->href('#ln3')->anchor('Something else here')
            )
        ])->class('mt-3');

        echo '<br/>';

        /* Carrousel */

        // echo tag('carousel')->content([
        //     tag('carouselItem')->content(
        //         tag('carouselImg')->src(asset('img/slide-1.jpeg'))
        //     )->caption(
        //         '<h5>First slide label</h5>
        //         <p>Some representative placeholder content for the first slide.</p>'
        //     ),

        //     tag('carouselItem')->content(
        //         tag('carouselImg')->src(asset('img/slide-2.jpeg'))
        //     ),

        //     tag('carouselItem')->content(
        //         tag('carouselImg')->src(asset('img/slide-3.jpeg'))
        //     )
        // ])->id("carouselExampleControls")->withControls()->withIndicators()
        // // ->dark()
        // ->height('400px');
        

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


        // Size

        ?>

        <h2 class="my-3">Size utility</h2>

        <div class="w-25 p-3" style="background-color: #eee;">Width 25%</div>
        <div class="w-50 p-3" style="background-color: #eee;">Width 50%</div>
        <?php
            echo tag('div')->content(
            'Width 75%'
            )
            ->w(75)
            ->bg('warning')
            ->class('p-3');

        ?>
        <div class="w-100 p-3" style="background-color: #eee;">Width 100%</div>
        <?php
            echo tag('div')->content(
            'Width auto'
            )
            ->w('auto')
            ->bg('warning')
            ->class('p-3');
        ?>

        <p></p>
            
        <div style="height: 100px; background-color: rgba(255,0,0,0.1);">
            <div class="h-25 d-inline-block" style="width: 120px; background-color: rgba(0,0,255,.1)">Height 25%</div>
            <div class="h-50 d-inline-block" style="width: 120px; background-color: rgba(0,0,255,.1)">Height 50%</div>

            <?php
            echo tag('div')->content(
                'Height 75%'
            )
            ->w(75)
            ->h(75)
            ->bg('warning')
            ->class('d-inline-block');
            ?>

            <div class="h-100 d-inline-block" style="width: 120px; background-color: rgba(0,0,255,.1)">Height 100%</div>
            <?php
            echo tag('div')->content(
                'Height auto'
            )
            ->w(75)
            ->h('auto')
            ->bg('danger')
            ->class('d-inline-block');
            ?>
        </div>
        <?php

        // Cards

        echo '<br/>';

        echo tag('h3')->text('Quotes')->class('my-3');
        
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

        echo tag('card')
        ->header('Quote') 
        ->body(

            tag('blockquote')->content(
                tag('p')->text(
                    'A well-known quote, contained in a blockquote element.'
                ) .
                tag('blockquoteFooter')->content('Someone famous in ' . 
                tag('cite')->title("Source Title")->content('Source Title'))

            )->class('mb-0')->color('secondary')
        
        ) 
        ->footer('Some footer')
        ->class('mb-4');


        echo tag('h3')->text('Cards')->class('my-3');

        echo tag('card')
        ->header(tag('cardTitle')->text('Some title'))
        ->body(
            tag('cardSubtitle')->text('Some subtitle')->class('mb-2')->textMuted()
        )
        ->class('mb-4');

        echo tag('card')->style('width: 18rem;')
        ->header(tag('cardTitle')->text('Some title'))
        ->body([            
            tag('cardText')->text('Some quick example text to build on the card title and make up the bulk of the cards content.'),
            tag('inputButton')->value('Go somewhere')->info()->textColor('white')
        ])
        ->class('my-3')
        ->bg('primary')
        ->textColor('white');


        echo tag('card')->style('width: 18rem;')->class('my-3')
        ->content(
            tag('cardImgTop')->src(asset('img/mail.png'))
        )
        ->header(tag('cardTitle')->text('Some title'))
        ->body([            
            tag('cardText')->text('Some quick example text to build on the card title and make up the bulk of the cards content.'),
            tag('inputButton')->value('Go somewhere')
        ]);

        echo tag('card')->style('width: 18rem;')->class('my-3')
        ->content(
            tag('cardImgTop')->src(asset('img/mail.png'))
        )
        ->header(tag('cardTitle')->text('Some title'))->placeholder()
        ->body([         
            tag('cardSubtitle')
            ->placeholder()->bg('warning'),

            tag('cardText')
            ->placeholder()->bg('success'),
            
            tag('inputButton')->value('Go somewhere')
            ->placeholder()
        ]);


        echo tag('h3')->text('flip-card');
        
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

        /* Badges */    

        echo tag('h3')->text('Badges')->class('my-3');

        echo tag('badge')->content('barato')->class('mb-3 me-3 rounded-pill')->bg('success'); // ok

        echo tag('notificationButton')->text('Correos')->qty(101);

        // buttons

        echo tag('h3')->text('Buttons')->class('my-3');

        echo tag('buttonGroup')->content([
            tag('inputButton')->value('Un botón')->info()->class('rounded-pill'),
            tag('inputButton')->value('Otro botón')->warning()->class('rounded-pill')->large(),
            tag('inputButton')->value('Peque')->info()->class('rounded-pill mx-3')->small()
        ])->class('my-3');

        echo tag('buttonGroup')->content([
            tag('button')->content('Un botón')->large()->bg('success'),
            tag('button')->content('Un botón')->large()->bg('success')->gradient()
        ])->class('my-3');

        echo tag('button')->content('Un botón')->large()->bg('danger');
        echo tag('button')->content('Un botón')->large()->bg('warning')->flat();

        echo tag('buttonGroup')->content(
            tag('button')->content('A')->danger()->class('rounded-pill') .
            tag('button')->content('B')->success()->class('rounded-pill')->outline()
        )->aria_label("Basic example")->class('mx-3')->small();

        echo tag('buttonGroup')->content(
            tag('button')->content('C')->danger()->class('rounded-pill') .
                tag('button')->content('D')->success()->class('rounded-pill')->outline()
        )->aria_label("Basic example")->class('mx-3');

        echo tag('buttonGroup')->content(
            tag('button')->content('E')->danger()->class('rounded-pill') .
                tag('button')->content('F')->success()->class('rounded-pill')->outline()
        )->aria_label("Basic example")->class('mx-3')->large();

        echo tag('button')
        ->content('.btn-block')
        ->block()->class('mt-3');   

        echo tag('button')
        ->content('.btn-block')
        ->block()->outline();   
        
        echo tag('button')
        ->content('.btn-block')
        ->block()   
        ->icon("bell");  

        echo tag('button')
        ->content('.btn-block')
        ->block()->outline()
        ->icon("bell");   
        
        echo tag('appButton')
        ->content("Edit")
        ->icon('edit')
        ->href('#edit')
        ->class('mt-3'); 

        echo tag('appButton')
        ->content("Edit")
        ->icon('edit')
        ->href('#edit')
        ->bg('danger')
        ->badgeQty(5)
        ->badgeColor('warning')
        ->class('mt-3'); 



        echo '<br/>';

        echo tag('buttonGroup')->content([
            tag('inputButton')->value('Un botón')->info()->class('rounded-pill'),
            tag('inputButton')->value('Otro botón')->warning()->class('rounded-pill')->large(),
            tag('inputButton')->value('Peque')->info()->class('rounded-pill mx-3')->small()
        ])->class('my-3');
        
        /* Input groups */

        echo tag('h3')->text('inputGroup')->class('my-3');

        echo tag('inputGroup')
        ->content(
          tag('inputText')
        )
        ->prepend(
          tag('button')->danger()->content('Action')
        )->class('mb-3');

        echo tag('inputGroup')
        ->content(
          tag('inputText')
        )
        ->append(
            tag('button')->info()->content('Go!')
        )->class('mb-3');

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


        echo tag('inputGroup')
        ->content(
            tag('inputText')
            ->placeholder('Buscar')
        )
        ->append(
            tag('button')->info()->icon('search')
            ->class("ms-n5")
        );

        /* Button groups */

        echo tag('h3')->text('buttonGroup')->class('my-3');

        echo tag('buttonGroup')->content([
            tag('button')->content('Left')->info(),
            tag('button')->content('Middle')->info(),
            tag('button')->content('Right')->info()
        ])->class('my-3');
    
        echo tag('p');
    
        echo tag('buttonGroup')->content([
            tag('button')->content('Top')->warning(),
            tag('button')->content('Middle')->warning(),
            tag('button')->content('Bottom')->warning()
        ])->vertical();


        /* buttonToolbar */

        echo tag('h3')->text('buttonToolbar')->class('my-3');

        echo tag('buttonToolbar')->content([
            tag('buttonGroup')->content(
                tag('button')->content('Botón rojo')->danger()->class('rounded-pill') .
                tag('button')->content('Botón verde')->success()->class('rounded-pill')->outline()
            )->aria_label("Basic example")->class('mx-3'),

            tag('buttonGroup')->content(
                tag('button')->content('Botón azul')->info()->class('rounded-pill') .
                    tag('button')->content('Botón amarillo')->warning()->class('rounded-pill')->outline()
            )->aria_label("Another group")->class('mx-3')
        ])->class('my-3');

       

        
        /* Collapse */

        echo tag('h3')->text('Collapse')->class('mb-3');

        echo tag('p')->text(
            tag('collapseLink')->href("#collapseExample")->anchor('Link with href')->class('me-1') .            
            tag('collapseButton')->dataBsTarget("#collapseExample")->content('Button with data-bs-target')
        );

        echo tag('collapse')->id("collapseExample")->content(
            tag('cardBody')->content('Some placeholder content for the collapse component. This panel is hidden by default but revealed when the user activates the relevant trigger.')
        );


        /*
            Alerts
        */

        echo tag('h3')->text('Alert')->class('mb-3');

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
        //->title('Error!')
        ->danger()
        ->dismissible(true);

        echo AdminLte::alert(content: 'Some content', attributes: ['warning', 'dismissible']);


        // Callouts
        echo tag('h2')->text('Callouts')->class('my-3');

        echo tag('callout')->color('danger')
        ->content('There is a problem that we need to fix. A wonderful serenity has taken possession of my entire
        soul, like these sweet mornings of spring which I enjoy with my whole heart.')
        ->title('Some title');
        

        /*
            Select
        */
        echo tag('h3')->text('Select')->class('mb-3');

        // Select simple
        echo AdminLte::select(name: 'sexo', options: [
            'varon' => 1,
            'mujer' => 2
        ], default: 1, placeholder: 'Su sexo', attributes: ['class' => 'my-3']);

        // Select con option groups
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

    
        /*
            DataList
        */
        echo tag('h3')->text('DataList')->class('mb-3');

        echo AdminLte::dataList(listName: 'datalistOptions', id: 'occupation', options: [
            'programador',
            'software engenierer'
        ], placeholder: 'Escriba aquí', label: 'Ocupación');

        /*
            El tag 'hr' ni siquiera está definido en la clase Html
        */
        echo tag('hr')->style('color:cyan; height: 10px;');

        echo tag('p')->text("Hola mundo cruel");

        echo tag('h3')->text('Opacity (text utility)')->class('mb-3');

        echo tag('div')->content('Some content')->textColor('primary')->class('mt-3');    
        echo tag('div')->content('Some content but with opacity of 50%')->textColor('primary')->opacity(0.5)->class('mb-3');

        // Input color
        echo tag('h3')->text('inputColor')->class('mb-3');
        echo tag('inputColor')->name('my_color')->text('Color')->id('c1')
        ->value('#563d7c')
        ;

        echo tag('h3')->text('inputGroup implementado con div')->class('mb-3');

        echo AdminLte::div(
            content: [
                tag('span')->text('@')->id('basic-addon')->class('input-group-text'),
                tag('inputText')->name('nombre')->placeholder("Username")
            ],
            class: "input-group mb-3"
        );

        echo tag('h3')->text('inputText')->class('mb-3');

        echo tag('inputText')->name('iq')->placeholder("IQ")->disabled();

        // Ion Sliders

        echo tag('h3')->text('Ion Sliders')->class('mb-3 mt-3');
        
		/*
			Slider "simple"
		*/

		echo tag('ionSlider')->id('range_1')
          ->min(0)
          ->max(6000)
          ->value(50) 
          ->postfix(" &euro;")
          ->step(10);

		/*
			Slider "doble"
		*/

		echo tag('ionSlider')->id('range_2')
		->min(-100)
		->max(400)          
		->postfix(" C")
		->step(1)

		->type('double')
		->from(100)
		->to(200);


        echo tag('h3')->text('input range')->class('mt-3 mb-3');

        echo tag('label')->for("edad")->text("Edad");
        echo AdminLte::range(name: 'edad', min: 0, max: 99, default: 10, class: 'my-3');

        echo tag('p');

        echo tag('label')->for("exp")->text("Experiencia");
        echo tag('range')->name('exp')->min(0)->max(99)->default(30)->class('my-3')->color('teal');


        // checkGroup
        
        echo tag('h3')->text('checkGroup')->class('mb-3 mt-3');

        echo tag('checkGroup')->content([
            AdminLte::radio(name: 'civil', text: "soltero", checked: true, id: 'soltero')
        ])->class('mt-3');

        echo tag('checkGroup')->content([
            AdminLte::radio(name: 'civil', text: "casado", checked: true, id: 'casado')
        ])->class('mb-3');

        echo tag('h3')->text('switch')->class('mb-3');

        echo AdminLte::switch(id: "hijos", text: "Hijos", checked: true);


        // Stack de checkbox / radios

        echo tag('formCheck')->content(
            tag('checkbox')->id("defaultCheck1")->class('me-2').
            tag('formCheckLabel')->for("defaultCheck1")->text('Default checkbox')
        )->class('mt-3');
    
        echo tag('formCheck')->content(
            tag('checkbox')->id("defaultCheck2")->class('me-2')->disabled() .
            tag('formCheckLabel')->for("defaultCheck2")->text('Disabled checkbox')
        );

        echo AdminLte::url(default: "https://www.linkedin.com/in/pablo-bozzolo/", class: "mt-3");


        echo tag('file')
        ->multiple()
        ->class('mt-3 mb-5');

        echo tag('file')->large()
        ->class('mt-3 mb-5');

        echo tag('file')->small()
        ->class('mt-3 mb-5');


        echo tag('buttonGroup')->content(
            tag('inputButton')->id("comprar")->value('Comprar')->danger()->class('rounded-pill') .
                tag('reset')->id("limpiar")->value("Limpiar")->warning() .
                tag('submit')->id("enviar")->value("Enviar")->success()->disabled()
        )->aria_label("Basic example")->class('mb-3');

        echo tag('p');

        echo tag('buttonGroup')->content(
            tag('inputButton')->id("comprar")->value('Comprar')->danger() .
                tag('reset')->id("limpiar")->value("Limpiar")->warning() .
                tag('submit')->id("enviar")->value("Enviar")->success()->disabled()
        )->aria_label("Basic example")->vertical();

        // si escribo mal el nombre del tag se rompe feo
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
        ->class('mt-4')
        ->always_open(true)
        ->attributes(['class' => 'accordion-flush'])
        ;

        /*  List groups */

        echo tag('listGroup')->content([
            tag('listGroupItem')->text('An item')->active(),
            tag('listGroupItem')->text('An item #2')->color('warning'),
            tag('listGroupItem')->text('An item #3')->color('success')
        ])->class('mt-2')->horizontal();

        echo AdminLte::br();

        echo tag('h3')->text('Borders')->class('mb-3');

        // Borders

        echo tag('div')->width(100)->content(
            tag('cardBody')->content(
                tag('div')->content('Some content')->class('my-3')
            )->border()->h('auto')
        )
        ->border('left')
        ->borderWidth(5)
        ->borderRad(3)
        ->borderColor('info')
        ->class('pe-3 mb-3');

        echo tag('div')->width(100)->content('
        Some content,...
        ')
        //->border()
        ->borderWidth(5)
        ->borderRad(3)
        ->borderColor('warning')
        ->borderPill()
        ->borderCorner('left')
        ->class('p-3 mb-3');

        echo tag('img')
        ->src(asset('img/personal_data.png'))
        ->id('i1')
        ->class('w-100')
        ->alt("Some alternative text");

        echo tag('mask')
        ->img([
          'src' => asset('img/slide-3.jpeg'), 
          'class' => "w-100",
          'alt' => "Louvre Museum"
        ])
        ->text('Puedes verme?')->style('font-size:130%;');

        echo AdminLte::br();
        echo AdminLte::salutor("Isabel", "bella", ['class' => 'my-3 me-1', 'style' => 'color: red']); 
        echo ' ~ '; 
        echo AdminLte::link(href: "www.solucionbinaria.com", anchor: 'SolucionBinaria .com', class: 'mb-3 text-success');


        echo tag('h3')->text('Ribbons')->class('mb-3 mt-5');

        ?>

        <div class="card card-primary">
              <div class="card-header">
                <h3 class="card-title">Ribbons</h3>
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                
              <div class="row">

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
                  
              </div>

              <!-- /.card-body -->
            </div>

        <?php
        


        /*
            Widgets ----
        */

        echo tag('hr');
        echo tag('h2')->text('Widgets')->class('mb-3');

        echo tag('h3')->text("Milestone / Timeline")->class('mb-3');

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

        echo tag('h3')->text('Steps')->class('mb-3');

        echo tag('steps')->max(4)->current(3);

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

        echo tag('wizard_steps')
        ->max(5)
        ->current(3);

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

        /* Notes */

        echo tag('h3')->text('Notes')->class('mb-3');

        echo tag('note')
        ->text('<strong>!!! Note secondary:</strong> Lorem, ipsum dolor sit amet consectetur adipisicing
        elit. Cum doloremque officia laboriosam. Itaque ex obcaecati architecto! Qui
        necessitatibus delectus placeat illo rem id nisi consequatur esse, sint perspiciatis
        soluta porro?')
        ->color('secondary')->class('mb-5');

        /* Shadows */

        echo tag('h3')->text('Shadows')->class('mt-5 mb-3');

        echo tag('shadow')
        ->content('Some content')
        ->class("p-3");


        ?>

        <p><p>
    </div>
</div>


<?php
    // revisar no este roto
    View::js_file(ASSETS_PATH . 'third_party/adminlte/plugins/ion-rangeslider/js/ion.rangeSlider.min.js');
?>

<script>
    var popover = new bootstrap.Popover(document.querySelector('.popovers'), {
        container: 'body'
    });

    document.getElementById("toastbtn").onclick = function() {
        var myAlert =document.querySelector('.toast');
        var bsAlert = new bootstrap.Toast(myAlert);//inizialize it
        bsAlert.show();//show it
    };

    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    })

</script>