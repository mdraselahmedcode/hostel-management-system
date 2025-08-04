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

### Landing Page

![Landing Page](screenshots/index.png)

### Check Status After Hostel Apply

![Check Status](screenshots/check_status.png)

### 👨‍🎓 Student Dashboard

![Student Dashboard](screenshots/student_dashboard.png)

### 🧾 Payment Section

![Payment](screenshots/student_payment.png)
![Payment Detail](screenshots/student_payment_detail.png)

### 📢 Complaint Section

![Complaints](screenshots/student_complaints.png)

### Room Change Request

![Room Change Request](screenshots/student_room_change_request.png)

### xProfile

![Profile](screenshots/student_profile.png)

### Room Detail

![Room Detail](screenshots/student_room_detail.png)

### Monthly payment Report

![Monthly Payment Report](screenshots/student_monthly_payment_report.png)


---

### 👨‍💼 Admin Dashboard

![Admin Dashboard](screenshots/admin_dashboard.png)

### 🏢 Payment Generation

![Payment Generation](screenshots/admin_payment_section.png)
![Admin Payment Activity](screenshots/admin_payment_inside.png)

### 💰 Payment Generation & Report

![Payment Generation](screenshots/admin_payment_report_generation.png)

### Complaints

![Managing Complaints](screenshots/admin_complaint_manage_01.png)
![Chatting with the Student](screenshots/admin_managing_complaints.png)

### Managing Students

![Managing Students](screenshots/admin_students_view.png)


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
