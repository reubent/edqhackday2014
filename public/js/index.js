/*
 Document   : planCreative.js
 Author     : Fred ..
 Description:
 class handling the operation on creative section of plan
 we try to register all the event here for both pages (plan, plan-separate)
 in the init sections as it doesnt involved if this or that
 Dependancies introcuiced facebookFeed.js, creativePreview.js
 */

$(document).ready(function() {
	var indexPage = new Index(jQuery);
	indexPage.init();
});

function Index($) {
	var DEBUG = false;

	var index = {

		init: function() {

			index.renderGraph();
			this.refreshLoop();

			this.registerListeners();

		},

		renderGraph: function() {
console.log('render graph');
			var label = [],
				labelData1 = [],
				labelData2 = [],
				labelData3 = [],
				data = ["January","February","March","April","May","June","July"];

			for (var i=0; i < data.length; i++) {
				console.log(data[i]);
				label.push(data[i]);
				labelData1.push(parseInt(Math.random()*200));
				labelData2.push(parseInt(Math.random()*200));
				labelData3.push(parseInt(Math.random()*200));
			}

			var lineChartData = {
//				labels : ["January","February","March","April","May","June","July"],
				labels : label,
				datasets : [
					{
						fillColor : "transparent",
						strokeColor : "#FF0000",
						pointColor : "rgba(220,220,220,1)",
						pointStrokeColor : "#FF0000",
						data : labelData1
					},
					{
						fillColor : "transparent",
						strokeColor : "#6699FF",
						pointColor : "rgba(151,187,205,1)",
						pointStrokeColor : "#6699FF",
						data : labelData2
					},
					{
						fillColor : "transparent",
						strokeColor : "#33CC33",
						pointColor : "rgba(220,220,220,1)",
						pointStrokeColor : "#33CC33	",
						data : labelData3
					},
				]
			};

			var myLine = new Chart($("canvas")[0].getContext("2d")).Line(lineChartData);
		},

		registerListeners: function() {

		},

		sendRequest: function() {
			console.log('request');
			$.ajax({
				url: '/feed.php',
				type: 'get',
//				data:  {"cid": 2},
				dataType: 'json',
				success: function(response) {
					index.populateCell(2);
					index.renderGraph();
				},
				error: function(response) {
					console.log('failed');
				},
				complete: function() {

				}
			});
		},

		populateCell: function(index) {
			var json = {"created_at":"Wed May 07 09:00:07 +0000 2014","id":463966497325719552,"id_str":"463966497325719552","text":"The #AlJazeeraMag meets #Rwandans who say they have fled political or personal persecution in their home country http:\\/\\/t.co\\/c6jm6mHCA9","source":"\\u003ca href=\\\"http:\\/\\/www.socialflow.com\\\" rel=\\\"nofollow\\\"\\u003eSocialFlow\\u003c\\/a\\u003e","truncated":false,"in_reply_to_status_id":null,"in_reply_to_status_id_str":null,"in_reply_to_user_id":null,"in_reply_to_user_id_str":null,"in_reply_to_screen_name":null,"user":{"id":4970411,"id_str":"4970411","name":"Al Jazeera English","screen_name":"AJEnglish","location":"Doha, Qatar","url":"http:\\/\\/aljazeera.com","description":"Al Jazeera English, the 24-hour English-language news and current affairs channel, headquartered in Doha, Qatar. Follow @AJELive for breaking news alerts.","protected":false,"followers_count":1941397,"friends_count":141,"listed_count":43163,"created_at":"Tue Apr 17 08:23:08 +0000 2007","favourites_count":22,"utc_offset":3600,"time_zone":"London","geo_enabled":true,"verified":true,"statuses_count":121084,"lang":"en","contributors_enabled":false,"is_translator":false,"is_translation_enabled":true,"profile_background_color":"FFFFFF","profile_background_image_url":"http:\\/\\/pbs.twimg.com\\/profile_background_images\\/435161658852257792\\/GQQnmlD_.jpeg","profile_background_image_url_https":"https:\\/\\/pbs.twimg.com\\/profile_background_images\\/435161658852257792\\/GQQnmlD_.jpeg","profile_background_tile":true,"profile_image_url":"http:\\/\\/pbs.twimg.com\\/profile_images\\/448478192022474752\\/yOHtpEoL_normal.jpeg","profile_image_url_https":"https:\\/\\/pbs.twimg.com\\/profile_images\\/448478192022474752\\/yOHtpEoL_normal.jpeg","profile_banner_url":"https:\\/\\/pbs.twimg.com\\/profile_banners\\/4970411\\/1381187663","profile_link_color":"0000FF","profile_sidebar_border_color":"FFFFFF","profile_sidebar_fill_color":"DDDDDD","profile_text_color":"000000","profile_use_background_image":false,"default_profile":false,"default_profile_image":false,"following":null,"follow_request_sent":null,"notifications":null},"geo":null,"coordinates":null,"place":null,"contributors":null,"retweet_count":0,"favorite_count":0,"entities":{"hashtags":[{"text":"AlJazeeraMag","indices":[4,17]},{"text":"Rwandans","indices":[24,33]}],"symbols":[],"urls":[{"url":"http:\\/\\/t.co\\/c6jm6mHCA9","expanded_url":"http:\\/\\/aje.me\\/1g5xIKh","display_url":"aje.me\\/1g5xIKh","indices":[113,135]}],"user_mentions":[]},"favorited":false,"retweeted":false,"possibly_sensitive":true,"filter_level":"medium","lang":"en"};

			var twitHtml = this.twitHtml(json);
			$('#data-row .twit-row').eq(index).prepend($(twitHtml));
		},

		refreshLoop: function() {
			this.sendRequest();
			console.log('hello');
			setTimeout(function() {
				index.refreshLoop();
			}, 2000);
		},

		twitHtml: function(data) {

			 var html = '<div class="blockquote-box pbm">' +
				 '<div class="square pull-left mrm">' +
				 ' <img src="' + data.user.profile_image_url + '" alt="" class="">' +
				 '</div>' +
				 '<div class="unit twit-ctnt">' +
				 '	 <h4>' + data.user.name + '</h4>' +
				 '	 <p>' +
					data.text +
				 '	 </p>' +
				 '</div>' +
				 '</div>';

			return html;
		}
	};

	return index;
}
