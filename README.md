# Logos CMS

A minimalist, high-performance PHP blog engine styled after Aegea.
Designed for simplicity ("User Friendly") and architectural purity ("Clean Code").

## ✨ Features

-   **Zero Configuration**: Open the site, filling the installer form, and you are ready.
-   **Modern Core**: PHP 8.0+, Composer, PSR-4 Autoloading.
-   **MVC Architecture**: Strict separation of Controllers, Services, and Views.
-   **Aegea Design**: Minimalist visual style with a focus on typography.
-   **Markdown Native**: All posts are stored and rendered using CommonMark.
-   **Secure**: PDO Singleton, Prepared Statements, XSS protection.

## 🚀 Installation

1.  **Deploy**:
    Copy all files to your web server root.

2.  **Dependencies**:
    Run in your terminal:
    ```bash
    composer install
    ```

3.  **Install**:
    Open your website in a browser (e.g., `http://localhost`).
    You will be automatically redirected to the **Installer**.
    
    *Enter your database credentials and create an admin account.*

## 📂 Project Structure

```text
.
├── assets/                  # Public assets (CSS, JS, Fonts)
├── src/                     # Application Core (Classes)
│   ├── Config/              # Database Singleton
│   ├── Controllers/         # HTTP Logic (Home, Post)
│   ├── Services/            # Helpers (Render, Auth)
│   └── admin/               # Legacy Admin files (Refactoring in progress)
├── storage/                 # Data (SQL dumps, Uploads)
├── templates/               # HTML Views
├── vendor/                  # Composer Dependencies
├── index.php                # Main Router (Bramus)
├── install.php              # One-Click Installer
└── composer.json            # Project definition
```

## � Tech Stack

-   **Router**: `bramus/router`
-   **Markdown**: `league/commonmark`
-   **Frontend**: `simple.css` + Custom Overrides
-   **Database**: MySQL / MariaDB

## 👤 Admin Access

After installation, go to `/admin` to manage your posts and settings.
