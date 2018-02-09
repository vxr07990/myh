<html>
	<head>
		<title>Video Survey</title>
        <link href="libraries/bootstrap/css/bootstrap.min.css" rel="stylesheet" />
        <script src="libraries/jquery/jquery.min.js"></script>
        <script src="resources/Connector.js"></script>
		<script src="https://static.opentok.com/v2/js/opentok.min.js" id="TokBoxScript"></script>
		<script>
			var apiKey = '{$APIKEY}';
			var sessionId = '{$SESSION}';
			var token = '{$TOKEN}';
			var domain = '{$DOMAIN}';
			var record = '{$RECORD}';
			var archiveId = '{$ARCHIVEID}';
{literal}
			var session = {};
			var publisher = null;
			var subscriber = {};
			var numStreams = 0;
			var keepAlive = {};

			function initializeSession() {
				if(OT.checkSystemRequirements() == 1) {
					session = OT.initSession(apiKey, sessionId);

					session.on('streamCreated', function(event) {
						//if(event.stream.streamId != publisher.stream.streamId) {
						console.log("New stream in the session: " + event.stream.streamId);
						numStreams++;
						var options = {width:'95%', height:'85%'};
						subscriber = session.subscribe(event.stream, 'TokBoxVideoFeed', options);
                        var url = "index.php?module=Cubesheets&action=ToggleArchive&record="+record+"&numStreams="+numStreams+"&archiveId="+archiveId;
                        if(numStreams == 2) {
                            AppConnector.request(url).then(function (data) {
                                if (data.success) {
                                    archiveId = data.result;
                                }
                            });
                        }
						//}
					});

					session.on('streamDestroyed', function(event) {
//						event.preventDefault();
//						var videoBox = jQuery('#TokBoxVideoFeed');
						numStreams--;
//						videoBox.empty();
//						videoBox.removeClass();
//						videoBox.html("<span>Stream has disconnected. Please wait for stream to become available.</span>");
						setTimeout(function() {
                            var url = "index.php?module=Cubesheets&action=ToggleArchive&record=" + record + "&numStreams=" + numStreams + "&archiveId=" + archiveId;
                            AppConnector.request(url).then(function (data) {
                                if (data.success) {
                                    archiveId = data.result;
                                }
                            });
						}, 10000);
					});

					setTimeout(function() {connectToSession();}, 1500);
				}
			}

			function connectToSession() {
				if(OT.checkSystemRequirements() == 1) {
					session.connect(token, function(error) {
						if(error) {
							console.log("Error connecting: ", error.code, error.message);
						} else {
							console.log("Connected to the session.");
							document.getElementById('TokBoxVideoFeed').innerHTML = "<span>Successfully connected to session. Please wait for stream to become available.</span>";

							var pubOptions = {videoSource:null, width:'95%', height:'10%'};

							publisher = OT.initPublisher('TokBoxAudioFeed', pubOptions, function(err) {
								if(err) {
									console.log(err);
								} else {
									console.log("Publisher initialized");
								}
							});

							session.publish(publisher, function(err) {
								if(err) {
									console.log(err);
								} else {
									console.log('Publishing a stream.')
									numStreams++;
                                    var url = "index.php?module=Cubesheets&action=ToggleArchive&record="+record+"&numStreams="+numStreams+"&archiveId="+archiveId;
                                    if(numStreams == 2) {
                                        AppConnector.request(url).then(function (data) {
                                            if (data.success) {
                                                archiveId = data.result;
                                            }
                                        });
                                    }

							        console.log(session.getSubscribersForStream(publisher.stream));
								}
							});
							keepAlive = setTimeout(function(){AppConnector.request('index.php?module=Cubesheets&action=KeepAlive');}, 600000);
						}
					});
				}
			}

			function getQueryVariable(variable) {
				var query = window.location.search.substring(1);
				var vars = query.split("&");
				for (var i=0; i<vars.length; i++) {
					var pair = vars[i].split("=");
					if(pair[0] == variable) {return pair[1];}
				}
				return(false);
			}

            function registerCaptureImageFromStream() {
                jQuery('#videoFeedImageCapture').off('click').on('click', function() {
                    if(typeof subscriber.getImgData == 'function') {
                        var base64EncodedImage = encodeURIComponent(subscriber.getImgData());

                        AppConnector.request('index.php?module=Cubesheets&action=SaveImageAndGenerateThumbnail&record='+record+'&imageData='+base64EncodedImage).then(
                            function() {
                                alert("Screenshot saved successfully");
                            }
                        );
                    }
                });
            }

			window.onload = function() {initializeSession(); registerCaptureImageFromStream();};
			document.domain = domain;
			window.onunload = function(e) {
			    session.unpublish(publisher);
                window.opener.reattachPopout(archiveId);
                window.opener.bindPopoutIcon();
                window.opener.connectToSession();
            }
		</script>
	</head>
	<body>
    <span style="width:100%;height:25px;display:block"><a href="#" id="videoFeedImageCapture" title="Click to capture a still image of the video feed"><i title="Capture Image" class="icon-picture alignMiddle" style="position:absolute;left:10px;top:5pxcolor:black;text-decoration:none;"></i><span style="position:absolute;left:25px;top:5px">Capture Image</span></a></span>
        <div id='TokBoxVideoFeed' style='width:95%;height:300px;padding:5px;padding-left:0;margin:0 auto;'><span>Initializing session. Please wait.</span></div>
		<div id='TokBoxAudioFeed' style='width:90%;height:15%;padding:5px;padding-left:0;margin:0 auto;'></div>
	</body>
</html>
{/literal}
