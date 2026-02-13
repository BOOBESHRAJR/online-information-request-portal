# Online Information Request Portal - Setup Guide

## System Overview
A complete PHP-based application with user and admin roles for submitting and managing information requests.

## Requirements
- XAMPP or similar PHP/MySQL environment
- PHP 7.0+
- MySQL 5.7+
- Modern web browser

## Installation Steps

### 1. Database Setup
1. Open phpMyAdmin at `http://localhost/phpmyadmin`
2. Create a new database named `online_request_portal`
3. Select the new database
4. Go to the SQL tab and paste the contents of `db/schema.sql`
5. Click "Go" to execute the SQL script

**Default Test Accounts (passwords are hashed in DB):**
- **Admin**: Email: `admin@portal.com` | Password: `admin123`
- **User**: Email: `user@example.com` | Password: `user123`

### 2. File Location
Place all files in: `C:\xampp\htdocs\online_request_portal\`

### 3. Access the Application
Open your browser and go to:
```
http://localhost/online_request_portal/
```

## Project Structure
```
online_request_portal/
├── config/
│   └── database.php          # Database configuration
├── db/
│   └── schema.sql            # Database schema and sample data
├── user/
│   ├── login.php             # User login
│   ├── dashboard.php         # User dashboard
│   ├── submit_request.php    # Submit new request
│   ├── view_status.php       # View request status
│   └── logout.php            # User logout
├── admin/
│   ├── login.php             # Admin login
│   ├── dashboard.php         # Admin dashboard
│   ├── view_requests.php     # New pending requests (approve/reject)
│   ├── approved_rejected.php # Processed requests
│   └── logout.php            # Admin logout
├── assets/
│   ├── style.css             # Styling
│   └── script.js             # JavaScript (if needed)
├── index.php                 # Home page
├── register.php              # User registration
└── README.md                 # This file
```

## Features

### User Module
1. **Registration** (register.php)
   - Create new account with name, email, password, and phone
   - Passwords are securely hashed using password_hash()
   - Email validation and unique email checks

2. **Login** (user/login.php)
   - Secure login with email and password
   - Session-based authentication

3. **Dashboard** (user/dashboard.php)
   - Quick access to submit requests
   - Quick access to view requests status

4. **Submit Request** (user/submit_request.php)
   - Form fields: Title, Details, Category
   - Auto-populated fields: user_id, register_id, submitted_at, status='Pending'
   - Success popup with options (submit another, view requests, return to dashboard)

5. **View Requests** (user/view_status.php)
   - Display all requests submitted by logged-in user
   - Show: Title, Details, Category, Submitted Date/Time, Status
   - Status colors: Orange (Pending), Green (Approved), Red (Rejected)

### Admin Module
1. **Admin Login** (admin/login.php)
   - Secure login for administrators only
   - Role-based access control

2. **Admin Dashboard** (admin/dashboard.php)
   - Display count of pending requests
   - Display count of processed requests
   - Quick navigation cards

3. **New Requests** (admin/view_requests.php)
   - Display only pending requests
   - Show: Serial #, Name, Email, Phone, Title, Details, Category, Submitted Date/Time
   - Action buttons: Approve / Reject
   - Auto-remove processed requests from this view

4. **Processed Requests** (admin/approved_rejected.php)
   - Display approved and rejected requests
   - Show: Serial #, Name, Email, Title, Details, Submitted Date/Time, Status
   - Status colors: Green (Approved), Red (Rejected)

## Database Schema

### Users Table
```
id (INT, PRIMARY KEY, AUTO_INCREMENT)
name (VARCHAR 100)
email (VARCHAR 100, UNIQUE)
password (VARCHAR 255)
phone (VARCHAR 20)
role (ENUM: 'user', 'admin')
created_at (TIMESTAMP)
```

### Requests Table
```
id (INT, PRIMARY KEY, AUTO_INCREMENT)
user_id (INT, FOREIGN KEY)
register_id (INT)
title (VARCHAR 255)
details (TEXT)
category (VARCHAR 50)
submitted_at (DATETIME)
status (VARCHAR 20: 'Pending', 'Approved', 'Rejected')
```

## Security Features
- ✅ Passwords hashed using `password_hash()` with bcrypt
- ✅ Passwords verified using `password_verify()`
- ✅ MySQLi prepared statements to prevent SQL injection
- ✅ Session-based authentication
- ✅ Role-based access control (user/admin)
- ✅ Users can only view their own requests
- ✅ Input validation and sanitization
- ✅ Output escaping with `htmlspecialchars()`

## User Flow

### User Path
1. User → Opens `index.php`
2. User → Clicks "Register" → Fills `register.php`
3. User → Redirected to `user/login.php`
4. User → Enters credentials → Dashboard (`user/dashboard.php`)
5. User → Can submit request via `user/submit_request.php`
6. User → Can view request status via `user/view_status.php`

### Admin Path
1. Admin → Opens `admin/login.php`
2. Admin → Enters credentials → Dashboard (`admin/dashboard.php`)
3. Admin → Views new requests via `admin/view_requests.php`
4. Admin → Approves/Rejects requests
5. Admin → Views processed requests via `admin/approved_rejected.php`

## Testing

### Test User Registration
1. Go to `http://localhost/online_request_portal/register.php`
2. Fill in details and register
3. Login with credentials
4. Submit a request
5. View request status (will show "Pending")

### Test Admin Functionality
1. Go to `http://localhost/online_request_portal/admin/login.php`
2. Login with: `admin@portal.com` / `admin123`
3. View new pending requests
4. Approve or reject requests
5. Verify they appear in processed requests

### Test User Viewing Updates
1. Login as user
2. Go to "View Requests"
3. Should see updated status after admin action

## Customization

### Database Connection
Edit `config/database.php` to change:
- Server name
- Username
- Password
- Database name

### Styling
Modify `assets/style.css` to customize appearance

### Request Categories
Edit the category dropdown in `user/submit_request.php`

## Troubleshooting

**Database Connection Error**
- Verify MySQL is running
- Check credentials in `config/database.php`
- Ensure database `online_request_portal` exists

**SQL Errors**
- Make sure `db/schema.sql` was executed completely
- Check PhpMyAdmin for any error messages

**Session Issues**
- Clear browser cookies
- Ensure PHP session.save_path is writable
- Check session timeout settings

## Additional Notes
- Passwords in the sample accounts are hashed versions of `admin123` and `user123`
- The system uses MySQLi for database operations
- All dates and times use DATETIME format
- The application is responsive and mobile-friendly

---
**Created**: February 2026
**Version**: 1.0
**Status**: Complete and Ready for Deployment
