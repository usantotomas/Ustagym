// Logica para deshabilitar el boton de "Registrar" hasta completar el formulario

document.addEventListener('DOMContentLoaded', function() {
    
    const form = document.querySelector('.form2');
    const btn = document.querySelector('#submit-button');

    // Deshabilita el bot�n de registro al inicio.
    btn.disabled = true;

    // Este c�digo se ejecuta cada vez que se realiza una entrada en el formulario.
    form.addEventListener('input', function() {
		
        // Inicializa la variable que indica si todos los campos de entrada est�n llenos.
        let filled = true;
        // Inicializa la variable que indica si al menos un checkbox est� marcado.
        let checkboxChecked = false;

        // Para cada campo de entrada en el formulario (excluyendo checkboxes):
		
        document.querySelectorAll('.form2 input[type="text"], .form2 input[type="number"]').forEach(function(input) {
            // Si el campo de entrada est� vac�o, establece 'filled' como falso.
            if (input.value === '' && input.name !== 't_celular' && input.name !== 'edad' && input.name !== 'mail') {
                filled = false;
            }
        });

        // Para cada checkbox en el formulario:
        document.querySelectorAll('.form2 input[type="checkbox"]').forEach(function(checkbox) {
            // Si el checkbox est� marcado, establece 'checkboxChecked' como verdadero.
            if (checkbox.checked) {
                checkboxChecked = true;
            }
        });

        // Si todos los campos de entrada est�n llenos y al menos un checkbox est� marcado, habilita el bot�n de registro.
        // Si no, deshabilita el bot�n de registro.
        btn.disabled = !(filled && checkboxChecked);
    });
	
// ------------------------------------------------------------------------------------------------------//
	
//cancelar documento adjunto adjunto
	
	document.getElementById('cancel_file').addEventListener('click', function() {
    
    document.getElementById('file_archivo').value = '';
	
		
});
	
});

