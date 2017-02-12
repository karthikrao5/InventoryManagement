#!/bin/bash

mongo inventorytracking --eval "db.equipments.drop()"

curl -X POST -H "Content-Type: application/json" -H "Cache-Control: no-cache" -H "Postman-Token: 0eea3ca1-53c2-9884-c403-01839e850ac8" -d '{
	"department_tag": "School of Math",
	"gt_tag": "9034532",
	"equipment_type": "Wireless Router",
	"equipment_name": "Linksys N",
	"status": "checked out",
	"loaned_to": "Justin Filoseta"
}' "http://localhost/add"

curl -X POST -H "Content-Type: application/json" -H "Cache-Control: no-cache" -H "Postman-Token: 78c8ebc1-1ca9-c42e-8d07-d675a3b849af" -d '{
	"department_tag": "School of Biology",
	"gt_tag": "90245543",
	"equipment_type": "USB Drive",
	"equipment_name": "Western Digital 1TB",
	"status": "checked out",
	"loaned_to": "Yury Chernoff"
}' "http://localhost/add"

curl -X POST -H "Content-Type: application/json" -H "Cache-Control: no-cache" -H "Postman-Token: 28ccc991-4073-0368-bfbe-33d08309e87c" -d '{
	"department_tag": "College of Computing",
	"gt_tag": "90356632",
	"equipment_type": "Laptop",
	"equipment_name": "Macbook Pro",
	"status": "checked out",
	"loaned_to": "Bob Waters"
}' "http://localhost/core/add-equipment"

curl -X POST -H "Content-Type: application/json" -H "Cache-Control: no-cache" -H "Postman-Token: da09892e-ae4b-1bd9-58ac-482f3aa8ae5b" -d '{
	"department_tag": "College of Sciences",
	"gt_tag": "903435352",
	"equipment_type": "Laptop",
	"equipment_name": "Lenovo Thinkpad X1",
	"status": "checked out",
	"loaned_to": "Professor X"
}' "http://localhost/core/add-equipment"
