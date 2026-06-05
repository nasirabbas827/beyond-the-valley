# Beyond_the_valley_final

A PHP‑based web application that lets administrators manage hotels, tourist places, users, and bookings for the **Beyond the Valley** travel portal. The system includes a simple front‑end for visitors to make reservations and a back‑office suite for content management.

---  

## Overview
**Beyond_the_valley_final** provides a complete CRUD interface for travel‑related data:

* Admins can add, edit, and delete hotels, places, and user accounts.  
* Bookings are stored in a MySQL database and can be reviewed or updated by staff.  
* A contact‑support page enables visitors to send inquiries.  
* All pages share a unified CSS stylesheet for a consistent look and feel.

The project is delivered with a ready‑to‑import MySQL dump (`Database/valley.sql`) and a set of sample images in `admin/uploads/`.

---  

## Features
- **Admin Dashboard** – Central hub (`admin_home.php`) with navigation (`admin_navbar.php`).  
- **User Management** – Add, edit, delete, and view users (`add_users.php`, `edit_user.php`, `view_users.php`).  
- **Hotel & Place Management** – Full CRUD for hotels and tourist spots, including image uploads.  
- **Booking System** – Visitors can create bookings (`booking.php`); admins can view and update them (`view_bookings.php`, `update_booking.php`).  
- **Authentication** – Secure admin login (`admin_login.php`) with session handling.  
- **Responsive Styling** – Simple, clean UI powered by `css/style.css`.  
- **Database Schema** – Pre‑defined tables for users, hotels, places, and bookings (`valley.sql`).  

---  

## Tech Stack
| Layer | Technology |
|-------|------------|
| Backend | PHP 7+ |
| Database | MySQL |
| Front‑end | HTML5, CSS3 |
| Server | Apache / Nginx (any LAMP stack) |
| Version Control | Git |

---  

## Installation
1. **Clone the repository**  
   ```bash
   git clone https://github.com/yourusername/Beyond_the_valley_final.git
   cd Beyond_the_valley_final
   ```

2. **Set up the database**  
   - Create a new MySQL database (e.g., `valley`).  
   - Import the schema and sample data:  
     ```bash
     mysql -u root -p valley < Database/valley.sql
     ```

3. **Configure PHP connection**  
   - Open `config.php` (and `admin/config.php` if you prefer a separate admin config).  
   - Replace placeholder values with your own credentials:  
     ```php
     $host = 'YOUR_DB_HOST';
     $user = 'YOUR_DB_USER';
     $pass = 'YOUR_DB_PASSWORD';
     $dbname = 'YOUR_DB_NAME';
     ```

4. **Adjust file permissions** (for image uploads)  
   ```bash
   chmod -R 755 admin/uploads
   ```

5. **Serve the project**  
   - Place the project folder inside your web server’s document root (e.g., `/var/www/html`).  
   - Ensure PHP is enabled and the server points to `booking.php` as the entry point for visitors.

---  

## Usage
### Visitor side
1. Navigate to `http://yourdomain/booking.php`.  
2. Fill out the booking form and submit.  
3. Use the **Contact Support** page (`contact_support.php`) for any queries.

### Admin side
1. Open `http://yourdomain/admin/admin_login.php`.  
2. Log in with the admin credentials created during the database import (default: `admin / admin123`).  
3. From the dashboard you can:
   - **Add / Edit / Delete** hotels (`add_hotel.php`, `edit_hotel