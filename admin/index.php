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
    <link rel="stylesheet" href="/admin/admin-style.css">
</head>
<body>
    <div class="admin-header">
        <div class="container">
            <div class="logo-section">
                <img src="/frontend/assets/images/temer-logo.jpg" alt="Temer Properties" class="logo">
                <h1>Admin Dashboard</h1>
            </div>
            <nav>
                <a href="/">View Site</a>
                <button class="dark-mode-toggle" onclick="toggleDarkMode()">
                    <svg id="darkModeIcon" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/>
                    </svg>
                </button>
                <a href="/logout" style="background: rgba(255,255,255,0.2);">Logout</a>
            </nav>
        </div>
    </div>

    <div class="admin-container">
        <div class="admin-tabs">
            <button class="tab-btn active" onclick="switchTab('sliders')">Hero Sliders</button>
            <button class="tab-btn" onclick="switchTab('properties')">Properties</button>
            <button class="tab-btn" onclick="switchTab('settings')">Settings</button>
        </div>

        <div id="slidersTab" class="tab-content active">
            <div class="admin-card">
                <h2>Manage Hero Sliders</h2>
                <form id="sliderForm">
                    <input type="hidden" id="sliderId">
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="sliderTitle">Title *</label>
                            <input type="text" id="sliderTitle" required>
                        </div>
                        <div class="form-group">
                            <label for="sliderSubtitle">Subtitle</label>
                            <input type="text" id="sliderSubtitle">
                        </div>
                        <div class="form-group">
                            <label for="sliderOrder">Display Order</label>
                            <input type="number" id="sliderOrder" value="0" min="0">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="sliderImageFile">Upload Image</label>
                        <input type="file" id="sliderImageFile" accept="image/*">
                    </div>
                    <div class="form-group">
                        <label for="sliderImage">Or Enter Image URL</label>
                        <input type="text" id="sliderImage" placeholder="https://example.com/image.jpg">
                    </div>
                    <div id="sliderImagePreview" class="image-preview" style="display:none;"></div>
                    <div class="form-group">
                        <label>
                            <input type="checkbox" id="sliderActive" checked>
                            Active
                        </label>
                    </div>
                    <div class="btn-group">
                        <button type="submit" class="btn">Save Slider</button>
                        <button type="button" class="btn btn-secondary" onclick="resetSliderForm()">Cancel</button>
                    </div>
                </form>
            </div>

            <div class="admin-card">
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
            </div>
        </div>

        <div id="propertiesTab" class="tab-content">
            <div class="admin-card">
                <h2>Add / Edit Property</h2>
                <form id="propertyForm">
                    <input type="hidden" id="propertyId">
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="title">Title *</label>
                            <input type="text" id="title" required>
                        </div>
                        <div class="form-group">
                            <label for="location">Location *</label>
                            <input type="text" id="location" required>
                        </div>
                        <div class="form-group">
                            <label for="price">Price *</label>
                            <input type="number" id="price" required>
                        </div>
                        <div class="form-group">
                            <label for="type">Type *</label>
                            <select id="type" required>
                                <option value="sale">For Sale</option>
                                <option value="rent">For Rent</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="bedrooms">Bedrooms</label>
                            <input type="number" id="bedrooms" value="0">
                        </div>
                        <div class="form-group">
                            <label for="bathrooms">Bathrooms</label>
                            <input type="number" id="bathrooms" value="0">
                        </div>
                        <div class="form-group">
                            <label for="area">Area (sqft)</label>
                            <input type="number" id="area" value="0">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea id="description"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="propertyImageFile">Upload Image</label>
                        <input type="file" id="propertyImageFile" accept="image/*">
                    </div>
                    <div class="form-group">
                        <label for="image">Or Enter Image URL</label>
                        <input type="text" id="image" placeholder="https://example.com/property.jpg">
                    </div>
                    <div id="propertyImagePreview" class="image-preview" style="display:none;"></div>
                    <div class="form-group">
                        <label>
                            <input type="checkbox" id="featured">
                            Featured Property
                        </label>
                    </div>
                    <div class="btn-group">
                        <button type="submit" class="btn">Save Property</button>
                        <button type="button" class="btn btn-secondary" onclick="resetForm()">Cancel</button>
                    </div>
                </form>
            </div>

            <div class="admin-card">
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
        </div>

        <div id="settingsTab" class="tab-content">
            <div class="admin-card">
                <h2>Website Settings</h2>
                <form id="settingsForm">
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="siteName">Site Name</label>
                            <input type="text" id="siteName" name="site_name">
                        </div>
                        <div class="form-group">
                            <label for="siteTagline">Site Tagline</label>
                            <input type="text" id="siteTagline" name="site_tagline">
                        </div>
                        <div class="form-group">
                            <label for="contactPhone">Contact Phone Number *</label>
                            <input type="tel" id="contactPhone" name="contact_phone" placeholder="+1234567890" required>
                            <small style="color: var(--text-light); font-size: 12px; margin-top: 5px; display: block;">
                                Used for call and WhatsApp buttons on the website
                            </small>
                        </div>
                        <div class="form-group">
                            <label for="contactEmail">Contact Email</label>
                            <input type="email" id="contactEmail" name="contact_email">
                        </div>
                    </div>
                    <h3 style="margin-top: 30px; margin-bottom: 15px; color: var(--primary-color);">Social Media Links</h3>
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="facebookUrl">Facebook URL</label>
                            <input type="url" id="facebookUrl" name="facebook_url" placeholder="https://facebook.com/yourpage">
                        </div>
                        <div class="form-group">
                            <label for="instagramUrl">Instagram URL</label>
                            <input type="url" id="instagramUrl" name="instagram_url" placeholder="https://instagram.com/yourpage">
                        </div>
                        <div class="form-group">
                            <label for="twitterUrl">Twitter URL</label>
                            <input type="url" id="twitterUrl" name="twitter_url" placeholder="https://twitter.com/yourpage">
                        </div>
                        <div class="form-group">
                            <label for="linkedinUrl">LinkedIn URL</label>
                            <input type="url" id="linkedinUrl" name="linkedin_url" placeholder="https://linkedin.com/company/yourpage">
                        </div>
                    </div>
                    <div id="settingsMessage"></div>
                    <div class="btn-group">
                        <button type="submit" class="btn">Save Settings</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="/admin/admin.js"></script>
    <script>
        // Dark Mode Toggle
        function toggleDarkMode() {
            document.body.classList.toggle('dark-mode');
            const isDarkMode = document.body.classList.contains('dark-mode');
            localStorage.setItem('darkMode', isDarkMode ? 'enabled' : 'disabled');
            updateDarkModeIcon(isDarkMode);
        }

        function updateDarkModeIcon(isDarkMode) {
            const icon = document.getElementById('darkModeIcon');
            if (isDarkMode) {
                icon.innerHTML = '<path d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>';
            } else {
                icon.innerHTML = '<path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/>';
            }
        }

        // Load dark mode preference
        if (localStorage.getItem('darkMode') === 'enabled') {
            document.body.classList.add('dark-mode');
            updateDarkModeIcon(true);
        }
    </script>
</body>
</html>
