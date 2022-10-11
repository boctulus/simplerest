<!--
    https://www.bootdey.com/snippets/view/Your-Mail-Sent-Successfully#html
-->

<script>
    document.addEventListener("DOMContentLoaded", function(event) { 
        var mail = window.atob(window.location.pathname.split('/')[3]);
        document.querySelector('#mail').innerText = mail;
    });    
</script>

<style>
    #mail {
        color: blue;
    }

    body{margin-top:20px;}

    .mail-seccess {
        text-align: center;
        background: #fff;
        border-top: 1px solid #eee;
    }
    .mail-seccess .success-inner {
        display: inline-block;
    }
    .mail-seccess .success-inner h1 {
        font-size: 100px;
        text-shadow: 3px 5px 2px #3333;
        color: #006DFE;
        font-weight: 700;
    }
    .mail-seccess .success-inner h1 span {
        display: block;
        font-size: 25px;
        color: #333;
        font-weight: 600;
        text-shadow: none;
        margin-top: 20px;
    }
    .mail-seccess .success-inner p {
        padding: 20px 15px;
    }
    .mail-seccess .success-inner .btn{
        color:#fff;
    }
</style>


<section class="mail-seccess section">
	<div class="container">
		<div class="row">
			<div class="col-lg-6 offset-lg-3 col-12">
				<!-- Error Inner -->
				<div class="success-inner">
					<h1><i class="fa fa-envelope"></i><span>Correo enviado!</span></h1>
					<p>Si el correo <span id='mail'>correo</span> se encuentra en base de datos, recibirás allí el enlace para cambiar la contraseña.</p>
					<a href="#" class="btn btn-primary btn-lg">Ir a inicio</a>
				</div>
				<!--/ End Error Inner -->
			</div>
		</div>
	</div>
</section>