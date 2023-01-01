<?php

namespace simplerest\controllers\html_builder;

use simplerest\controllers\MyController;
use simplerest\core\Request;
use simplerest\core\Response;
use simplerest\core\libs\Factory;
use simplerest\core\libs\DB;

use simplerest\core\libs\HtmlBuilder\Bt5Form;
use simplerest\core\libs\HtmlBuilder\Tag;


class TestController extends MyController
{
    function __construct()
    {
        parent::__construct();
        //css_file('vendors/tabulator/dist/css/tabulator_bootstrap5.min.css');
    }

    function index()
    {   
        $json  = file_get_contents(ETC_PATH . 'countries_states.json');

        css_file('https://cdn.jsdelivr.net/npm/select2@4.0.3/dist/css/select2.min.css');
        css_file('https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.2.0/dist/select2-bootstrap-5-theme.min.css');

        js_file('https://cdn.jsdelivr.net/npm/select2@4.0.3/dist/js/select2.min.js');

        view('select2/diagnosticojournal.php', [
            'json' => $json
        ], 'templates/tpl_basic.php');              
        

        // Tag::registerBuilder(\simplerest\core\libs\HtmlBuilder\Bt5Form::class);

        // $html = tag('inputText')->value(microtime());
        
        // return $html->render();
    }

    function t2(){
        //css_file('css/html_builder/steps/steps.css'); 	

        Tag::registerBuilder(\simplerest\core\libs\HtmlBuilder\Bt5Form::class);

        $html = tag('div')->content([
            tag('steps')->max(4)->current(3),
            tag('steps')->max(5)->current(2),
            tag('steps')->max(10)->current(7),
        ]);

        return view('generic', [
            'content' => $html
        ]);
    }

     /*
        Aca no estan saliendo los estilos ni cargando ningun js / css cuando se utiliza render() en vez de view()
    */
    function note_r()
    {
        Tag::registerBuilder(\simplerest\core\libs\HtmlBuilder\Bt5Form::class);

        $html = tag('note')
        ->text('<strong>!!! Note secondary:</strong> Lorem, ipsum dolor sit amet consectetur adipisicing
        elit. Cum doloremque officia laboriosam. Itaque ex obcaecati architecto! Qui
        necessitatibus delectus placeat illo rem id nisi consequatur esse, sint perspiciatis
        soluta porro?')
        ->color('secondary')->class('mb-5');

        set_template('templates/tpl_basic.php');

        return render($html);
    }

    function note()
    {
        return view('html_builder_examples\note', null, 'templates/tpl_basic.php');
    }

    function steps()
    {   
        Tag::registerBuilder(\simplerest\core\libs\HtmlBuilder\Bt5Form::class);

        $html = tag('wizard_steps')
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

        return render($html);
    }

    function select2_r()
    {   
        Tag::registerBuilder(\simplerest\core\libs\HtmlBuilder\Bt5Form::class);

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


