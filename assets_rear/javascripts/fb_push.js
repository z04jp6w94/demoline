		/*FB發布粉絲團文章*/
		var appGetPermission = false;

      window.fbAsyncInit = function() {
        FB.init({
          appId      : '131476200882600', // App ID
          channelUrl : '//hasse.chiliman.com.tw/channel.html', // Channel File
          status     : true, // check login status
          cookie     : true, // enable cookies to allow the server to access the session
          xfbml      : true,  // parse XFBML
		 // oauth : true,
          version    : 'v2.11'
        });

        /*FB.getLoginStatus(function(response)
        {
            if(response.status=="not_authorized") {
                appGetPermission = false;
            } else if(response.status=="connected") {
                appGetPermission = true;
                $("#step1").html("狀態：已授權");
            }
        });*/
      };	
	  
      (function(d){
         var js, id = 'facebook-jssdk', ref = d.getElementsByTagName('script')[0];
         if (d.getElementById(id)) {return;}
         js = d.createElement('script'); js.id = id; js.async = true;
         js.src = "//connect.facebook.net/en_US/all.js";
         ref.parentNode.insertBefore(js, ref);
       }(document));
	   
      /*var fbLogin = function()
      {
		 // alert(appGetPermission);
        if(!appGetPermission)
        {
            FB.login(function(response)
              {
              if(response.status=="not_authorized") {
                  appGetPermission = false;
              } else if(response.status=="connected") {
                  appGetPermission = true;
				  alert('授權成功');
                  $("#step1").html("狀態：已授權");
              }
            },{"scope":"email,publish_actions,user_birthday"})
        }
      }*/
	  
      function fbPost(num, fbnm, fbtitle, fbcontent, url, fbIMG, fbtoken){
		console.log('num='+num);
		console.log('fbnm='+fbnm);
		console.log('fbtitle='+fbtitle);
		console.log('fbcontent='+fbcontent);
		console.log('url='+url);
		console.log('fbIMG='+fbIMG);
		console.log('fbtoken='+fbtoken);
        var fbMessage = fbtitle+'\n'+' \n'+fbcontent;
		//因link與object_attachment無法共存，以圖片為優先展示如無上傳圖片才以line展示
		if(fbIMG!=''){
			FB.api("/2033377083618765/photos","post",{no_story: true,url: 'https://social-crm.chiliman.com.tw'+fbIMG,access_token:fbtoken} ,function(res) {
			  if(!res || res.error){
				alert('上傳失敗');
			  } else{
				FB.api("/2033377083618765/feed","post",{message: fbMessage+'\n'+url, object_attachment:res.id,access_token:fbtoken} ,function(response) {
				  if(!response || response.error){
					alert('發布失敗');
				  }else{
					$.post(
						"FBPush_Push.php",
						{FBID:response.id,p_id:num},
						function(xml){
							if($('resu', xml).text() == '1'){	
								alert('發布成功');
								location.reload();
							}else{
								alert($('msg', xml).text());
							}
					});
				  }
				});
			  }
			});
		}else{
			FB.api("/2033377083618765/feed","post",{message: fbMessage,link: url,access_token:fbtoken} ,function(response) {
			  if(!response || response.error){
				alert('發布失敗2');
			  } else{
				$.post(
					"FBPush_Push.php",
					{FBID:response.id,p_id:num},
					function(xml){
						if($('resu', xml).text() == '1'){	
							alert('發布成功');
							location.reload();
						}else{
							alert($('msg', xml).text());
						}
				});
			  }
			});
		}
		
        return false;

      }

