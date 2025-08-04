# 🏨 Hostel Management System

A web-based Hostel Management System designed to streamline hostel admission, room allocation, payment handling, and communication between students and hostel administration.

---

## 🌐 Live Demo

🔗 [Live Site](https://hostel-management-system.infinityfreeapp.com/)

You can explore the functionalities as a **student** or **admin** through the live demo.

---

## 📌 Features

### 👨‍🎓 Student Panel
- Apply for hostel admission.
- Email verification via verification link.
- Wait for admin approval (notified via email).
- After approval and check-in, access full system features:
  - Login to student dashboard.
  - Check application status before login.
  - Reset password via email link.
  - Edit profile and upload profile image.
  - Change password.
  - Send room change requests.
  - File complaints and chat with admin.
  - Make payments (manual match via mobile banking).
  - Print receipts and monthly payment reports.
  - Overpayment is auto-adjusted to the next month.
  - View room details and roommate list.

### 🛠️ Admin Panel
- Admin and Super Admin roles:
  - Super Admin can manage all admins.
- Manage:
  - Hostels
  - Floors
  - Rooms and room types
  - Student profiles (approve, verify, check-in)
  - Room assignments
- Payment Management:
  - Generate monthly payments.
  - Auto late fee addition after due date.
  - Download reports in PDF or CSV format.
  - Match mobile banking payments via transaction ID and reference code.
- Complaint Management:
  - Respond to student complaints via chat.
- Handle room change requests.

---

## 🧰 Tech Stack

- **Backend:** PHP  
- **Frontend:** HTML, CSS, Bootstrap  
- **Interactivity:** jQuery, AJAX  
- **Database:** MySQL  

---

## 📸 Screenshots


### 👨‍🎓 Student Dashboard

![Student Dashboard](screenshots/student_dashboard.png)

### 🧾 Payment Section

![Payment](screenshots/student_payment.png)
![Payment](screenshots/student_payment_detail.png)

### 📢 Complaint Section

![Complaints](screenshots/student-complaint.png)

---

### 👨‍💼 Admin Dashboard

![Admin Dashboard](screenshots/admin-dashboard.png)

### 🏢 Room & Hostel Management

![Room Management](screenshots/admin-room.png)

### 💰 Payment Generation & Report

![Payment Generation](screenshots/admin-payment-generation.png)

---

## 🗃️ Project Setup

> This project is hosted. To set up locally:

1. Clone the repository  
2. Import the SQL database into your MySQL  
3. Configure `/config/config.php` and `/config/db.php` for DB connection  
4. Run using local server (XAMPP/Laragon etc.)

---

## 📧 Contact

Have feedback or need help? Open an [issue](https://github.com/your-repo/issues) or connect via email.

---

## 📜 License

This project is licensed under the MIT License. See the `LICENSE` file for more details.
