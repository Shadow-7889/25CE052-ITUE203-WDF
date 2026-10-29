# Web Development Practicals

Lab work for a web development course, kept as one self-contained folder per practical (`Practical-1` to `Practical-14`). Early folders are static HTML, CSS and JavaScript pages. Later folders add PHP and MySQL, and the last ones build up the features of a complete student portal.

Each folder can be opened and run on its own.

---

## Technologies

- HTML5, CSS3, JavaScript (ES6)
- PHP 8 with MySQLi
- MySQL / MariaDB
- Apache (WAMP or XAMPP)
- Git and GitHub

---

## Repository Layout

```
.
├── Practical-1/
├── Practical-2/
├── ...
└── Practical-14/
```

Typical contents of a folder:

| Item | Purpose |
|---|---|
| `index.html` or `index.php` | Entry page |
| `*.css`, `*.js` or `assets/` | Styles and scripts |
| `includes/` | Shared PHP files (database connection, header, footer, authentication) |
| `sql/` | Database script, where the practical uses a database |
| `README.md` | Run instructions for that folder, where present |

---

## How to Run

### Static pages (HTML, CSS, JavaScript only)
Open the folder's `index.html` in a browser, or use the VS Code Live Server extension.

Pages that load JSON with the Fetch API need a local server, so open them through `http://localhost/...` or Live Server rather than by double-clicking the file.

### PHP and MySQL practicals
1. Install **WAMP** or **XAMPP**, then start **Apache** and **MySQL**.
2. Copy the practical folder into your server folder:
   - WAMP: `www\`
   - XAMPP: `htdocs\`
3. Open **phpMyAdmin** (`http://localhost/phpmyadmin`) and import the folder's `.sql` file, where one is provided.
   > The provided script creates a database named `studenthub`. If one with that name exists, it is replaced.
4. Open the folder in your browser, for example:
   ```
   http://localhost/Practical-10/
   ```
5. If your MySQL user or password is not the default (`root` with an empty password), edit `includes/db.php` in that folder.

### Demo accounts
For the folders that include a login, the SQL script creates two sample accounts:

| Role | Email | Password |
|---|---|---|
| Admin | admin@studenthub.local | Admin@123 |
| Student | student@studenthub.local | Student@123 |

These are for local testing only.

---

## Notes

- Several practicals share the same database (`studenthub`). Importing a folder's SQL script resets that database, so import once and reuse it.
- All PHP practicals keep the browser session on `localhost`, so a login made in one folder also applies to others. Log out before testing another folder if you see unexpected redirects.
- Folder names with spaces or different capitalization are supported, because each PHP practical detects its own URL.

---

## License

Created for educational purposes as part of university coursework.
