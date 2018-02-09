<html>
	<head>
		<title>Video Survey</title>
		<script src="https://static.opentok.com/v2/js/opentok.min.js" id="TokBoxScript"></script>
		<script>
			var element = <?php echo urldecode($_GET['element']); ?>;
			var sessionId = element.session;
			var token = element.token;
			var session = {};
			var publisher = {};
			var numStreams = 0;
			
			function initializeSession() {
				if(OT.checkSystemRequirements() == 1) {
					session = OT.initSession('45261902', sessionId);
					
					session.on('streamCreated', function(event) {
						//if(event.stream.streamId != publisher.stream.streamId) {
						console.log("New stream in the session: " + event.stream.streamId);
						numStreams++;
						var options = {width:'100%', height:'85%'};
						var subscriber = session.subscribe(event.stream, 'TokBoxVideoFeed', options);
						var url = "index.php?module=Cubesheets&action=ToggleArchive&record="+getQueryVariable('record')+"&numStreams="+numStreams;
						AppConnector.request(url);
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
							if(numStreams < 2) {
								var url = "index.php?module=Cubesheets&action=ToggleArchive&record=" + getQueryVariable('record') + "&numStreams=" + numStreams;
								AppConnector.request(url);
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
						} else {
							console.log("Connected to the session.");
							document.getElementById('TokBoxVideoFeed').innerHTML = "<span>Successfully connected to session. Please wait for stream to become available.</span>";
							
							var pubOptions = {width:'100%', height:'10%'};
					
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
									var url = "index.php?module=Cubesheets&action=ToggleArchive&record="+getQueryVariable('record')+"&numStreams="+numStreams;
									AppConnector.request(url);
							
							console.dir(session.getSubscribersForStream(publisher.stream));
								}
							});
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
			setTimeout(function() {initializeSession();}, 1500);
		</script>
	</head>
	<body>
		<div id='TokBoxVideoFeed' style='width:90%;height:85%;padding:5px;padding-left:0;margin:0 auto;'><span>Initializing session. Please wait.</span></div>
		<div id='TokBoxAudioFeed' style='width:90%;height:15%;padding:5px;padding-left:0;margin:0 auto;'></div>
	</body>
</html>