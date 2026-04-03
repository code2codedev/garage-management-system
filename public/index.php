<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="css/main.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <title>Alpha Garage</title>
</head>
<body>

  <!-- Header -->
  <header>
    <nav>
      <!-- Logo -->
      <div class="logo">
        <img src="images/l1 (1).jpeg" alt="Alpha Garage Logo">
      </div>

      <!-- Navigation -->
      <ul id="navLinks">
        <li><a href="#home">Home</a></li>
        <li><a href="#about">About</a></li>
        <li><a href="#services">Services</a></li>
        <li><a href="#blog">Blog</a></li>
        <li><a href="#contact">Contact</a></li>

        <!-- Add User Icon -->
        <li class="mobile-user-icon">
          <h3>Sign In 👉 </h3>
          <a href="/GMS_TEST/public/login.php" id="addUserIcon" title="Add User">
            <span class="user-icon">👤</span>
          </a>
        </li>
      </ul>

      <!-- Mobile Menu Icon -->
      <div class="menu-icon" id="menuIcon">&#9776;</div>
    </nav>
  </header>

  <!-- Hero Section -->
  <section id="home">
    <div class="hero-background">
      <img src="images/img 1.jpg" alt="Garage Hero Background">
    </div>
    <div class="hero-content">
      <h1>ALPHA GARAGE</h1>
      <h1>Crafting Garage Art & Services</h1>
      <p>Where mechanics meet creativity</p>
      <a href="#services">Explore Services</a>
    </div>
  </section>

  <!-- About Section -->
  <section id="about">
    <div class="about-wrapper">
      <div class="about-image">
        <img src="images/img 2.jpg" alt="About Alpha Garage">
      </div>
      <div class="about-text">
        <h2>About Us</h2>
        <p>We blend technical excellence with artistic sensibility—from repairs to custom builds and garage art.</p>
      </div>
    </div>
  </section>

  <!-- Services Section -->
  <section id="services">
    <h2>Our Services</h2>
    <div class="services-grid">
      <div class="service-card">
        <h3>Repairs</h3>
        <p>Diagnostics, engine work, brakes, and more.</p>
      </div>
      <div class="service-card">
        <h3>Custom Builds</h3>
        <p>Performance tuning and bespoke upgrades.</p>
      </div>
      <div class="service-card">
        <h3>Garage Art</h3>
        <p>Paint, wraps, and crafted details.</p>
      </div>
      <div class="service-card">
        <h3>Consultation</h3>
        <p>Upgrade planning and budget clarity.</p>
      </div>
    </div>
  </section>

  <!-- Blog Section -->
  <section id="blog">
    <h2>From Our Blog</h2>
    <div class="blog-grid">
      <article class="blog-post">
        <div class="blog-image">
          <img src="images/img 3.jpg" alt="Performance Tuning">
        </div>
        <h3>How we approach performance tuning</h3>
        <p>April 15, 2026</p>
      </article>
      <article class="blog-post">
        <div class="blog-image">
          <img src="images/img 4.jpg" alt="Reliable Repairs">
        </div>
        <h3>The checklist behind reliable repairs</h3>
        <p>May 2, 2026</p>
      </article>
      <article class="blog-post">
        <div class="blog-image">
          <img src="images/img 5.jpg" alt="Garage Art">
        </div>
        <h3>Garage art: subtle, tasteful, timeless</h3>
        <p>June 8, 2026</p>
      </article>
    </div>
  </section>

  <!-- Contact Section (Updated to Social Media Handles) -->
  <section id="contact">
    <div class="social-wrapper">
      <h2>Remember  To Follow All Our Social Handles</h2>
      <div class="social-links">
        <a href="https://www.tiktok.com/@alphagarage" target="_blank"><i class="fab fa-tiktok"></i> TikTok</a>
        <a href="https://www.instagram.com/alphagarage" target="_blank"><i class="fab fa-instagram"></i> Instagram</a>
        <a href="https://www.facebook.com/alphagarage" target="_blank"><i class="fab fa-facebook"></i> Facebook</a>
        <a href="https://wa.me/254700000000" target="_blank"><i class="fab fa-whatsapp"></i> WhatsApp</a>
        <h2>Connect With Us</h2>
      </div>
    </div>

    <!-- Image beside socials -->
    <div class="map-wrapper">
      <div class="map-header">
        <h3>Our Location</h3>
        <p>Visit us at the garage</p>
      </div>
      <div class="map-placeholder">
        <img src="Images/map.jpeg" alt="Map Placeholder">
      </div>
    </div>
  </section>

  <!-- Footer -->
  <footer>
    <div class="social-icons">
      <a href="#"><i class="fab fa-facebook"></i></a>
      <a href="#"><i class="fab fa-twitter"></i></a>
      <a href="#"><i class="fab fa-instagram"></i></a>
      <a href="#"><i class="fab fa-linkedin"></i></a>
    </div>
    <p>© 2026 Alpha Garage. All rights reserved.</p>
  </footer>

  <!-- Mobile Menu Script -->
  <script>
    const menuIcon = document.getElementById("menuIcon");
    const navLinks = document.getElementById("navLinks");

    menuIcon.addEventListener("click", () => {
      navLinks.classList.toggle("show");
    });
  </script>

</body>
</html>