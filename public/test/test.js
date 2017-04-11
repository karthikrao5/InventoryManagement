$(document).ready(function() {
    console.log( "ready!" );

    var data;

    $.ajax({
		type: "GET",
		beforeSend: function(xhrObj){
			xhrObj.setRequestHeader("Content-Type","application/json");
			xhrObj.setRequestHeader("Accept","application/json");
			// xhrObj.setRequestHeader("Access-Control-Allow-Origin", "*")
    	},
        url: "http://localhost:8080/v1/equipments",
        processData: false,
        success: function(json){
        	
        	var keys = [];
		 	var values = [];

		 	var bool = 0;

		 	var equipments = json['equipments'];
			for (var key in equipments) {
				var singleItem = equipments[key];
				var singleItemValues = [];
				for (var field in singleItem) {
					if (bool == 0) {
						keys.push(field);
					}
					
					singleItemValues.push(singleItem[field]);
					// console.log(field + " : " + singleItem[field]);
				}
				bool += 1;
				values.push(singleItemValues)
			}

			for (var i = 0; i < keys.length; i++) {
				$('tr').append("<th>" + keys[i] + "</th>");
			}

			for (var i = 0; i < values.length; i++) {
				var $row = $('tbody').append("<tr></tr>")
				for (var j = 0; j < values[i].length; j++) {
					$row.append("<th>" + values[i][j] + "</th>");
				}
				
			}
        	console.log("Successfully got equipments");
        }
	  
	});


 // 	var keys = [];
 // 	var values = [];

 // 	var bool = 0;

 // 	var equipments = data['equipments'];
	// for (var key in equipments) {
	// 	var singleItem = equipments[key];
	// 	var singleItemValues = [];
	// 	for (var field in singleItem) {
	// 		if (bool == 0) {
	// 			keys.push(field);
	// 		}
			
	// 		singleItemValues.push(singleItem[field]);
	// 		// console.log(field + " : " + singleItem[field]);
	// 	}
	// 	bool += 1;
	// 	values.push(singleItemValues)
	// }

	// for (var i = 0; i < keys.length; i++) {
	// 	$('tr').append("<th>" + keys[i] + "</th>");
	// }

	// for (var i = 0; i < values.length; i++) {
	// 	var $row = $('tbody').append("<tr></tr>")
	// 	for (var j = 0; j < values[i].length; j++) {
	// 		$row.append("<th>" + values[i][j] + "</th>");
	// 	}
		
	// }

	// console.log(keys);
	// console.log(values);
});

