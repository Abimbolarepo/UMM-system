A. Frontend Development
System Interfaces
This section presents the graphical user interfaces developed for the University Maintenance Management System (UMMS). The interfaces were designed with usability, simplicity, consistency, and responsiveness in mind using HTML5, CSS3, Bootstrap 5, JavaScript, PHP, and MySQL. The system provides different interfaces depending on the authenticated user's role.
1.	User Registration Page
The User Registration page allows new users to create an account before accessing the system. During registration, users are required to provide personal information including their first name, last name, email address, phone number, department, and password.
The system validates all mandatory fields before saving the information into the database. Passwords are securely encrypted using PHP's password hashing mechanism before storage to improve system security.
The registration page also prevents duplicate email addresses by checking whether the supplied email address already exists within the database.
Features
•	User registration form 
•	Required field validation 
•	Email uniqueness validation 
•	Password encryption 
•	Success and error notification messages 
User Registration Page
 
User Login Page
The Login page provides secure authentication for registered users. Users log in using their email address and password.
After successful authentication, the system identifies the user's role and redirects them to the appropriate dashboard. Invalid login attempts generate descriptive error messages to guide users.
The login module uses PHP sessions to maintain user authentication throughout the system.
Features
•	Secure authentication 
•	Session management 
•	Role-based redirection 
•	Error message display 
•	Responsive interface 
User Login Page
 


2.	Role-Base dashboards
Student/Staff Dashboard
After successful authentication, students and staff members are redirected to their dashboard.
The dashboard provides quick access to maintenance services including submitting maintenance requests, viewing previously submitted requests, checking request status, updating user profile information, and logging out.
The dashboard presents information in a simple and intuitive interface to enhance user experience.
Features
•	Submit Maintenance Request 
•	View Submitted Requests 
•	Track Request Status 
•	View Profile 
•	Logout 
Student/Staff Dashboard
 
 
Maintenance Officer Dashboard
The Maintenance Officer Dashboard displays maintenance requests assigned by the administrator.
Maintenance officers can view assigned requests, update job progress, mark jobs as completed, provide completion remarks, and review completed maintenance history.
The dashboard also displays statistics summarizing assigned, in-progress, and completed jobs.

Features
•	View Assigned Jobs 
•	View Job Details 
•	Update Job Status 
•	Complete Maintenance Jobs 
•	View Completed Jobs 
•	Dashboard Statistics 
Maintenance Officer Dashboard
 
 
Administrator Dashboard
The Administrator Dashboard serves as the control center of the application. It provides complete management capabilities over users, maintenance requests, categories, assignments, and reports.
Dashboard statistics provide a quick overview of system activities including:
•	Total Users 
•	Total Requests 
•	Pending Requests 
•	Assigned Requests 
•	In Progress Requests 
•	Completed Requests 
The administrator can easily navigate to different modules through the Quick Actions panel.
Features
•	User Management 
•	Maintenance Request Management 
•	Category Management 
•	Maintenance Officer Assignment 
•	Reports 
•	Dashboard Statistics 
Administrator Dashboard
 
Service Request Submission Page
The Service Request Submission page enables students and staff members to report maintenance problems occurring within the university.
The form captures all relevant information required by maintenance personnel, including maintenance category, title, description, location, building, room number, priority level, and optional image upload.
Input validation ensures that incomplete or invalid requests are rejected before submission.
Features
•	Maintenance category selection 
•	Problem description 
•	Building and room information 
•	Priority selection 
•	Image upload 
•	Input validation 
•	Success confirmation 
Service Request Submission Form
3.	Request Tracking Page
The Request Tracking page allows users to monitor the progress of submitted maintenance requests.
Users can view detailed information about each request including ticket number, maintenance category, assigned maintenance officer, request status, date submitted, and completion information.
Status updates are displayed using colour-coded badges to improve readability.
Possible request statuses include:
•	Pending 
•	Assigned 
•	In Progress 
•	Completed 
Features
•	Track maintenance progress 
•	View request details 
•	Status indicators 
•	Assignment information 
•	Completion history 
Request Tracking Page
 
 

4.	Administrator Request Management Interface
This interface enables administrators to manage all maintenance requests submitted within the university.
Administrators can search requests, assign maintenance officers, monitor request progress, update request information, and review completed maintenance activities.
The interface also supports filtering requests according to their current status.
Features
•	View all requests 
•	Search requests 
•	Assign officers 
•	Monitor request progress 
•	View request details 
•	Manage request lifecycle 
Administrator Request Management Interface
 
5.	Navigation and User Interface Design
The University Maintenance Management System was designed with an emphasis on ease of use, consistency, and accessibility.
The application incorporates a responsive navigation structure that adapts to different screen sizes while maintaining a consistent user experience.
The interface uses the official MIVA Open University colour scheme to maintain institutional branding.
The following design principles were implemented:
•	Consistent page layouts 
•	Responsive Bootstrap components 
•	Intuitive navigation menus 
•	Colour-coded status indicators 
•	Confirmation dialogs for critical operations 
•	Session-based authentication 
•	User-friendly icons 
•	Professional typography 
•	Mobile-friendly design 
System Navigation Interface
 Form Validation and User Feedback
To improve usability and maintain data integrity, the system validates user inputs before processing any request.
Validation is performed on both the client side and server side.
The application provides immediate feedback through informative messages whenever users perform actions such as registration, login, request submission, updating records, assigning maintenance officers, or completing maintenance tasks.
The feedback mechanisms include:
•	Success messages 
•	Error messages 
•	Warning messages 
•	Confirmation prompts 
•	Required field indicators 
These feedback mechanisms improve user interaction and reduce data entry errors.
User Feedback Messages
 
This section is written in a formal academic style and is ready to be inserted into Chapter Four. Once you add your screenshots, it will closely resemble the implementation chapters commonly found in undergraduate Computer Science project reports.

B. Backend/API Development
Backend Implementation
The backend of the University Maintenance Management System (UMMS) was implemented using PHP following the Model–View–Controller (MVC) architectural pattern. The backend is responsible for processing user requests, enforcing security, interacting with the MySQL database, and implementing all business logic required for maintenance request management.
The application was designed to support multiple categories of users, namely students and staff, maintenance officers, and administrators. Each user role was provided with different privileges based on the responsibilities assigned within the system.
1.	User Authentication and Authorization
The authentication module was implemented to ensure that only registered users could gain access to the system. During login, users provide their email address and password.
The authentication process performs the following operations:
•	Validates the submitted credentials. 
•	Retrieves the user record from the database. 
•	Verifies the password using PHP's password_verify() function. 
•	Creates a secure session after successful authentication. 
•	Redirects the user to the appropriate dashboard according to their assigned role. 
Authorization was implemented using middleware classes that validate user sessions and role permissions before granting access to protected pages.
The implemented middleware includes:
•	AuthMiddleware 
o	Confirms that the user has logged into the system. 
o	Redirects unauthenticated users to the login page. 
•	RoleMiddleware 
o	Restricts access according to user roles. 
o	Prevents unauthorized users from accessing administrative or maintenance functions. 
This implementation ensures that users can only access features that correspond to their responsibilities.

2.	RESTful APIs
Although the system was developed as a server-rendered PHP web application, the backend architecture follows RESTful principles through the use of controllers that process HTTP requests and return appropriate responses.
The controllers expose resource-oriented endpoints responsible for handling system operations such as:
i.	User registration 
ii.	User authentication 
iii.	Category management 
iv.	Maintenance request management 
v.	Assignment management 
vi.	User administration 
HTTP request methods were used appropriately:
HTTP Method	Purpose
GET	Retrieve records
POST	Create new records
PUT / UPDATE	Modify existing records
DELETE	Remove records
Each controller validates incoming requests before forwarding them to the corresponding model for database interaction.
3.	CRUD Operations for Service Requests
The Service Request module provides complete Create, Read, Update, and Delete (CRUD) functionality.
Create
Students and staff members submit maintenance requests by completing a request submission form containing:
i.	Category 
ii.	Title 
iii.	Description 
iv.	Building 
v.	Location 
vi.	Room number 
vii.	Priority level 
viii.	Image attachment (optional) 
Upon submission, the system automatically generates a unique ticket number and stores the request in the database with a default status of Pending.
Read
Users can retrieve maintenance requests according to their assigned roles.
Students can:
i.	View all requests submitted by themselves. 
ii.	Monitor request status. 
iii.	Track maintenance progress. 
Maintenance Officers can:
i.	View only jobs assigned to them. 
Administrators can:
i.	View every maintenance request within the system. 
ii.	Search requests. 
iii.	Filter requests. 
iv.	Monitor request progress. 
Update
Authorized users can modify request information according to system rules.
Examples include:
i.	Administrators assigning officers. 
ii.	Maintenance officers updating job status. 
iii.	Maintenance officers recording completion remarks. 
iv.	Administrators updating categories. 
Delete
Delete operations are restricted to administrators.
Validation rules prevent deletion when:
i.	Categories are currently linked to maintenance requests. 
ii.	Database integrity would be compromised. 
4.	Role-Based Access Control (RBAC)
Role-Based Access Control was implemented to enforce security throughout the system.
Three user roles were defined:
Student / Staff
Users in this category are permitted to:
i.	Register an account. 
ii.	Login. 
iii.	Submit maintenance requests. 
iv.	Track submitted requests. 
v.	View request history. 
They cannot:
i.	Assign requests. 
ii.	Manage users. 
iii.	View administrator pages. 
Maintenance Officer
Maintenance officers are authorized to:
i.	View assigned maintenance jobs. 
ii.	Start maintenance activities. 
iii.	Update job progress. 
iv.	Complete assigned jobs. 
v.	Record completion remarks. 
vi.	View completed jobs. 
Maintenance officers cannot:
i.	Manage users. 
ii.	Assign requests. 
iii.	Delete categories. 
Administrator
Administrators possess full system privileges including:
i.	User management. 
ii.	Category management. 
iii.	Request management. 
iv.	Officer assignment. 
v.	Dashboard monitoring. 
vi.	Report generation. 
vii.	Viewing completed maintenance jobs. 
Role validation is performed before every protected page is loaded.
5.	Request Assignment to Maintenance Officers
The assignment module enables administrators to allocate maintenance requests to qualified maintenance officers.
The assignment workflow consists of:
1.	Administrator reviews submitted requests. 
2.	Administrator selects an available maintenance officer. 
3.	Assignment record is created. 
4.	Request status changes from Pending to Assigned. 
5.	Assigned officer receives the request in the officer dashboard. 
Maintenance officers can subsequently update the request through the following lifecycle:
Pending → Assigned → In Progress → Completed
This workflow provides complete visibility into maintenance activities while ensuring accountability for assigned personnel.
6.	Error Handling and Validation
Input validation was implemented at both the client side and server side.
Client-side validation was achieved using:
i.	HTML5 required fields 
ii.	Input constraints 
iii.	Bootstrap validation components 
Server-side validation was implemented in the controllers to ensure that invalid or malicious requests cannot bypass frontend validation.
Validation rules include:
i.	Mandatory field verification 
ii.	Email format validation 
iii.	Duplicate email detection 
iv.	Duplicate category prevention 
v.	Request ownership verification 
vi.	User existence checks 
vii.	Assignment validation 
viii.	Role verification 
User-friendly feedback messages were displayed for successful operations and validation failures.
Examples include:
i.	"Category created successfully." 
ii.	"Category already exists." 
iii.	"Maintenance job completed successfully." 
iv.	"Unable to complete maintenance job." 
v.	"Invalid maintenance request." 
These messages improve usability while preventing application crashes caused by invalid inputs.
7.	Secure Password Handling and Session Management
Security was considered throughout the implementation of the backend.
Passwords were never stored as plain text.
Instead, passwords were securely hashed using PHP's built-in password_hash() function before being saved into the database.
During login, password verification was performed using:
password_verify($password, $hashedPassword)
Session management was implemented using PHP sessions.
After successful authentication, the application stores user information such as:
i.	User ID 
ii.	First name 
iii.	Role ID 
iv.	Role name 
These session variables are used to authorize subsequent requests without requiring repeated authentication.
Users can securely terminate their sessions through the logout functionality, which destroys the active session and redirects them to the login page.
This implementation significantly improves application security by protecting user credentials and preventing unauthorized access.

C. Database Integration
Database Design
The University Maintenance Management System (UMMS) uses a MySQL relational database to store, manage, and retrieve all application data. A relational database was selected because it provides data integrity, consistency, support for relationships through foreign keys, and efficient querying for transactional systems such as maintenance request management.
The database is designed using normalization principles to eliminate redundancy while maintaining data consistency across all modules.
Database Management System
i.	Database Type: Relational Database 
ii.	DBMS: MySQL 
iii.	Access Method: PHP Data Objects (PDO) 
iv.	Architecture: Three-tier architecture (Presentation Layer, Business Logic Layer, Data Layer) 
Database Entities
The application consists of the following major entities.
1. Users Table
The Users table stores all registered users of the system, including students, staff members, maintenance officers, and administrators.
Attributes
i.	user_id (Primary Key) 
ii.	firstname 
iii.	lastname 
iv.	email 
v.	phone 
vi.	department 
vii.	password (hashed) 
viii.	role_id (Foreign Key) 
ix.	status 
x.	created_at 
xi.	updated_at 
Purpose
The Users table is responsible for:
i.	User registration 
ii.	Authentication 
iii.	Role assignment 
iv.	Account management 
v.	User profile information 
2. Roles Table
The Roles table defines every access level available within the system.
Attributes
i.	role_id (Primary Key) 
ii.	role_name 
Available Roles
i.	Administrator 
ii.	Maintenance Officer 
iii.	Student 
Purpose
The Roles table enables:
i.	Role-Based Access Control (RBAC) 
ii.	Authorization 
iii.	Permission management 
3. Service Requests Table
This is the core table of the application.
Every maintenance request submitted by students or staff is stored here.
Attributes
i.	request_id (Primary Key) 
ii.	ticket_number 
iii.	user_id (Foreign Key) 
iv.	category_id (Foreign Key) 
v.	title 
vi.	description 
vii.	location 
viii.	building 
ix.	room_number 
x.	priority 
xi.	image 
xii.	status 
xiii.	assigned_to 
xiv.	assigned_by 
xv.	assigned_at 
xvi.	completed_at 
xvii.	created_at 
Purpose
The Service Requests table supports:
i.	Request submission 
ii.	Status tracking 
iii.	Assignment 
iv.	Completion 
v.	Reporting 

4. Categories Table
This table stores all maintenance categories.
Attributes
i.	category_id (Primary Key) 
ii.	category_name 
iii.	description 
Sample Categories
i.	Electrical 
ii.	Plumbing 
iii.	ICT 
iv.	Carpentry 
v.	Cleaning 
vi.	Furniture Repair 
Purpose
Categories allow maintenance requests to be grouped according to maintenance type.
5. Assignments Table
This table records every request assigned to a maintenance officer.
Attributes
i.	assignment_id (Primary Key) 
ii.	request_id (Foreign Key) 
iii.	officer_id (Foreign Key) 
iv.	assigned_by 
v.	assigned_at 
vi.	status 
vii.	remarks 
Purpose
The Assignments table records:
i.	Which officer is responsible 
ii.	Assignment date 
iii.	Completion remarks 
iv.	Assignment status 
6. Status Updates / Logs
Instead of creating a separate logs table, the application records status progression using timestamp fields and assignment information.
The following fields provide request history:
i.	assigned_at 
ii.	completed_at 
iii.	status 
iv.	assignment status 
v.	remarks 
These fields provide sufficient audit information for monitoring request progress throughout its lifecycle.
Database Relationships
The relationships among the database tables are summarized below.
Users → Roles
i.	One Role can belong to many Users. 
ii.	Each User belongs to one Role. 
Users → Service Requests
i.	One Student can submit many requests. 
ii.	Each request belongs to one student. 
Categories → Service Requests
i.	One category can contain many service requests. 
ii.	Each request belongs to only one category. 
Service Requests → Assignments
i.	One request can have one assignment record. 
ii.	Each assignment belongs to one request. 
Maintenance Officers → Assignments
i.	One maintenance officer may receive many assignments. 
ii.	Every assignment belongs to one officer. 
Database Integrity
The system enforces database integrity through:
i.	Primary Keys 
ii.	Foreign Keys 
iii.	Unique email addresses 
iv.	NOT NULL constraints 
v.	Referential integrity 
vi.	PDO prepared statements 
vii.	Server-side validation 
These mechanisms prevent duplicate records and ensure that all relationships remain consistent.
Database Features
The UMMS database supports the following operations:
i.	User registration and authentication 
ii.	Role management 
iii.	Maintenance request submission 
iv.	Request categorization 
v.	Officer assignment 
vi.	Request tracking 
vii.	Request completion 
viii.	Dashboard statistics 
ix.	Reporting 
x.	Search and filtering

D. Advanced Web Application Features
1. Session-Based Authentication
The University Maintenance Management System implements session-based authentication to ensure that only authenticated users can access protected areas of the application. After successful login, the system creates a secure PHP session containing the user's identity and role information. Every protected page verifies the existence of the active session before granting access. If no valid session is found, the user is automatically redirected to the login page.
The authentication process includes:
•	User login using email and password. 
•	Password verification using PHP's password_verify() function. 
•	Secure password storage using password_hash(). 
•	Session creation after successful authentication. 
•	Automatic logout by destroying the active session. 
•	Protection against unauthorized access to secured pages. 
This implementation ensures that user credentials remain secure while providing a reliable authentication mechanism throughout the application.

2. Role-Based Access Control (RBAC)
The application implements Role-Based Access Control (RBAC) to restrict access to system functionalities based on user roles. Each authenticated user is assigned a role, such as Administrator, Maintenance Officer, or Student, which determines the actions they are permitted to perform.
The system uses middleware components (AuthMiddleware and RoleMiddleware) to verify both authentication and authorization before allowing access to protected pages.
The implemented roles include:
•	Administrator 
o	Manage users 
o	Manage maintenance categories 
o	View all maintenance requests 
o	Assign requests to maintenance officers 
o	Monitor completed maintenance jobs 
o	Generate reports 
•	Maintenance Officer 
o	View assigned maintenance requests 
o	Update request status 
o	Complete maintenance jobs 
o	Add completion remarks 
•	Student 
o	Submit maintenance requests 
o	Upload supporting images 
o	Track request status 
o	View request history 
This RBAC implementation enhances system security by ensuring users only access features relevant to their responsibilities.

3. File/Image Upload for Maintenance Evidence
The system supports image upload functionality during maintenance request submission. Students can attach photographs showing the fault or damaged facility when reporting a maintenance issue.
Uploaded images are stored securely on the server, while the file path is saved in the database and linked to the corresponding maintenance request.
This feature provides several benefits:
•	Enables maintenance officers to assess faults before visiting the location. 
•	Improves the accuracy of maintenance diagnosis. 
•	Assists administrators during request verification. 
•	Serves as supporting evidence for completed maintenance records. 
Supported image formats are validated before upload to prevent invalid or potentially harmful files from being stored.


4. Search and Filtering Functionality
The application implements search and filtering capabilities to improve the usability of administrative modules. Administrators can quickly locate records without manually browsing large datasets.
Implemented search features include:
•	Search maintenance categories by category name. 
•	Search maintenance requests using relevant keywords. 
•	Filter maintenance records based on request status. 
•	Retrieve assigned and completed jobs efficiently. 
These search functions are implemented using SQL queries with prepared statements to improve performance while preventing SQL injection attacks.
The search functionality significantly enhances user productivity by allowing administrators and maintenance officers to quickly access the information required for decision-making.

E. Testing and Deployment  
1. Frontend Component Testing
The major user interface components of the University Maintenance Management System (UMMS) were tested to verify that they function correctly and provide an intuitive user experience. Testing focused on page navigation, form validation, user interaction, and responsiveness.
The following frontend components were tested:
Component	Test Performed	Result
User Registration	Valid and invalid user registration	Passed
User Login	Authentication using correct and incorrect credentials	Passed
Student Dashboard	Display of user information and maintenance requests	Passed
Administrator Dashboard	Display of statistics and management modules	Passed
Maintenance Officer Dashboard	Display of assigned jobs and status updates	Passed
Submit Maintenance Request	Form validation, image upload, and request submission	Passed
Request Tracking	Status updates displayed correctly	Passed
Manage Categories	Add, edit, delete, and search categories	Passed
Manage Users	Create, edit, activate/deactivate users	Passed
Reports Module	Display of completed maintenance jobs	Passed

2. Backend Testing
The backend components were tested to verify that business logic, database operations, authentication, and authorization function correctly.
The following backend functions were successfully tested:
Backend Function	Test	Result
User Authentication	Login and Logout	Passed
Session Management	Protected pages require authentication	Passed
Role-Based Access Control	Users restricted according to role	Passed
Create Service Request	Request stored successfully	Passed
Update Service Request Status	Status changes reflected correctly	Passed
Assign Maintenance Officer	Request assignment successful	Passed
Complete Maintenance Job	Completion updates database successfully	Passed
Category CRUD	Create, Read, Update, Delete	Passed
User CRUD	Create, Edit, Activate, Deactivate	Passed
Image Upload	Images uploaded and stored correctly	Passed
Database transactions were verified to ensure data consistency, particularly during maintenance assignment and job completion.

3. Backend API Testing
Although the application primarily uses a server-side PHP architecture, the backend controllers and endpoints were tested by submitting HTTP requests through the application's forms and verifying the responses.
The following endpoints were tested:
Endpoint	Method	Purpose	Result
AuthController.php?action=login	POST	User authentication	Passed
AuthController.php?action=register	POST	User registration	Passed
ServiceRequestController.php?action=create	POST	Submit maintenance request	Passed
AssignmentController.php?action=assign	POST	Assign maintenance officer	Passed
complete_job.php	POST	Complete maintenance job	Passed
Category CRUD pages	GET / POST	Category management	Passed
User management pages	GET / POST	User management	Passed
Each endpoint was verified to ensure:
•	Correct input validation. 
•	Successful database interaction. 
•	Proper error handling. 
•	Appropriate success and failure messages. 
•	Prevention of unauthorized access through middleware. 

4. Application Deployment
The University Maintenance Management System was successfully deployed in a web server environment using the XAMPP Apache server and MySQL database.
Deployment Environment
Component	Technology
Web Server	Apache (XAMPP)
Backend	PHP 8.x
Database	MySQL
Frontend	HTML5, CSS3, Bootstrap 5, JavaScript
Database Access	PDO
Deployment Process
The deployment process involved the following steps:
1.	Copy the UMMS project folder into the XAMPP htdocs directory. 
2.	Start the Apache and MySQL services using the XAMPP Control Panel. 
3.	Create the maintenance_db database in phpMyAdmin. 
4.	Import the SQL database schema. 
5.	Configure the database connection in the application. 
6.	Launch the application through: 
http://localhost/maintenance-system/
7.	Verify that all modules function correctly after deployment. 
Database Connectivity Verification
After deployment, the following operations were successfully performed to confirm proper database connectivity:
•	User registration 
•	User login 
•	Category creation 
•	Maintenance request submission 
•	Request assignment 
•	Job completion 
•	Dashboard statistics retrieval 
•	Report generation 
Successful completion of these operations confirmed that the deployed application communicates correctly with the MySQL database.






