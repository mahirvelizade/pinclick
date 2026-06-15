# PinClick Project Context

## Live Site
- URL: `http://pinclick.site.je/`
- Hosting: InfinityFree
- Subdomain DNS: `185.27.134.155`

## FTP (file upload)
- Host: `ftpupload.net`
- User: `if0_40570634`
- Pass: `lbF5B7s8ueLHx`
- Base path: `/pinclick.site.je/htdocs/`
- Upload individual files via Python ftplib or any FTP client

## GitHub
- Repo: `https://github.com/mahirvelizade/pinclick.git`
- Branch: `main`
- Deploy flow: `git push` + FTP upload (no CI/CD)

## Tech Stack
- PHP 8+ (no frameworks, no Composer)
- MySQL (`if0_40570634_pinclick` on InfinityFree)
- Vanilla JS (no build step, no npm)
- System font stack (no Google Fonts)
- Dark theme: `#0B1020` bg, `#6D5DFB` primary

## Architecture
```
config.php          - DB connection, helper functions (get_demo, create_demo, etc.)
index.php           - Landing page with hero, features, CTA
dashboard.php       - Demo list, create/publish/delete
editor.php          - Step manager, pin placement, drag, tooltip editor
viewer.php          - Self-contained viewer (CSS + JS inline), play overlay, auto tooltip
upload.php          - Screenshot upload page
embed.php           - Embeddable iframe version
export.php          - ZIP export
api/
  save_demo.php     - Save/update demo data
  upload.php        - File upload handler
  track_view.php    - View tracking
  track_click.php   - Pin click tracking
assets/style.css    - Shared styles (editor, dashboard, landing)
uploads/            - Screenshot images
```

## Data Model
`demos` table:
- `id` INT AUTO_INCREMENT
- `title` VARCHAR
- `data` TEXT (JSON) — `{ title, steps: [{ image, pins: [{ x, y, title, text, action, url }] }], accent_color }`
- `status` ENUM('draft','published')
- `created_at`, `updated_at` DATETIME

## Key Design Decisions
- Viewer is fully self-contained (all CSS/JS inline) to defeat InfinityFree browser check CORB
- Pins use percentage positioning
- Accent color (`accent_color`) controls pin dots, play button, title, step indicators, btn-primary
- Play overlay covers first screen; pins/tooltips hidden until "▶" clicked
- Tooltip auto-opens on first pin of each step (after play)
- No login system

## Recent Changes
- Play triangle SVG (40px, pulse animation)
- Accent color picker in editor (pin + play + title + step dots + btn-primary)
- Tooltip auto-open on first pin per step
- Tooltip arrow removed
- Tooltip buttons: text only, no emojis
- `save_demo.php` saves `accent_color`

## Quick Commands
```bash
# Upload single file
python3 -c "
import ftplib
f=ftplib.FTP('ftpupload.net','if0_40570634','lbF5B7s8ueLHx')
with open('file.php','rb') as fp: f.storbinary('STOR /pinclick.site.je/htdocs/file.php',fp)
f.quit()
"

# Commit & push
git add -A && git commit -m "message" && git push
```
