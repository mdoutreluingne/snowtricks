(function () {

    let donnees = {
        "trick": $("#deleteMainPicture").data("trick")
    }

    $("#deleteMainPicture").click(function () {
        $.ajax({
            type: "POST",
            url: '/trick/ajax-delete-mainpicture',
            data: JSON.stringify(donnees),
            contentType: 'application/json',
            success: function () {
                alert("Image supprimée avec succès");
                document.location.reload();
            },
            error: function (data) {
                alert('error ' + data);
            }
        });
    });

})();