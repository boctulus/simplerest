<style>
/* Estilos para el aro del puntaje */
.score-ring {
    width: 200px; /* Ajusta el tamaño del aro según tus preferencias */
    height: 200px; /* Ajusta el tamaño del aro según tus preferencias */
    background-color: transparent;
    border: 10px solid #76c96e; /* Color verde opaco para el aro */
    border-radius: 50%; /* Convierte el elemento en un círculo */
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 20px; /* Ajusta el margen según tus preferencias */
}

.score-number {
    font-size: 4rem; /* Ajusta el tamaño de fuente de los números según tus preferencias */
    color: #76c96e; /* Color del texto dentro del aro */
}

/* Estilos para la tabla */
.custom-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 1.5rem;
}

.custom-table td {
    padding: 10px;
    text-align: left;
    border-bottom: 1px solid #ccc; /* Líneas horizontales internas */
    border-right: 1px solid transparent; /* Líneas verticales internas (transparentes) */
}

.custom-table td:first-child {
    font-weight: bold;
    border-left: none; /* Elimina la línea vertical en la primera columna */
}

.custom-table tr:last-child td {
    border-bottom: none;
}


/* Estilos personalizados para el título */
.custom-title {
    font-size: 180%; /* Ajusta el tamaño de fuente del título según tus preferencias */
    color: #76c96e; /* Color del título */
    display: block;
    text-align: center;
    margin-bottom: 20px; /* Ajusta el margen según tus preferencias */
}

/* Estilos personalizados para el contenedor de SweetAlert */
.custom-swal-container {
    width: 80%; /* Ajusta el ancho del contenedor según tus preferencias */
}
</style>

<script>

document.addEventListener('DOMContentLoaded', () => {
    Swal.fire({
        title: "<span class='custom-title'>APROBADO</span>",
        html: `<div class="score-ring">
                <span class="score-number">45</span>
            </div>
            <table class="custom-table">
                <tbody>
                    <tr>
                        <td id="st_pt">Puntaje</td>
                        <td>00</td>
                    </tr>
                    <tr>
                        <td id="st_qq">Preguntas</td>
                        <td>00</td>
                    </tr>           
                    <tr>
                        <td id="st_rq">Correctas</td>
                        <td>00</td>
                    </tr>
                    <tr>
                        <td id="st_fq">Fallidas</td>
                        <td>00</td>
                    </tr>
                </tbody>
            </table>`,
        customClass: {
            container: 'custom-swal-container'
        }
    });
})

</script>