$(document).ready(function() {
    $("input[type=text],input[type=password],textarea").blur(function() {
        if (this.id != "tidigareTimeEdit")
            window.scroll(0, 0);

        console.log('hehrdoing j');
    });
    $('.checkicon.unchecked').click(function() {
        trainingInfo = $.jStorage.get('userdetails');
        // $(this).removeClass('unchecked');
        // $('#Register .checkicon').addClass('full')

        setTimeout(function() {

            setTimeout(function() {
                //bring back the button to the fresh green state as soon as possible
                // $('#Register .checkicon').removeClass('full');
                // $('#Register .checkicon').removeClass('full');
                //  $('#Register .checkicon').addClass('unchecked');
                //  if(trainingInfo.training.type == 1)
                //      $('#Register .checkicon').addClass('full');

            }, 4000);





            $("#txtComments").val("");
            $("#practicedate").val(new Date().format("yyyy-mm-dd HH:MM"));
            $("#practicedate").change();


            $("#slider-fill,#slider-fill_2_2,#slider-fill_2_4").hide();

            $("div.rangeslider div.ui-slider-bg.ui-btn-active.ui-btn-corner-all").height(sliderpercentage[$("#slider-fill").val()] + "%");
            $("#rangeslider_2_2 div.ui-slider-bg.ui-btn-active.ui-btn-corner-all").height(sliderpercentage[$("#slider-fill_2_2").val()] + "%");
            $("#rangeslider_2_4 div.ui-slider-bg.ui-btn-active.ui-btn-corner-all").height(sliderpercentage[$("#slider-fill_2_4").val()] + "%");


            $('#slider-fill, #slider-fill_2_2, #slider-fill_2_4').unbind("change");

            $('#slider-fill').val('0').css('bottom', sliderFillpercentage[$('#slider-fill').val()] + '%');
            $('#slider-fill_2_2').val('0').css('bottom', sliderFillpercentage[$('#slider-fill_2_2').val()] + '%');
            $('#slider-fill_2_4').val('0').css('bottom', sliderFillpercentage[$('#slider-fill_2_4').val()] + '%');

            //var slide_bottom,slide2_2_bottom;
            $('#slider-fill').change(function() {
                $("div.rangeslider div.ui-slider-bg.ui-btn-active.ui-btn-corner-all").attr("style", "display:block");
                $("div.rangeslider div.ui-slider-bg.ui-btn-active.ui-btn-corner-all").height(sliderpercentage[$(this).val()] + "%");
                $("#slider-fill-span").attr("style", "display:block").html($(this).val()).css('bottom', sliderpercentage[$(this).val()] - 20 + "%");
                $("#sliderVal").html($(this).val() + " av 10");
            });



            $('#slider-fill_2_2').change(function() {
                $("#rangeslider_2_2 div.ui-slider-bg.ui-btn-active.ui-btn-corner-all").attr("style", "display:block");
                //slide2_2_bottom = sliderpercentage[$(this).val()];
                $("#rangeslider_2_2 div.ui-slider-bg.ui-btn-active.ui-btn-corner-all").height(sliderpercentage[$(this).val()] + "%");
                $("#slider-fill-span_2_2").attr("style", "display:block").html($(this).val()).css('bottom', sliderpercentage[$(this).val()] - 20 + "%");
                // $("#sliderVal_2_2").html($(this).val() + " av 10");
            });

            $('#slider-fill_2_4').change(function() {
                $("#rangeslider_2_4 div.ui-slider-bg.ui-btn-active.ui-btn-corner-all").attr("style", "display:bolck");
                //slide2_2_bottom = sliderpercentage[$(this).val()];
                $("#rangeslider_2_4 div.ui-slider-bg.ui-btn-active.ui-btn-corner-all").height(sliderpercentage[$(this).val()] + "%");
                $("#slider-fill-span_2_4").attr("style", "display:block").html($(this).val()).css('bottom', sliderpercentage[$(this).val()] - 20 + "%");
                // $("#sliderVal_2_2").html($(this).val() + " av 10");
            });

            $('#slider-fill,#slider-fill_2_2,#slider-fill_2_4').change();

            $("#rangeslider_2_4 div.ui-slider-bg.ui-btn-active.ui-btn-corner-all").attr("style", "display:none");
            $("#rangeslider_2_2 div.ui-slider-bg.ui-btn-active.ui-btn-corner-all").attr("style", "display:none");
            $("div.rangeslider div.ui-slider-bg.ui-btn-active.ui-btn-corner-all").attr("style", "display:none");
            $(".slider-fill-span").attr("style", "display:none");

            Training.process.init();

            Training.editedTrainingID = 0;
            if (Training.editedTrainingID !== 0) {
                var trainings = $.grep(Training.TrainingLists, function(e) {
                    return parseInt(e.trainingId) === parseInt(Training.editedTrainingID);
                });
                if (trainings.length > 0) {
                    console.log(trainings);
                    console.log('ever reached here??? wait which case is this one?')
                    var training = trainings[0];
                    $('#slider-fill').val(training.estimatedvalue).css('bottom', sliderFillpercentage[$("#slider-fill").val()] + "%");
                    $('#slider-fill_2_2').val(training.estimatedvalue).css('bottom', sliderFillpercentage[$("#slider-fill_2_2").val()] + "%");
                    if (training.estimatedvalue_end) {
                        $('#slider-fill_2_4').val(training.estimatedvalue_end).css('bottom', sliderFillpercentage[$("#slider-fill_2_4").val()] + "%");
                    }
                    $('#slider-fill').change();
                    $("#txtComments").val(training.comment);
                    $("#practicedate").val(training.trainingdatetime);
                    $("#practicedate").change();

                    $('#slider-fill_2_2').change();
                    $('#slider-fill_2_4').change();
                }
            }
        }, 100);
});
    // initialize datepickers
    var pickerTheme = 'ios7';
    if (IsIDevice) {
        var deviceIosVersion = iOSversion();
        if (deviceIosVersion != undefined && deviceIosVersion[0] === 7) {
            //$.loadCss("mobiscroll/css/mobiscroll.ios7.css");
            //$.getScriptSync("mobiscroll/js/mobiscroll.ios7.js");
            pickerTheme = 'ios7';
        } else {
            //$.loadCss('mobiscroll/css/mobiscroll.ios.css');
            //$.getScriptSync("mobiscroll/js/mobiscroll.ios.js");
            pickerTheme = 'ios';
        }

    } else {
        //$.loadCss('mobiscroll/css/mobiscroll.android.css');
        //$.getScriptSync("mobiscroll/js/mobiscroll.android.js");
        pickerTheme = 'andriod';

    }
    //pickerTheme = 'ios7';
    pickerTheme = 'android';
    var dateNow = new Date();

    // $(".bipDate").mobiscroll().datetime({
    //     preset: 'datetime',
    //     theme: pickerTheme,
    //     display: 'bottom',
    //     mode: 'mixed',
    //     startYear: dateNow.getFullYear(),
    //     endYear: dateNow.getFullYear(),
    //     maxDate: dateNow,
    //     lang: 'sv'
    // });
var mobiopt = {
    preset: 'datetime',
        //minDate: new Date(2012, 3, 10, 9, 22),
        //maxDate: new Date(2014, 7, 30, 15, 44),
        //stepMinute: 5,
        startYear: dateNow.getFullYear(),
        endYear: dateNow.getFullYear(),
        maxDate: dateNow,
        theme: pickerTheme,
        mode: 'mixed',
        setText: "Lägg till",
        cancelText: "Avbryt",
        secText: 'sec',
        lang: 'sv',
        display: 'bottom',
        animate: 'none',
        onShow: function() {
            //setTimeout(function() {
            // alert('shown');
            adjustDateTimePickerTopPosition();

            //}, 1500);
}
};


var mobiscrollTimeOptions = window.mobiscrollTimeOptions = {
    preset: 'time',
    theme: pickerTheme,
    timeWheels: 'iiss',
    timeFormat: 'ii:ss',
    mode: 'mixed',
    lang: 'sv',
    setText: "Lägg till",
    cancelText: "Avbryt",
    display: 'bottom',
    animate: 'none',
    minuteText: 'Minuter',
    secText: 'Sekunder',
    onShow: function() {
            //setTimeout(function() {
            // alert('shown');
            adjustDateTimePickerTopPosition();

            //}, 1500);
}
};

$(".bipDate").scroller('destroy').scroller(mobiopt);
$("#btnpracticedate").click(function() {
        //$('#practicedate').mobiscroll('show');
        $('#practicedate').scroller('show');
        //adjustDateTimePickerTopPosition();
        return false;
    });

    //shortcut
    $("#edit_practiced_date_single").click(function(e) {
        $('#practicedate').scroller('show');
        //adjustDateTimePickerTopPosition();
        e.preventDefault();
        return false;
    });


    $(".bip_edit_date").click(function(e) {
        //var tDate = new Date($("#tidigare_date_1_2").attr("value"));
        var tDate = parseDateTime($("#tidigare_date_1_2").attr("value"));
        var mobiScrollOptions = {
            preset: 'datetime',
            startYear: tDate.getFullYear(),
            endYear: tDate.getFullYear(),
            maxDate: tDate,
            theme: pickerTheme,
            mode: 'mixed',
            secText: 'sec',
            lang: 'sv',
            display: 'bottom',
            animate: 'none',
            onShow: function() {
                //setTimeout(function() {
                // alert('shown');
                adjustDateTimePickerTopPosition();

                //}, 1500);
}
};
$("#tidigare_date_1_2").scroller('destroy').scroller(mobiScrollOptions).scroller('setDate', tDate, true);

        //$("#tidigare_date_1_2").scroller('setDate', tDate, true);

        $("#tidigare_date_1_2").scroller('show');

        //adjustDateTimePickerTopPosition();

        e.preventDefault();
        return false;
    });

    // $(".bip_edit").click(function(e) {
    //     $("#tidigareTimeEdit").scroller(mobiscrollTimeOptions);
    //     $("#editminute").removeClass('bip_hidden');
    //     $(".bip_edit").addClass('bip_hidden');
    // });

$("#editminute").click(function(e) {
    $("#tidigareTimeEdit").scroller(mobiscrollTimeOptions);

    $("#tidigareTimeEdit").scroller('show');
    e.preventDefault();
    return false;
        //$("#editminute").removeClass('bip_hidden');
        //$(".bip_edit").addClass('bip_hidden');
    });

    // $("#editminute").click(function(e) {
    //     $("#tidigareTimeEdit").scroller('show');
    //     //adjustDateTimePickerTopPosition();
    //     e.preventDefault();
    //     return false;
    // })





$("#defaultTimeSpent").click(function(e) {
    updateUserTrainingTime();
});

$('#editTimeSpent').click(function(e) {
    $('#training_span').scroller('show');
        //adjustDateTimePickerTopPosition();
        e.preventDefault();
        return false;
    });

$("#training_span").change(function() {
    var times = $(this).val().split(":");;
    var totalSeconds = parseInt(times[0], 10) * 60 + parseInt(times[1], 10);
    Training.process.sec = totalSeconds;
    $("#dintid").addClass("bip_hidden");
});

$("#practicedate").change(function() {
    console.log('practice date changed');
    $(".popupTime span").html($(this).val().replace(' ', '<br/>'));
    var arr = $(this).val().split(" ");
    if (arr.length > 0) {
        $("#TrainingZone_1_2 .datestrong,#popUp3 .datestrong").html(arr[0]);
        $("#TrainingZone_1_2 .timestrong,#popUp3 .timestrong").html(arr[1]);
    }
});

$("#tidigare_date_1_2").change(function() {
    var arr = $(this).val().split(" ");
    console.log(arr);
    if (arr.length > 0) {
        $("#tidigare_review #training_review_datetime").html(arr[0] + ", " + arr[1]);
        $("#tidigare_review #training_review_datetime").attr("data-value", arr);
        $("#tidigare_date_1_2").attr("value", arr[0] + " " + arr[1]);
    }
});

$('#showWeeklyActivityOnGraph').off('click').on('click', Graph.showActivityGraphperweek);

function adjustDateTimePickerTopPosition() {
    console.log('top (1)=' + $(".dw.dwbg.dw-slideup.dw-in.dw-ltr").css("top"));

    var top1 = $(".dw.dwbg.dw-slideup.dw-in.dw-ltr").css("top").replace("px", "") - 22 + 'px';
    console.log('top (2)=' + $(".dw.dwbg.dw-slideup.dw-in.dw-ltr").css("top"));

    $(".dw.dwbg.dw-slideup.dw-in.dw-ltr").css("top", top1);
    console.log('top (3)=' + $(".dw.dwbg.dw-slideup.dw-in.dw-ltr").css("top"));


}

});

$(document).bind("mobileinit", function() {
    $.mobile.defaultPageTransition = "none";


});