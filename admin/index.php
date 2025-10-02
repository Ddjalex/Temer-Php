<?php
require_once __DIR__ . '/../backend/auth.php';
requireLogin();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Temer Properties</title>
    <link rel="stylesheet" href="/frontend/style.css">
</head>
<body>
    <header>
        <div class="container">
            <div class="header-content">
                <img src="/frontend/assets/images/temer-logo.jpg" alt="Temer Properties" class="logo">
                <h1>Temer Properties - Admin</h1>
                <nav>
                    <a href="/">Back to Site</a>
                    <a href="/logout" style="background: rgba(255,255,255,0.2); padding: 8px 16px; border-radius: 4px;">Logout</a>
                </nav>
            </div>
        </div>
    </header>

    <section class="hero-slider">
        <div class="slider-container">
            <div class="slide active">
                <div class="slide-content">
                    <h2>Welcome to Admin Dashboard</h2>
                    <p>Manage your property listings efficiently</p>
                </div>
            </div>
            <div class="slide">
                <div class="slide-content">
                    <h2>Add New Properties</h2>
                    <p>Create and showcase amazing real estate listings</p>
                </div>
            </div>
            <div class="slide">
                <div class="slide-content">
                    <h2>Edit & Update</h2>
                    <p>Keep your property information current and accurate</p>
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

    <section class="admin-section">
        <div class="container">
            <h2>Manage Hero Sliders</h2>
            <div class="admin-form">
                <form id="sliderForm">
                    <input type="hidden" id="sliderId">
                    <div class="form-group">
                        <label for="sliderTitle">Title</label>
                        <input type="text" id="sliderTitle" required>
                    </div>
                    <div class="form-group">
                        <label for="sliderSubtitle">Subtitle</label>
                        <input type="text" id="sliderSubtitle">
                    </div>
                    <div class="form-group">
                        <label for="sliderImage">Image</label>
                        <input type="file" id="sliderImageFile" accept="image/*">
                        <input type="text" id="sliderImage" placeholder="Or enter image URL" style="margin-top: 10px;">
                        <div id="sliderImagePreview" style="margin-top: 10px;"></div>
                    </div>
                    <div class="form-group">
                        <label for="sliderOrder">Display Order</label>
                        <input type="number" id="sliderOrder" value="0" min="0">
                    </div>
                    <div class="form-group">
                        <label>
                            <input type="checkbox" id="sliderActive" checked>
                            Active
                        </label>
                    </div>
                    <button type="submit" class="btn">Save Slider</button>
                    <button type="button" class="btn" onclick="resetSliderForm()" style="background: #666;">Cancel</button>
                </form>
            </div>

            <h2>Existing Sliders</h2>
            <div class="admin-table">
                <table>
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Subtitle</th>
                            <th>Order</th>
                            <th>Active</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="slidersTable"></tbody>
                </table>
            </div>

            <h2>Add New Property</h2>
            <div class="admin-form">
                <form id="propertyForm">
                    <input type="hidden" id="propertyId">
                    <div class="form-group">
                        <label for="title">Title</label>
                        <input type="text" id="title" required>
                    </div>
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea id="description" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="price">Price</label>
                        <input type="number" id="price" required>
                    </div>
                    <div class="form-group">
                        <label for="location">Location</label>
                        <input type="text" id="location" required>
                    </div>
                    <div class="form-group">
                        <label for="type">Type</label>
                        <select id="type" required>
                            <option value="sale">For Sale</option>
                            <option value="rent">For Rent</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="bedrooms">Bedrooms</label>
                        <input type="number" id="bedrooms" required>
                    </div>
                    <div class="form-group">
                        <label for="bathrooms">Bathrooms</label>
                        <input type="number" id="bathrooms" required>
                    </div>
                    <div class="form-group">
                        <label for="area">Area (sqft)</label>
                        <input type="number" id="area" required>
                    </div>
                    <div class="form-group">
                        <label for="image">Image URL</label>
                        <input type="text" id="image" placeholder="https://example.com/image.jpg">
                    </div>
                    <div class="form-group">
                        <label>
                            <input type="checkbox" id="featured">
                            Featured Property
                        </label>
                    </div>
                    <button type="submit" class="btn">Save Property</button>
                    <button type="button" class="btn" onclick="resetForm()" style="background: #666;">Cancel</button>
                </form>
            </div>

            <h2>Manage Properties</h2>
            <div class="admin-table">
                <table>
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Location</th>
                            <th>Price</th>
                            <th>Type</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="propertiesTable"></tbody>
                </table>
            </div>
        </div>
    </section>

    <script src="/admin/admin.js"></script>
</body>
</html>
