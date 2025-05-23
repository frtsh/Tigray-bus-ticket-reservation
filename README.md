
# 🚍 Tigray Bus Ticket Reservation System 🚌

This project is a **Bus Ticket Reservation System** forked and heavily modified from Subramanian V's original project.
It is built using **PHP**, **MySQL**, **HTML**, **CSS**, and **JavaScript**, and runs on **WAMP**.

It enables users to book and manage bus tickets for buses in Tigray efficiently.

---

## ✨ What’s New / Key Features in My Version

- 🌍 Improved user interface tailored for Tigray region buses
- 🛠️ Added Admin Dashboard for managing buses, users, and bookings
- 🎫 Enhanced seat selection with real-time availability updates
- 🔒 More robust security and input validations
- ❌ Integration of advanced ticket cancellation features
- 🧱 Cleaner, modularized codebase for easier maintenance

---

## 🛠️ Technologies Used

- **Frontend**: HTML, CSS, JavaScript
- **Backend**: PHP
- **Database**: MySQL
- **Server**: WAMP (Apache & MySQL)

---
## 🚀 Installation and Setup (Using WAMP)

1. 🔽 **Download and Install WAMP**  
   - Download WAMP Server from [WampServer](http://www.wampserver.com/en/) and install it.

2. 🧩 **Clone the Repository**  
   - Clone or download the ZIP and extract it into `C:\wamp64\www`.

3. 💾 **Import Database**  
   - Open **phpMyAdmin** via `http://localhost/phpmyadmin`.  
   - Create a database named `final`.  
   - Import `final.sql` into the `final` database.

4. ⚙️ **Update Database Configuration**  
   - Edit `db.php`:  
     - **Server name**: `localhost`  
     - **Username**: `root`  
     - **Password**: *(leave blank)*  
     - **Database name**: `final`

5. 🌐 **Run the Project**  
   - Start WAMP and ensure Apache/MySQL are running.  
   - Visit `http://localhost/online-bus-ticket-reservation` in your browser.

## 📋 Project Workflow

- 📝 User registration and login
- 🚌 View available buses and select seats
- 💳 Simulated payment (Card, Phone, UPI)
- 📄 View and cancel booked tickets
- 🛠️ Admin Dashboard to manage buses, users, and bookings

---

## 🧩 File Structure

```
/final
├── admin/                  # Admin panel (new)
├── db.php                  # Database connection
├── header.php
├── footer.php
├── index.php               # Login/Registration
├── buses.php               # Bus listing
├── seat_selection.php      # Seat selection
├── payment.php             # Payment simulation
├── view_booked.php         # View user bookings
├── cancel_ticket.php       # Cancel ticket
├── styles.css
└── ... (additional files)
```

---

## 📊 Database Schema

### **users**
- `id`, `name`, `email`, `password`

### **tickets**
- `ticket_id`, `user_id`, `bus_id`, `seat_number`, `travel_date`, `booking_time`

### **seats**
- `id`, `bus_id`, `seat_number`, `is_booked`

### **payments**
- `payment_id`, `ticket_id`, `amount`, `payment_method`, `payment_date`

### **buses**
- `id`, `bus_name`, `bus_number`, `source`, `destination`, `departure_time`, `arrival_time`, `total_seats`, `price`, `created_at`

### **bookings**
- `id`, `user_id`, `bus_id`, `travel_date`, `seats`, `total_amount`, `booking_reference`, `status`

### **admin**
- `id`, `name`, `email`, `password`

---

## 💡 How My Project Uses Open Source

- Forked from a [MIT licensed open-source project](https://github.com/Subramanian7986/online-bus-ticket-reservation)
- Uses a fully open-source stack: PHP, MySQL, Apache
- Licensed under MIT, allowing modifications and redistribution with credit

---

## 🎯 Future Enhancements

- 📧 Add email notifications for ticket confirmation and cancellation
- 🔁 Real-time seat updates using AJAX or WebSockets
- 💰 Integrate real online payment gateways
- 📱 Make the UI mobile-responsive
- 🌐 Add multilingual support (Tigrigna, Amharic)

---

## 📜 License

This project is licensed under the **MIT License**.  
Original project also licensed under MIT License by **Subramanian V**.

---

## 🙌 Author

**group two
