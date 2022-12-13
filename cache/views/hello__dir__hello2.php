
<div class="row mt-5">
    <div class="col-6 offset-3">

        <nav class="navbar navbar-expand-lg navbar-light bg-light nav mb-3 fixed-top" expand=""><div class="container-fluid"><a class="navbar-brand " href="#"><img src="http://simplerest.lan/public/assets/img/ai_logo.png" class="d-inline-block align-text-top" witdh="24" height="24"></img>&nbsp;&nbsp; Some text</a> <button class="navbar-toggler " data-bs-target="#navbarNavAltMarkup" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation" data-bs-toggle="collapse" type="button"><span class="navbar-toggler-icon"></span></button> <div class="collapse navbar-collapse" id="navbarNavAltMarkup"><div class="navbar-nav"><a class="nav-link  active" aria-current="page" href="#">Home</a> <a class="nav-link " href="#features">Features</a> <a class="nav-link " href="#pricing">Pricing</a> <a class="disabled nav-link " aria-disabled="true" href="#">Disabled</a></div></div></div></nav>        
        <!-- implementar -->
        <div class="btn-group mt-3" role="group" aria-label="Basic radio toggle button group">
            <input
                type="radio"
                class="btn-check"
                name="unidad_longitud"
                id="unidad_centimetro"
                autocomplete="off"
                value="cm"
                onclick="cambioUnidad(this);"
                checked
            />
            <label class="btn btn-outline-success" for="unidad_centimetro" style="width: 5em;">cm</label>
            <input
                type="radio"
                class="btn-check"
                name="unidad_longitud"
                id="unidad_metro"
                autocomplete="off"
                value="mt"
                onclick="cambioUnidad(this);"
            />
            <label class="btn btn-outline-success" for="unidad_metro" style="width: 5em;">mts</label>
            <input
                type="radio"
                class="btn-check"
                name="unidad_longitud"
                id="unidad_pulgada"
                autocomplete="off"
                value="pulg"
                onclick="cambioUnidad(this);"
            />
            <label class="btn btn-outline-success" for="unidad_pulgada" style="width: 5em;">pulg.</label>
        </div>
                    



        <p class="mt-5"></p><a class="btn btn-primary  me-3" data-bs-toggle="tooltip" title="Some title" data-bs-placement="bottom" tabindex="0" role="button">Tooltip on bottom</a><button type="button" class="btn btn-primary" id="toastbtn">Abrir toast</button><div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11"><div class="toast" role="alert" aria-live="assertive" aria-atomic="true"><div class="toast-header"><svg class="bd-placeholder-img rounded me-2" width="20" height="20" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" preserveAspectRatio="xMidYMid slice" focusable="false"><rect width="100%" height="100%" fill="#007aff"></rect></svg>
                    <strong class="me-auto">Bootstrap</strong>
                    <small>11 mins ago</small> <button class="btn-close " data-bs-dismiss="toast" aria-label="Close" type="button"></button></div> <div class="toast-body">Hello, world! This is a toast message.</div></div></div><p></p><button class="btn popovers btn btn-danger btn-lg my-3" data-bs-toggle="popover" title="Popover title" data-bs-content="And here's some amazing content. It's very engaging. Right?" data-bs-placement="top" data-bs-trigger="focus" type="button" as="button">Click to toggle popover</button>    <style>
    .ms-n5 {
	margin-left: -40px;
}    </style>
    <div class="col-md-5 mx-auto my-3" id="my_search">
            <div class="input-group">
                <input class="form-control border-end-0 border" type="search" id="my_search" placeholder="Search">
                <span class="input-group-append">
                    <button class="btn btn-outline-secondary bg-white border-start-0 border ms-n5" type="button">
                        <i class="fa fa-search"></i>
                    </button>
                </span>
            </div>
        </div><table class="table table-light"><thead class="table-dark"><th scope="row">#</th> <th scope="row">First</th> <th scope="row">Last</th> <th scope="row">Handle</th></thead> <tbody><tr><th scope="row">1</th> <td>Mark</td> <td>Otto</td> <td>@mmd</td></tr> <tr><th scope="row">2</th> <td>Lara</td> <td>Cruz</td> <td>@fat</td></tr> <tr><th scope="row">3</th> <td>Juan</td> <td>Cruz</td> <td>@fat</td></tr> <tr><th scope="row">4</th> <td>Feli</td> <td>Bozzolo</td> <td>@facebook</td></tr></tbody></table><nav aria-label="Page naviation" class="mt-3"><ul class="pagination justify-content-center"><li class="page-item"><a class="page-link " href="#?page=1">&laquo;</a></li> <li class="page-item"><a class="page-link " href="#?page=1">1</a></li> <li class="page-item"><a class="page-link " href="#?page=2">2</a></li> <li class="page-item active"><span class="page-link">3</span</li> <li class="page-item disabled" disabled><a class="page-link " href="#">..</a></li> <li class="page-item"><a class="page-link " href="#?page=10">10</a></li> <li class="page-item"><a class="page-link " href="#?page=11">&raquo;</a></li></ul></nav><nav aria-label="Page naviation" class="mt-5"><ul class="pagination pagination-lg justify-content-center"><li class="page-item disabled"><a class="page-link " href="#?page=1">Previous</a></li> <li class="page-item active"><span class="page-link">1</span</li> <li class="page-item"><a class="page-link " href="#?page=2">2</a></li> <li class="page-item"><a class="page-link " href="#?page=3">3</a></li> <li class="page-item"><a class="page-link " href="#?page=4">Next</a></li></ul></nav><div class="spinner-grow my-3 bg-danger" role="status" style="width: 5rem; height: 5rem;" size="5"><span class="visually-hidden">Loading...</span></div><p></p><button class="btn btn-primary btn my-3" type="button" disabled="disabled" as="button"><span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
            <span class="visually-hidden">Loading...</span></button><p></p><button class="btn btn-primary btn my-3" type="button" disabled="disabled" as="button"><span class="spinner-grow spinner-grow-sm" role="status" aria-hidden="true"></span>
            <span class="visually-hidden">Loading...</span></button><p></p><button class="btn btn-primary btn my-3" type="button" disabled="disabled" as="button" unhide=""><span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
            <span class="">Loading...</span></button><p></p><button class="btn btn-primary btn my-3" type="button" disabled="disabled" as="button" unhide=""><span class="spinner-grow spinner-grow-sm" role="status" aria-hidden="true"></span>
            <span class="">Cargando..</span></button><div class="progress mt-5"><div class="progress-bar" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="80" style="width: 80%"></div></div><div class="progress my-5"><div class="progress-bar progress-bar-striped" role="progressbar" aria-valuemin="5" aria-valuemax="25" aria-valuenow="15" style="width: 75%" withLabel="" striped="">75%</div></div><div class="progress my-5" style="height: 50px;"><div class="progress-bar progress-bar-striped progress-bar-animated bg- bg-danger" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="25" style="width: 25%" withLabel="" animated="">25%</div></div><div class="progress mt-3"><div class="progress-bar bg- bg-primary" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="15" style="width: 15%" withLabel="">15%</div> <div class="progress-bar bg- bg-success" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="30" style="width: 30%" withLabel="">30%</div> <div class="progress-bar bg- bg-info" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="25" style="width: 25%" withLabel="">25%</div></div><div class="progress progress-xxs my-5" size="xxs"><div class="progress-bar progress-bar-striped progress-bar-animated bg- bg-danger" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="25" style="width: 25%" animated=""></div></div><div class="progress progress-xxs vertical my-5" size="xxs" vertical=""><div class="progress-bar progress-bar-striped progress-bar-animated bg- bg-danger" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="25" style="height: 25%" animated="" vertical=""></div></div><br/><nav class="nav justify-content-end nav-tabs mb-3" justifyRight="" tabs="" role="tablist"><a data-bs-toggle="tab" class="nav-link  active" aria-current="page" href="#uno">Uno</a> <a data-bs-toggle="tab" class="nav-link " href="#dos">Dos</a> <a data-bs-toggle="tab" class="nav-link " href="#tres">Tres</a></nav><div class="tab-content"><div role="tabpanel" class="show active  tab-pane fade" id="uno">Textoooooooooo oo</div> <div role="tabpanel" class=" tab-pane fade" id="dos">otroooooo</div> <div role="tabpanel" class=" tab-pane fade" id="tres">y otro más</div></div><nav class="nav justify-content-end nav-tabs mb-3" justifyRight="" tabs=""><a class="nav-link  active" aria-current="page" href="#">Home</a> <a class="nav-link " href="#library">Library</a> <a class="nav-link " href="#">Data</a></nav><nav class="nav justify-content-end nav-tabs mb-3" justifyRight="" tabs=""><a class="nav-link  active" aria-current="page" href="#">Active</a> <div class="dropdown"><button class="btn dropdown-toggle " data-bs-toggle="dropdown" type="button" id="dropdownMenuButton1">Dropdown button</button><ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1"><li><a class="dropdown-item " href="#">Action</a></li><li><a class="dropdown-item " href="#">Another action</a></li><li><hr class="dropdown-divider"></li><li><a class="dropdown-item " href="#">Something else here</a></li></ul></div> <button class="nav-link btn btn-primary" data-bs-target="#" type="button" as="button">Link</button> <a class="nav-link  disabled" tabindex="-1" aria-disabled="true" href="#" disabled>Disabled</a></nav><div class="vstack gap-2 col-md-5 mx-auto my-3"><a data-bs-toggle="offcanvas" class="btn btn-primary" href="#offcanvasExample">Link with href</a> <button data-bs-toggle="offcanvas" data-bs-target="#offcanvasExample" role="button" type="button" class="btn btn-primary">Button with data-bs-target</button></div><div class="offcanvas offcanvas-end" data-bs-scroll="true" data-bs-backdrop="true" tabindex="-1" id="offcanvasExample"><div class="offcanvas-header"><h5 class="offcanvas-title">Offcanvas</h5> <button class="btn-close text-reset " data-bs-dismiss="offcanvas" aria-label="Close" type="button"></button></div><div class="offcanvas-body">Some text as placeholder. In real life you can have the elements you have chosen. Like, text, images, lists, etc.<div class="dropdown mt-3"><button class="btn dropdown-toggle  btn-success" data-bs-toggle="dropdown" type="button" id="dropdownMenuButtonX">Dropdown button</button><ul class="dropdown-menu" aria-labelledby="dropdownMenuButtonX"><li><a class="dropdown-item " href="#">Action</a></li><li><a class="dropdown-item " href="#">Another action</a></li><li><hr class="dropdown-divider"></li><li><a class="dropdown-item " href="#">Something else here</a></li></ul></div></div></div><nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item "><a href="#">Home</a></li> <li class="breadcrumb-item "><a href="#library">Library</a></li> <li class="breadcrumb-item ">Data</li></ol></nav><h3 class="mb-3">Datos</h3><div class="dropdown"><button class="btn dropdown-toggle  btn-danger" data-bs-toggle="dropdown" type="button" id="dropdownMenuButton2">Dropdown button</button><ul class="dropdown-menu animated--grow-in" aria-labelledby="dropdownMenuButton2"><li><a class="dropdown-item " href="#">Action</a></li><li><a class="dropdown-item " href="#">Another action</a></li><li><hr class="dropdown-divider"></li><li><a class="dropdown-item " href="#">Something else here</a></li></ul></div><br/><div class="carousel slide" data-bs-ride="carousel" id="carouselExampleControls" withControls="" withIndicators=""><div class="carousel-indicators"><button type="button" class=" active" data-bs-target="carouselExampleControls" data-bs-slide-to="0" aria-current="true"></button> <button type="button" class="" data-bs-target="carouselExampleControls" data-bs-slide-to="1" aria-current="true"></button></div> <div class="carousel-inner"><div class="carousel-item active" style="max-height: 300px;"><img class="d-block w-100" src="http://simplerest.lan/public/assets/img/carousel_swamp.png" 0=""></img><div class="carousel-caption d-none d-md-block"><h5>First slide label</h5>
                <p>Some representative placeholder content for the first slide.</p></div></div> <div class="carousel-item" style="max-height: 300px;"><img class="d-block w-100" src="http://simplerest.lan/public/assets/img/carousel_flight.png" 0=""></img></div></div> <button class="carousel-control-prev " data-bs-slide="prev" type="button" data-bs-target="#carouselExampleControls"><span class="carousel-control-prev-icon" aria-hidden="true"></span><span class="visually-hidden">Previous</span></button><button class="carousel-control-next " data-bs-slide="next" type="button" data-bs-target="#carouselExampleControls"><span class="carousel-control-next-icon" aria-hidden="true"></span><span class="visually-hidden">Next</span></button></div><div class="modal" tabindex="-1" id="exampleModal"><div class="modal-dialog"><div class="modal-content"><div class="modal-header"><h5 class="modal-title">Modal title</h5><button class="btn-close " aria-label="Close" type="button" data-bs-dismiss="modal"></button></div> <div class="modal-body"><p>Modal body text goes here!</p></div> <div class="modal-footer"><button class="btn-secondary btn" data-bs-dismiss="modal" type="button">Close</button><button type="button" class="btn btn-primary">Save changes</button></div></div></div></div><button class="btn-primary btn my-3" data-bs-toggle="modal" data-bs-target="#exampleModal" type="button">Launch demo modal</button>
        <h2 class="my-3">Size utility</h2>

        <div class="w-25 p-3" style="background-color: #eee;">Width 25%</div>
        <div class="w-50 p-3" style="background-color: #eee;">Width 50%</div>
        <div class="bg-warning p-3" style="max-width: 75%;">Width 75%</div>        <div class="w-100 p-3" style="background-color: #eee;">Width 100%</div>
        <div class="bg-warning p-3" style="max-width: auto%;">Width auto</div>
        <p></p>
            
        <div style="height: 100px; background-color: rgba(255,0,0,0.1);">
            <div class="h-25 d-inline-block" style="width: 120px; background-color: rgba(0,0,255,.1)">Height 25%</div>
            <div class="h-50 d-inline-block" style="width: 120px; background-color: rgba(0,0,255,.1)">Height 50%</div>

            <div class="bg-warning h-75 d-inline-block" style="max-width: 75%;">Height 75%</div>
            <div class="h-100 d-inline-block" style="width: 120px; background-color: rgba(0,0,255,.1)">Height 100%</div>
            <div class="bg-danger h-auto d-inline-block" style="max-width: 75%;">Height auto</div>        </div>
        <br/><h3 class="my-3">Cards</h3><div class="card mb-4"><div class="card-header">Quote</div> <div class="card-body"><blockquote class="quote-primary mb-0"><p>A well-known quote, contained in a blockquote element.</p><footer class="blockquote-footer">Someone famous in <cite title="Source Title">Source Title</cite></footer></blockquote></div> <div class="card-footer">Some footer</div></div><div class="card mb-4"><div class="card-header"><h5 class="card-title">Some title</h5></div> <div class="card-body"><h6 class="card-subtitle mb-2" textMuted="">Some subtitle</h6></div> </div><div class="card my-3 text-white bg-primary" style="width: 18rem;"><div class="card-header"><h5 class="card-title">Some title</h5></div> <div class="card-body"><p class="card-text">Some quick example text to build on the card title and make up the bulk of the cards content.</p> <input class="btn btn-info text-white" type="button" value="Go somewhere"></input></div> </div><div class="card my-3" style="width: 18rem;"><div class="card-header"><h5 class="card-title">Some title</h5></div> <img class="card-img-top" src="http://simplerest.lan/public/assets/img/mail.png" 0=""></img> <div class="card-body"><p class="card-text">Some quick example text to build on the card title and make up the bulk of the cards content.</p> <input class="btn btn-primary" type="button" value="Go somewhere"></input></div> </div><div class="card my-3" style="width: 18rem;" placeholder=""><div class="card-header"><h5 class="card-title">Some title</h5></div> <img class="card-img-top" src="http://simplerest.lan/public/assets/img/mail.png" 0=""></img> <div class="card-body"><h6 class="card-subtitle placeholder-glow"><span class="placeholder col-9 bg-warning"></span></h6> <p class="card-text placeholder-glow">
                <span class="placeholder col-7 bg-success"></span>
                <span class="placeholder col-4 bg-success"></span>
                <span class="placeholder col-4 bg-success"></span>
                <span class="placeholder col-6 bg-success"></span>
                <span class="placeholder col-8 bg-success"></span>
            </p> <input class="btn btn-primary disabled placeholder w-50" type="button" placeholder="" value=""></input></div> </div><h3 class="my-3">Badges</h3><div class="badge mb-3 me-3 rounded-pill bg-success">barato</div><button class="rounded position-relative btn-primary btn" type="button">Correos <div class="badge position-absolute top-0 start-100 translate-middle rounded-pill bg-danger">99+</div></button><h3 class="my-3">buttonToolbar</h3><div class="btn-toolbar my-3" role="toolbar"><div class="btn-group mx-3" role="group" aria-label="Basic example"><button type="button" class="btn btn-danger rounded-pill">Botón rojo</button><button type="button" class="btn btn-outline-success rounded-pill" outline="">Botón verde</button></div> <div class="btn-group mx-3" role="group" aria-label="Another group"><button type="button" class="btn btn-info rounded-pill">Botón azul</button><button type="button" class="btn btn-outline-warning rounded-pill" outline="">Botón amarillo</button></div></div><h3 class="my-3">buttonGroup</h3><div class="btn-group-sm mx-3" role="group" aria-label="Basic example"><button type="button" class="btn btn-danger rounded-pill">A</button><button type="button" class="btn btn-outline-success rounded-pill" outline="">B</button></div><div class="btn-group mx-3" role="group" aria-label="Basic example"><button type="button" class="btn btn-danger rounded-pill">C</button><button type="button" class="btn btn-outline-success rounded-pill" outline="">D</button></div><div class="btn-group-lg btn-group-vertical mx-3" role="group" aria-label="Basic example"><button type="button" class="btn btn-danger rounded-pill">E</button><button type="button" class="btn btn-outline-success rounded-pill" outline="">F</button></div><br/><div class="btn-group my-3" role="group"><input class="btn btn-info rounded-pill" type="button" value="Un botón"></input> <input class="btn btn-lg btn-warning form-control-lg rounded-pill" type="button" large="" value="Otro botón"></input> <input class="btn btn-sm btn-info form-control-sm rounded-pill mx-3" type="button" small="" value="Peque"></input></div><h3 class="mb-3">Collapse</h3><p><a class="btn btn-primary  me-1" data-bs-toggle="collapse" role="button" href="#collapseExample">Link with href</a><button data-bs-toggle="collapse" type="button" class="btn btn-primary" data-bs-target="#collapseExample">Button with data-bs-target</button></p><div class="collapse" id="collapseExample"><div class="card-body">Some placeholder content for the collapse component. This panel is hidden by default but revealed when the user activates the relevant trigger.</div></div><h3 class="mb-3">Alert</h3><div class="alert alert-primary  alert-success" role="alert" success="">OK !</div><div 0="warning" 1="dismissible" class="alert alert-primary alert-dismissible fade show" role="alert" 2="warning" 3="dismissible">Some content<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div><div class="alert alert-dismissible fade show alert-danger" role="alert"><a class="alert-link " href="#">A danger content</a><button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div><h3 class="mb-3">Select</h3><select placeholder="Tu comida favorita" class="form-select my-3" name="comidas" multiple="" id="comidas"><optgroup label="Asado" class="form-label"><option hidden="hidden" selected="selected">Tu comida favorita</option> <option value="pasta" >Pasta</option> <option value="pizza" >Pizza</option> <option value="asado" >Asado</option></optgroup><optgroup label="Frutilla" class="form-label"><option hidden="hidden" selected="selected">Tu comida favorita</option> <option value="banana" >Banana</option> <option value="frutilla" >Frutilla</option></optgroup></select><select class="my-3 form-select" placeholder="Su sexo" name="sexo" id="sexo"><option >Su sexo</option> <option value="1" selected>varon</option> <option value="2" >mujer</option></select><h3 class="mb-3">DataList</h3><label for="occupation" class="form-label">Ocupación</label><input placeholder="Escriba aquí" class="form-control" list="datalistOptions" id="occupation"></input><dataList id="datalistOptions"><option value="programador"/> <option value="software engenierer"/></dataList><hr style="color:cyan; height: 10px;"></hr><p>Hola mundo cruel</p><h3 class="mb-3">Opacity (text utility)</h3><div class="text-primary mt-3">Some content</div><div class="text-primary mb-3" style="--bs-text-opacity: 0.5;" opacity="0.5">Some content but with opacity of 50%</div><h3 class="mb-3">inputColor</h3><input class="form-control form-control-color" type="color" id="my_color" name="my_color" value="#563d7c"><label for="c1" class="form-label" name="my_color" value="#563d7c" id="my_color">Color</label></input><h3 class="mb-3">inputGroup implementado con div</h3><div class="input-group mb-3"><span class="input-group-text" id="basic-addon">@</span> <input class="form-control" type="text" name="nombre" placeholder="Username" id="nombre"></input></div><h3 class="mb-3">inputText</h3><input class="form-control disabled" type="text" name="iq" placeholder="IQ" id="iq"  disabled></input><h3 class="mt-3 mb-3">input range</h3><label for="edad" class="form-label">Edad</label><input class="form-range my-3" min="0" max="99" type="range" name="edad" id="edad" value="10"></input><label for="exp" class="form-label">Experiencia</label><input class="form-range my-3" min="0" max="99" type="range" name="exp" id="exp" value="30"></input><h3 class="mb-3">checkGroup</h3><div class="form-check mt-3"><input type="radio" class="form-check-input" id="civil" name="civil" checked><label for="soltero" class="form-label">soltero</label></input></div><div class="form-check mb-3"><input type="radio" class="form-check-input" id="civil" name="civil" checked><label for="casado" class="form-label">casado</label></input></div><h3 class="mb-3">switch</h3><div class="form-check form-switch"><input type="checkbox" class="form-check-input" id="hijos" checked>Hijos</input></div><div class="mt-3"><input type="checkbox" class="form-check-input me-2" id="defaultCheck1"></input><label class="form-label form-label" for="defaultCheck1">Default checkbox</label></div><div class=""><input type="checkbox" class="form-check-input disabled me-2" id="defaultCheck2" disabled></input><label class="form-label form-label" for="defaultCheck2">Disabled checkbox</label></div><input class="form-control mt-3" type="url" value="https://www.linkedin.com/in/pablo-bozzolo/"></input><label for="comment" class="form-label">Algo que desea agregar:</label><textarea class="form-control my-3" id="comment">bla bla</textarea><input class="form-control mt-3 mb-5" type="file" multiple></input><input class="form-control form-control-lg mt-3 mb-5" type="file" large=""></input><input class="form-control form-control-sm mt-3 mb-5" type="file" small=""></input><div class="btn-group mb-3" role="group" aria-label="Basic example"><input class="btn btn-danger rounded-pill" type="button" id="comprar" value="Comprar"></input><input class="btn btn-warning" type="reset" id="limpiar" value="Limpiar"></input><input class="btn btn-success disabled" type="submit" id="enviar" value="Enviar" disabled></input></div><p></p><div class="btn-group-vertical" role="group" aria-label="Basic example"><input class="btn btn-danger" type="button" id="comprar" value="Comprar"></input><input class="btn btn-warning" type="reset" id="limpiar" value="Limpiar"></input><input class="btn btn-success disabled" type="submit" id="enviar" value="Enviar" disabled></input></div><div class="accordion-flush mt-4 accordion" id="accordionExample"><div class="accordion-item"><h2 class="accordion-header" id="heading-flush-collapseOne"><button type="button" class="accordion-button" data-bs-toggle="collapse" data-bs-target="#flush-collapseOne" aria-expanded="false" aria-controls="flush-collapseOne">Accordion Item #1</button></h2><div class="accordion-flush accordion-collapse collapse show" id="flush-collapseOne" aria-labelledby="heading-flush-collapseOne" data-bs-parent=""><div class="accordion-body">Placeholder content for this accordion, which is intended to demonstrate the <code>.accordion-flush</code> class. This is the first items accordion body.</div></div></div> <div class="accordion-item"><h2 class="accordion-header" id="heading-flush-collapseTwo"><button type="button" class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#flush-collapseTwo" aria-expanded="false" aria-controls="flush-collapseTwo">Accordion Item #2</button></h2><div class="accordion-flush accordion-collapse collapse" id="flush-collapseTwo" aria-labelledby="heading-flush-collapseTwo" data-bs-parent=""><div class="accordion-body">Placeholder 2</div></div></div> <div class="accordion-item"><h2 class="accordion-header" id="heading-flush-collapseThree"><button type="button" class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#flush-collapseThree" aria-expanded="false" aria-controls="flush-collapseThree">Accordion Item #3</button></h2><div class="accordion-flush accordion-collapse collapse" id="flush-collapseThree" aria-labelledby="heading-flush-collapseThree" data-bs-parent=""><div class="accordion-body">Placeholder 3</div></div></div></div><ul class="list-group list-group-horizontal mt-2" horizontal=""><li class="list-group-item active active" aria-current="true">An item</li> <li class="list-group-item list-group-item-warning">An item #2</li> <li class="list-group-item list-group-item-success">An item #3</li></ul><br></br><h3 class="mb-3">Borders</h3><div class="border-start border-5 rounded rounded-3 border-info pe-3 mb-3" width="100"><div class="card-body border h-auto"><div class="my-3">Some content</div></div></div><div class="border border-5 rounded-end rounded-3 rounded-pill border-warning p-3 mb-3" width="100">
        Some content,...
        </div><img src="http://simplerest.lan/public/assets/img/personal_data.png" class="w-100" id="i1" alt="Some alternative text"></img>    <style>
    /*
    https://mdbootstrap.com/docs/standard/content-styles/masks/
*/

.bg-image {
    position: relative;
    overflow: hidden;
    background-repeat: no-repeat;
    background-size: cover;
    background-position: 50%
}

.mask {
    position: absolute;
    top: 0;
    right: 0;
    bottom: 0;
    left: 0;
    width: 100%;
    height: 100%;
    overflow: hidden;
    background-attachment: fixed
}    </style>
    <div class="bg-image rounded" text="Puedes verme?" style="font-size:130%;"><img src="http://simplerest.lan/public/assets/img/slide-3.jpeg" class="w-100" alt="Louvre Museum"></img>
           <div class="mask" style="background-color: rgba(0, 0, 0, 0.6)">
            <div class="d-flex justify-content-center align-items-center h-100">
                <p class="text-white mb-0">Puedes verme?</p>
            </div>
        </div></div><br></br><span class="my-3 me-1" style="color: red">Hello bella Isabel</span> ~ <a class="mb-3 text-success" href="http://www.solucionbinaria.com">SolucionBinaria .com</a><hr></hr><h2 class="mb-3">Widgets</h2><h3 class="mb-3">Milestone / Timeline</h3>    <style>
      body{margin-top:20px;}
.timeline-steps {
    display: flex;
    justify-content: center;
    flex-wrap: wrap
}

.timeline-steps .timeline-step {
    align-items: center;
    display: flex;
    flex-direction: column;
    position: relative;
    margin: 1rem
}

@media (min-width:768px) {
    .timeline-steps .timeline-step:not(:last-child):after {
        content: "";
        display: block;
        border-top: .25rem dotted #3b82f6;
        width: 3.46rem;
        position: absolute;
        left: 7.5rem;
        top: .3125rem
    }
    .timeline-steps .timeline-step:not(:first-child):before {
        content: "";
        display: block;
        border-top: .25rem dotted #3b82f6;
        width: 3.8125rem;
        position: absolute;
        right: 7.5rem;
        top: .3125rem
    }
}

.timeline-steps .timeline-content {
    width: 10rem;
    text-align: center
}

.timeline-steps .timeline-content .inner-circle {
    border-radius: 1.5rem;
    height: 1rem;
    width: 1rem;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background-color: #3b82f6
}

.timeline-steps .timeline-content .inner-circle:before {
    content: "";
    background-color: #3b82f6;
    display: inline-block;
    height: 3rem;
    width: 3rem;
    min-width: 3rem;
    border-radius: 6.25rem;
    opacity: .5
}
    </style>
    <div class="timeline-steps aos-init aos-animate" data-aos="fade-up">
                <div class="timeline-step">
                    <div class="timeline-content" data-toggle="popover" data-trigger="hover" data-placement="top" title="" data-content="" data-original-title="2010">
                        <div class="inner-circle"></div>
                        <p class="h6 mt-3 mb-1">2010</p>
                        <p class="h6 text-muted mb-0 mb-lg-0">algo remoto</p>
                    </div>
                </div> 
                <div class="timeline-step">
                    <div class="timeline-content" data-toggle="popover" data-trigger="hover" data-placement="top" title="" data-content="" data-original-title="2011">
                        <div class="inner-circle"></div>
                        <p class="h6 mt-3 mb-1">2011</p>
                        <p class="h6 text-muted mb-0 mb-lg-0">algo hermoso</p>
                    </div>
                </div> 
                <div class="timeline-step">
                    <div class="timeline-content" data-toggle="popover" data-trigger="hover" data-placement="top" title="" data-content="" data-original-title="2016">
                        <div class="inner-circle"></div>
                        <p class="h6 mt-3 mb-1">2016</p>
                        <p class="h6 text-muted mb-0 mb-lg-0">algo horrible</p>
                    </div>
                </div></div><h3 class="mb-3">Steps</h3>    <style>
    /*
     https://bbbootstrap.com/snippets/bootstrap-5-simple-multi-step-form-33401608
*/

.step {
     height: 15px;
     width: 15px;
     margin: 0 2px;
     background-color: #bbbbbb;
     border: none;
     border-radius: 50%;
     display: inline-block;
     opacity: 0.5
 }

 .step.active {
     opacity: 1
 }

 .step.finish {
     background-color: #4CAF50
 }

 .all-steps {
     text-align: center;
     margin-top: 30px;
     margin-bottom: 30px
 }    </style>
    <div class="all-steps"><span class="step"></span> <span class="step"></span> <span class="step active"></span> <span class="step"></span></div>    <style>
    

.wizard_horizontal ul.wizard_steps {
    display: table;
    list-style: none;
    position: relative;
    width: 100%;
  margin: 0 0 20px
}

.wizard_horizontal ul.wizard_steps li {
  display: table-cell;
  text-align: center
}

.wizard_horizontal ul.wizard_steps li a,
.wizard_horizontal ul.wizard_steps li:hover {
  display: block;
  position: relative;
  -moz-opacity: 1;
  filter: alpha(opacity=100);
  opacity: 1;
  color: #666
}

.wizard_horizontal ul.wizard_steps li a:before {
  content: "";
  position: absolute;
  height: 4px;
  background: #ccc;
  top: 20px;
  width: 100%;
  z-index: 4;
  left: 0
}

.wizard_horizontal ul.wizard_steps li a.disabled .step_no {
  background: #ccc
}

.wizard_horizontal ul.wizard_steps li a .step_no {
  width: 40px;
  height: 40px;
  line-height: 40px;
  border-radius: 100px;
  display: block;
  margin: 0 auto 5px;
  font-size: 16px;
  text-align: center;
  position: relative;
  z-index: 5
}

.wizard_horizontal ul.wizard_steps li a.selected:before,
.step_no {
  background: #34495E;
  color: #fff
}

.wizard_horizontal ul.wizard_steps li a.done:before,
.wizard_horizontal ul.wizard_steps li a.done .step_no {
  background: #1ABB9C;
  color: #fff
}

.wizard_horizontal ul.wizard_steps li:first-child a:before {
  left: 50%
}

.wizard_horizontal ul.wizard_steps li:last-child a:before {
  right: 50%;
  width: 50%;
  left: auto
}

.wizard_verticle .stepContainer {
  width: 80%;
  float: left;
  padding: 0 10px
}

a {
  color: #5A738E;
  text-decoration: none
}

a,
a:visited,
a:focus,
a:active,
:visited,
:focus,
:active,
.btn:focus,
.btn:active:focus,
.btn.active:focus,
.btn.focus,
.btn:active.focus,
.btn.active.focus {
  outline: 0
}

a:hover,
a:focus {
  text-decoration: none
}
    </style>
    <div class="form_wizard wizard_horizontal"><ul class="wizard_steps anchor"><li><a rel="1" class="selected " isdone="1" href="#s1">
                    <span class="step_no">1</span>
                        <span class="step_descr">
                        Step 1<br>
                        <small>Step 1 description</small>
                    </span></a></li> <li><a rel="2" class="selected " isdone="1" href="#s2">
                    <span class="step_no">2</span>
                        <span class="step_descr">
                        Step 2<br>
                        <small>Step 2 description</small>
                    </span></a></li> <li><a rel="3" class="disabled " isdone="0" href="#s3">
                    <span class="step_no">3</span>
                        <span class="step_descr">
                        Step 3<br>
                        <small>Step 3 description</small>
                    </span></a></li></ul></div>    <style>
    

.wizard_horizontal ul.wizard_steps {
    display: table;
    list-style: none;
    position: relative;
    width: 100%;
  margin: 0 0 20px
}

.wizard_horizontal ul.wizard_steps li {
  display: table-cell;
  text-align: center
}

.wizard_horizontal ul.wizard_steps li a,
.wizard_horizontal ul.wizard_steps li:hover {
  display: block;
  position: relative;
  -moz-opacity: 1;
  filter: alpha(opacity=100);
  opacity: 1;
  color: #666
}

.wizard_horizontal ul.wizard_steps li a:before {
  content: "";
  position: absolute;
  height: 4px;
  background: #ccc;
  top: 20px;
  width: 100%;
  z-index: 4;
  left: 0
}

.wizard_horizontal ul.wizard_steps li a.disabled .step_no {
  background: #ccc
}

.wizard_horizontal ul.wizard_steps li a .step_no {
  width: 40px;
  height: 40px;
  line-height: 40px;
  border-radius: 100px;
  display: block;
  margin: 0 auto 5px;
  font-size: 16px;
  text-align: center;
  position: relative;
  z-index: 5
}

.wizard_horizontal ul.wizard_steps li a.selected:before,
.step_no {
  background: #34495E;
  color: #fff
}

.wizard_horizontal ul.wizard_steps li a.done:before,
.wizard_horizontal ul.wizard_steps li a.done .step_no {
  background: #1ABB9C;
  color: #fff
}

.wizard_horizontal ul.wizard_steps li:first-child a:before {
  left: 50%
}

.wizard_horizontal ul.wizard_steps li:last-child a:before {
  right: 50%;
  width: 50%;
  left: auto
}

.wizard_verticle .stepContainer {
  width: 80%;
  float: left;
  padding: 0 10px
}

a {
  color: #5A738E;
  text-decoration: none
}

a,
a:visited,
a:focus,
a:active,
:visited,
:focus,
:active,
.btn:focus,
.btn:active:focus,
.btn.active:focus,
.btn.focus,
.btn:active.focus,
.btn.active.focus {
  outline: 0
}

a:hover,
a:focus {
  text-decoration: none
}
    </style>
    <div class="form_wizard wizard_horizontal"><ul class="wizard_steps anchor"><li><a class="selected " isdone="1">
                    <span class="step_no">1</span></a></li> <li><a class="selected " isdone="1">
                    <span class="step_no">2</span></a></li> <li><a class="selected " isdone="1">
                    <span class="step_no">3</span></a></li> <li><a class="disabled " isdone="0">
                    <span class="step_no">4</span></a></li> <li><a class="disabled " isdone="0">
                    <span class="step_no">5</span></a></li></ul></div>    <style>
    

.wizard_horizontal ul.wizard_steps {
    display: table;
    list-style: none;
    position: relative;
    width: 100%;
  margin: 0 0 20px
}

.wizard_horizontal ul.wizard_steps li {
  display: table-cell;
  text-align: center
}

.wizard_horizontal ul.wizard_steps li a,
.wizard_horizontal ul.wizard_steps li:hover {
  display: block;
  position: relative;
  -moz-opacity: 1;
  filter: alpha(opacity=100);
  opacity: 1;
  color: #666
}

.wizard_horizontal ul.wizard_steps li a:before {
  content: "";
  position: absolute;
  height: 4px;
  background: #ccc;
  top: 20px;
  width: 100%;
  z-index: 4;
  left: 0
}

.wizard_horizontal ul.wizard_steps li a.disabled .step_no {
  background: #ccc
}

.wizard_horizontal ul.wizard_steps li a .step_no {
  width: 40px;
  height: 40px;
  line-height: 40px;
  border-radius: 100px;
  display: block;
  margin: 0 auto 5px;
  font-size: 16px;
  text-align: center;
  position: relative;
  z-index: 5
}

.wizard_horizontal ul.wizard_steps li a.selected:before,
.step_no {
  background: #34495E;
  color: #fff
}

.wizard_horizontal ul.wizard_steps li a.done:before,
.wizard_horizontal ul.wizard_steps li a.done .step_no {
  background: #1ABB9C;
  color: #fff
}

.wizard_horizontal ul.wizard_steps li:first-child a:before {
  left: 50%
}

.wizard_horizontal ul.wizard_steps li:last-child a:before {
  right: 50%;
  width: 50%;
  left: auto
}

.wizard_verticle .stepContainer {
  width: 80%;
  float: left;
  padding: 0 10px
}

a {
  color: #5A738E;
  text-decoration: none
}

a,
a:visited,
a:focus,
a:active,
:visited,
:focus,
:active,
.btn:focus,
.btn:active:focus,
.btn.active:focus,
.btn.focus,
.btn:active.focus,
.btn.active.focus {
  outline: 0
}

a:hover,
a:focus {
  text-decoration: none
}
    </style>
    <div class="form_wizard wizard_horizontal"><ul class="wizard_steps anchor"><li><a rel="1" class="selected " isdone="1" href="#s1">
                    <span class="step_no">1</span>
                        <span class="step_descr">
                        <br>
                        <small></small>
                    </span></a></li> <li><a rel="2" class="selected " isdone="1" href="#s2">
                    <span class="step_no">2</span>
                        <span class="step_descr">
                        <br>
                        <small></small>
                    </span></a></li> <li><a rel="3" class="selected " isdone="1" href="#s3">
                    <span class="step_no">3</span>
                        <span class="step_descr">
                        <br>
                        <small></small>
                    </span></a></li> <li><a rel="4" class="disabled " isdone="0" href="#s4">
                    <span class="step_no">4</span>
                        <span class="step_descr">
                        <br>
                        <small></small>
                    </span></a></li></ul></div><h3 class="mb-3">Notes</h3>    <style>
    .note {
    padding: 10px;
    border-left: 6px solid;
    border-radius: 5px
  }

  .note strong {
    font-weight: 600
  }

  .note p {
    font-weight: 500
  }

  .note-primary {
    background-color: #e1ecfd;
    border-color: #1266f1
  }

  .note-secondary {
    background-color: #f4e3ff;
    border-color: #b23cfd
  }

  .note-success {
    background-color: #c6ffdd;
    border-color: #00b74a
  }

  .note-danger {
    background-color: #fee3e8;
    border-color: #f93154
  }

  .note-warning {
    background-color: #fff1d6;
    border-color: #ffa900
  }

  .note-info {
    background-color: #e1f6fc;
    border-color: #39c0ed
  }

  .note-light {
    background-color: #fbfbfb;
    border-color: #262626
  }    </style>
    <p class="note note-secondary mb-5"><strong>!!! Note secondary:</strong> Lorem, ipsum dolor sit amet consectetur adipisicing
        elit. Cum doloremque officia laboriosam. Itaque ex obcaecati architecto! Qui
        necessitatibus delectus placeat illo rem id nisi consequatur esse, sint perspiciatis
        soluta porro?</p><h3 class="mt-5 mb-3">Shadows</h3>    <style>
    /*
    https://mdbootstrap.com/docs/standard/content-styles/shadows/
  */

  .shadow-sm {
    box-shadow: 0 .125rem .25rem rgba(0, 0, 0, .075) !important
  }

  .shadow-lg {
    box-shadow: 0 1rem 3rem rgba(0, 0, 0, .175) !important
  }

  .shadow-0,
  .shadow-none {
    box-shadow: none !important
  }

  .shadow-1 {
    box-shadow: 0 1px 2px 0 rgba(0, 0, 0, .07) !important
  }

  .shadow-2 {
    box-shadow: 0 1px 3px 0 rgba(0, 0, 0, .07), 0 1px 2px 0 rgba(0, 0, 0, .05) !important
  }

  .shadow-3 {
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, .07), 0 2px 4px -1px rgba(0, 0, 0, .05) !important
  }

  .shadow-4 {
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, .07), 0 4px 6px -2px rgba(0, 0, 0, .05) !important
  }

  .shadow-5 {
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, .07), 0 10px 10px -5px rgba(0, 0, 0, .05) !important
  }

  .shadow-6 {
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, .21) !important
  }

  .shadow-1-soft {
    box-shadow: 0 1px 5px 0 rgba(0, 0, 0, .05) !important
  }

  .shadow-2-soft {
    box-shadow: 0 2px 10px 0 rgba(0, 0, 0, .05) !important
  }

  .shadow-3-soft {
    box-shadow: 0 5px 15px 0 rgba(0, 0, 0, .05) !important
  }

  .shadow-4-soft {
    box-shadow: 0 10px 20px 0 rgba(0, 0, 0, .05) !important
  }

  .shadow-5-soft {
    box-shadow: 0 15px 30px 0 rgba(0, 0, 0, .05) !important
  }

  .shadow-6-soft {
    box-shadow: 0 20px 40px 0 rgba(0, 0, 0, .05) !important
  }

  .shadow-1-strong {
    box-shadow: 0 1px 5px 0 rgba(0, 0, 0, .21) !important
  }

  .shadow-2-strong {
    box-shadow: 0 2px 10px 0 rgba(0, 0, 0, .21) !important
  }

  .shadow-3-strong {
    box-shadow: 0 5px 15px 0 rgba(0, 0, 0, .21) !important
  }

  .shadow-4-strong {
    box-shadow: 0 10px 20px 0 rgba(0, 0, 0, .21) !important
  }

  .shadow-5-strong {
    box-shadow: 0 15px 30px 0 rgba(0, 0, 0, .21) !important
  }

  .shadow-6-strong {
    box-shadow: 0 20px 40px 0 rgba(0, 0, 0, .21) !important
  }

  .shadow-inner {
    box-shadow: inset 0 2px 4px 0 rgba(0, 0, 0, .06) !important
  }

  .shadow {
    box-shadow: 0 2px 5px 0 rgba(0, 0, 0, .25), 0 3px 5px 5px rgba(0, 0, 0, 0.05) !important;
  }    </style>
    <div class="shadow p-3">Some content</div>
        <p><p>
    </div>
</div>

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