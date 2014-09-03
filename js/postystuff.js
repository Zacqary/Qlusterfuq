var faders=0;
var editEditor = null;

$(document).ready(function () {
	//Truncate posts
	$('.stream-post-body').each(function(){
		if($(this).height() > 400) {
			var edit = $(this).find('.edit-timestamp').outerHTML();
			$(this).find('.edit-timestamp').remove();
			$(this).truncate();
			$(this).parent().find('.post-meta').before(edit);
		}
	});
	$('.comment-body').truncate({max_length: 480});
	$('a[rel=tooltip]').tooltip();
	$(window).hashchange();
	$(this).scrollTop(0); //For Firefox
});
$(window).hashchange( function(){
	var hash = window.location.hash;
	if(hash == "#control-panel") {
		$('#change-settings').addClass("active");
		$('#change-settings').attr("data-original-title","Close");
		$('#change-settings').tooltip("show");
		$('.settings-title').hide();
		$('.user-control-panel').show();
	}
	else{
		$(hash).addClass('highlight');
		$('.highlight').on("transitionend",function(){
			setTimeout(function(){
				$(hash).addClass('dehighlight');
			},1000);
		});
		$('.highlight').on("oTransitionEnd",function(){
			setTimeout(function(){
				$(hash).addClass('dehighlight');
			},1000);
		});
		$('.highlight').on("webkitTransitionEnd",function(){
			setTimeout(function(){
				$(hash).addClass('dehighlight');
			},1000);
		});
		$('.highlight').on("MSTransitionEnd",function(){
			setTimeout(function(){
				$(hash).addClass('dehighlight');
			},1000);
		});
	}
})
//Comment Edit Button
	$(document).on("click",".c-edit-click",function(){
		var thePost = $(this).parent().attr('id');
		var thisPost = "#"+thePost;
		var postBody = $(thisPost+">.comment-body").html();
		var editclick = $(this);
		$(thisPost).wrap("<form action='' method='post' id='post-edit-form'>");
		$(this).after("<span class='edit-buttons'><button class='save-button btn btn-primary'><i class='icon-ok icon-white'></i> Save</button> <button class='cancel-button btn-warning btn'><i class='icon-white icon-remove'></i> Cancel</button></span>");
		$(this).hide();
		$(thisPost+">.comment-body").html("<img src='img/ajax-loader.gif'>");
		$.ajax({
			type: "POST",
			url: theRoot+"edit.php",
			data: {op: "oc", pid: thePost},
			success: function(data){
				$(thisPost+">.comment-body").html("<textarea rows='5' name='body' id='comment-form-body-"+thePost+"' class='comment-editor'>"+data+"</textarea>"); 
			},
			
		});
		$('.cancel-button').click(function(){
			$(thisPost+">.comment-body").html(postBody);
			$(this).parent().remove();
			$(editclick).show();
			$(thisPost+">br").remove();
			$(thisPost).find('.comment-error').remove();
			$(thisPost).unwrap();
		});
		$('.save-button').click(function(){
			var body = $('#comment-form-body-'+thePost).val();
			if (body == '') {
				$(thisPost).find('.comment-error').remove();
				$(thisPost).find('.post-meta').after("<div class='alert alert-error comment-error'>At least type <em><strong>something!</strong></em></div>");
				return false;
			}
			var dataString = "body=" + encodeURIComponent(body) + "&pid=" + thePost + "&op=upc";
			$(this).parent().after("<img class='loader' src='"+theRoot+"/img/ajax-loader.gif'>");
			$.ajax({
				type: "POST",
				url: theRoot+"edit.php",
				data: dataString,
				success: function(data){
					$(thisPost+">.comment-body").hide().html(data).fadeIn(500);
					$(thisPost).find('.comment-error').remove();
					$('.loader').remove();
					$(thisPost).find('.edit-buttons').remove();
					$(editclick).show();
					$(thisPost+">br").remove();
					$(thisPost).unwrap();
				}
			})
			return false;
		});
	});
	
	//Post Edit Button
	$(document).on("click",".edit-click",function(){
		var thePost = $(this).parent().attr('id');
		var thisPost = "#"+thePost;
		var postBody = $(thisPost+">.post-body").html();
		var thread = $(thisPost+'>.comments').attr('id');
		var img = $(thisPost).find('.image-share').outerHTML();
		if ($(thisPost).find('.event-details').is(':visible')) {
			$(thisPost).find('.event-attendees').hide();
			
			var ename = $(thisPost).find('.event-title').html();
			var edate = $(thisPost).find('.event-date').html();
			var etime = $(thisPost).find('.event-time').html();
			var ehour = etime.substring(0,2);
			var emin = etime.substring(3,5);
			var eampm = etime.substring(5,7);
			var elocation = $(thisPost).find('.event-location').html();
			var emeta = "<p class='event-meta'>Date: <span class='event-date'>"+edate+"</span> · Time: <span class='event-time'>"+etime+"</span>";
			if (elocation != null) emeta += "<br><a href='https://maps.google.com/?q="+elocation+"' class='event-location'>"+elocation+"</a>";
			var metaform = "<span id='metaform'>Date: <input type='text' name='date' id='edit-datepicker' value='"+edate+"'></input>";
			metaform += " · <span class='edit-time'>Time: <input type='text' id='hour' name='hour' class='edit-time-field' maxlength='2' value='"+ehour+"'></input>";
			metaform += " : <input type='text' id='minute' name='minute' class='edit-time-field' maxlength='2' value='"+emin+"'></input>";
			metaform += "<select name='ampm' class='edit-ampm' id='ampm' value='"+eampm+"'><option>pm</option><option>am</option></select></span>";
			if (elocation != null) metaform += "<br><input type='text' name='location' id='edit-location' value='"+elocation+"'></input>";
			else metaform += "<br><input type='text' name='location' id='edit-location' value=''></input>";
			metaform += "</span>";
			$(thisPost).find('.event-meta').replaceWith(metaform);
			$(thisPost).find('.event-title').replaceWith("<input type='text' name='name' id='edit-event-title' value='"+ename+"'></input><br>");
			$(thisPost).find('#edit-event-title').val(ename);
			$(thisPost).find('#location').val(elocation);
			$(thisPost).find('#edit-datepicker').datepicker({
				showAnim: 'slide',
				showOtherMonths: true,
				selectOtherMonths: true
			});
			$(thisPost).find('#hour').blur(function(){
				var val = $(this).val();
				if (val == '') $(this).val('12');
				if (val.length == 1) $(this).val('0'+val);
			});
			$(thisPost).find('#minute').blur(function(){
				var val = $(this).val();
				if (val == '') $(this).val('00');
				if (val.length == 1) $(this).val('0'+val);
			});
		}
		$(thisPost+">.trunc").remove();
		$(thisPost+">.post-body").show();
		$(thisPost).wrap("<form action='' method='post' id='post-edit-form'>");
		$(thisPost+">.edit-click").after("<span class='edit-buttons'><button class='save-button btn btn-primary'><i class='icon-ok icon-white'></i> Save</button> <button class='cancel-button btn btn-warning'><i class='icon-remove icon-white'></i> Cancel</button></span>");
		$(thisPost+">.edit-click").hide();
		$(thisPost+">.post-body").html("<img class='loader' src='"+theRoot+"/img/ajax-loader.gif'>");
		$(thisPost+">.post-body").prepend(img);
		$('#'+thread+'>.comment-decollapse').hide();
		$('#'+thread+'>.collapse-wrap').show();
		$.ajax({
			type: "POST",
			url: theRoot+"edit.php",
			data: {op: "o", pid: thePost},
			success: function(data){
				$(thisPost+">.post-body").html("<div id='post-ed-"+thePost+"' class='body-editor'></div><textarea cols='80' rows='5' id='post-form-body-"+thePost+"' name='body'>"+data+"</textarea>");
				$(thisPost+">.post-body").prepend(img);
				$(thisPost+">#post-ed-"+thePost).hide();
				element = document.getElementById('post-ed-'+thePost);
				editEditor = new EpicEditor(element).options({
					file:{
					    name:'PostFormEd'+thePost,
					    defaultContent:data
					  }
				}).load();
				$(thisPost+">.post-body").find("#post-form-body-"+thePost).hide();
				editEditor.on('save',function(){
					var text = editEditor.get('editor').value;
					$('#post-form-body-'+thePost).val(text);
				}); 
				editEditor.save();
				$(thisPost+">#post-ed-"+thePost).show();
				editEditor.focus();
			},
		});
		$('.cancel-button').click(function(){
			$(thisPost).find('.event-attendees').show();
			$(thisPost).find('#edit-event-title').replaceWith("<h3 class='event-title'>"+ename+"</h3>");
			$(thisPost).find('.event-details>br').remove();
			$(thisPost).find('#metaform').replaceWith(emeta);
			$(thisPost+">#post-ed-"+thePost).remove();
			$(thisPost+">.post-body").html(postBody);
			$(thisPost+">.edit-buttons").remove();
			$(thisPost+">.edit-click").show();
			$(thisPost+">br").remove();
			editEditor.remove('PostFormEd'+thePost);
			$(thisPost).unwrap();
		});
		$('.save-button').click(function(){
			editEditor.remove('PostFormEd'+thePost);
			var body = $('#post-form-body-'+thePost).val();
			var name = $(thisPost).find('#edit-event-title').val();
			var date = $(thisPost).find('#edit-datepicker').val();
			var hour = $(thisPost).find('#hour').val();
			var minute = $(thisPost).find('#minute').val();
			var ampm = $(thisPost).find('#ampm').val();
			var location = $(thisPost).find('#edit-location').val();
			var meta = "<p class='event-meta'>Date: <span class='event-date'>"+date+"</span> · Time: <span class='event-time'>"+hour+":"+minute+ampm+"</span>";
			if (location != '') meta += "<br><a href='https://maps.google.com/?q="+location+"' class='event-location'>"+location+"</a>";
			var dataString = "pid=" + thePost + "&op=up&body=" + encodeURIComponent(body) + "&name=" + encodeURIComponent(name) + "&date=" + encodeURIComponent(date) + "&hour=" + hour + "&minute=" + minute + "&ampm=" + ampm + "&location=" + encodeURIComponent(location);
			$(this).parent().after("<img class='loader' src='"+theRoot+"/img/ajax-loader.gif'>");
			$.ajax({
				type: "POST",
				url: theRoot+"edit.php",
				data: dataString,
				success: function(data){
					$(thisPost).find('.event-attendees').show();
					$(thisPost+">#post-ed-"+thePost).remove();
					$(thisPost+">.post-body").hide().html(data).fadeIn(500);
					$(thisPost).find('#edit-event-title').replaceWith("<h3 class='event-title'>"+name+"</h3>");
					$(thisPost).find('.event-details>br').remove();
					$(thisPost).find('#metaform').replaceWith(meta);
					$(thisPost+">.edit-buttons").remove();
					$(thisPost+">.edit-click").show();
					$(thisPost+">br").remove();
					$('.loader').remove();
					editEditor.remove('PostFormEd'+thePost);
					$(thisPost).unwrap();
				}
			});
			return false;
		});
	});

	//Post Delete Button
	$(document).on('click','.delete-click',function(){
			var thePost = $(this).parent().attr('id');
			var thisPost = "#"+thePost;
			$(this).after("<span class='delete-confirm' id='"+thisPost+"'>Are you sure? <a class='click' title='Yes' rel='tooltip' id='yes"+thePost+"'><i class='icon-ok'></i></a> / <a class='click' title='No' rel='tooltip' id='no"+thePost+"'><i class='icon-remove'></i></a></span>");
			$('.delete-confirm>a').tooltip();
			$(this).hide();
			var me = $(this);
			$('#no'+thePost).click(function(){
				$('.delete-confirm>a').tooltip('hide');
					$(this).parent().remove();
					$(me).show();
			});
			$('#yes'+thePost).click(function(){
					$('.delete-confirm>a').tooltip('hide');
					if ($(thisPost).find('.event-details').is(':visible')) {
						var evname = $(thisPost).find('.event-title').html();
					}
					$(this).parent().before("<img src='"+theRoot+"/img/ajax-loader.gif'>");
					$.ajax({
						type: "POST",
						url: theRoot+"edit.php",
						data: {op: "d", pid: thePost, ename: evname},
						success: function(data){
							$(".row-"+thePost).fadeOut(500);
						}
					});
					return false;
			});
	});

	//Comment Delete Button
	$(document).on('click','.c-delete-click',function(){
			var thePost = $(this).parent().attr('id');
			var thisPost = "#"+thePost;
			$(this).after("<span class='delete-confirm' id='"+thisPost+"'>Are you sure? <a class='click' title='Yes' rel='tooltip'  id='yes"+thePost+"'><i class='icon-ok'></i></a> / <a class='click' title='No' rel='tooltip' id='no"+thePost+"'><i class='icon-remove'></i></a></span>");
			$('.delete-confirm>a').tooltip();
			$(this).hide();
			var me = $(this);
			$('#no'+thePost).click(function(){
				$('.delete-confirm>a').tooltip('hide');
					$(this).parent().remove();
					$(me).show();
			});
			$('#yes'+thePost).click(function(){
					$('.delete-confirm>a').tooltip('hide');
					$(this).parent().before("<img src='"+theRoot+"/img/ajax-loader.gif'>");
					$.ajax({
						type: "POST",
						url: theRoot+"edit.php",
						data: {op: "d", pid: thePost},
						success: function(data){
							$(".row-"+thePost).fadeOut(500);
						}
					});
					return false;
			});
	});

	//Follow/Unfollow
	$(document).on('click','.follow-click',function(){
		var pid = $(this).attr('data-pid');
		var uid = $(this).attr('data-uid');
		var op;
		if ($(this).attr('data-follow') == "follow"){
			$(this).html("<i class='icon-star'></i> Unfollow post");
			$(this).attr('data-follow','unfollow');
			$(this).attr('data-original-title','Stop getting notified about comments on this post');
			op = "f";
		}
		else if ($(this).attr('data-follow') == "unfollow"){
			$(this).html("<i class='icon-star-empty'></i> Follow post");
			$(this).attr('data-follow','follow');
			$(this).attr('data-original-title','Get notified about comments on this post');
			op = "u";
		}
		var dataString = "op=" + op + "&uid=" + uid + "&pid=" + pid;
		$.ajax({
			type: "POST",
			url: theRoot+"follow.php",
			data: dataString,
			success: function(data){
				return false;
			}
		});
		return false;
	});
	
	//RSVP
	$(document).on('click','.attend-click',function(){
		var pid = $(this).attr('data-pid');
		var uid = $(this).attr('data-uid');
		var op;
		if ($(this).hasClass("disabled")) return false;
		if ($(this).attr('data-attend') == "attend"){
			$("#rsvp-button-"+pid).html("I'm attending");
			$("#attend-"+pid).addClass('disabled');
			$("#ride-"+pid).removeClass('disabled');
			$("#unattend-"+pid).removeClass('disabled');
			op = "a";
		}
		else if ($(this).attr('data-attend') == "ride"){
			$("#rsvp-button-"+pid).html("I need a ride");
			$("#attend-"+pid).removeClass('disabled');
			$("#ride-"+pid).addClass('disabled');
			$("#unattend-"+pid).removeClass('disabled');
			op = "r";
		}
		else if ($(this).attr('data-attend') == "unattend"){
			$("#rsvp-button-"+pid).html("I'm not attending");
			$("#attend-"+pid).removeClass('disabled');
			$("#ride-"+pid).removeClass('disabled');
			$("#unattend-"+pid).addClass('disabled');
			op = "u";
		}
		var dataString = "op=" + op + "&uid=" + uid + "&pid=" + pid;
		$.ajax({
			type: "POST",
			url: theRoot+"rsvp.php",
			data: dataString,
			success: function(data){
				$("#attendee-list-"+pid).fadeOut(400, function() {
					$(this).html(data).fadeIn(400)
				});
				return false;
			}
		});
		return false;
	});
	
	//Load more posts
	$(document).on('click','#show-more-posts',function(){
		$(this).text("Loading...");
		faders++;
		var fader = "<div class='fader' id='fader"+faders+"'></div>";
		$('.stream').append(fader);
		var pc = $(this).attr("data-postcount");
		var scroll = $(document).scrollTop();
		$.ajax({
			type: "POST",
			url: theRoot+"stream.php",
			data: {postcount: pc},
			success: function(data){
				$('#fader'+faders).hide().html(data);
				$('#show-more-posts').remove();
				$('#fader'+faders).fadeIn(1000);
				$('#fader'+faders).find('.post-body').each(function(){
					if($(this).height() > 400) {
						var edit = $(this).find('.edit-timestamp').outerHTML();
						$(this).find('.edit-timestamp').remove();
						$(this).truncate();
						$(this).parent().find('.post-meta').before(edit);
					}
				});
				scroll = $('#fader'+faders).offset().top;
				$('html,body').animate({scrollTop: scroll},1000);
			}
		})
	});