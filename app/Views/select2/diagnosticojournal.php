
<script>
  const arr = <?= $json ?>

  function setSelect2Options(select_elem, options, default_option){
    // clear
    jQuery(select_elem).val(null).trigger('change')
    jQuery(select_elem).val(null).empty().select2('destroy')
    jQuery(select_elem).val('').trigger('change')
    jQuery(select_elem).select2({data: {id:null, text: null}})

    // agrego delante la opcion por defecto
    options.unshift(default_option)

    jQuery(select_elem).select2({data: options});
  }

  let countries_elem;
  let states_elem;
  
  let country_items = [];
  let state_items = [];

  /*
    Genera valores para rellenar el select
  */
  function fill_countries(){      
    for (var i=0; i<countries.length; i++)
    {
      country_items.push({
        'id': countries[i],
        'text': countries[i]
      });
    }
  }

  function fill_states(states){      
    for (var i=0; i<states.length; i++)
    {
      state_items.push({
        'id': states[i],
        'text': states[i]
      });
    }
  }


  jQuery(document).ready(function() {
	  const countries = 'mce-MMERGE5';
	  const states    = 'mce-MMERGE6';

      $('.select2-countries').select2();
      $('.select2-states').select2();

      countries_elem = document.getElementById(countries);
      states_elem    = document.getElementById(states);

      fill_countries()
      setSelect2Options(countries_elem, country_items, {'id': 'NULL', 'text': 'Pais'});

      jQuery(countries_elem).change(function(){
        let country_name_selected = countries_elem.value;
        
        let state_name_selected = find_country(country_name_selected);

        if (state_name_selected != null){
          state_items = [];
          fill_states(state_name_selected)
          setSelect2Options(states_elem, state_items, {'id': 'NULL', 'text': 'Provincia o estado'});
        }

      });
  });

  const array_column = (array, column) => {
      return array.map(item => item[column]);
  };

  const countries = array_column(arr['countries'], 'country');

  /*
    Busca por pais y devuelve los estados
  */
  function find_country(name){
    for (var i=0; i<arr['countries'].length; i++)
    {
      if (arr['countries'][i]['country'] == name){
          return arr['countries'][i]['states'];
      }
    }

    return null;
  }

</script>

<style type="text/css">
	#mc_embed_signup{background:#fff; clear:left; font:14px Helvetica,Arial,sans-serif;  width:600px;}
	/* Add your own Mailchimp form style overrides in your site stylesheet or in this style block.
	   We recommend moving this block and the preceding CSS link to the HEAD of your HTML file. */
</style>

<div id="mc_embed_signup">

<form action="https://stencilventas.us7.list-manage.com/subscribe/post?u=58f0eff62ecb1fbec7fa20640&amp;id=16878eeb56" method="post" id="mc-embedded-subscribe-form" name="mc-embedded-subscribe-form" class="validate" target="_blank" novalidate>
    <div id="mc_embed_signup_scroll">
	<h2>Subscribe</h2>
<div class="indicates-required"><span class="asterisk">*</span> indicates required</div>
<div class="mc-field-group">
	<label for="mce-EMAIL">Email Address  <span class="asterisk">*</span>
</label>
	<input type="email" value="" name="EMAIL" class="required email" id="mce-EMAIL">
</div>
<div class="mc-field-group">
	<label for="mce-FNAME">First Name </label>
	<input type="text" value="" name="FNAME" class="" id="mce-FNAME">
</div>
<div class="mc-field-group">
	<label for="mce-MMERGE5">Pais </label>
	<select name="MMERGE5" class="select2-countries" id="mce-MMERGE5">
		<option value=""></option>
		<option value="Argentina">Argentina</option>
		<option value="México">México</option>
		<option value="Brasil">Brasil</option>
	</select>
</div>
<div class="mc-field-group">
	<label for="mce-MMERGE6">Provincia </label>
	<select name="MMERGE6" class="select2-states" id="mce-MMERGE6">
		<option value=""></option>
		<option value="Prov 1">Prov 1</option>
		<option value="Prov 2">Prov 2</option>
		<option value="Prov 3">Prov 3</option>
	</select>
</div>
	<div id="mce-responses" class="clear foot">
		<div class="response" id="mce-error-response" style="display:none"></div>
		<div class="response" id="mce-success-response" style="display:none"></div>
	</div>    <!-- real people should not fill this in and expect good things - do not remove this or risk form bot signups-->
    <div style="position: absolute; left: -5000px;" aria-hidden="true"><input type="text" name="b_58f0eff62ecb1fbec7fa20640_16878eeb56" tabindex="-1" value=""></div>
        <div class="optionalParent">
            <div class="clear foot">
                <input type="submit" value="Subscribe" name="subscribe" id="mc-embedded-subscribe" class="button">
                <p class="brandingLogo"><a href="http://eepurl.com/h48ldf" title="Mailchimp - email marketing made easy and fun"><img src="https://eep.io/mc-cdn-images/template_images/branding_logo_text_dark_dtp.svg"></a></p>
            </div>
        </div>
    </div>
</form>
</div>


<script type='text/javascript'>

(
	function($) {
		window.fnames = new Array(); 
		window.ftypes = new Array();
		fnames[0]='EMAIL';
		ftypes[0]='email';
		fnames[1]='FNAME';
		ftypes[1]='text';
		fnames[2]='LNAME';
		ftypes[2]='text';
		fnames[3]='ADDRESS';
		ftypes[3]='address';
		fnames[4]='PHONE';
		ftypes[4]='phone';
		fnames[5]='MMERGE5';
		ftypes[5]='dropdown';
		fnames[6]='MMERGE6';
		ftypes[6]='dropdown';
	}(jQuery)
);

//var $mcj = jQuery.noConflict(true);
</script>
<!--End mc_embed_signup-->

