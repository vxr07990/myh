
<script src="https://static.opentok.com/v2/js/opentok.min.js" id="TokBoxScript"></script>
<script type='text/javascript'>
	var sessionId = '{$TOKBOX_SESSIONID}';
	var token = '{$TOKBOX_SERVERTOKEN}';
	var apiKey = '{getenv('TOKBOX_API_KEY')}';
	{literal}
    var session = {};
    var publisher = {};
    var subscriber = {};
    var numStreams = 0;
    var archiveId = '';
    var keepAlive = {};
	function initializeSession() {
		if(OT.checkSystemRequirements() == 1) {
			session = OT.initSession(apiKey, sessionId);

			session.on('streamCreated', function(event) {
				//if(event.stream.streamId != publisher.stream.streamId) {
				console.log("New stream in the session: " + event.stream.streamId);
				numStreams++;
				var options = {width:'100%', height:500};
				subscriber = session.subscribe(event.stream, 'TokBoxVideoFeed', options);
				var url = "index.php?module=Cubesheets&action=ToggleArchive&record="+getQueryVariable('record')+"&numStreams="+numStreams+"&archiveId="+archiveId;
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
				event.preventDefault();
				numStreams--;
				var videoBox = jQuery('#TokBoxVideoFeed');
				videoBox.empty();
				videoBox.removeClass();
				videoBox.html("<span>Stream has disconnected. Please wait for stream to become available.</span>");
				setTimeout(function() {
                    var url = "index.php?module=Cubesheets&action=ToggleArchive&record=" + getQueryVariable('record') + "&numStreams=" + numStreams + "&archiveId=" + archiveId;
                    if(event.stream.streamId != publisher.stream.streamId) {
                        AppConnector.request(url).then(function (data) {
                            if (data.success) {
                                archiveId = data.result;
                            }
                        });
                    }
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
					if(error.code == 1004) {
						var errorString = 'Connection failed: ';
						if(error.message.indexOf('Token expired') > -1) {
							errorString += 'Connection token has expired.';
						}
						alert(errorString);
					}
				} else {
					console.log("Connected to the session.");
					jQuery('#TokBoxVideoFeed').html("<span>Successfully connected to session. Please wait for stream to become available.</span>");

					var pubOptions = {insertMode: 'after', width:'100%', height:300};

					publisher = OT.initPublisher('TokBoxVideoFeed', pubOptions, function(err) {
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
							var url = "index.php?module=Cubesheets&action=ToggleArchive&record="+getQueryVariable('record')+"&numStreams="+numStreams+"&archiveId="+archiveId;
							if(numStreams == 2) {
                                AppConnector.request(url).then(function (data) {
                                    if (data.success) {
                                        archiveId = data.result;
                                    }
                                });
                            }

							console.dir(session.getSubscribersForStream(publisher.stream));
						}
					});

					keepAlive = setTimeout(function(){AppConnector.request('index.php?module=Cubesheets&action=KeepAlive');}, 600000);
				}
			});
		}
	}

	function bindPopoutIcon() {
		var popout = jQuery('#videoFeedPopout');

		var windowFeatures = "menubar=no,location=no,resizable=yes,scrollbars=no,status=yes,width=400,height=600,left=50,top=50";

		popout.on('click', function() {
			var pathArr = window.location.pathname.split('/');
			var path = (pathArr[1]=='' || pathArr[1]=='index.php') ? '/' : '/'+pathArr[1]+'/';
			var domain = jQuery('#site_domain').val();
			var record = getQueryVariable('record');
			var element = encodeURIComponent(JSON.stringify({apiKey:apiKey,session:sessionId,token:token,domain:domain,record:record,archiveId:archiveId}));
			var url = window.location.protocol+'//'+window.location.hostname+path+'index.php?module=Cubesheets&view=VideoSurveyPopout&element='+element;

			console.dir(url);

			var newWindow = window.open(url, "_blank", windowFeatures);
			/*console.dir("Protocol: "+window.location.protocol);
			 console.dir("Domain: "+window.location.hostname);
			 console.dir("Path: "+window.location.pathname);
			 var currentLocation = window.location.href;
			 var splitLocation = currentLocation.split("/");*/
			jQuery('#Cubesheets_sideBar_LBL_CUBESHEETS_TOKBOX').html("<span>Video Survey widget has been popped out into a new window</span>");
			session.unpublish(publisher);
			session.disconnect();
			clearTimeout(keepAlive);
			numStreams = 0;

//			newWindow.onbeforeunload = function() {
//				jQuery('#Cubesheets_sideBar_LBL_CUBESHEETS_TOKBOX').html("<span style='width:100%;height:25px;display:block'><a href='#' id='videoFeedPopout' title='Click to popout widget to new window. Closing the new window will restore widget.'><img src='layouts/vlayout/skins/images/Popout.png' alt='Popout to new window' style='position:absolute;right:10px' /></a></span><div id='TokBoxVideoFeed' style='width:100%;height:300px;padding:5px;'><span>Initializing session. Please wait.</span></div>");
//				bindPopoutIcon();
//				connectToSession();
//			}
		});
	}

	function reattachPopout(archive) {
	    archiveId = archive;
        jQuery('#Cubesheets_sideBar_LBL_CUBESHEETS_TOKBOX').html('<span style="width:100%;height:25px;display:block"><a href="#" id="videoFeedImageCapture" title="Click to capture a still image of the video feed"><i title="Capture Image" class="icon-picture alignMiddle" style="position:absolute;left:10px;top:5px;color:black;text-decoration:none;"></i><span style="position:absolute;left:25px;top:5px">Capture Image</span></a><a href="#" id="videoFeedPopout" title="Click to popout widget to new window. Closing the new window will restore widget."><img src="layouts/vlayout/skins/images/Popout.png" alt="Popout to new window" style="position:absolute;right:10px" /></a></span> <div id="TokBoxVideoFeed" style="width:95%;height:300px;padding:5px;"><span>Initializing session. Please wait.</span></div>');
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

                AppConnector.request('index.php?module=Cubesheets&action=SaveImageAndGenerateThumbnail&record='+getQueryVariable('record')+'&imageData='+base64EncodedImage).then(
                    function() {
                        bootbox.alert("Screenshot saved successfully");
                    }
				);
            }
        });
    }

	setTimeout(function() {bindPopoutIcon(); initializeSession(); registerCaptureImageFromStream();}, 10000);
	var oldHandler = window.onbeforeunload;
	window.onbeforeunload = function(e) {
	    if(oldHandler){ oldHandler(e); }
		session.disconnect();
        numStreams--;
        var videoBox = jQuery('#TokBoxVideoFeed');
        videoBox.empty();
        videoBox.removeClass();
        videoBox.html("<span>Stream has disconnected. Please wait for stream to become available.</span>");
        var url = "index.php?module=Cubesheets&action=ToggleArchive&record=" + getQueryVariable('record') + "&numStreams=" + numStreams + "&archiveId=" + archiveId;
        AppConnector.request(url).then(function (data) {
            if (data.success) {
                archiveId = data.result;
            }
        });
	}
</script>
{/literal}
<span style="width:100%;height:25px;display:block"><a href="#" id="videoFeedImageCapture" title="Click to capture a still image of the video feed"><i title="Capture Image" class="icon-picture alignMiddle" style="position:absolute;left:10px;top:5pxcolor:black;text-decoration:none;"></i><span style="position:absolute;left:25px;top:5px">Capture Image</span></a><a href='#' id="videoFeedPopout" title="Click to popout widget to new window. Closing the new window will restore widget."><img src="layouts/vlayout/skins/images/Popout.png" alt="Popout to new window" style="position:absolute;right:10px" /></a></span>
<div id='TokBoxVideoFeed' style='width:95%;height:300px;margin-bottom:5px'><span>Initializing session. Please wait.</span></div>
