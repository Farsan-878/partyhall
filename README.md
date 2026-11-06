# Cafe Coffea – Cups & Moments
## Premium Party Hall Website + Admin Dashboard

A complete, mobile-first website and content management system for **Cafe Coffea – Cups & Moments** Party Hall in Kottakkal, Kerala.

---

### 🌐 Official Brand Reference
- **Brand Name:** Cafe Coffea – Cups & Moments
- **Official Website:** [https://cafecoffea.in/](https://cafecoffea.in/)
- **Location:** Ground Floor, The Souk, Swagathamad, Kottakkal, Malappuram, Kerala 676510, India
- **Contact:** +91 81570 22220

---

### 🚀 Running the Project Locally

#### Option A: Using XAMPP Apache (Recommended)
1. Open the **XAMPP Control Panel**.
2. Click **Start** next to **Apache**.
3. Open your browser and navigate to:
   - **Public Website:** `http://localhost/partyhall.1/`
   - **Admin Dashboard:** `http://localhost/partyhall.1/admin/`

#### Option B: Using PHP Built-in Server
Open PowerShell or Command Prompt in this folder and run:
```bash
php -S localhost:8000
```
Then visit:
- **Public Website:** `http://localhost:8000/`
- **Admin Dashboard:** `http://localhost:8000/admin/`

---

### 🔐 Admin Dashboard Credentials
- **Admin URL:** `/admin` (or `/admin/login.php`)
- **Username:** `admin`
- **Password:** `Coffea@2026!`
*(You can change the password at any time from the Admin Dashboard > Settings)*

---

### 🌟 Features & Structure

1. **Homepage / Hero Section:**
   - Full-width hero background using actual wide interior photograph of Cafe Coffea.
   - Original Cafe Coffea gold monogram logo.
   - Prominent heading: *"Cups & Moments"*
   - Subheading: *"Celebrate Your Special Moments at Cafe Coffea."*
   - Direct CTA buttons: *"Explore Our Party Hall"* & *"Enquire Now"*.

2. **Quick Navigation:**
   - 4 shortcut cards: **Gallery**, **Videos**, **Packages & Charges**, and **Instructions**.

3. **Photo Gallery & Lightbox:**
   - Scans and organizes actual café photographs across categories: Stage & Backdrop, Hall Interior, Seating & Dining, and Decor.
   - Full-screen interactive lightbox with touch-swipe on mobile and keyboard arrow support.

4. **Video Gallery:**
   - Responsive HTML5 player showcasing walkthroughs and ambient tours (`IMG_1974.MOV`, `IMG_1981.MOV`).

5. **Packages & Transparent Pricing:**
   - Party Hall Charge: **₹1,000 / hour**
   - Cleaning Charge: **₹200 / booking**
   - Standard 1-Hour Total: **₹1,200**
   - **Special Privilege Offer:** Reach **₹5,000** in Cafe Coffea purchases and receive **1 hour of party hall usage FREE**.
   - All rates and thresholds are 100% editable from the Admin Dashboard.

6. **Instructions & Policies:**
   - Party Hall Rules (tariffs, punctuality, additional hours).
   - Decoration Policy (wallpaper protection, damage prevention, safe methods).
   - Cake Policy (Cafe Coffea cakes, outside cake allowance, food restrictions).

7. **Direct WhatsApp Booking:**
   - Pre-filled booking enquiry message template with date, time, guest count, occasion, and cake request.
   - Floating WhatsApp button on mobile for quick access.

8. **Admin Dashboard (`/admin`):**
   - **Overview:** Instant metrics (photos, videos, tariffs, active hero preview).
   - **Photo Manager:** Upload images (JPG, PNG, WEBP, HEIC), reorder, delete, and set hero image with one click.
   - **Video Manager:** Upload videos, update titles, descriptions, and poster thumbnails.
   - **Pricing Manager:** Edit hourly charge, cleaning fee, offer threshold, and free duration with live preview calculation.
   - **Instructions Manager:** Add, edit, or delete policy rules across categories.
   - **Contact Manager:** Edit WhatsApp number, phone, address, and Google Maps embed.
   - **Settings:** Update headings, subheadings, pre-filled WhatsApp templates, and change admin password.

---

### 📂 Technology Stack
- **Backend:** PHP 8.2+
- **Database:** SQLite 3 (stored at `data/database.sqlite`, zero manual configuration required)
- **Frontend:** Vanilla HTML5, Mobile-First CSS3 (Custom Properties & Glassmorphism), Vanilla JavaScript
- **Security:** Bcrypt password hashing, session regeneration, CSRF tokens, input sanitization.

