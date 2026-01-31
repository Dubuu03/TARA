# TARA

TARA is a multi-page PHP website showcasing Philippine travel content — tourist attractions, festivals, local foods, services and an admin dashboard for managing tours, hotels, cars, users and ratings.

Features
- Multi-page site with dedicated pages for attractions, festivals, foods, services, and about/contact sections
- Admin panel (admin/) for managing tours, hotels, car rentals, users and ratings
- Responsive layouts and components (header, footer, cards, modals)
- Static assets organized under `public/` (images, icons, etc.)
- Simple PHP templates per page (no heavy framework)

Quick start (local)
1. Requirements
   - PHP 7.4+ (or your preferred PHP runtime)
   - Optional: MySQL / MariaDB if you enable admin persistence

2. Run with PHP built-in server (development)
```bash
# from the repository root
php -S localhost:8000
# then open http://localhost:8000 in your browser
```

3. Or deploy with a LAMP/AMP stack (XAMPP, MAMP, etc.)
   - Place the repo folder into your web server's document root.
   - If admin features use a database, create a database and update connection settings in your admin/config files (if present).

Project structure (high level)
- index.php — main homepage
- about/, bohol/, siargao/, etc. — content pages for destinations, foods, festivals
- admin/ — admin dashboard pages (tours.php, hotels.php, users.php, ratings.php, ...)
- public/ — images and static assets
- other page folders (adobo/, lechon/, tubbataha/, etc.) — specific content pages

Notes
- No database connection file is required to view static pages; admin features may require DB setup.
- No LICENSE file included — add one if you want to publish the project.
```
