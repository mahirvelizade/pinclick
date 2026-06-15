# PinClick — Full Project Reference

## Live
- **Site**: http://pinclick.site.je/
- **Hosting**: InfinityFree (browser check JS tələb edir)

## FTP
| Field | Value |
|-------|-------|
| Host | `ftpupload.net` |
| User | `if0_40570634` |
| Pass | `lbF5B7s8ueLHx` |
| Path | `/pinclick.site.je/htdocs/` |

## GitHub
- **Repo**: `https://github.com/mahirvelizade/pinclick.git`
- **Branch**: `main`

## Tech
- PHP 8+, MySQL, Vanilla JS, no frameworks/npm/Composer
- Dark theme: `#0B1020` bg / `#6D5DFB` primary
- System font stack (no external fonts)

## Files
| File | Role |
|------|------|
| `config.php` | DB, helpers |
| `index.php` | Landing page |
| `dashboard.php` | Demo list / CRUD |
| `editor.php` | Pin editor |
| `viewer.php` | **Self-contained** viewer (inline CSS+JS) |
| `upload.php` | Screenshot upload |
| `embed.php` | iframe embed |
| `export.php` | ZIP export |
| `api/save_demo.php` | Save demo |
| `api/upload.php` | File upload handler |
| `assets/style.css` | Shared styles |

## Database (`demos` table)
- `id`, `title`, `data` (JSON), `status` (draft/published), `created_at`, `updated_at`
- JSON structure: `{ title, steps: [{ image, pins: [{ x, y, title, text, action, url }] }], accent_color }`

## Key Behaviors
- Viewer fully self-contained (inline CSS+JS) → defeats InfinityFree CORB
- Pins = percentage positioning
- `accent_color` → pin dots, play btn, title, step dots, btn-primary
- Play overlay on first screen → click reveals everything
- First pin tooltip auto-opens per step (after play)
- No login/auth

## Quick FTP Upload
```bash
python3 -c "
import ftplib
f=ftplib.FTP('ftpupload.net','if0_40570634','lbF5B7s8ueLHx')
with open('file.php','rb') as fp: f.storbinary('STOR /pinclick.site.je/htdocs/file.php',fp)
f.quit()
"
```

## Quick Git
```bash
git add -A && git commit -m "message" && git push
```
