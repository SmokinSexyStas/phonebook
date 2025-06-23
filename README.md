#### Description
The APP provides functionality for managing user contacts.

##### Steps to start
1) Go to APP directory:
```bash
cd phonebook 
```

2) Add variables from `.env.example` to your local `.env` file.
```bash
cp .env.example .env
```

3) Run the following command to install the required PHP dependencies via Composer:
```bash
composer install
```

4) Run the "init.sql" script from "/database" directory to set up the database

5) Go to public directory
```bash
cd public
```

6) Run command to start local server
```bash
php -S localhost:8000
```

Check endpoint http://localhost:8000