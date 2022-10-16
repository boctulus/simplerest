<?php

use simplerest\core\libs\HtmlBuilder\Bt5Form;
use simplerest\core\libs\HtmlBuilder\Tag;

Tag::registerBuilder(\simplerest\core\libs\HtmlBuilder\Bt5Form::class);

css('
.text-small {
    font-size: 0.9rem !important;
}

body {
    background: linear-gradient(to left, #56ab2f, #a8e063);
}

.cursor-pointer {
    cursor: pointer;
}');

?>

<!-- Demo header-->
<section class="py-5 header text-center text-white">
    <div class="container pt-4">
        <header>
            <h1 class="display-4">Categorias</h1>
        </header>
    </div>
</section>


<section>
    <div class="container">
        <div class="row">
            <div class="col-lg-7 mx-auto">
                
                <div class="card shadow border-0 mb-5">
                    <div class="card-body p-5">
                        <h2 class="h4 mb-1">Categorias de productos</h2>
                        <p class="small text-muted font-italic mb-4">Selecccione hasta un maximo de 3</p>
                        <ul class="list-group">
                            <li class="list-group-item rounded-0 d-flex align-items-center justify-content-between">
                                <div class="custom-control custom-radio">
                                    <input class="custom-control-input" id="customRadio1" type="checkbox" name="customRadio">
                                    <label class="custom-control-label" for="customRadio1">
                                        <p class="mb-0">Limpieza e higiene</p><span class="small font-italic text-muted">/</span>
                                    </label>
                                </div>
                                <!-- label for="customRadio1"><img src="https://i.postimg.cc/Hsq4Ygss/1-ezgo0i.png" alt="" width="60"></label -->
                                <span class="badge bg-primary rounded-pill">17</span>
                            </li>
                            <li class="list-group-item rounded-0 d-flex align-items-center justify-content-between">
                                <div class="custom-control custom-radio">
                                    <input class="custom-control-input" id="customRadio1" type="checkbox" name="customRadio">
                                    <label class="custom-control-label" for="customRadio1">
                                        <p class="mb-0">Cuidado personal</p><span class="small font-italic text-muted">Limpieza e higiene > Cuidado personal</span>
                                    </label>
                                </div>
                                <label for="customRadio1"><img src="https://i.postimg.cc/Hsq4Ygss/1-ezgo0i.png" alt="" width="60"></label>
                            </li>

                            <li class="list-group-item d-flex align-items-center justify-content-between">
                                <div class="custom-control custom-radio">
                                    <input class="custom-control-input" id="customRadio2" type="checkbox" name="customRadio">
                                    <label class="custom-control-label" for="customRadio2">
                                        <p class="mb-0">Decoracion</p><span class="small font-italic text-muted"></span>
                                    </label>
                                </div>
                                <label for="customRadio2"><img src="https://i.postimg.cc/zf5ChFgs/2-rqo4zs.gif" alt="" width="60"></label>
                            </li>
                            <li class="list-group-item d-flex align-items-center justify-content-between">
                                <div class="custom-control custom-radio">
                                    <input class="custom-control-input" id="customRadio2" type="checkbox" name="customRadio">
                                    <label class="custom-control-label" for="customRadio2">
                                        <p class="mb-0">Decoracion</p><span class="small font-italic text-muted">Decoracion > Artesanias</span>
                                    </label>
                                </div>
                                <label for="customRadio2"><img src="https://i.postimg.cc/zf5ChFgs/2-rqo4zs.gif" alt="" width="60"></label>
                            </li>        

                            <li class="list-group-item d-flex align-items-center justify-content-between">
                                <div class="custom-control custom-radio">
                                    <input class="custom-control-input" id="customRadio3" type="checkbox" name="customRadio">
                                    <label class="custom-control-label" for="customRadio3">
                                        <p class="mb-0">Calefaccion</p><span class="small font-italic text-muted"></span>
                                    </label>
                                </div>
                                <label for="customRadio3"><img src="https://i.postimg.cc/Jnzj67KK/4-t444fl.png" alt="" width="60"></label>
                            </li>
                        </ul>
                    </div>
                </div>
                
            </div>
        </div>
    </div>
</section>

