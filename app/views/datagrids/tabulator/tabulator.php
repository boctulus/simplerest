<?php

use simplerest\core\libs\HtmlBuilder\Tag;

Tag::registerBuilder(\simplerest\core\libs\HtmlBuilder\Bt5Form::class);


/**
 * Se utiliza un array temporal para colocar los campos de
 * textarea al final en las definiciones y por ende en el formulario
 */
$textAreas = [];
foreach ($defs as $field => $info)
{
    if(in_array($info['formatter'] ?? '', ['textarea', 'ckdeditor', 'editor'])) {
        $textAreas[$field] = $info;
        unset($defs[$field]);
    }
}

$defs = array_merge($defs, $textAreas);

var_encode('defs',     $defs);
var_encode('entity',   $entity);
var_encode('tenantid', $tenantid ?? 'main');

/** Hojas de estilo */
css_file('vendors/tabulator/dist/css/tabulator_bootstrap5.min.css');
css_file('css/theme.css');
css_file('css/bt-custom.css');

/** Scripts */
js_file('vendors/axios/axios.min.js', null, true);
js_file('js/bootstrap/bt_validation_ss.js');
js_file('js/bootstrap/bt-utilities.js');
js_file('js/utilities.js', null, true);
js_file('js/main/view.js', null, true); // Scripts de la vista
js_file('js/plugins/jsPDF/jspdf.umd.min.js');
js_file('js/plugins/jsPDF/autotable/jspdf.plugin.autotable.min.js');
js_file('vendors/sweetalert2/sweetalert2@11.js');

echo tag('div')->content([
    tag('openButton')
    ->target("row-form-modal")
    ->content('<span class="label-icon">
        <i class="fa fa-plus"></i>
        </span>New')
    ->class('btn btn-label text-white mb-3')
    ->id('btn-create')
    ->info()
    ->borderRad(3),
    
    tag('button')
    ->content('<span class="label-icon">
        <i class="fa fa-trash"></i>
        </span>Delete')
    ->class('btn btn-label mb-3')
    ->id('btn-multiple-delete')
    ->danger()
    ->borderRad(3),

    tag('dropdown')->content(
        tag('dropdownButton')->id('dropdownMenuButton1')->content(
            '<span>
                <i class="fa fa-ellipsis-vertical"></i>
            </span>'
        ) .    
        tag('dropdownMenu')->ariaLabel('dropdownMenuButton1')->content(
            tag('button')
                ->content('<span>
                    <i class="fa fa-file-pdf"></i>
                    </span>')
                ->id('btn-download')
                ->danger()
                ->style("margin-left: 15px;")
                ->borderRad(3) .
            tag('button')
                ->content('<span>
                    <i class="fa fa-file-csv"></i>
                    </span>')
                ->id('tab-csv-download')
                ->danger()
                ->style("margin-left: 5px;")
                ->borderRad(3) .
            tag('dropdownDivider') .
            '<div id="tabulator-columns-toggler-container"></div>'
        )
    )->class('ms-auto mb-3 text-right')
])
->class('d-flex flex-wrap gap-1');
?>

<div id="example-table"></div>


<?php
/** Crear / editar row  */

//foo();

$fields = [];
foreach ($defs as $field => $info)
{
    $id          = $field;
    $title       = $info['name'] ?? ucfirst($field);
    $type        = $info['type'];
    $max         = $info['max'] ?? null;
    $min         = $info['min'] ?? null;
    $is_required = $info['required'] ?? null;
    $is_fillable = $info['fillable'];
    $is_nullable = $info['nullable'];
    $formatter   = $info['formatter'] ?? null;

    if (!$is_fillable && $field !== 'id'){
        continue;
    }

    $col_size   = "col-md-6";

    switch ($formatter){
        case 'textarea':
            $tag_name   = 'area';
            $col_size   = "col-md-12";
            $last_field = $field;
            break;

        default:
            $tag_name = 'inputText';
    }

    if($field !== 'id'){
        $fields[] = tag('div')->class("$col_size position-relative")
        ->content([
            tag('label')->for("col-{$id}")->text($is_required ? "$title *" : $title),
            tag($tag_name)
            ->placeholder($title)
            ->style('font-size:1rem')
            ->id("col-{$id}")
            ->name($id)
            ->attributes([
                'aria-describedby' => "invalid-col-{$id}"
            ])
            ->class('col2save'),
                
            tag('div')
            ->id("invalid-col-{$id}")
            ->class('invalid-feedback')
            ->content('Campo obligatorio'),
        ]);
    } else {
        $fields[] = tag('input')
            ->type('hidden')
            ->id("col-{$id}")
            ->name($id);
    }

} // end foreach

$modal_body   = $fields;

$buttons = tag('div')
//->style('background-color:red')
->content(
    tag('buttonGroup')
    ->style('padding-right: 0.7em;')
    ->class('position-absolute end-0')
    ->content([
        tag('closeModal')
        ->value('Cerrar')
        //->borderRad(0)
        ->style('margin-right:0.25em'),
        
        tag('submit')
        ->id("save_row")
        ->value('Save')
        //->borderRad(0)
    ])   
)->class('col-md-12 position-relative');

$modal_body[] = $buttons;

// area de errores
$modal_body[] = tag('div')->content([
    tag('hr')->style('margin-top:2em'),

    tag('p')->id("modal_notifications")
    ->style('min-height: 1.5em')
    ->class("mt-3")
    ->text("&nbsp;")
]); 

$form = tag('form')
->id('mainForm')
// para validacion client-side agregar la clase 'needs-validation' al form
->class('g-4 bt-form row') // g-* sirce para el interlineado -ver "gutters"-
->novalidate()
->content($modal_body);

echo tag('modal')
->header(
    tag('modalTitle')->text('New / Edit') . 
    tag('closeButton')->dataBsDismiss('modal')
)
->body(
    $form
)
->options([
    //'fullscreen',
    //'center',
    //'scrollable'  // ojo: renderiza a  0="scrollable" 
])
//->show() ///
->id('row-form-modal');

