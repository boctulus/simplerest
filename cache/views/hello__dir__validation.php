
<h3>Bt5 Form validation</h3>

<div class="row mt-5">
    <div class="col-6 offset-3">

    <form class="needs-validation" novalidate=""><label for="nombre" class="form-label">Nombre</label> <input class="form-control" type="text" name="nombre" required="" id="nombre"></input> <label for="apellido" class="form-label mt-3">Apellido</label> <input class="form-control" type="text" name="apellido" placeholder="apellido" id="apellido"></input> <label for="edad" class="form-label mt-3">Edad</label> <input class="form-range" min="0" max="99" type="range" name="edad" id="edad" value="10"></input> <label for="exp" class="form-label mt-3">Experiencia</label> <input class="form-range" min="0" max="99" type="range" name="exp" id="exp" value="30"></input> <input class="btn btn-success mt-3" type="submit" id="enviar" value="Enviar"></input></form>