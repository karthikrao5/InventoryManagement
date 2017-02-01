getFirstKey = function(dict) {
    
    for (key in dict) {
        first = key;
        return key;
    }
}

toDate = function(sec) {

    var date = new Date(sec * 1000);
    var months = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", 
        "Sep", "Oct", "Nov", "Dec"];

    var result = months[date.getMonth()] + "-";
    result += date.getDate() + "-";
    result += date.getFullYear() + " ";
    result += date.getHours() + ":";
    result += date.getMinutes() + ":";
    result += date.getSeconds();
    return result;



}

$(document).ready(function() {

    //get inventory data
    $.getJSON('http://localhost/add', function(inventory) {

        var first = getFirstKey(inventory);
        $('#test').append(key);

        //create string to append to table
        var tab;

        //generate header row
        tab += ("<thead> <tr>");
        for (key in inventory[first]) {
            tab += "<th>"  + key.toUpperCase() + "</th>";
        }
        tab += "</tr> </thead><tbody class='dest'>";

        //fill in table body
        for (var key in inventory) {

            tab += ("<tr>");

            for (var key2 in inventory[key]) {

                //if the element is an object index one level deeper
                if (typeof(inventory[key][key2]) === 'object') {
                    if (key2 == "timestamp") {
                        var time = toDate(inventory[key][key2]
                        [getFirstKey(inventory[key][key2])]);
                        tab += "<td>" + time + "</td>";
                    }

                    else {
                        tab += "<td>" + inventory[key][key2]
                        [getFirstKey(inventory[key][key2])]+ "</td>";
                    }
                }
                else {
                    tab += "<td>" + (inventory[key][key2])+ "</td>";
                }
            }
            tab += "</tr>";
        }
        tab += "</tbody> </table>";

        //append string to inventory table
        $('#inventory').append(tab);

    });

});



//     data = 
// [
//     {
//         "id": 0,
//         "name": "test0",
//         "price": "$0"
//     },
//     {
//         "id": 1,
//         "name": "test1",
//         "price": "$1"
//     },
//     {
//         "id": 2,
//         "name": "test2",
//         "price": "$2"
//     },
//     {
//         "id": 3,
//         "name": "test3",
//         "price": "$3"
//     },
//     {
//         "id": 4,
//         "name": "test4",
//         "price": "$4"
//     },
//     {
//         "id": 5,
//         "name": "test5",
//         "price": "$5"
//     },
//     {
//         "id": 6,
//         "name": "test6",
//         "price": "$6"
//     },
//     {
//         "id": 7,
//         "name": "test7",
//         "price": "$7"
//     },
//     {
//         "id": 8,
//         "name": "test8",
//         "price": "$8"
//     },
//     {
//         "id": 9,
//         "name": "test9",
//         "price": "$9"
//     },
//     {
//         "id": 10,
//         "name": "test10",
//         "price": "$10"
//     },
//     {
//         "id": 11,
//         "name": "test11",
//         "price": "$11"
//     },
//     {
//         "id": 12,
//         "name": "test12",
//         "price": "$12"
//     },
//     {
//         "id": 13,
//         "name": "test13",
//         "price": "$13"
//     },
//     {
//         "id": 14,
//         "name": "test14",
//         "price": "$14"
//     },
//     {
//         "id": 15,
//         "name": "test15",
//         "price": "$15"
//     },
//     {
//         "id": 16,
//         "name": "test16",
//         "price": "$16"
//     },
//     {
//         "id": 17,
//         "name": "test17",
//         "price": "$17"
//     },
//     {
//         "id": 18,
//         "name": "test18",
//         "price": "$18"
//     },
//     {
//         "id": 19,
//         "name": "test19",
//         "price": "$19"
//     },
//     {
//         "id": 20,
//         "name": "test20",
//         "price": "$20"
//     }
// ];


   // var tab

    // tab += ("<thead> <tr>");

    // for (key in data[0]) {
    //     tab += "<th>"  + key.toUpperCase() + "</th>";
    // }

    // tab += "</tr> </thead><tbody class='dest'>";

    // for (var i = 0; i < data.length; i++) {

    //  tab += ("<tr>");

    //  for (var key in data[i]) {

    //      tab += "<td>" + data[i][key]+ "</td>";
    //  }
    //  tab += "</tr>";
    // }
    // tab += "</tbody> </table>";

    // $('#inventory').append(tab);



