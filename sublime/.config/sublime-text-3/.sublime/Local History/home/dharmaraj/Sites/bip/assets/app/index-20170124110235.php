<!DOCTYPE html>
<html>
<head>
    <title>BIP</title>
    <link href="css/jquery.mobile-1.3.2.css" rel="stylesheet" type="text/css">
    <link href="css/core.css" rel="stylesheet" type="text/css">
    <link href="css/iscroll.css" rel="stylesheet" type="text/css">
    <!-- // <script type="text/javascript" src="js/jquery-1.9.1.min.js"></script> -->
    <script src="https://code.jquery.com/jquery-1.12.3.js"></script>
    <script src="https://code.jquery.com/jquery-migrate-1.4.0.js"></script>

    <script type="text/javascript" src="js/jquery.mobile-1.3.2.min.js"></script>
    <script type="text/javascript" src="js/webservice.js"></script>
    <script type="text/javascript" src="js/common.js"></script>
    <script type="text/javascript" src="js/reportgraphs.js"></script>
</head>
<body>
    <script type="text/javascript">
    function getParameterByName( name,href )
    {
      name = name.replace(/[\[]/,"\\\[").replace(/[\]]/,"\\\]");
      var regexS = "[\\?&]"+name+"=([^&#]*)";
      var regex = new RegExp( regexS );
      var results = regex.exec( href );
      if( results == null )
        return "";
      else
        return decodeURIComponent(results[1].replace(/\+/g, " "));
    }
    $(function() {
        var pathname=window.location.href;
        var json=getParameterByName('json',pathname);
        delete Graph;
        Graph.showEstimatesGraphfromstart(json);
    });
    </script>
    <div data-role="page" id="TrainingRating" data-theme="b">
        <div data-role="content" class="innerWrapper scrollwrapper" id="bodyContent7" data-theme="c">
        <div class="scroller">
            <div class="boundbox fleft">
                <h1 class="ui-title-small" data-key="register">
                    Skattningar sedan start</h1>
                <div class="barchart box-size-border">
                    <div class="numtext">
                        <div class="textbar">
                            10
                        </div>
                        <div class="textbar">
                            9
                        </div>
                        <div class="textbar">
                            8
                        </div>
                        <div class="textbar">
                            7
                        </div>
                        <div class="textbar">
                            6
                        </div>
                        <div class="textbar">
                            5
                        </div>
                        <div class="textbar">
                            4
                        </div>
                        <div class="textbar">
                            3
                        </div>
                        <div class="textbar">
                            2
                        </div>
                        <div class="textbar">
                            1
                        </div>
                        <div class="textbar">
                            0
                        </div>
                    </div>
                    <div class="lines" id="divRatingLines">

                    </div>
                </div>
            </div>
            <div class="boundbox fleft">
                <div class="antalveckor fleft" id="EstimateAntalskattningar">
                    <span class="text fleft">Antal
                        <br />
                        skattningar</span> 24
                </div>
                <div class="aktivet fright" style="width: auto;" id="EstimateAntaldagar">
                    <span class="text fleft">Antal<br />
                        dagar</span> 79
                </div>
            </div>
            <div class="boundbox fleft">
                <div class="datestart fleft" id="EstimateStartdatum">
                    Startdatum 2013-09-21</div>
                <div class="enddate fright" id="EstimateDegansdatum">
                    Dagens datum 2013-11-05</div>
            </div>
            <div style="height: 150px; visibility: hidden;float:left;">
                    &nbsp;&nbsp;&nbsp;
                </div>
            </div>
        </div>
    </div>
</body>
</html>
