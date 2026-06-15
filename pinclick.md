# PinClick Changes

## Root Cause: CORB (Cross-Origin Read Blocking)

The InfinityFree browser check (`aes.js`) was intercepting requests and returning HTML instead of the expected resources. When `assets/viewer.js` was loaded via `<script src="...">`, the browser check returned HTML, which triggered CORB because the MIME type mismatch. This prevented `initViewer()` from ever running, so pins never rendered.

## Fixes Applied

### 1. viewer.php — Fully Self-Contained
- Inline all JavaScript (moved from `assets/viewer.js`)
- Inline all CSS (viewer-specific styles, base variables)
- Removed `<link href="...">` to Google Fonts
- Uses system font stack: `-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif`
- Image loading uses absolute URLs first, falls back to relative
- `onerror` retry logic for failed image loads
- No external script/CSS dependencies = no CORB risk

### 2. Removed Google Fonts (All Pages)
- Removed from `dashboard.php`, `editor.php`, `embed.php`, `upload.php`, `index.php`, `export.php`
- System font stack in `style.css` replaces Inter
- Eliminates the only cross-origin request on every page

### 3. Emoji Icons (All Pages)
- `dashboard.php`: Replaced all SVG icon buttons with emojis:
  - ✏️ Edit, 👁️ View, 🔗 Embed, 📦 Export, 🗑️ Delete
  - 👁️ views, 📅 date
  - 🎯 empty state, 🚀 Get Started, ✨ New Demo
  - 📊 My Demos header
- `viewer.php`: 📦 Embed, 🔄 Restart, ⬅️ Previous, Next ➡️, ✅ Finish
- `export.php`: ⬅️ Previous, Next ➡️

### 4. UI/UX Improvements
- Glass card hover animation: `translateY(-2px)` + glow shadow
- Pin drop animation: pins bounce in with staggered delay
- Pulse animation enhanced: pin dots scale to 1.1x on pulse
- Smoother transitions on all interactive elements

### 5. Cleanup
- Deleted `assets/viewer.js` from server (no longer needed)
- Deleted debug files (`test_debug.php`, `test_raw.php`, `debug.php`)
- Removed cache-busting `?v=` query params (not needed with inline)

## Files Changed
| File | Change |
|------|--------|
| `viewer.php` | Complete rewrite — inline JS + CSS, no external deps |
| `dashboard.php` | Removed Google Fonts, SVGs → emojis, improved copy |
| `editor.php` | Removed Google Fonts |
| `embed.php` | Removed Google Fonts |
| `upload.php` | Removed Google Fonts |
| `index.php` | Removed Google Fonts |
| `export.php` | Removed Google Fonts, emoji arrows |
| `assets/style.css` | System font stack, glass card hover, pin drop animation |
| `assets/viewer.js` | Removed from server (code inline in viewer.php) |
