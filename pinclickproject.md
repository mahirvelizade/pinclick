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
- Dark theme: `#0B1020` bg / `#27ff4b` accent (landing page), `#6D5DFB` default
- System font stack (no external fonts)

## Files
| File | Role |
|------|------|
| `config.php` | DB, helpers |
| `index.php` | Landing page (hero, clover images, green CTA) |
| `dashboard.php` | Demo list / CRUD |
| `editor.php` | Pin editor (areas, undo, delete) |
| `viewer.php` | **Self-contained** viewer (inline CSS+JS, tooltip area avoidance, border always visible) |
| `upload.php` | Screenshot upload |
| `embed.php` | iframe embed |
| `export.php` | ZIP export |
| `api/save_demo.php` | Save demo |
| `api/upload.php` | File upload handler |
| `api/track_click.php` | Click tracking |
| `api/track_view.php` | View tracking |
| `api/get_demo.php` | Get demo data |
| `api/save_step.php` | Save step |
| `assets/style.css` | Shared styles (landing, navbar, buttons, editor) |
| `assets/pin_left.png` | Hero left clover image |
| `assets/pin_right.png` | Hero right clover image |

## Database (`demos` table)
- `id`, `title`, `data` (JSON), `status` (draft/published), `created_at`, `updated_at`
- JSON structure: `{ title, steps: [{ image, pins: [{ x, y, title, text, action, url, areas: [] }] }], accent_color }`

## Key Behaviors
- Viewer fully self-contained (inline CSS+JS) → defeats InfinityFree CORB
- Pins = percentage positioning (`x`, `y`)
- Areas per pin (max 1 per pin), percentage positioning (`x, y, width, height`)
- `accent_color` → pin dots, play btn, title, step dots, btn-primary
- Tooltip avoids areas via 9-position × 3-gap trial
- Area 1px border always visible via `::before` pseudo-element
- Play overlay on first screen → click reveals everything
- Sequential pin reveal (next advances through pins, then steps)
- Finish → Replay (restart demo)
- No login/auth

## Editor Features
- Pin creation (click on image)
- Area creation (drag on image, pin must be selected)
- Max 1 area per pin (new replaces old, drawing blocked if exists)
- Area resize (4 corner handles always visible)
- Area drag (reposition)
- Ctrl+Z / Cmd+Z undo (removes last added pin)
- Delete key: area selected → delete area, pin selected → delete pin
- Pin/Area mode toggle in navbar header
- Save on every change

## Landing Page (index.php)
- Hero with decorative clover images (pin_left.png / pin_right.png)
- Animated float on clovers
- Green `#27ff4b` CTA button (Get Started Free)
- Logo icon color: `#27ff4b`
- Footer with same branding

## Quick FTP Upload
```bash
curl -T file.php ftp://ftpupload.net/pinclick.site.je/htdocs/file.php --user if0_40570634:lbF5B7s8ueLHx
```

## Quick Git
```bash
git add -A && git commit -m "message" && git push
```
