(function () {
    $("#showMoreOffsetComments").val(10); //Init display card

    $(".loadmore-comments").click(function () {
        var offset = $("#showMoreOffsetComments").val();
        var trick = $("#trickId").val();

        $.ajax({
            type: 'POST',
            url: '/comment/loadmore',
            data: {
                offset: offset,
                trick: trick
            },
            success: function (data) {

                //Display comment
                for (var i = 0; i < data.length; i++) {
                    var html = "";

                    html += '<h4 class="col-sm-3 mt-3 commentnotes">';
                    html += '<div class="comment-avatar">';
                    if (data[i]['avatar'] != null) {
                        html += '<img src="/img/avatars/' + data[i]['avatar'] + '" class = "img-fluid rounded-circle avatar-comment" >';
                    } else {
                        html += '<i class="fas fa-user-circle fa-sm"></i>';
                    }
                    html += '</div>';
                    html += '<strong>' + data[i]['user'] + '</strong><br /> publié le<br />';
                    html += '<strong>' + data[i]['created'] + '</strong>';
                    html += '</h4>';
                    html += '<div class="col-sm-9">' + data[i]['content'] + '</div>';

                    $("#list-comments").append(html);
                }

                $(".loadmore-comments").hide();
            },
            error: function (data) {
                alert("error " + data);
            }
        });
    });

})();