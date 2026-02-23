# DVC Internship Assessment — Vikas Uniyal

**Position:** Web Development Intern  
**Company:** Digital Visibility Concepts  
**Repository:** `dvc-internship-assessment-vikasuniyal`

---

## 👤 Contact Information

- **Name:** Vikas Uniyal
- **Location:** Dehradun, Uttarakhand
- **Email:** [vikasuniyalcsa@gmail.com](mailto:vikasuniyalcsa@gmail.com)
- **Phone:** +91-8171474466
- **GitHub:** [github.com/virusvickee](https://github.com/virusvickee)
- **LinkedIn:** [linkedin.com/in/vikas-uniyal-](https://linkedin.com/in/vikas-uniyal-)

---

## 📁 Repository Structure

```
/
├── question1/
│   └── index.html              # Responsive Product Card Component
├── question2/
│   └── testimonials-plugin.php # WordPress Testimonials Plugin
├── question3/
│   └── weather-dashboard.html  # Weather Dashboard Application
└── README.md
```

---

## 📋 Question Summaries

### Question 1 — Responsive Product Card

**Approach:**  
Built a single self-contained HTML file using semantic HTML5, mobile-first CSS with custom properties, and vanilla JS.

- **Mobile-first** CSS with breakpoints at 768 px (tablet) and 1024 px (desktop)
- **Quantity selector** clamped to 1–10; `disabled` attribute applied at min/max boundaries automatically
- **Add to Cart** logs `{productName, quantity, unitPrice, total, addedAt}` to the browser console and triggers an animated slide-up success notification
- **Fallback image** handled via the `onerror` attribute — renders an inline SVG placeholder if the image URL fails
- Keyboard shortcuts (+ / − / Arrow keys) for quantity control; full `focus-visible` styling for accessibility

**Assumptions:**
- Product data is static/demo; no backend integration required
- A single representative product is displayed (not a product grid)

**Estimated time:** ~2 hours

---

### Question 2 — WordPress Testimonials Plugin

**Approach:**  
A fully standalone PHP plugin file following WordPress coding standards throughout.

- **Part A:** Registers a `dvc_testimonial` CPT with Dashicons `format-quote` icon, `show_in_rest: true` for Gutenberg, and supports `title`, `editor`, `thumbnail`, `revisions`
- **Part B:** Meta box with four fields (client name, position, company, rating 1–5). Data is saved through `save_post_dvc_testimonial` hook with nonce verification, `current_user_can` check, `sanitize_text_field`, and `absint` sanitization
- **Part C & D:** `[testimonials]` shortcode accepts `count`, `orderby`, `order` parameters. Output is a CSS-only glassmorphism-styled slider with Previous/Next buttons, dot indicators, and keyboard arrow navigation. Uses `ob_start`/`ob_get_clean` for clean separation of PHP and HTML
- **Security:** `wp_nonce_field` + `wp_verify_nonce`, capability checks, `esc_html__`, `esc_attr`, `esc_url`, `wp_kses` on all output

**Assumptions:**
- Plugin is installed as a single-file plugin (dropped in `wp-content/plugins/`)
- Testimonials are entered via the WordPress admin; no front-end submission form is required

**Estimated time:** ~3 hours

---

### Question 3 — Weather Dashboard

**Approach:**  
A single-file HTML app using vanilla JS (no frameworks), `async/await` with `Promise.all` for concurrent API requests, and a glassmorphism dark-mode UI.

- **Search:** Handles form submit and Enter key natively; empty-input guard prevents meaningless API calls
- **Current Weather:** Displays city + country, date, emoji icon (mapped from OWM icon codes), temperature in °C, feels-like, humidity, wind speed (km/h), pressure (hPa), and visibility (km)
- **5-Day Forecast:** Groups 3-hourly OWM forecast data by calendar day, picks the entry closest to noon, and renders 5 cards with day name, emoji icon, temp, and description
- **Loading / Error:** Spinner shown during fetch; `navigator.onLine` check distinguishes network errors from API errors (404 City not found, 401 invalid key, other status codes)
- **localStorage:** `lsGet`/`lsSet` wrappers safely handle `SecurityError` when localStorage is unavailable (e.g., private browsing with strict settings)

**Assumptions:**
- User must supply their own free OpenWeatherMap API key at the `API_KEY` constant (clearly marked with a comment)
- Temperatures displayed in Celsius

**Live Demo:** *(deploy via GitHub Pages — open `question3/weather-dashboard.html`)*

**Estimated time:** ~3.5 hours

---

## ⚙️ Running the Solutions

| Question | How to run |
|----------|-----------|
| Q1 — Product Card | Open `question1/index.html` directly in any browser |
| Q2 — WordPress Plugin | Upload to `wp-content/plugins/` then activate in WordPress admin |
| Q3 — Weather Dashboard | Open `question3/weather-dashboard.html` in a browser; add your OWM API key first |

---

## 🌐 Live Demos

- Q1: [Live Demo — Product Card](https://sage-chimera-139106.netlify.app/)
- Q3: [Live Demo — Weather Dashboard](https://gleaming-jelly-03d890.netlify.app/)

---

## 🧪 Testing

- Tested on Chrome 121, Firefox 123, Edge 121, Safari 17
- Tested on iPhone 14 (iOS 17) and Android 14 viewport simulations
- Lighthouse accessibility audit: 95+ for Q1 and Q3
