var Graph = {
    showEstimatesGraphfromstart: function (json) {
        var jobAlreadyStarted = Graph.showEstimatesGraphfromstart_doing || false;
        if (jobAlreadyStarted) {
            //console.log('fired twice');
            //lock for multiple
            return false;
        }
        Graph.showEstimatesGraphfromstart_doing = true;
        callWebService('Getestimatesfromstart', json, function(response) {
            if (response.status === "ok") {
                if (response.data.Estimates.length === 0) {
                    msgBox('Du har inte övat ännu'); //No estimate yet.
                } else {
                    //random data for testing
                    //
                    //response.data.TodayDays = Math.floor(Math.random() * 60) + 1;

                    //for (var i = 0; i < response.data.TodayDays; i++) {
                    //    response.data.Estimates[i] = Math.floor(Math.random() * 10) + 1;
                    //}



                    var graphhtml = new Array();
                    for (var i = 0; i < response.data.Estimates.length; i++) {
                        graphhtml.push('<div class="barline">');
                        if (response.data.Estimates[i] == 0) {
                            graphhtml.push('<span class="whitebox" style="visibility:hidden;">&nbsp;</span>');
                        } else {
                            for (var j = 0; j < response.data.Estimates[i]; j++) {
                                graphhtml.push('<span class="whitebox">&nbsp;</span>');
                            }
                        }
                        graphhtml.push('</div>');
                    }
                    $("#divRatingLines").html(graphhtml.join(""));
                    //Other Details
                    if (response.data.Estimates.length < 30) {
                        $("#TrainingRating .barline").css({
                            "padding": "0 1px"
                        });
                        $("#TrainingRating .whitebox").css({
                            "width": "94%"
                        });
                    } else if (response.data.Estimates.length < 50) {
                        $("#TrainingRating .barline").css({
                            "padding": "0 1px"
                        });
                        $("#TrainingRating .whitebox").css({
                            "width": "2px"
                        });
                    } else {
                        $("#TrainingRating .barline").css({
                            "padding": "0 0"
                        });
                        $("#TrainingRating .whitebox").css({
                            "width": "1px"
                        });
                    }

                    $("#EstimateAntalskattningar").html('<span class="text fleft">Antal<br />skattningar</span> ' + response.data.Estimates.length);
                    $("#EstimateAntaldagar").html('<span class="text fleft">Antal<br />dagar</span> ' + response.data.TodayDays);
                    $("#EstimateStartdatum").html("Startdatum " + response.data.startdate);
                    $("#EstimateDegansdatum").html("Dagens datum " + response.data.todaydate);                }
            } else {
                msgBox('Ett fel har inträffat, vänligen försök igen.');
            }
        }, function() {

            Graph.showEstimatesGraphfromstart_doing = false;
        });
    }
};