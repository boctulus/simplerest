<?php

namespace Boctulus\Simplerest\Controllers\html_builder;

use Boctulus\Simplerest\Core\Controllers\Controller;
use Boctulus\Simplerest\Core\Request;
use Boctulus\Simplerest\Core\Response;
use Boctulus\Simplerest\Core\Libs\Factory;
use Boctulus\Simplerest\Core\Libs\DB;

use Boctulus\Simplerest\Core\Libs\HtmlBuilder\Bt5Form;
use Boctulus\Simplerest\Core\Libs\HtmlBuilder\Tag;


/*
    Controlador de prueba de generador de tags
*/

class HtmlDemo1Controller extends Controller
{
    function __construct()
    {
        parent::__construct();
        //css_file('third_party/tabulator/dist/css/tabulator_bootstrap5.min.css');
    }

    function index()
    {  
        Tag::registerBuilder(\Boctulus\Simplerest\Core\Libs\HtmlBuilder\Html::class);

        $html = tag('inputText')->value('Pablo');
        
        return $html->render();
    }

    function t2(){
        //css_file('css/html_builder/steps/steps.css'); 	

        Tag::registerBuilder(\Boctulus\Simplerest\Core\Libs\HtmlBuilder\Bt5Form::class);

        $html = tag('div')->content([
            tag('steps')->max(4)->current(3),
            tag('steps')->max(5)->current(2),
            tag('steps')->max(10)->current(7),
        ]);

        return view('tpl', [
            'content' => $html
        ]);
    }

     /*
        Aca no estan saliendo los estilos ni cargando ningun js / css cuando se utiliza render() en vez de view()
    */
    function note_r()
    {
        // Tag::registerBuilder(\Boctulus\Simplerest\Core\Libs\HtmlBuilder\Bt5Form::class);

        // $html = tag('note')
        // ->text('<strong>!!! Note secondary:</strong> Lorem, ipsum dolor sit amet consectetur adipisicing
        // elit. Cum doloremque officia laboriosam. Itaque ex obcaecati architecto! Qui
        // necessitatibus delectus placeat illo rem id nisi consequatur esse, sint perspiciatis
        // soluta porro?')
        // ->color('secondary')
        // ->class('mb-5');

        $html = Bt5Form::note(
        '<strong>!!! Note secondary:</strong> Lorem, ipsum dolor sit ...	soluta porro?', 
        [
            'color' => 'secondary',
            'class' => 'mb-5'
        ]); 

        set_template('templates/tpl_basic.php');

        return render($html);
    }

    function note()
    {
        return view('html_builder_examples\note', null, 'templates/tpl_basic.php');
    }

    function steps()
    {   
        $html = Bt5Form::wizard_steps(2, 10, 
            [
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
            ]
        );

        set_template('templates/tpl_basic.php');

        return render($html);
    }

    function select2_r()
    {   
        Tag::registerBuilder(\Boctulus\Simplerest\Core\Libs\HtmlBuilder\Bt5Form::class);

        /*
            Select 2
        */
        
        //set_template('templates/tpl.php');

        $html = tag('div')->content([
            tag('h3')->text('Select2')->class('mb-3'),
        
            tag('select2')->name('sexo')
            ->options([
                'mujer'  => 1,
                'hombre' => 2,
                'indef'  => 3,
            ])
            ->default(1)
            ->placeholder('Su sexo')
            ->attributes(['class' => 'my-3'])
        ]);
        
        return render($html);
    }

    function select2()
    {
        return view('html_builder_examples\select2', null, 'templates/tpl_basic.php');
    }
}


