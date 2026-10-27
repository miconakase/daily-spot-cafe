# Local setup without Apache

Requires PHP 8.x (with `extension=mysqli` enabled in `php.ini`) and MySQL 8.x.
Credentials must match `src/config/database.php` (`root` / `admin123`).

```bash
winget install PHP.PHP.8.3
winget install Oracle.MySQL
# Then run "C:\Program Files\MySQL\MySQL Server 8.4\bin\mysql_configurator.exe"
# and set the root password to admin123.

mysql -u root -padmin123 < database/schema.sql
mysql -u root -padmin123 < database/seed.sql      # optional sample data
php database/create-admin.php admin <password>
```

Already have a database from an older `schema.sql`? Apply the migrations in order instead of re-running the schema:

```bash
mysql -u root -padmin123 < database/migrations/001_customer_flow.sql
mysql -u root -padmin123 < database/migrations/002_roles_and_qrph.sql
```

If the database was filled by older code (names showing `&amp;`), repair it once:

```bash
php database/fix-escaped-text.php           # preview
php database/fix-escaped-text.php --apply   # save
```

`mysql.exe` lives in `C:\Program Files\MySQL\MySQL Server 8.4\bin` if it is not on your PATH.

Serve from the folder *above* the project, since some redirects use the `/daily-spot-cafe/` prefix:

```bash
php -S localhost:8000 -t ..
```

- Site: http://localhost:8000/daily-spot-cafe/public/
- Admin: http://localhost:8000/daily-spot-cafe/admin/auth/login.php
