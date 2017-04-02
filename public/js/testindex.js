$(document).ready(function() {
	console.log("Hello");

	$(".attribute-button").click(function() {
		console.log("add new attribute button clicked");
		$(".equipment-type-attribute-fieldset").append("<input name=\"attributes[key]\" type=\"text\" placeholder=\"Attribute key\"> | <input name=\"attributes[value]\" type=\"text\" placeholder=\"Attribute value\">");
	});
});