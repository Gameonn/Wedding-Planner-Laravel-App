<html> 
  <head></head> 
  <body> 
    <div id='myPublisherDiv'></div> 
    <div id='subscribersDiv'></div> 
    
    <script src='//static.opentok.com/v2/js/opentok.min.js'></script> 
    <script> 
      var apiKey = '45502342';
      var sessionId = '1_MX40NTUwMjM0Mn5-MTQ1NjEzMjYzNjA2N35BSkJiTFhqNmNoMkpmRElIQ25CdnZqTER-UH4'; 
      var token = 'T1==cGFydG5lcl9pZD00NTUwMjM0MiZzaWc9OTZhNjhlNWMyZDBjMDRjYjgxNWRmZTE1NzU2OWM1M2Q0MmZiODRjYzpyb2xlPXB1Ymxpc2hlciZzZXNzaW9uX2lkPTFfTVg0ME5UVXdNak0wTW41LU1UUTFOakV6TWpZek5qQTJOMzVCU2tKaVRGaHFObU5vTWtwbVJFbElRMjVDZG5acVRFUi1VSDQmY3JlYXRlX3RpbWU9MTQ1NjEzMjYzOSZub25jZT0wLjY5ODM4MTU2MTAxNTIwNzQmZXhwaXJlX3RpbWU9MTQ1NjEzNjE4NyZjb25uZWN0aW9uX2RhdGE9';
      var session = OT.initSession(apiKey, sessionId); 
      session.on({ 
          streamCreated: function(event) { 
            session.subscribe(event.stream, 'subscribersDiv', {insertMode: 'append'}); 
          } 
      }); 
      session.connect(token, function(error) {
        if (error) {
          console.log(error.message);
        } else {
          session.publish('myPublisherDiv', {width: 320, height: 240}); 
        }
      });
    </script> 
  </body> 
</html>      