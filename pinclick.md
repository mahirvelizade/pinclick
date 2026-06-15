# PinClick

Interactive screenshot demo builder (Storylane-style). Formerly DemoFlow AI.

## Live Site

- **URL:** http://pinclick.site.je/
- **Hosting:** InfinityFree (PHP 8+, MySQL)
- **Source:** https://github.com/mahirvelizade/pinclick

## Features

- Upload screenshots (PNG, JPG, WEBP)
- Click on images to place pins (hotspots) with percentage positioning
- Drag pins to reposition
- Edit tooltip text and actions (next / previous / external URL)
- Step management (add/delete/reorder)
- Public demo viewer with responsive design
- iframe embed system
- ZIP export for self-hosting
- View and click analytics tracking
- No login required

## Tech Stack

- **Backend:** PHP 8+ (vanilla, no frameworks)
- **Database:** MySQL with JSON data storage
- **Frontend:** Vanilla JavaScript, CSS with glassmorphism design
- **No dependencies, no build step, no Composer**

## File Structure

```
├── .htaccess
├── config.php                  # DB connection + helpers
├── database.sql                # MySQL schema
├── index.php                   # Landing page
├── dashboard.php               # Demo list
├── editor.php                  # Demo builder (core)
├── viewer.php                  # Public demo player
├── upload.php                  # Image upload page
├── export.php                  # ZIP export
├── embed.php                   # iframe embed code
├── api/
│   ├── get_demo.php
│   ├── save_demo.php
│   ├── save_step.php
│   ├── upload.php
│   ├── track_view.php
│   └── track_click.php
├── assets/
│   ├── style.css
│   └── viewer.js
└── uploads/
```

## Database

Tables: `demos`, `analytics_views`, `analytics_clicks`, `uploads`

Demo data stored as JSON in `demos.data` column.

## InfinityFree Setup

- **MySQL Host:** sql100.infinityfree.com
- **Database:** if0_40570634_pinclick
- **User:** if0_40570634
- **Subdomain:** pinclick.site.je
- **Document root:** `/pinclick.site.je/htdocs/`

## Deployment

1. Upload files to subdomain's `htdocs` directory via FTP
2. Import `database.sql` via phpMyAdmin
3. Edit `config.php` with DB credentials
4. Site is ready
