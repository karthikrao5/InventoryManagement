db.equipments.remove({});
db.equipmenttypes.remove({});

var equipment_type_laptop =
{
    equipment_type : "laptop",
    serialnumber : ["required", "unique", "string", "[a-zA-Z0-9]+-[a-zA-Z0-9]", "Do not put a dummy serial."],
    make : ["required", "not_unique", "string", "", "Use all caps."],
    cpu_model_number : ["required", "not_unique", "string", "", "Use exact model number from the manufacturer."],
    os : ["optional", "not_unique", "string", "", ""]
};

var equipment1 = 
{
    department_tag : "MATH-15D5E8S6",
    gt_tag : null,
    equipment_type : "laptop",
    status : "loaned",
    loaned_to : "bkang61",
    // created_on : new Date(),
    // last_updated : new Date(),
    comment : "Battery is bad but renter doesn't care. Replace the battery later.",
    serialnumber : "59449-55842",
    make : "DELL",
    cpu_model_number : "i7-7700HQ",
    os : "Windows 10 Pro",
    logs : []
};

db.equipments.insert(equipment1);
db.equipmenttypes.insert(equipment_type_laptop);