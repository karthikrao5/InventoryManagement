curl -X POST -H "Content-Type: application/json" -H "Cache-Control: no-cache" -H "Postman-Token: fd8aedd5-cfea-e19e-0ce8-ec96d038fd1a" -d '[
{
	"department_tag": "College of Computing",
	"gt_tag": "90436234",
	"equipment_type": "Laptop",
	"equipment_name": "Lenovo Yoga",
	"status": "checked out",
	"loaned_to": "Karthik Rao"
},
{
	"department_tag": "School of Math",
	"gt_tag": "9034532",
	"equipment_type": "Wireless Router",
	"equipment_name": "Linksys N",
	"status": "checked out",
	"loaned_to": "Justin Filoseta"
},
{
	"department_tag": "School of Biology",
	"gt_tag": "90245543",
	"equipment_type": "USB Drive",
	"equipment_name": "Western Digital 1TB",
	"status": "checked out",
	"loaned_to": "Yury Chernoff"
},
{
	"department_tag": "College of Computing",
	"gt_tag": "90356632",
	"equipment_type": "Laptop",
	"equipment_name": "Macbook Pro",
	"status": "checked out",
	"loaned_to": "Bob Waters"
},
{
	"department_tag": "College of Sciences",
	"gt_tag": "903435352",
	"equipment_type": "Laptop",
	"equipment_name": "Lenovo Thinkpad X1",
	"status": "checked out",
	"loaned_to": "Professor X"
}
]' "http://localhost/add"