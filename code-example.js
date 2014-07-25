/**
 *
 */
var apiHostName = "http://admin.mangomolo.com/",
	apiPath = "api/index.php?key=",
	apiKey = "a684eceee76fc522773286a8";


var get_tags_list = function() {
	var action = "get_tags_list";
	var url = apiHostName + apiPath + apiKey + "&action=" + action;

	$.get(url, {action:"get_tags_list", key:"a684eceee76fc522773286a8"}, function(response) {
		console.log(response);
	});
}