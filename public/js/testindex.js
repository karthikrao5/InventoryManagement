$(document).ready(function() {
	console.log("Hello");

	$(".add-field-button").click(function() {
		console.log("new field clicked");
		$(this).parent().append("<input name=\"attributes[key]\" type=\"text\" placeholder=\"Attribute key\"> | <input name=\"attributes[value]\" type=\"text\" placeholder=\"Attribute value\">");
	});

	$(".add-attribute-button").click(function() {
		console.log("add new attribute button clicked");
		$(".attributes-container").append("<div class=\"single-attribute-form\"><fieldset id=\"attribute-fieldset\"><input name=\"attributes[key]\" type=\"text\" placeholder=\"Attribute key\"> | <input name=\"attributes[value]\" type=\"text\" placeholder=\"Attribute value\"><button type=\"button\" class=\"add-field-button\">Add another field</button></fieldset></div>");
	});
});