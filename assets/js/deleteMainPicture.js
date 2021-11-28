(function () {

    let donnees = {
        "trick": $("#deleteMainPicture").data("trick")
    }

    $("#deleteMainPicture").click(function () {
        
        if (window.confirm("Êtes-vous sûr de bien vouloir supprimer cet élément ?")) {
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
                    if (data.status === 404) {
                        alert("Aucune image à la une disponible");
                    } else {
                        alert("error " + data);
                    }
                }
            });
        }
    });

})();