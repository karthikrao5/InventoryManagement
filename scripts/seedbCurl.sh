curl -X POST -H "Content-Type: application/json" -H "Cache-Control: no-cache" -H "Postman-Token: 22a673d1-d138-9b4c-5472-e4d479cdc35c" -d '{
	"entry1": 
	{
		"item" : 
		{
			"department_tag" : "College of Math",
		    "gt_tag" : 90342342,
		    "equipment_type" : "laptop",
		    "status" : "loaned",
		    "loaned_to" : "bkang61",
		    "comment" : "Battery is bad but renter doesn'\''t care. Replace the battery later.",
		    "serialnumber" : "59449-55842",
		    "make" : "DELL",
		    "cpu_model_number" : "i7-7700HQ",
		    "os" : "Windows 10 Pro",
		    "logs" : []
		},
		"itemtype":
		{
			"equipment_type" : "laptop",
		    "serialnumber" : ["required", "unique", "string", "[a-zA-Z0-9]+-[a-zA-Z0-9]", "Do not put a dummy serial."],
		    "make" : ["required", "not_unique", "string", "", "Use all caps."],
		    "cpu_model_number" : ["required", "not_unique", "string", "", "Use exact model number from the manufacturer."],
		    "os" : ["optional", "not_unique", "string", "", ""]
		}
	},
	
	"entry2":
	{
		"item": 
		{
			"department_tag" : "Math Dept",
		    "gt_tag" : 902423522,
		    "equipment_type" : "iPad",
		    "status" : "loaned",
		    "loaned_to" : "krao34",
		    "comment" : "iPad screen is broken. Replace when returned.",
		    "serialnumber" : "23482-65434",
		    "make" : "Apple",
		    "logs" : []
		},
		"itemtype":
		{
			"equipment_type" : "mobile",
		    "serialnumber" : ["required", "unique", "string", "[a-zA-Z0-9]+-[a-zA-Z0-9]", "Do not put a dummy serial."],
		    "make" : ["required", "not_unique", "string", "", "Use all caps."]
		}
	},
	
	"entry3":
	{
		"item":
		{
			"department_tag" : "College of Business",
		    "gt_tag" : 8923425,
		    "equipment_type" : "laptop",
		    "status" : "loaned",
		    "loaned_to" : "bwaters5",
		    "comment" : "Needs new wifi adapter. Renter is using wired conenction.",
		    "serialnumber" : "56732-12352",
		    "make" : "LENOVO",
		    "cpu_model_number" : "i7-6600",
		    "os" : "Windows 10 Pro",
		    "logs" : []
		},
		
		"itemtype":
		{
			"equipment_type" : "laptop",
		    "serialnumber" : ["required", "unique", "string", "[a-zA-Z0-9]+-[a-zA-Z0-9]", "Do not put a dummy serial."],
		    "make" : ["required", "not_unique", "string", "", "Use all caps."],
		    "cpu_model_number" : ["required", "not_unique", "string", "", "Use exact model number from the manufacturer."],
		    "os" : ["optional", "not_unique", "string", "", ""]
		}
	}
}

' "http://localhost:8080/inventory"