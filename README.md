# Cafe Coffea – Cups & Moments
## Official Party Hall Website (Static CDN / Vercel Architecture)

A fast, mobile-first, public-facing website for **Cafe Coffea – Cups & Moments** Party Hall in Kottakkal, Kerala.

---

### 🌐 Official Brand Reference
- **Brand Name:** Cafe Coffea – Cups & Moments
- **Official Website:** [https://cafecoffea.in/](https://cafecoffea.in/)
- **Theme:** Official Dark Forest Green (`#081612`, `#0E241E`, `#132E27`) with Luxury Gold Accents (`#D4AF37`)
- **Location:** Ground Floor, The Souk, Swagathamad, Kottakkal, Malappuram, Kerala 676510, India
- **Contact:** +91 81570 22220 | [Chat on WhatsApp](https://wa.me/918157022220)

---

### 🚀 Key Features & Highlights

1. **Dark Forest Green Visual Identity:**
   - Accurate recreation of the authentic `cafecoffea.in` brand colors: Deep forest green, subtle gold badges, Playfair Display typography, and polished dark glassmorphism.
   - Clean, customer-focused design with no admin overhead or database lag.

2. **Clean 18-Photo Gallery:**
   - Real high-resolution party hall photographs without disruptive text overlays or between-image headings.
   - Filter tabs: All Photos, Stage & Backdrop, Hall Interior, Seating & Dining, Ambiance & Decor.
   - Fullscreen lightbox with touch swipe on mobile and keyboard navigation.

3. **Replaced Video Showcase:**
   - Features the two newest videos (`cafecoffe_hall_v2_reel.mp4` and `IMG_2079.MOV`).
   - Custom posters, play overlay controls, responsive layout, and fullscreen support.

4. **Transparent Packages & Charges:**
   - **Party Hall Rental:** ₹1,000 per hour.
   - **Cleaning Charge:** ₹200 per booking (one-time charge, applied only once regardless of the number of hours booked).
   - Clear pricing examples (1 Hr = ₹1,200 | 2 Hrs = ₹2,200 | 3 Hrs = ₹3,200).
   - Interactive live duration calculator.

5. **Special Food Order Offer:**
   - *"Enjoy 1 Hour of Party Hall FREE! When your eligible food orders at Cafe Coffea reach ₹5,000 or above, you receive 1 hour of party hall usage FREE."*
   - Prominently highlighted with a dedicated gold badge and explanation.

6. **Party Hall Rules & Policies:**
   - Booking, punctuality, and extension rules.
   - Decoration guidelines (wallpaper protection, approved tapes, damage assessments).
   - Cake guidelines (in-house pre-booking, outside cakes allowed, food policy).

7. **Direct WhatsApp Booking & Google Maps:**
   - Instant WhatsApp enquiry with pre-filled message template.
   - Embedded interactive Google Maps directions.

---

### ⚡ Deployment Architecture (Vercel Ready)

- **Pure Static Architecture:** Zero PHP or SQLite dependencies. Fully compatible with Vercel's Edge CDN, GitHub Pages, Netlify, or any static hosting service.
- **Instant Global Deployments:** 2–5 second deployment time, 100% uptime, zero cold starts.
- **Configured `vercel.json`:**
  ```json
  {
    "cleanUrls": true,
    "trailingSlash": false
  }
  ```
