<div align="center">
  <img src="public/assets/images/logo.png" alt="Aura Logo" width="150" height="auto" />
  <h1>Aura PDP</h1>
  <p><em>Enterprise-Grade School Well-being & Anti-Bullying Management Platform</em></p>

  <p>
    <a href="https://php.net"><img src="https://img.shields.io/badge/PHP-8.2+-777BB4?logo=php&logoColor=white&style=for-the-badge" alt="PHP Version"></a>
    <a href="#"><img src="https://img.shields.io/badge/Security-Bank_Grade-red?style=for-the-badge&logo=security" alt="Security Level"></a>
    <a href="#"><img src="https://img.shields.io/badge/Architecture-Native_MVC-blueviolet?style=for-the-badge" alt="Architecture"></a>
    <a href="#"><img src="https://img.shields.io/badge/License-Proprietary-darkgreen?style=for-the-badge" alt="License"></a>
  </p>
</div>

---

Aura is a robust, highly secure reporting and management platform built specifically for educational institutions. It empowers students to safely report incidents (anonymously or identified) and equips school administration with legally-compliant, regional workflow automation.

## 🚀 Key Features

### 🛡️ Uncompromising Security
*   **Bank-Grade Hardening:** Strict IDOR (Insecure Direct Object Reference) prevention via cryptographic ownership verification (`findByIdWithDetails`).
*   **Multi-Factor Authentication (MFA):** Biometric WebAuthn (FaceID/TouchID) for students, and AES-256-GCM encrypted TOTP for staff.
*   **DDoS & Brute Force Mitigation:** Composite Rate Limiting (IP + Identifier) to prevent credential stuffing.
*   **Immutable Audit Trails:** Tamper-evident forensic logging of all system interactions (`audit_logs`).

### 🗺️ Regional Legal Protocols (State Machine)
Aura dynamically adapts its legal workflows based on regional directives (Comunidades Autónomas):
*   **Galicia (v2.23):** 6-phase legal state machine, 16 auto-generated PDF annexes, and integrated "Medidas Urxentes" modules.
*   **Aragón:** Enforces Anexo I-a tracking, team constitution, and specialized checklists.
*   **Murcia & C. Valenciana:** Custom workflows and strict compliance guards preventing out-of-order phase transitions.

### 🌐 Internationalization & Accessibility
*   **5 Native Languages:** Fully localized in Spanish, Català, Galego, Euskara, and English.
*   **Interactive Sociograms:** Built-in `Cytoscape.js` integration to map classroom relationships and detect vulnerabilities visually.

---

## 🏗️ System Architecture

Aura operates on a **Native MVC** (Model-View-Controller) architecture. By actively avoiding monolithic frameworks, Aura maintains a zero-bloat footprint, lightning-fast response times, and minimal dependency supply chain risks.

<details>
<summary><b>📂 Click to view Directory Structure</b></summary>

```text
aura/
├── app/
│   ├── Controllers/    # Endpoints & IDOR validation
│   ├── Core/           # Framework: Router, Auth, Middleware, AuditLogger
│   ├── Models/         # Parameterized SQLite PDO interactions
│   └── Views/          # PHP Templates with automatic XSS escaping
├── database/
│   ├── .htaccess       # Denies HTTP access to DB
│   └── aura.sqlite     # Portable persistence layer
├── public/             
│   ├── index.php       # Front Controller (Single Entry Point)
│   └── .htaccess       # Mod_rewrite rules
└── storage/            # Evidence and logs
```
</details>

---

## 💻 Quick Start & Deployment

Aura is designed for seamless on-premise installation, eliminating the need for complex external database daemons via its highly optimized SQLite 3.35+ engine.

### Prerequisites
*   Apache 2.4+ (with `mod_rewrite`)
*   PHP 8.2+ (`pdo_sqlite`, `sodium`, `mbstring`)
*   Composer 2.x

### Installation Steps

1. **Clone the Repository**
   ```bash
   git clone https://github.com/your-org/aura-pdp.git
   cd aura-pdp
   ```

2. **Install Dependencies**
   ```bash
   composer install --no-dev --optimize-autoloader
   ```

3. **Set Permissions**
   Grant the webserver write access to the persistence directories:
   ```bash
   chmod -R 775 storage database
   ```

4. **Web Server Configuration**
   > ⚠️ **CRITICAL:** The Apache `DocumentRoot` must point **EXCLUSIVELY** to the `public/` directory.

   ```apache
   <VirtualHost *:443>
       ServerName colegio-aura.com
       DocumentRoot /var/www/aura/public

       <Directory /var/www/aura/public>
           AllowOverride All
           Require all granted
       </Directory>
   </VirtualHost>
   ```

---

## 🔐 Administration & Initial Setup

Upon the first execution, the database will be auto-generated. Log in using the default administrative credentials provided by your deployment lead. 
**Immediate Post-Install Actions Required:**
1. Configure the `APP_KEY` environment variable.
2. Update the Admin Password and enable TOTP.
3. Configure the SMTP Gateway for automated notifications.

---

<div align="center">
  <b>© 2026 EmoTerraLab — Proyecto Aura (GIR)</b><br>
  <i>Confidencial y Propietario</i>
</div>