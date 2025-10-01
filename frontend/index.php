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
                    <a href="/admin">Admin</a>
                </nav>
            </div>
        </div>
    </header>

    <section class="hero">
        <div class="container">
            <h2>Find Your Dream Property</h2>
            <p>Discover the perfect place to call home</p>
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

    <script src="/frontend/app.js"></script>
</body>
</html>
