/*
    Recuperar valores desde acá
*/

let $filas = [];
let $columnas = [];
let $filtros = [];
let $valores = [];

var columnas_array = [];
var totaliza = null;

$(document).ready(function () {
    let container_id;

    $(".droppable").droppable({
        drop: function (event, ui) {
            var $list = $(this);

            if (typeof $(this).attr("id") !== 'undefined') {
                //console.log("ID CONTENEDOR DESTINO: "+$(this).attr("id"));
                container_id = $(this).attr("id");
            } else {
                console.log("UNDEFINED ---> " + $(this));
            }
            
            $helper = ui.helper;
            $($helper).removeClass("selected");
            
            var $selected = $(".selected");
            
            if ($selected.length > 1) {
                moveSelected($list, $selected);
            } else {
                moveItem(ui.draggable, $list);
            }

        }, tolerance: "touch"
    });

    $(".draggable").draggable({
        revert: "invalid",
        helper: "clone",
        cursor: "move",
        drag: function (event, ui) {
            var $helper = ui.helper;
            $($helper).removeClass("selected");
            var $selected = $(".selected");
            if ($selected.length > 1) {
                $($helper).html($selected.length + " items");
            }
        }
    });

    function moveSelected($list, $selected) {
        $($selected).each(function () {
            $(this).fadeOut(function () {
                $(this).appendTo($list).removeClass("selected").fadeIn();
            });
        });
    }

    let co_ant_id;
    let fi_ant_id;
    let vl_ant_id;

    function moveItem($item, $list) {
        $item.fadeOut(function () {
            $item.find(".item").remove();

            let text = $item.text().trim();
            let id = $($list.children()[1]).attr('id');

            switch (container_id) {
                case 'filas':
                    /// patch
                    if ($filas.includes(text)) { return; }

                    $($list.children()[1]).append('<option value="' + text + '" selected="selected">' + text + '</option>');



                    $filas.push(text);
                    break;
                case 'valores':
                    /// patch
                    if ($valores.includes(text)) { return; }

                    $($list.children()[1]).val("Suma de " + text).fadeIn();
                    $valores[0] = text;
                    totaliza = text;
                    if (typeof vl_ant_id != 'undefined' && vl_ant_id != null) {
                        $('#' + vl_ant_id).css("display", "");
                        $('#' + vl_ant_id).parent().removeClass('d-none');
                    }

                    vl_ant_id = $item.attr('id');
                    break
                case 'columnas':
                    /// patch
                    if ($columnas.includes(text)) { return; }

                    $($list.children()[1]).val(text).fadeIn();
                    $columnas[0] = text;

                    $('#input_coln').append($('<option>', {
                        value: text,
                        text: text
                    }));


                    columnas_array.push(text)

                    if (typeof co_ant_id != 'undefined' && co_ant_id != null) {
                        $('#' + co_ant_id).css("display", "");
                        $('#' + co_ant_id).parent().removeClass('d-none');
                    }

                    co_ant_id = $item.attr('id');

                    break;
                case 'filtros':
                    /// patch
                    if ($filtros.includes(text)) { return; }

                    $($list.children()[1]).val(text).fadeIn();
                    //       $filtros[0] = text;
                    $('#input_filt').append($('<option>', {
                        value: text,
                        text: text
                    }));
                    if (typeof fi_ant_id != 'undefined' && fi_ant_id != null) {
                        $('#' + fi_ant_id).css("display", "");
                        $('#' + fi_ant_id).parent().removeClass('d-none');
                    }

                    fi_ant_id = $item.attr('id');
                    break;
            }
        });

        setTimeout(function () {
            $item.parent().addClass('d-none');
        }, 500);
    }

    $(".item").click(function () {
        $(this).toggleClass("selected");
    });

    // Filtro al buscar
    jQuery('body').on('keyup', 'input#input_busca', function () {
        let written = this.value.toUpperCase();
        $('input#input_busca').val(written);

        $('#campos_todos').children().each((el) => {
            let valor = $($('#campos_todos').children()[el]).data('name');

            if (!valor.startsWith(written)) {
                $('#campo-' + valor).addClass('d-none');
            } else {
                $('#campo-' + valor).removeClass('d-none');
            }
        });
    });

    $("#btn_reset").click(function () {
        $('#campos_todos').children().each((el) => {
            let id = $($('#campos_todos').children()[el]).attr('id');

            $('#' + id).removeClass('d-none');
            $('#draggable-' + id).css("display", "");
        });

        $('#input_filt').val('');
        $('#input_coln').val('');
        $('#input_vals').val('');

        // limpio el dropdown
        let select = document.getElementById("input_fila");
        let length = select.options.length;

        for (i = length - 1; i >= 0; i--) {
            select.options[i] = null;
        };

        let filas = [];
        let columnas = [];
        let filtros = [];
        let valores = [];

        // limpio el buscador
        $('input#input_busca').val('');
    });


});