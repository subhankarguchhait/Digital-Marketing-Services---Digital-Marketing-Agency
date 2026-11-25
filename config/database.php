<?php
/**
 * Database credentials.
 *
 * XAMPP / MAMP (local):   host localhost, user root, password '' (XAMPP) or 'root' (MAMP, port 8889).
 * cPanel (live):          use the database, user and password you created in cPanel → MySQL Databases.
 *                         cPanel prefixes names, e.g. "myaccount_dreamspark" / "myaccount_dsuser".
 *
 * This folder is blocked from the web by /config/.htaccess and the root .htaccess.
 * Never commit real production passwords to a public repository.
 */
return [
    'driver'  => getenv('DS_DB_DRIVER') ?: 'mysql',   // 'sqlite' is used only by the automated test suite
    'host'    => 'localhost',
    'port'    => 3306,
    'name'    => 'dream_spark',
    'user'    => 'root',
    'pass'    => 'root',
    'charset' => 'utf8mb4',
    'sqlite_path' => getenv('DS_SQLITE_PATH') ?: '',
];
