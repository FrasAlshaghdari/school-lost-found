# 🎓 School Lost & Found System

The **School Lost & Found System** is a web application designed to help students and staff report, search, and recover lost items efficiently.  
This project was built as part of a **school competition** to demonstrate practical web development skills using full-stack technologies.

---

## 🌐 Live Website
You can visit and test the live website here:

👉 https://schoollostfound.com  

---

## 📌 Project Features

### ✅ User Features:
- Register and login securely
- Report lost and found items
- Search for items by name and category
- Upload item images
- Claim an item
- Contact administration
- Personal user dashboard (My Claims)
- Profile management

### ✅ Admin Panel:
- View and manage users
- Manage items and claims
- Approve or reject claims
- View system statistics
- Manage messages from contact form
- Change user roles (admin / user)

---

## 🛠️ Technology Stack

This project uses modern web development tools:

| Layer | Technology |
|-------|-----------|
| Frontend | HTML, CSS, JavaScript |
| Backend | PHP (Server-side logic) |
| Database | MySQL / MariaDB |
| Hosting | Google Cloud VM |
| Web Server | Apache |
| Version Control | Git & GitHub |
| Security | HTTPS (Let’s Encrypt SSL) |

---

## 🧱 Project Structure
school_lost_found/
│
├── admin/ # Admin dashboard & controls
├── api/ # API endpoints
│ └── admin/ # Admin API routes
├── assets/
│ ├── css/ # Styling
│ ├── js/ # JavaScript logic
│ └── images/ # Logo & image uploads
├── includes/
│ ├── navbar.php # Navigation bar
│ ├── footer.php # Website footer
│ └── config.php # Database config (excluded from GitHub)
│
├── uploads/ # User uploaded images
├── index.php # Homepage
├── login.php # Login system
├── register.php # User registration
├── report.php # Report lost item
├── search.php # Search system
├── contact.php # Contact form
└── install.php # Database installer

---

## 🔐 Security Features

The system was built with security in mind:

- Password hashing
- Session protection
- Role-based access control
- Admin panel protection
- SQL injection prevention
- HTTPS encryption (SSL)
- Secure database user
- Controlled error display


---

## 🎯 Improvements Made for Competition

Several improvements were added to strengthen the project for judging:

- Live hosting on Google Cloud
- SSL security activation
- Admin dashboard
- Professional UI design
- Centralized config file
- Organized folder structure
- Real database integration
- Performance optimization
- Clean user experience
- Logo branding
- Footer and navbar written dynamically

---

## ⚙️ Setup Instructions (For Developers)

### 1. Clone the repository

git clone https://github.com/FrasAlshaghdari/school-lost-found.git
### 2. Configure database
Rename:
config.sample.php → config.php
Update database credentials.
### 3. Import database
Import the included SQL file to phpMyAdmin.
### 4. Run locally
Place inside:
xampp/htdocs/
Open:
http://localhost/school_lost_found

---


## 🚀 Why This Project Matters

This project solves a real-world problem that impacts students every day:  
lost items with no organized system to recover them.

This project was built not just for competition, but to demonstrate:

✅ Real-world software development  
✅ Secure user authentication  
✅ Backend and database design  
✅ Cloud deployment skills  
✅ Professional system architecture  
✅ Practical problem solving  
✅ UI/UX design principles  

It provides:
- A clean interface for reporting lost items
- A searchable database
- Role-based admin control
- A real production deployment
- A secure HTTPS connection

This project shows my ability to:

> **Design, develop, deploy, secure, and maintain a full production web system.**



👨‍💻 Author

Fras Alshaghdari 

Student Developer 

Email: fras.alshaghdari@gmail.com

🏁 Final Note

This project was designed, coded, tested, deployed, secured, and published by the developer independently as a demonstration of skill and creativity.

Feel free to explore, test, and review the system.

Thank you for your time and evaluation.
