# StudentHub Test Report (Practical 15)

| # | Module | Test | Expected | Result |
|---|--------|------|----------|--------|
| 1 | Navigation | Click every menu link | All pages open, none 404 | |
| 2 | Responsive | Resize to mobile width | Hamburger menu appears, layout stacks | |
| 3 | Theme | Click Dark, refresh | Dark theme stays | |
| 4 | Register | Submit empty / weak password | Inline errors shown | |
| 5 | Register | Valid data | Success message, redirect to login | |
| 6 | Register | Same email again | "already registered" error | |
| 7 | Login | Wrong password | "Invalid email or password" | |
| 8 | Login | Student account | Redirect to dashboard | |
| 9 | Login | Admin account | Redirect to admin panel | |
| 10 | Access | Student opens /admin/students.php | 403 Access Denied | |
| 11 | Access | Logged out opens /dashboard.php | Redirect to login | |
| 12 | Session | Stay idle 30 minutes | Logged out with message | |
| 13 | Events | Student registers for an event | Success, appears on dashboard | |
| 14 | Events | Register for same event twice | "already registered" | |
| 15 | Students CRUD | Add, edit, search, filter, delete | Each works with messages | |
| 16 | Events CRUD | Add event with valid poster | Poster shown | |
| 17 | Events CRUD | Upload .pdf or file over 2 MB | Rejected with message | |
| 18 | AJAX | Add/edit/delete without reload | Table updates, no page refresh | |
| 19 | Dashboard | Counts and audit log | Values match database | |
| 20 | JSON Explorer | Search, filter, sort, paginate | Results update | |
| 21 | Contact/Feedback | Submit valid and invalid data | CSV saved / error shown | |
