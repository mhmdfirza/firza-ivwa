# 🚀 IVWA - Quick Start Guide

Panduan cepat untuk setup dan mulai menggunakan IVWA dalam 5 menit!

---

## ⚡ 5-Minute Setup

### Langkah 1: Pastikan Prerequisites
```bash
# Check PHP
php -v

# Check MySQL
mysql --version
```

### Langkah 2: Setup Database (Pilih salah satu)

**Opsi A: Command Line**
```bash
mysql -u root -p < database/init.sql
```

**Opsi B: Automatic Script**
```bash
chmod +x setup.sh
./setup.sh
```

### Langkah 3: Start Web Server

**XAMPP/LAMPP:**
```bash
/opt/lampp/lampp start
# atau buka XAMPP Control Panel dan klik Start
```

**Apache:**
```bash
sudo systemctl start apache2
sudo systemctl start mysql
```

### Langkah 4: Buka di Browser

```
http://localhost/myOwn-ivwa
```

### Langkah 5: Login

```
Username: admin
Password: password123
```

---

## 🧪 Quick Test Vulnerabilities

### Test 1: SQL Injection (3 detik)

1. Go to Login page
2. Enter username: `admin' OR '1'='1`
3. Password: anything
4. Click Login - Anda akan berhasil login!

---

### Test 2: Stored XSS (1 menit)

1. Login (gunakan admin)
2. Go to Dashboard
3. Di comment form, paste:
   ```html
   <img src=x onerror="alert('XSS Vulnerability!')">
   ```
4. Click "Post Comment"
5. Refresh halaman - alert akan muncul!

---

### Test 3: Reflected XSS (30 detik)

1. Login (gunakan admin)
2. Go to Search page
3. Copy-paste URL ini ke address bar:
   ```
   http://localhost/myOwn-ivwa/search.php?q=<img src=x onerror="alert('Reflected XSS')">
   ```
4. Press Enter - alert akan muncul!

---

## 📁 Important Files

| File | Purpose |
|------|---------|
| `README.md` | Dokumentasi lengkap |
| `INSTALLATION.md` | Panduan instalasi detail |
| `PAYLOADS.md` | Kumpulan payload & testing |
| `config/db.php` | Database configuration |
| `database/init.sql` | Database schema |
| `login.php` | SQL Injection vulnerability |
| `dashboard.php` | Stored XSS vulnerability |
| `search.php` | Reflected XSS vulnerability |

---

## 🔑 Demo Accounts

| User | Password | Purpose |
|------|----------|---------|
| admin | password123 | Admin account |
| user1 | password123 | Regular user |
| user2 | password123 | Regular user |
| hacker | password123 | For testing |

---

## 🎯 Learning Path

### Day 1: SQL Injection
- [ ] Read `README.md` - SQL Injection section
- [ ] Examine `login.php` code
- [ ] Try payloads di `PAYLOADS.md`
- [ ] Understand secure fix di `assets/secure-examples.php`

### Day 2: Stored XSS
- [ ] Read `README.md` - Stored XSS section
- [ ] Examine `dashboard.php` code
- [ ] Try payloads di `PAYLOADS.md`
- [ ] Understand secure fix

### Day 3: Reflected XSS
- [ ] Read `README.md` - Reflected XSS section
- [ ] Examine `search.php` code
- [ ] Try payloads di `PAYLOADS.md`
- [ ] Understand secure fix

---

## ❌ Troubleshooting

### "Connection refused" error?
```bash
# Start MySQL
sudo systemctl start mysql
# atau
/opt/lampp/lampp restart
```

### "Database doesn't exist" error?
```bash
# Import database
mysql -u root < database/init.sql

# Verify
mysql -u root -e "SHOW DATABASES;" | grep ivwa
```

### Cannot access application?
1. Check if Apache running: `systemctl status apache2`
2. Check if file di correct location: `/var/www/html/myOwn-ivwa/`
3. Restart Apache: `sudo systemctl restart apache2`

---

## 🛠️ Useful Commands

```bash
# View database
mysql -u root ivwa
> SELECT * FROM users;
> SELECT * FROM comments;

# Test SQL Injection via curl
curl -X POST "http://localhost/myOwn-ivwa/login.php" \
    -d "username=admin' OR '1'='1&password=test" -L

# View logs
tail -f /var/log/apache2/error.log
```

---

## 📚 Next Steps

1. **Deep Dive into Vulnerabilities**
   - Read README.md completely
   - Read INSTALLATION.md
   - Experiment with PAYLOADS.md

2. **Advanced Testing**
   - Install Burp Suite
   - Install OWASP ZAP
   - Try SQLMap

3. **Fix Vulnerabilities**
   - Review `assets/secure-examples.php`
   - Create secure version
   - Compare dengan vulnerable version

4. **Learn More**
   - OWASP Top 10
   - PortSwigger Web Security Academy
   - HackTheBox
   - TryHackMe

---

## 💡 Tips & Tricks

### Browser Developer Tools (F12)
```javascript
// Check for sensitive data
console.log(document.cookie);
console.log(localStorage);
console.log(sessionStorage);
```

### Network Tab
- Monitor requests
- Check response headers
- Find API endpoints

### Console
- Execute JavaScript
- Test payloads
- Monitor network calls

---

## ⚠️ Keep in Mind

✅ This is INTENTIONALLY VULNERABLE
✅ For LEARNING PURPOSES ONLY
✅ Use in OFFLINE/LOCAL ENVIRONMENT

❌ DO NOT deploy to production
❌ DO NOT use on other people's systems without permission
❌ DO NOT use for illegal purposes

---

## 🚀 Ready to Start?

1. Ensure prerequisites installed
2. Run setup.sh or manual setup
3. Open http://localhost/myOwn-ivwa
4. Login with admin/password123
5. Try first payload!

---

## 💬 Common Questions

**Q: Bisakah saya reset password?**
```sql
mysql -u root ivwa
UPDATE users SET password='newpass' WHERE username='admin';
```

**Q: Bagaimana menambah user baru?**
```sql
INSERT INTO users (username, password, email, full_name) VALUES 
('newuser', 'password123', 'new@example.com', 'New User');
```

**Q: Apakah saya bisa modify kode?**
Ya! Silakan modify sesuai kebutuhan pembelajaran Anda.

---

## 📞 Need Help?

1. Check README.md
2. Check INSTALLATION.md
3. Check PAYLOADS.md
4. Google the error
5. Ask on Stack Overflow

---

Happy Learning! 🎓

Created for educational purposes only.
