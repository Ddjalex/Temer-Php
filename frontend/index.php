<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Temer Properties - Real Estate Listings</title>
    <link rel="stylesheet" href="/frontend/style.css">
</head>
<body>
    <header>
        <div class="container">
            <div class="header-content">
                <img src="/frontend/assets/images/temer-logo.jpg" alt="Temer Properties" class="logo">
                <h1>Temer Properties</h1>
                <nav>
                    <a href="/">Home</a>
                </nav>
            </div>
        </div>
    </header>

    <section class="hero-slider">
        <div class="slider-container">
            <div class="slide active">
                <div class="slide-content">
                    <h2>Find Your Dream Property</h2>
                    <p>Discover the perfect place to call home</p>
                </div>
            </div>
            <div class="slide">
                <div class="slide-content">
                    <h2>Premium Real Estate Listings</h2>
                    <p>Explore luxury homes and prime locations</p>
                </div>
            </div>
            <div class="slide">
                <div class="slide-content">
                    <h2>Your Trusted Property Partner</h2>
                    <p>Professional service for buying and renting</p>
                </div>
            </div>
        </div>
        <button class="slider-nav prev" onclick="changeSlide(-1)">&#10094;</button>
        <button class="slider-nav next" onclick="changeSlide(1)">&#10095;</button>
        <div class="slider-dots">
            <span class="dot active" onclick="goToSlide(0)"></span>
            <span class="dot" onclick="goToSlide(1)"></span>
            <span class="dot" onclick="goToSlide(2)"></span>
        </div>
    </section>

    <section class="filters">
        <div class="container">
            <div class="filter-group">
                <select id="typeFilter">
                    <option value="">All Types</option>
                    <option value="sale">For Sale</option>
                    <option value="rent">For Rent</option>
                </select>
                <input type="number" id="minPrice" placeholder="Min Price">
                <input type="number" id="maxPrice" placeholder="Max Price">
                <input type="text" id="locationFilter" placeholder="Location">
                <button onclick="filterProperties()">Search</button>
            </div>
        </div>
    </section>

    <section class="properties">
        <div class="container">
            <div id="propertiesList" class="properties-grid"></div>
        </div>
    </section>

    <footer>
        <div class="container">
            <div class="footer-content">
                <p>&copy; 2025 Temer Properties. All rights reserved.</p>
                <p>Want to develop a website? Contact <a href="https://t.me/Ethioads012" target="_blank" style="color: #8BC34A; text-decoration: none;">@Ethioads012</a> on Telegram</p>
                <div class="social-links"></div>
            </div>
        </div>
    </footer>

    <script src="/frontend/app.js"></script>
</body>
</html>
