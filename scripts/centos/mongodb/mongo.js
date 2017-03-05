db.equipments.remove({});
db.equipmenttypes.remove({});
db.equipmentlogs.remove({});

var equipment_type_laptop =
{
    equipment_type : "laptop",
    make : ["required", "not_unique", "string", "", "Use all caps."],
    cpu_model_number : ["required", "not_unique", "string", "", "Follow exact name from manufacturer."],
	os : ["optional", "not_unique", "string", "", ""]
};

var equipment1 = 
{
    department_tag : "MATH-15D5E8S6",
    gt_tag : null,
    equipment_type : "laptop",
    status : "loaned",
    loaned_to : "bkang61",
    created_on : new Date("<YYYY-mm-ddTHH:MM:ss>"),
    last_updated : new Date("<YYYY-mm-ddTHH:MM:ss>"),
    comment : "Battery is bad but renter doesn't care. Replace the battery later.",
    logs : [],
    make : "DELL",
    cpu_model_number : "i7-7700HQ",
    os : "Windows 10 Pro"
};

var log1 = 
{
    _id : new ObjectId(),
    timestamp : new Date("<YYYY-mm-ddTHH:MM:ss>"),
    action_by : "bkang61",
    action_via : "API",
    changes : 
			[
				{
					affected_attribute : "status",
					current_value : "inventory",
					new_value : "loaned"
				},
				
				{
					affected_attribute : "loaned_to",
					current_value : null,
					new_value : "bkang61"
				}
			]
};

db.equipments.insert(equipment1);
db.equipmenttypes.insert(equipment_type_laptop);
db.equipmentlogs.insert(log1);
db.equipments.update
(
    {department_tag : "MATH-15D5E8S6"},
    {
        $push : {logs : log1}
    }
);