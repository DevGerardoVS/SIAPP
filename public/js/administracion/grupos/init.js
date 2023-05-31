var dao = {
	eliminarRegistro: function (id) {
		if (id != null) {
			Swal.fire({
				title: '¿Seguro que quieres eliminar este usuario?',
				text: "Esta accion es irreversible",
				icon: 'warning',
				showCancelButton: true,
				confirmButtonColor: '#3085d6',
				cancelButtonColor: '#d33',
				confirmButtonText: 'Confirmar'
			}).then((result) => {
				if (result.isConfirmed) {
					$.ajax({
						url: "{{ route('postDelete') }}",
						type: "POST",
						data: {
							"_token": "{{ csrf_token() }}",
							"id": id
						},
						success: function (response) {
							console.log(response)
							if (response == "done") {
								getData();
							}
						},
						error: function (response) {
							console.log('Error: ' + response);
						}
					});

				}
			});
		} else {
			Swal.fire({
				icon: 'info',
				title: 'No se puede eliminar, ya cuenta con usuarios relacionados',
				showConfirmButton: false,
				timer: 1500
			})
		}
	},

	crearGrupo: function () {
		var form = $('#frmCreate')[0];
		var data = new FormData(form);
		$.ajax({
			type: "POST",
			url: '/adm-grupos/store',
			data: data,
			enctype: 'multipart/form-data',
			processData: false,
			contentType: false,
			cache: false,
			timeout: 600000
		}).done(function (response) {
			CierraPopup();
			Swal.fire({
				icon: 'success',
				title: 'Your work has been saved',
				showConfirmButton: false,
				timer: 1500
			});
			getData();
		});
	},

	editarGrupo: function (id) {
		$.ajax({
			type: "GET",
			url: '/adm-grupos/update/' + id,
			enctype: 'multipart/form-data',
			processData: false,
			contentType: false,
			cache: false,
			timeout: 600000
		}).done(function (response) {
			const { id, nombre_grupo } = response;
			$('#id_user').val(id);
			$('#nombre').val(nombre_grupo);
		});
	},
	limpiar: function () {
		$('#id_user').val(null);
		$('#nombre').val("");
		$('#nombre-error').text("").removeClass("has-error").removeClass('d-block'); 
		$('.col-md-8').removeClass("has-error");


	},
	CierraPopup: function () {
		$("#createGroup").modal('hide'); //ocultamos el modal
		$('body').removeClass('modal-open'); //eliminamos la clase del body para poder hacer scroll
		$('.modal-backdrop').remove(); //eliminamos el backdrop del modal
	}

};

var init = {
	validateCreate: function (form) {
		_gen.validate(form, {
			rules: {
				nombre: { required: true }
			},

			messages: {
				nombre: { required: "Este campo es requerido" }
			}
		});
	},
};

$(document).ready(function () {
	getData();
	$('#createGroup').modal({
		backdrop: 'static',
		keyboard: false
	});
	$('#btnSave').click(function (e) {
		e.preventDefault();
		if ($('#frmCreate').valid()) {
			dao.crearGrupo();
		}
	});
});