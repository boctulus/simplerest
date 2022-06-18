<h3>Practica Selenium</h3>

<script>
    function inc(){
        let el_counter = document.getElementById("counter")
        el_counter.value = parseInt(el_counter.value) +1;
    }
</script>

<div>
    <div class="form-group">
        <label for="counter">Contador</label>
        <input type="text" class="form-control" id="counter" value="0">
    </div>

    <div>
        <button type="button" class="btn-success btn" style="success" id="inc_counter" onClick="inc();">Incrementar</button>
    </div>
</div>