# Bus Ticketing System

## Features

(Note: Fares mentioned are not final and are subject to change accordingly. [Bus Ticketing Cashier] is the one who has control of the [Bus ticketing - Admin Control Panel])

### Client Side (Only for Students)

#### System Flow (Standard bus booking scenario)

1. **Book a Bus:**

   - Click on the 'Book a Bus' button.

2. **Choose Route:**

   - Select the destination route (e.g., North, South).

3. **Select Bus:**

   - Choose from the available buses.

4. **Ticket Fillup:**

   - Get redirected to the ticket fillup form for the chosen bus.
   - The ticket form displays the fare for the stop.
   - For air-conditioned buses, the fare includes an additional P10.00.

5. **Verification:**

   - Verify Student ID.
   - If verified, receive a [Bus Ticket Reference].
   - If not verified, cannot proceed to the next step.

   _Note: Having a [Bus Ticket Reference] doesn't guarantee a ride; payment is required._

6. **Payment:**

   - Proceed to [Bus Ticketing Cashier] for payment.
   - Use the [Bus Ticket Reference] during payment.

   _Note: After payment, [Bus Ticketing Cashier] will issue a [Bus Ride Ticket] for the student to use._

### Admin Control Panel

#### Bus Ticketing Cashier

The Bus Ticketing Cashier plays a crucial role in managing various aspects of the bus ticketing system. Here are the key responsibilities and features associated with the Bus Ticketing Cashier:

- **Overall Management:**

  - Manages buses, adds students, and views transaction history.

- **Ticket Generation:**

  - Can generate [Bus Ride Tickets] on-the-go for both students and non-students.
    - Note for students: Verification is needed for ticket issuance.
    - Note for non-students: Verification function can be toggled off.
  - Has the ability to issue [Bus Ride Tickets] for multiple passengers in a single transaction.

- **Booking Requests:**
  - Manages bus booking requests and processes payments.

\_Note: Ensure that proper security measures are in place for ticket generation and payment processing.

## Installation

Follow these steps to set up and run the project locally.

1. **Clone the Repository:**
   Open your terminal (PowerShell for Windows or Bash for Unix-like systems), navigate to your XAMPP `htdocs` folder, and run the following commands:

   ```bash
   cd path/to/xampp/htdocs
   git clone https://github.com/Rivera-C598/bus-ticketing-system.git
   cd bus-ticketing-system

   ```

2. Set up the database and configure connection settings.

## Database Setup

Follow these steps to set up the database for the Bus Ticketing System.

1. **Create Database in phpMyAdmin:**

   - Open phpMyAdmin and create a new database named `busticketing_db`.

2. **Import SQL File:**

   - In phpMyAdmin, select the newly created `busticketing_db`.
   - Go to the "Import" tab.
   - Choose the SQL file (`busticketing_db.sql`) provided.
   - Click "Go" to import the database structure and data.

3. **Update Database Configuration:**

   - Locate `database_config/db_config.php` file in the project directory.

   ```php
   <?php
   $db_host = 'localhost';
   $db_user = 'your_database_user';
   $db_pass = 'your_database_password';
   $db_name = 'busticketing_db';

   try {
       $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
       $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
   } catch (PDOException $e) {
       die("Database connection failed: " . $e->getMessage());
   }
   ```

   - Replace `'your_database_user'`, `'your_database_password'`, and `'your_database_name'` with your actual database credentials.

4. **Save the Configuration File:**

   - Save the changes in the `database_config/db_config.php` file.

5. **Verify Connection:**
   - Open your project in a web browser.
   - If configured correctly, project should now be connected to the MySQL database.

**Note:** Always keep sensitive information, such as database credentials, secure. Consider using environment variables or secure configuration methods in production environments.

3. Run the necessary scripts (if you added a new admin in database you can hash the password by entering php_scripts/hash_passwords.php in the url)




