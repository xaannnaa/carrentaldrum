# DRUM | Car Rental Management Platform

DRUM is a full-featured car rental web application built with PHP, MySQL, HTML, CSS, and JavaScript. The platform allows users to explore vehicles, make reservations, and manage their bookings, while administrators can manage vehicles, users, and reservations through a dedicated admin dashboard.

The application also features an interactive chatbot that assists users in finding suitable vehicles by answering their questions and recommending cars based on their preferences.

## Features

### User Features

* **Vehicle Listings** — Browse available cars and explore detailed vehicle information.
* **User Authentication** — Register, log in, and manage user accounts.
* **Car Reservations** — Book vehicles and calculate rental prices.
* **Booking History** — View and manage personal reservations.
* **Interactive Chatbot** — Ask questions and receive car recommendations based on individual preferences.
* **Responsive Interface** — Navigate the platform through a user-friendly web interface.

### Admin Dashboard

* **Dashboard Overview** — View platform statistics and booking information.
* **Vehicle Management** — Add, edit, delete, and manage vehicle listings.
* **Image Uploads** — Upload and manage vehicle images.
* **Reservation Management** — View reservations and update their statuses.
* **User Management** — View registered users.

### Database

* MySQL database integration.
* Structured database schema for users, vehicles, and reservations.
* Demo vehicle data for local development and testing.

## Tech Stack

| Technology | Purpose                                                  |
| ---------- | -------------------------------------------------------- |
| PHP        | Backend logic and server-side functionality              |
| MySQL      | Database management                                      |
| HTML5      | Page structure                                           |
| CSS3       | Styling and responsive layouts                           |
| JavaScript | Interactive features and live booking price calculations |
| XAMPP      | Local development environment                            |

## Project Structure

```text
rent-a-car/
├── admin/                 # Admin dashboard and management tools
├── config/                # Database configuration
├── css/                   # Main and admin stylesheets
├── database/              # Database schema and demo data
├── includes/              # Shared components and functions
├── js/                    # Client-side JavaScript
├── uploads/cars/          # Uploaded vehicle images
├── index.php              # Homepage
├── cars.php               # Vehicle listings
├── car-details.php        # Vehicle details
├── booking.php            # Booking functionality
├── my-bookings.php        # User booking history
├── login.php              # Login
├── register.php           # Registration
├── contact.php            # Contact page
└── logout.php             # Logout
```

## Installation and Setup

Follow these steps to run the project locally.

### Prerequisites

* [XAMPP](https://www.apachefriends.org/) with Apache, PHP, and MySQL.
* A web browser.

### 1. Clone or Download the Repository

Download the repository and place the project folder inside your XAMPP `htdocs` directory.

Example for Windows:

```text
C:\xampp\htdocs\rent-a-car\
```

### 2. Start the Local Server

Open XAMPP Control Panel and start:

* Apache
* MySQL

### 3. Set Up the Database

1. Open [phpMyAdmin](http://localhost/phpmyadmin).
2. Select the **SQL** tab.
3. Open `database/schema.sql` from the project.
4. Copy its contents into the SQL editor.
5. Execute the SQL script to create the `drum_rentacar` database and its tables.

The script also inserts demo vehicle data, if included in the provided schema.

### 4. Configure the Database Connection

Open `config/db.php` and verify the database credentials.

Update the host, database name, username, and password if your local MySQL configuration differs from the defaults.

### 5. Create an Administrator Account

If the project includes `create_admin.php`, open:

```text
http://localhost/rent-a-car/create_admin.php
```

Use the credentials displayed by the setup page to access the admin panel.

**Security note:** Delete `create_admin.php` immediately after creating the administrator account. Change any default credentials before using the application outside a local development environment.

### 6. Run the Application

Open the application in your browser:

**Main website:**
http://localhost/rent-a-car/

**Admin dashboard:**
http://localhost/rent-a-car/admin/

Register a user account to explore the booking functionality. Sign in with an administrator account to access the management dashboard.

## Future Improvements

Potential enhancements include:

* Email confirmations for bookings.
* Online payment integration.
* Advanced vehicle filtering by price and other criteria.
* User reviews and ratings.
* Additional chatbot capabilities and improved vehicle recommendations.

## Purpose

This project demonstrates practical web development skills, including backend development with PHP, relational database design with MySQL, user authentication, booking workflows, CRUD operations, administrative interfaces, and interactive chatbot functionality.

It was developed as a full-stack web development project to bring together essential car rental operations in one application.
