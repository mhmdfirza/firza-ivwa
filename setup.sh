#!/bin/bash

# ======================================
# IVWA Setup Script
# Automated installation for IVWA
# ======================================

echo "╔══════════════════════════════════════════════════════════════╗"
echo "║                                                              ║"
echo "║  IVWA - Intentional Vulnerable Web Application              ║"
echo "║  Installation Script                                        ║"
echo "║                                                              ║"
echo "╚══════════════════════════════════════════════════════════════╝"
echo ""

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Check if running as root (recommended for some operations)
# if [ "$EUID" -ne 0 ]; then 
#    echo -e "${YELLOW}Warning: Running as non-root. Some operations might fail.${NC}"
# fi

echo -e "${BLUE}[*] Checking prerequisites...${NC}"
echo ""

# Check PHP
if ! command -v php &> /dev/null; then
    echo -e "${RED}[!] PHP is not installed!${NC}"
    echo "Please install PHP and try again."
    exit 1
fi

PHP_VERSION=$(php -v | head -n 1)
echo -e "${GREEN}[✓] PHP found: $PHP_VERSION${NC}"

# Check MySQL/MariaDB
if ! command -v mysql &> /dev/null; then
    echo -e "${YELLOW}[!] MySQL/MariaDB client is not found${NC}"
    echo "    But this is optional if you import the database manually"
fi

# Check if web root exists
if [ ! -d "/opt/lampp/htdocs" ] && [ ! -d "/var/www/html" ]; then
    echo -e "${YELLOW}[!] Web root directory not found${NC}"
    echo "    Make sure you have a web server installed (Apache/Nginx)"
fi

echo ""
echo -e "${BLUE}[*] Configuration Setup${NC}"
echo ""

# Ask for database credentials
read -p "Enter MySQL host [localhost]: " DB_HOST
DB_HOST=${DB_HOST:-localhost}

read -p "Enter MySQL username [root]: " DB_USER
DB_USER=${DB_USER:-root}

read -sp "Enter MySQL password [empty]: " DB_PASS
echo ""

# Database name
DB_NAME="ivwa"

echo -e "${BLUE}[*] Creating database configuration...${NC}"

# Create or update db.php
cat > config/db.php << EOF
<?php
/**
 * Database Configuration
 * 
 * File ini berisi konfigurasi koneksi database
 * INTENTIONALLY VULNERABLE: Tidak menggunakan prepared statements di seluruh aplikasi
 */

// Database credentials
define('DB_HOST', '$DB_HOST');
define('DB_USER', '$DB_USER');
define('DB_PASS', '$DB_PASS');
define('DB_NAME', '$DB_NAME');

// Buat koneksi ke database
\$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Cek koneksi
if (\$conn->connect_error) {
    die("Connection failed: " . \$conn->connect_error);
}

// Set charset ke UTF-8
\$conn->set_charset("utf8mb4");

?>
EOF

echo -e "${GREEN}[✓] Database configuration created${NC}"

echo ""
echo -e "${BLUE}[*] Setting up database...${NC}"
echo ""

# Try to import database
if command -v mysql &> /dev/null; then
    echo "Importing database schema..."
    
    # Create database
    mysql -h "$DB_HOST" -u "$DB_USER" -p"$DB_PASS" << EOF
DROP DATABASE IF EXISTS $DB_NAME;
CREATE DATABASE $DB_NAME;
USE $DB_NAME;
EOF
    
    # Import schema
    if [ -f "database/init.sql" ]; then
        mysql -h "$DB_HOST" -u "$DB_USER" -p"$DB_PASS" "$DB_NAME" < database/init.sql
        
        if [ $? -eq 0 ]; then
            echo -e "${GREEN}[✓] Database imported successfully${NC}"
        else
            echo -e "${RED}[!] Failed to import database${NC}"
            echo "You can import manually using:"
            echo "  mysql -u $DB_USER -p < database/init.sql"
        fi
    else
        echo -e "${RED}[!] database/init.sql not found${NC}"
    fi
else
    echo -e "${YELLOW}[!] MySQL client not found. Skipping automatic database import.${NC}"
    echo ""
    echo "To import the database manually, run:"
    echo "  mysql -u $DB_USER -p < database/init.sql"
fi

echo ""
echo -e "${BLUE}[*] Setting permissions...${NC}"

# Set correct permissions
chmod 755 . 2>/dev/null
chmod 644 index.php login.php dashboard.php search.php logout.php 2>/dev/null
chmod 755 config/ database/ assets/ modules/ 2>/dev/null
chmod 644 config/db.php database/init.sql assets/style.css 2>/dev/null

echo -e "${GREEN}[✓] Permissions set${NC}"

echo ""
echo "╔══════════════════════════════════════════════════════════════╗"
echo "║                   SETUP COMPLETED!                           ║"
echo "╚══════════════════════════════════════════════════════════════╝"
echo ""

echo -e "${GREEN}Next steps:${NC}"
echo ""
echo "1. Make sure Apache/Nginx web server is running"
echo "2. Make sure MySQL/MariaDB database server is running"
echo ""
echo "3. Open your browser and navigate to:"
echo "   ${BLUE}http://localhost/myOwn-ivwa${NC}"
echo ""
echo "4. Use these credentials to login:"
echo "   ${BLUE}Username: admin${NC}"
echo "   ${BLUE}Password: password123${NC}"
echo ""
echo "5. Read ${BLUE}README.md${NC} for more information about the vulnerabilities"
echo ""

echo -e "${YELLOW}⚠️  IMPORTANT:${NC}"
echo "   This application is INTENTIONALLY VULNERABLE!"
echo "   Use only in local/offline environment!"
echo "   DO NOT deploy to production!"
echo ""
