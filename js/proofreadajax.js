$(function(){
    $(document).on("click", "#getbodytext", function() {
        // Search texts on the page.
        if ($(this).data('areaclass')) {
            var classname = $(this).data('areaclass');
            bodytext = $(classname).html();
        } else {
            bodytext = $("div.ccm-page").html();
        }
       if (bodytext) {
        bodytext = bodytext.replace(/<script.*?>[^<]*?<\/script>/g, "");
        bodytext = bodytext.replace(/<style.*?>[^<]*?<\/style>/g, "");
        bodytext = bodytext.replace(/<.*?>/g, "");
        bodytext = bodytext.replace(/\s+/g, " ");
        $("#textdata").val(bodytext);
        $("#textlen").text($("#textdata").val().length);
       }
    });
    $(document).on("change", "#textdata", function() {
        $("#textlen").text($("#textdata").val().length);
    });

    function dispLoading(){
        if($("#loading").size() == 0){
            $("#yahoosubmit").append("<div id='loading'></div>");
        }
    }
    function removeLoading(){
        $("#loading").remove();
    }

    $(document).on("submit", "#proofreading", function() {
        $("#result").text("");
        dispLoading();
        $.ajax({
            url: CCM_APPLICATION_URL + "/index.php/ounziw_proofreading/tools/proofpost",
            type: "post",
            data: $(this).serialize(),
            timeout: 10000,
    })
        .done(function(result, textStatus, xhr) {
            data = $.parseJSON(result);
            if (data.success == 0) {
                $("#result").text("Session Expired. Please reload the form and try again.");
            } else if (data.number) {
                $("#result").text(data.sentence);
                ResultText = $("#result").text();
                Total = parseInt(data.number)-1;
                // Highlights the words/phrases which the proofreading api alerted.
                // If we replace textdata from the beginning of the sentence, the StartPos of the remaining will change.
                // Thus we replace textdata from the end.
                for(i=Total;i>=0;i--) {
                    WordString = ResultText.substr(parseInt(data.data[i].StartPos), parseInt(data.data[i].Length));
                    if ($.isEmptyObject(data.data[i].ShitekiWord)) {
                        ShitekiWordText = '';
                    } else {
                        ShitekiWordText = '[' + data.data[i].ShitekiWord + ']';
                    }
                    WordWrapped = '<button type="button" class="btn btn-warning" data-toggle="tooltip" data-placement="bottom" title="'+ data.data[i].ShitekiInfo + ShitekiWordText +'">' + WordString + '</button>';
                    ResultText = ResultText.substr(0,parseInt(data.data[i].StartPos)) + WordWrapped + ResultText.substr(parseInt(data.data[i].StartPos) + parseInt(data.data[i].Length));
                }
                ResultText = ResultText.replace(/\. /g, ".<br><br>");

                $("#result").html(ResultText);
            } else {
                $("#result").text("There are no proofreading suggestions.");
            }
                removeLoading();
        })
            .fail(function(xhr, textStatus, error) {
                $("#result").text("Failed to connect to Yahoo. Could you please try again?");
                removeLoading();
            });
        return false;
    });
});