(function () {
    $("#showMoreOffset").val(8); //Init display card
    $(".back-to-top").removeClass('active'); //Hide chevron top
    

    $(".loadmore-tricks").click(function () {
        var offset = $("#showMoreOffset").val();
        var loggedin = $(".trick-home").data('loggedin');

        $.ajax({
            type: 'POST',
            url: '/trick/loadmore',
            data: {
                offset: offset
            },
            success: function (data) {
                
                //Display card
                for (var i = 0; i < data.length; i++) {
                    var urlViewTrick = "/trick/" + data[i]['slug'];
                    var urlEditTrick = "/trick/" + data[i]['slug'] + "/edit";
                    var urlDeleteTrick = "/trick/" + data[i]['slug'] + "/delete";

                    var html = "";

                    html += '<div class="col col-xl-3 col-lg-6 col-md-6 col-sm-6 col-12">';
                    html += '<div class="card">';
                    html += '<img class="img-fluid" src="/img/tricks/default.jpg" alt="default">';
                    html += '<div class="card-body">';
                    html += '<span class="tag tag-teal">' + data[i]['category'] + '</span>';
                    html += '<h4><a href="' + urlViewTrick + '" class="title_trick">' + data[i]['name'] + '</a>';
                    if (loggedin === true) {
                        html += '<a href="' + urlEditTrick + '" data-toggle="tooltip" data-placement="right" title="Modifier une figure"><i class="fas fa-pencil-alt"></i></a>';
                        html += '<form method="post" class="form-delete" action="'+ urlDeleteTrick + '" onsubmit = "return confirm(\'Êtes-vous sûr de bien vouloir supprimer cet élément ?\');" >';
                        html += '<button class="btn" data-toggle="tooltip" data-placement="right" title="Supprimer"><i class="fas fa-trash-alt fa-lg"></i></button>';
                        html += '</form>';
                    }
                    html += '</h4></div>';
                    html += '</div>';
                    html += '</div>';

                    $(".trick-home").append(html);
                }

                $(".loadmore-tricks").hide();

                if ($(".card").length > 15) {
                    $(".back-to-top").addClass('active');
                }
            },
            error: function (data) {
                alert("error " + data);
            }
        });
    });

})();