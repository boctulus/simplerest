
	<script>
		checkpoint();
	</script>

	<h1 class="red-text text-center" style="font-size:2em">Products</h1>

	<div id="tb_products" class="table-responsive" style="margin-top:4em;">
	</div>

	<a href="#productModalAdd" class="btn btn-info btn-sm" data-toggle="modal" onClick="clearForm();"> 
	  <span class="glyphicon glyphicon-plus"></span> Add 
	</a>

	<!-- Add Form Modal -->
	<div class="modal fade" id="productModalAdd" tabindex="-1" role="dialog" aria-labelledby="productModalAddLabel" aria-hidden="true">
	<div class="modal-dialog">
	  <div class="modal-content">
		<div class="modal-header">
		  <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		  <h4 class="modal-title" id="productModalAddTitle">Add product</h4>
		</div>
		<div class="modal-body">
			<form>
				<div class="form-group">
					<label for="first_name">Name</label>
					<input type="text" class="form-control" id="name"/>
				</div>
				
				<div class="form-group">
					<label for="last_name">Description</label>
					<textarea type="text" class="form-control" id="description"></textarea>
				</div>
				
				<div class="form-group">
					<label for="last_name">Cost</label>
					<input type="text" class="form-control" id="cost"/>
				</div>
				
				<div class="form-group">
					<label for="last_name">Size</label>
					<input type="text" class="form-control" id="size"/>
				</div>
			</form>
		</div>
		<div class="modal-footer">
		  <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
		  <button type="button" class="btn btn-primary" onClick="crear()">Save changes</button>
		</div>
	  </div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
	</div><!-- /.modal -->


	<!-- Edit Form Modal -->
	<div class="modal fade" id="productModalEdit" tabindex="-1" role="dialog" aria-labelledby="productModalAddLabel" aria-hidden="true">
	<div class="modal-dialog">
	  <div class="modal-content">
		<div class="modal-header">
		  <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		  <h4 class="modal-title" id="productModalAddTitle">Edit product</h4>
		</div>
		<div class="modal-body">
			<form>
				<input type="hidden" id="eid"/>
			
				<div class="form-group">
					<label for="first_name">Name</label>
					<input type="text" class="form-control" id="ename"/>
				</div>
				
				<div class="form-group">
					<label for="last_name">Description</label>
					<textarea type="text" class="form-control" id="edescription"></textarea>
				</div>
				
				<div class="form-group">
					<label for="last_name">Cost</label>
					<input type="text" class="form-control" id="ecost"/>
				</div>
				
				<div class="form-group">
					<label for="last_name">Size</label>
					<input type="text" class="form-control" id="esize"/>
				</div>
			</form>
		</div>
		<div class="modal-footer">
		  <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
		  <button type="button" class="btn btn-primary" onClick="do_edit()">Save changes</button>
		</div>
	  </div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
	</div><!-- /.modal -->


	<!-- footer                          -->
	<div class="footer navbar-fixed-bottom" style="padding: 0 15px 0 15px; font-size: 1.2em;">
	
	</div>
	
	
</div>		
<script type="text/javascript">
	const endpoint = '/api/v1/products';

	if (localStorage.getItem('expires_in') == null)
		localStorage.setItem('expires_in', 59);  

	let $data = [];
	let table = new JqTable('tb_products');
	
	$(document).ready(()=>{
		listar();
		
		// renew token
		setInterval(function() {
			if (localStorage.getItem('exp')==null)
				return;
			else
				checkpoint();
			
			// diff is less than ___
			if ( ((localStorage.getItem('exp')*1000) - (new Date()).getTime()) < 1000 * localStorage.getItem('expires_in') - 3000){
				renew();
			}
			
		}, localStorage.getItem('expires_in') * 1000 * 0.97); 
	});
	
	
	toastr.options = {
		"closeButton": false,
		"debug": false,
		"newestOnTop": false,
		"progressBar": false,
		"positionClass": "toast-top-right",
		"preventDuplicates": false,
		"onclick": null,
		"showDuration": "300",
		"hideDuration": "1000",
		"timeOut": "5000",
		"extendedTimeOut": "1000",
		"showEasing": "swing",
		"hideEasing": "linear",
		"showMethod": "fadeIn",
		"hideMethod": "fadeOut"
	};
	
	function clearForm(){
		checkpoint();
		
		$('#name').val("");	
		$('#description').val("");	
		$('#cost').val("");
		$('#size').val("");
	}
	
	/* Edit */
	function do_edit(){
		checkpoint();
		
		let obj ={};
		let id   = $('#eid').val();
		obj.name = $('#ename').val();	
		obj.description = $('#edescription').val();	
		obj.cost = $('#ecost').val();
		obj.size = $('#esize').val();		
					
		//console.log(obj);	
					
		let encoded = JSON.stringify(obj);

		$.ajax({
			type: "PUT",	/* PUT VERB */
			url: endpoint + '/' + id.toString(),
			data: encoded,
			dataType: 'json',
			headers: {"Authorization": 'Bearer ' + localStorage.getItem('access_token')},
			success: function(res){
				var data = res.data;
				
				console.log(data);
				
				if (!data.error){
					$('#productModalEdit').modal('hide');

					table.editRow([id,obj.name,obj.description, obj.size,obj.cost]);
					toastr["success"]("Product edited!", "Success");
				}else
					toastr["error"]("An error ocurred", "Error");				
			},
			error: function(data){
				console.log('!Error~', data);
				toastr["error"]("An error ocurred!", "Error");
			}
		});
	}
	
	/* just previous reading to save updated data */
	function editar(id)
	{
		checkpoint();
		
		$.ajax({
			type: "GET",	// lectura previa //
			url: endpoint+'/'+id.toString(),
			dataType: 'json',
			headers: {"Authorization": 'Bearer ' + localStorage.getItem('access_token')},
			success: function(res){
				var data = res.data;

				$('#eid').val(data.id);
				$('#ename').val(data.name);	
				$('#edescription').val(data.description);	
				$('#ecost').val(data.cost);
				$('#esize').val(data.size);	
				
				$('#productModalEdit').modal('show');
			},
			error: function(data){
				console.log('Error');
				console.log(data);
			}
		});		
		
		//console.log(id);
	}

	/* Create */
	function crear()
	{
		checkpoint();
		
		let obj ={};
		obj.name = $('#name').val();	
		obj.description = $('#description').val();	
		obj.cost = $('#cost').val();
		obj.size = $('#size').val();		
				
		let encoded = JSON.stringify(obj);
	
		$.ajax({
			type: "POST",
			url: endpoint,
			data: encoded,
			dataType: 'json',
			headers: {"Authorization": 'Bearer ' + localStorage.getItem('access_token')},
			success: function(res){
				var data = res.data;

				if (!data.error){
					$('#productModalAdd').modal('hide');

					obj.id = data.id;
					
					table.addRow([obj.id,obj.name,obj.description, obj.size,obj.cost]);
					toastr["success"]("Product added!", "Success");
				}else{
					toastr["error"]("An error ocurred", "Error");	
					console.log(data.error);
				}		
			},
			error: function(data){
				console.log('Error', data);
				toastr["error"]("An error ocurred!", "Error");
			}
		});
	}
	
	/* Delete */
	function borrar(id){
		checkpoint();
		
		bootbox.confirm("Are you sure you want to delete?", function(result) {
			if (result)	
				$.ajax({
						type: "DELETE",	/* DELETE VERB */
						url: endpoint + '/' + id.toString(),
						dataType: 'json',
						headers: {"Authorization": 'Bearer ' + localStorage.getItem('access_token')},
						success: function(res){
							var data = res.data;

							$('#tr'+id.toString()).remove();
							toastr["warning"]("Product deleted!", "Success");

							if(data.error)
								console.log('Error',data.error);
						},
						error: function(data){
							console.log('Error', data);
						}
					});		
		}).find('.modal-content').css({
			'background-color': '#f99',
			'font-weight' : 'bold',
			'color': '#F00',
			'font-size': '2em'
		});
		
	}
	
	/* Read */
	function listar(){
		checkpoint();
		
		$.ajax({
			type: "GET",
			url: endpoint,
			dataType: 'json',
			headers: {"Authorization": 'Bearer ' + localStorage.getItem('access_token')},
			success: function(res){
				var data = res.data;
				
				// unhide				
				$('#dvTable').removeClass('hidden');
				
				// array de objetos --> array de arrays
				for (i=0;i<data.length;i++){
					let row = [];
					for(let key in data[i]) {
						let value = data[i][key];
						row.push(value);
					}
					$data.push(row);
				}
		
				
				// headers
				$data.unshift(["Id","Name","Description","Size","Cost"]);
				
				// row classes
				$data.rowClasses = ['info','','warning'];
				
				table.render($data);
				//table.hide_first_col();
			},
			error: function(data, textStatus, errorThrown){
				if (data.status == 401){
					checkpoint(); /////////
				}

				console.log(textStatus);
				console.log(errorThrown);

				if (typeof data.responseJSON != 'undefined' && typeof data.responseJSON.error != 'undefined')
					toastr["error"](data.responseJSON.error, "Error");

				console.log('Error in GET all', data, data.status);
			}
		});		
	}
	
</script>
