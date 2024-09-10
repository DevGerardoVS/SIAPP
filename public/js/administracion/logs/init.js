var dao = {
	getData: function () {
		$.ajax({
			type : "GET",
			url : '/get-logs',
            dataType: "JSON",
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
        }).done(function (_data) {
            console.log(_data);
            var par = $('#selected');
            par.html('');
            $.each(_data, function (i, val) {
                par.append(new Option(val.filename, val.filename));
            });

		});
    },
    exportLog: function () {
        let file = $('#selected').val();
            _url = "/logs/download/" + file ;
            window.open(_url, '_blank');
    },
    cleantLog: function () {
        var form = $('#formLogs')[0];
        var data = new FormData(form);
        console.log("ee");
 /*        data.append("upp",$('#upp_filter').val()); */
        $.ajax({
            type: "POST",
            url: '/logs/clean',
            data: data,
            enctype: 'multipart/form-data',
            processData: false,
            contentType: false,
            cache: false,
        }).done(function (response) {
            location.reload();
        });
    },
    deleteLog: function () {
        var form = $('#formLogs')[0];
        var data = new FormData(form);
        console.log("ee");
 /*        data.append("upp",$('#upp_filter').val()); */
        $.ajax({
            type: "POST",
            url: '/logs/delete',
            data: data,
            enctype: 'multipart/form-data',
            processData: false,
            contentType: false,
            cache: false,
        }).done(function (response) {
            location.reload();

        });
    },
};

$(document).ready(function () {
	dao.getData();
});