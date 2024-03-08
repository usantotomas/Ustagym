
//funcion para limitar el tamaño del archivo adjunto y mostrar modal
			
	$(function(){
			$('#file_archivo').on('change', function() {
				var fileSize = this.files[0].size / 1024; 	// tamaño en KB
				if(fileSize > 800) { 						// si el tamaño supera los 800 KB
					$('#myModal').modal('show');			// mostrar el modal
					this.value = null; 						// restablecer la entrada del archivo
				}
			});
		});
			
			
			
			
//funcion para seleccionar solo un checkbox
			
		$(function(){  // Espera hasta que el documento esté listo
			$(':checkbox').on('change', function() {  // Al cambiar cualquier checkbox...
				$(':checkbox').not(this).prop('checked', false);  // Desmarca todos los otros checkbox
			});
		});





