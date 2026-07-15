<img src="https://capsule-render.vercel.app/api?type=waving&color=gradient&customColorList=6,11,20&height=160&section=header&text=Student%20Print%20Service&fontSize=32&fontColor=fff&animation=fadeIn&fontAlignY=35&desc=Online%20Printing%20Portal%20for%20College%20Campuses&descAlignY=58&descSize=15" width="100%"/>

<div align="center">

### 🚀 Trusted by 1,000+ Students &nbsp;|&nbsp; 4,000+ Print Orders Delivered

</div>

<div align="center">

[![Typing SVG](https://readme-typing-svg.demolab.com?font=Fira+Code&weight=600&size=22&duration=3000&pause=1000&color=28A745&center=true&vCenter=true&width=650&lines=PHP+%2B+MySQL+Powered+Print+Portal;Upload+%7C+Pay+%7C+Track+%7C+Collect;Used+by+1%2C000%2B+Students+%7C+4%2C000%2B+Orders)](https://git.io/typing-svg)

![PHP](https://img.shields.io/badge/PHP-777BB4?style=flat-square&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-4479A1?style=flat-square&logo=mysql&logoColor=white)
![Razorpay](https://img.shields.io/badge/Razorpay-02042B?style=flat-square&logo=razorpay&logoColor=white)
![Bootstrap](https://img.shields.io/badge/Bootstrap-7952B3?style=flat-square&logo=bootstrap&logoColor=white)
![HTML5](https://img.shields.io/badge/HTML5-E34F26?style=flat-square&logo=html5&logoColor=white)
![CSS3](https://img.shields.io/badge/CSS3-1572B6?style=flat-square&logo=css3&logoColor=white)

</div>

---

## 💬 Impact & Feedback

**Student Print Service** has made a real difference on campus — cutting queue times at the print shop, saving students hours every semester, and giving PSG Institute of Technology and Applied Research a faster, more organized way to handle printing.

<div align="center">

# 🔥 Hear It From the People Who Used It — Feedback From Our Students & Faculty ! 🔥

### 21 Anonymous Voices from Faculty & Students Who Lived the Difference 👇

</div>

<div align="center">
<table>
<tr>
<td width="50%" align="center"><img src="FeedBacks/F1.jpg" width="100%"/></td>
<td width="50%" align="center"><img src="FeedBacks/F2.jpg" width="100%"/></td>
</tr>
<tr>
<td width="50%" align="center"><img src="FeedBacks/F3.jpg" width="100%"/></td>
<td width="50%" align="center"><img src="FeedBacks/F4.jpg" width="100%"/></td>
</tr>
<tr>
<td width="50%" align="center"><img src="FeedBacks/F5.jpg" width="100%"/></td>
<td width="50%" align="center"><img src="FeedBacks/F6.jpg" width="100%"/></td>
</tr>
<tr>
<td width="50%" align="center"><img src="FeedBacks/F7.jpg" width="100%"/></td>
<td width="50%" align="center"><img src="FeedBacks/F8.jpg" width="100%"/></td>
</tr>
<tr>
<td width="50%" align="center"><img src="FeedBacks/F9.jpg" width="100%"/></td>
<td width="50%" align="center"><img src="FeedBacks/F10.jpg" width="100%"/></td>
</tr>
<tr>
<td width="50%" align="center"><img src="FeedBacks/F11.jpg" width="100%"/></td>
<td width="50%" align="center"><img src="FeedBacks/F12.jpg" width="100%"/></td>
</tr>
<tr>
<td width="50%" align="center"><img src="FeedBacks/F13.jpg" width="100%"/></td>
<td width="50%" align="center"><img src="FeedBacks/F14.jpg" width="100%"/></td>
</tr>
<tr>
<td width="50%" align="center"><img src="FeedBacks/F15.jpg" width="100%"/></td>
<td width="50%" align="center"><img src="FeedBacks/F16.jpg" width="100%"/></td>
</tr>
<tr>
<td width="50%" align="center"><img src="FeedBacks/F17.jpg" width="100%"/></td>
<td width="50%" align="center"><img src="FeedBacks/F18.jpg" width="100%"/></td>
</tr>
<tr>
<td width="50%" align="center"><img src="FeedBacks/F19.jpg" width="100%"/></td>
<td width="50%" align="center"><img src="FeedBacks/F20.jpg" width="100%"/></td>
</tr>
<tr>
<td width="50%" align="center"><img src="FeedBacks/F21.jpg" width="100%"/></td>
<td width="50%"></td>
</tr>
</table>
</div>

---

## 📖 About the Project

**Student Print Service** is a full-stack web platform that digitizes the campus printing process at PSG Institute of Technology and Applied Research. Instead of physically queuing at the print shop, students upload their PDF documents online, configure print preferences (copies, color, paper size, orientation), pay securely through Razorpay, and track their order status in real time. Print shop staff manage every incoming request through a dedicated admin dashboard — accepting, rejecting, and fulfilling orders — with automated email notifications keeping students informed at each step.

Built by a 6-member team as part of the Software Development Cell (SDC), the platform has processed **4,000+ print orders for 1,000+ students**, significantly reducing wait times and crowding at campus printing centers.

---

## ✨ Key Features

| Feature | Description |
|---|---|
| 🔐 **Student Accounts** | Roll-number-based signup/login with hashed passwords |
| 📤 **PDF Upload & Configuration** | Upload multiple PDFs; choose copies, color, sides, orientation, paper type, and page ranges (all/odd/even/custom) |
| 💰 **Dynamic Cost Calculation** | Auto-calculates total cost based on page count, color, and copy settings |
| 💳 **Razorpay Payment Gateway** | Secure online payment before order confirmation |
| 📊 **Order History & Tracking** | Students can view past orders and live status (pending → processing → completed) |
| 🖥️ **Admin Dashboard** | Print shop staff review, accept, or reject incoming orders |
| 📧 **Automated Email Notifications** | Students are emailed when their order is accepted, rejected, or ready |
| 📈 **Daily Collection Report** | Admin view of orders collected each day |

---

## 🏗️ System Architecture

```mermaid
flowchart LR
    subgraph Student["🎓 Student Portal"]
        SU["Signup / Login"]
        UP["Upload PDF +\nConfigure Print Options"]
        PAY["Razorpay Payment"]
        HIST["Order History"]
    end

    subgraph Backend["⚙️ PHP Backend"]
        AUTH["Session-based Auth"]
        FORM["combined_form.php\nFile handling + cost calc"]
        PAYAPI["payment.php\nRazorpay integration"]
    end

    subgraph DB["🗄️ MySQL Database"]
        SIGNUP[("signup table")]
        ORDERS[("combined_form table")]
        PAYMENTS[("payments table")]
        ACCEPTED[("accepted table")]
        REJECTED[("reject table")]
    end

    subgraph Admin["🖥️ Admin Dashboard"]
        DASH["admin_dashboard.php"]
        ACTION["process_action.php\nAccept / Reject"]
    end

    subgraph Notify["📧 Email Service"]
        SMTP["send_print_email.php"]
    end

    SU --> AUTH --> SIGNUP
    UP --> FORM --> ORDERS
    FORM --> PAY --> PAYAPI --> PAYMENTS
    PAYAPI --> ORDERS
    HIST --> ORDERS

    DASH --> ORDERS
    DASH --> ACTION
    ACTION -- "Accept" --> ACCEPTED
    ACTION -- "Reject" --> REJECTED
    ACTION --> SMTP
    SMTP -- "status update email" --> Student
```

---

## 🔄 End-to-End Order Flow

```mermaid
flowchart TD
    A(["🎓 Student Logs In"]) --> B["📤 Upload PDF(s) +\nSet Print Preferences"]
    B --> C["combined_form.php\nSaves order as 'pending'"]
    C --> D["💰 Cost Calculated\n(pages × copies × rate)"]
    D --> E["💳 Razorpay Checkout"]
    E --> F{"Payment\nSuccessful?"}
    F -- "No" --> G["❌ payment_failed.php"]
    G --> E
    F -- "Yes" --> H["✅ payment_success.php\nOrder confirmed"]
    H --> I["📊 Order visible in\nHistory + Order Details"]

    I --> J["🖥️ Admin Dashboard\nReviews Order"]
    J --> K{"Admin\nDecision"}
    K -- "Accept" --> L["Move to 'accepted' table\nStatus: Accepted"]
    K -- "Reject" --> M["Move to 'reject' table\nStatus: Rejected"]

    L --> N["📧 Email: Order Accepted"]
    M --> O["📧 Email: Order Rejected"]

    N --> P["🖨️ Print Shop Fulfills Order"]
    P --> Q["📥 Student Collects Printout"]
    Q --> R["📈 Logged in Daily Collection Report"]
```

---

## 🗂️ Project Structure

```
Student-Print-Service/
├── login.php                 # Student login
├── signup.php                # Student registration (roll number based)
├── home.php                  # Student landing page
├── combined_form.php         # Print order form — upload + preferences + cost
├── combined_form.html        # Static version of the order form
├── payment.php               # Razorpay checkout + order/payment DB writes
├── payment_success.php       # Post-payment confirmation handler
├── payment_failed.php        # Failed payment handler
├── razorpay_webhook.php      # Razorpay server-side webhook listener
├── order.php                 # Individual order detail view
├── history.php               # Student's order history
├── student.php                # Student dashboard
├── download_file.php         # Secure file download handler
├── contactus.php             # Contact form
├── connection.php            # Root-level DB connection
├── navbar.php / navbar.html  # Shared navigation
├── uploads/                  # Uploaded student PDF files
├── admin/
│   ├── admin_login.php       # Admin authentication
│   ├── admin_dashboard.php   # Main order review dashboard
│   ├── admin_accept.php      # View of accepted orders
│   ├── admin_reject.php      # View of rejected orders
│   ├── process_action.php    # Accept/Reject order logic + email trigger
│   ├── send_email.php / send_print_email.php  # Email notification senders
│   ├── daily_collection.php  # Daily print collection report
│   ├── view_order.php        # Detailed order view for admin
│   ├── connection.php        # Admin-side DB connection
│   └── vendor/                # Composer dependencies (PHPMailer etc.)
└── FeedBacks/                 # Collected student feedback screenshots
```

---

## 🧩 Tech Stack

**Backend:** PHP (procedural), MySQLi (prepared statements)
**Database:** MySQL
**Frontend:** HTML5, CSS3, Bootstrap 5, vanilla JavaScript
**Payments:** Razorpay Checkout + Webhooks
**Email:** PHPMailer / SMTP (via Composer, see `admin/vendor`)
**Auth:** PHP Sessions + `password_hash()` / `password_verify()`

---

## 🔌 Core Pages & Responsibilities

| File | Role |
|---|---|
| `signup.php` / `login.php` | Student authentication (roll number + password) |
| `combined_form.php` | Handles PDF upload, print preferences, and creates the order record |
| `payment.php` | Calculates final cost, creates Razorpay order, records payment |
| `payment_success.php` / `payment_failed.php` | Post-payment redirect handlers |
| `razorpay_webhook.php` | Server-side payment verification via Razorpay webhook |
| `order.php` / `history.php` | Student-facing order tracking |
| `admin/admin_dashboard.php` | Lists all pending orders for review |
| `admin/process_action.php` | Accepts/rejects an order, moves it to `accepted`/`reject` table, and triggers email |
| `admin/daily_collection.php` | Report of orders collected on a given day |

---

## ⚙️ Getting Started

### Prerequisites
- PHP 7.4+ with `mysqli` extension enabled
- MySQL Server
- Composer (for email dependencies in `admin/vendor`)
- A Razorpay account (test/live API keys)

### 1. Clone the repository
```bash
git clone https://github.com/Soorya-SS-01/Student-Print-Service.git
cd Student-Print-Service
```

### 2. Set up the database
Create a MySQL database named `mydatabase`. Core tables used by the app include:
```sql
CREATE TABLE signup (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(20) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
);

CREATE TABLE combined_form (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    username VARCHAR(50),
    copies INT DEFAULT 1,
    pages VARCHAR(20),
    custom_pages VARCHAR(100),
    color TINYINT(1) DEFAULT 0,
    orientation VARCHAR(20),
    sides VARCHAR(20),
    paper_type VARCHAR(20),
    message TEXT,
    files TEXT,
    total_pages INT,
    total_cost DECIMAL(10,2),
    order_id VARCHAR(255),
    status VARCHAR(20) DEFAULT 'pending',
    created_at DATETIME,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    order_id VARCHAR(255),
    payment_id VARCHAR(255),
    amount DECIMAL(10,2),
    pages INT,
    copies INT,
    paper_type VARCHAR(20),
    orientation VARCHAR(20),
    sides VARCHAR(20),
    color TINYINT(1),
    page_option VARCHAR(20)
);

-- 'accepted' and 'reject' tables mirror combined_form's structure
-- with added accepted_at / rejected_at timestamps and payment_status.
```

### 3. Configure database credentials
Update the database connection details (`servername`, `username`, `password`, `database`) in `connection.php` and `admin/connection.php` to match your local MySQL setup.

### 4. Configure Razorpay & email
Add your Razorpay API keys in `payment.php` / `razorpay_webhook.php`, and your SMTP/email credentials in `admin/send_email.php` and `admin/send_print_email.php`.

### 5. Install PHP dependencies
```bash
cd admin
php composer.phar install
```

### 6. Run locally
Serve the project root through a local PHP environment (e.g. XAMPP/WAMP or the built-in PHP server):
```bash
php -S localhost:8000
```
Visit `http://localhost:8000/login.php` to get started.

---

## 👥 Team — Software Development Cell

- Abinaya Devadarshini D — 22CSE
- Sangamithra Saravanan — 22CSE
- **Soorya S S** — 23CSBS
- Karthika S — 22CSE
- Madhumitha — 22CSE
- Hemanth R — 23CSBS

<div align="center">

[GitHub](https://github.com/Soorya-SS-01) · [LinkedIn](https://www.linkedin.com/in/soorya-s-s-364839370)

</div>

<img src="https://capsule-render.vercel.app/api?type=waving&color=gradient&customColorList=6,11,20&height=100&section=footer&animation=fadeIn" width="100%"/>
