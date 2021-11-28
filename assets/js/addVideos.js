$(document).ready(function () {
    var $collectionHolderVideo;
    var $newLinkLiVideo = $('#trickVideos');
    var regexYoutube = new RegExp("^(http(s)?:\\/\\/)?((w){3}.)?youtu(be|.be)?(\\.com)?\\/.+");
    var regexDailymotion = new RegExp("^.+dailymotion.com\\/((video|hub)\\/([^_]+))?[^#]*(#video=([^_&]+))?/");
    var regexVimeo = new RegExp("^(http(s)?:\\/\\/)?((w){3}.)?player.vimeo.com/video\\/.+");
    
    // Get the ul that holds the collection of tags
    $collectionHolderVideo = $('#trickVideos');

    $addTagButtonVideo = $('#addVideoUpload');

    $addTagButtonVideo.on('click', function (e) {
        // add a new tag form (see next code block)
        addTagForm($collectionHolderVideo, $newLinkLiVideo);
    });

    var initialVideosIndex = 0;

    function addTagForm($collectionHolder, $newLinkLi) {

        $('#trickVideos').data('index', initialVideosIndex + $('#trickVideos').find(':input').length);
        // Get the data-prototype explained earlier
        var prototype = $collectionHolder.data('prototype');
        // get the new index
        var index = $collectionHolder.data('widget-counter') || $collectionHolder.children().length;
        
        var newForm = prototype;

        // Replace '__name__' in the prototype's HTML to
        // instead be a number based on how many items we have
        newForm = newForm.replace(/__name__/g, index) + "<hr/>";

        // increase the index with one for the next item
        $collectionHolder.data('index', index + 1);

        // Display the form in the page in an li, before the "Add a tag" link li
        var $newFormLi = $('<div></div>').append(newForm);
        $newLinkLi.append($newFormLi);
    }

    $(document).on('click', '.deleteVideo', function (e) {
        $(this).parent().parent().remove();
    });

    $(document).on('change', '.videoAddInput', function () {
        var nbVideoFields = $('#trickVideos div input').length - 1 + initialVideosIndex;
        var nbNonValide = 0;

        for (var i = 0; i <= nbVideoFields; i++) {
            var url = $("#trick_add_form_videos_" + i + "_url").val();
            if (typeof url == "undefined") {
                url = $("#trick_edit_form_videos_" + i + "_url").val();
            }
            if (url !== "" && typeof url !== "undefined") {
                var valideYoutube = regexYoutube.test(url);
                var valideDailymotion = regexDailymotion.test(url);
                var valideVimeo = regexVimeo.test(url);
                if (!valideYoutube && !valideDailymotion && !valideVimeo) {
                    nbNonValide = nbNonValide + 1;
                }
            }
        }
        if (nbNonValide > 0) {
            alert("Une URL de vidéo entrée n'est pas valide ! Nous acceptons les vidéos provenant de Youtube, Dailymotion et Viméo.");
        }
    });

    /* Display number of file */
    $("#trick_picture_collection").change(function () {
        if (this.files.length > 1) {
            $(".custom-file-label").text(this.files.length + " images sélectionnés");
        } else {
            $(".custom-file-label").text(this.files.length + " image sélectionné");
        }
    });

});