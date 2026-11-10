# Laboratory Equipment Borrowing Logging System

Complete four-table practice solution based on the generated Skills Test rubric.
It is a local PHP + MySQL project, adapted from your uploaded CRUD ZIP.

## What is included?

- Local Admin login and logout
- Main index/menu dashboard
- Students: Add, Search, View, Edit, Delete
- Equipment: Add, Search, View, Edit, Delete
- Laboratory Staff: Add, Search, View, Edit, Delete
- Borrowing Transactions: Add, Search, View, Edit, Delete
- Five inquiry screens with SQL aggregate COUNT(*) plus matching rows
- Database SQL with four tables, three foreign keys, and sample records
- Input validation, parameterized queries, and POST-only deletion
- Simple welcome page with module cards, plus basic tables and forms

## 1. Install and run using XAMPP (Windows)

1. Install XAMPP if needed. It should include Apache, MySQL/MariaDB, PHP, and phpMyAdmin.
2. Extract the ZIP and put the lab-equipment-borrowing folder inside C:\xampp\htdocs\.
3. Open XAMPP Control Panel and start **Apache** and **MySQL**. Both must say Running.
   If MySQL shuts down unexpectedly, fix it before proceeding. Do not delete your data folder.
4. In a browser, open http://localhost/phpmyadmin/.
5. Choose the **Import** tab, select database.sql from this project, and click Import/Go.
   The SQL file creates a database named **laboratory** with tables:
   student, equipment, laboratory_staff, borrowing.
6. Open config/database.php. XAMPP's usual MySQL credentials are root with blank password.
   If your password or port differs, update the connection details.
7. Open http://localhost/lab-equipment-borrowing/login.php.
8. Use the **local practice account**:

   - Username: localadmin
   - Password: Admin123!

9. Choose one of the four management cards, or click Inquiries in the menu.

Required software: PHP 8.1+, PDO MySQL extension, MySQL/MariaDB, and Apache.
A typical recent XAMPP installation includes these.

## 2. How the four tables connect

**Primary key (PK)** is a unique ID belonging to its own table.
**Foreign key (FK)** is an ID pointing to a row in another table.

| SQL table | Primary key | Main columns |
| --- | --- | --- |
| student | stuID | stuFName, stuLName, stuCourse, stuYear |
| equipment | equipID | equipName, equipCategory, equipBrand, equipRoom |
| laboratory_staff | staffID | staffFName, staffLName, staffRole, staffTelNo |
| borrowing | borrowID | stuID FK1, equipID FK2, staffID FK3, borrowDate, dueDate, returnDate, remarks |

Example Borrowing #1 has stuID=1 (Vaugn), equipID=1 (Arduino), and staffID=1 (Ana).
The Borrowing form uses dropdowns so you can choose existing related records.

Because these are real foreign keys, a Student, Equipment, or Staff row referenced
by a Borrowing row cannot be deleted directly. Delete or change its borrowing
transactions first. The interface will explain this when deletion is blocked.

## 3. Practice the CRUD operations

CRUD means Create, Read, Update, Delete.

1. Click Students > Add Student, complete the form, then Save.
2. Use Search to find that student by name, ID, course, or year.
3. Use View to display details; Edit to change and save them.
4. Use Delete to remove a record that is not referenced by borrowings.
5. Repeat with Equipment and Laboratory Staff.
6. Add a Borrowing transaction selecting one student, one equipment item,
   one staff member, borrowing date/time, due date, optional return date and remarks.

Every module follows the same structure. The ID is created automatically.

## 4. Five SQL COUNT inquiries

Open Inquiries and try:

1. Equipment category = Microcontroller (Arduino Uno should appear).
2. Student year range = 1 to 3.
3. Borrowings for Student ID 1.
4. Borrowings handled by Staff ID 2.
5. Borrowing date range = 2026-10-01 to 2026-10-31 (all five sample transactions).

Every inquiry displays the actual matching records **and** COUNT(*)
calculated using a separate prepared SQL aggregate query.
With untouched sample data, the expected counts in this order are **1, 3, 2, 2, 5**.

## 5. Project file guide

    lab-equipment-borrowing/
      database.sql
      README.md
      RUBRIC_CHECKLIST.md
      index.php             Main menu with five required modules
      login.php             Local Admin login
      logout.php            Logout
      config/
        database.php        MySQL connection
        auth.php            Login/session helpers
        app.php             Shared helper functions
        modules.php         Table/field definitions for the 4 modules
      assets/style.css      CSS
      includes/header.php   Common navigation
      includes/footer.php   Common footer
      crud/
        index.php           Search/list records
        form.php            Add/edit form
        save.php            INSERT/UPDATE
        view.php            Details
        delete.php          DELETE
      inquiries/index.php   5 reports, including COUNT(*)

The CRUD code is shared by all four tables instead of copying the same
seven pages four times. The four sets of fields are in config/modules.php.
This still provides the same functionality as your original ZIP.

## 6. Adapting to a different exam question

1. Write down the given tables, field names, types, PKs, and FKs.
2. Adjust database.sql for those exact SQL tables and keys.
3. Adjust config/modules.php:
   - table: exact SQL table name
   - pk: primary key
   - fields: input labels and types
   - search: fields to search
   - select input type: foreign key dropdown; modify its option SELECT query
4. Adjust inquiries/index.php with the new SELECT and COUNT queries.
5. Test adding master records first, then child/transaction records.

For a new schema, use a new, empty practice database or a proper ALTER TABLE.
CREATE TABLE IF NOT EXISTS does not modify the columns of an existing table.
If adding a new module, also update dashboard descriptions for best appearance.

## 7. Test checklist

- Login works, menu shows four modules.
- Add, search, view, edit, delete a Student.
- Add, search, view, edit, delete Equipment and Staff.
- Add a Borrowing with three valid foreign keys.
- View its three linked names.
- Deleting a referenced Student is blocked with a useful message.
- Delete a Borrowing, then remove the unreferenced Student.
- Run all five inquiries; COUNT equals the matching number of rows.
- Logout, then attempt to open a module directly; it redirects to login.

## 8. Troubleshooting

| Problem | Check |
| --- | --- |
| Site not found | Is Apache running? Correct folder in htdocs? |
| Database connection failed | MySQL running? Imported database.sql? Correct credentials? |
| could not find driver | Enable PHP pdo_mysql extension |
| Blank PHP page | See Apache/PHP error log for the real error |
| Can't delete master record | Borrowing references that record |
| Foreign key dropdown empty | Add a Student, Equipment, or Staff record first |
| Login or page gives 404 | Use actual folder URL; no hardcoded /Garcia/ path |
| No inquiry results | Check the date/ID filters and data in phpMyAdmin |

**Scope:** The rubric asks for borrowing logging, not stock quantities,
approval workflows, overdue penalties, or inventory availability rules.
Those extra business rules are intentionally not included.

**Security:** The admin credentials are hardcoded for *offline/local practice only*.
Do not expose this project publicly without proper hashed-password user accounts.
