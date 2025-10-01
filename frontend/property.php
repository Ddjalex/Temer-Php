<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Property Details - Temer Properties</title>
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

    <section class="property-detail">
        <div class="container">
            <div id="propertyDetail"></div>
        </div>
    </section>

    <script>
        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
        
        const urlParams = new URLSearchParams(window.location.search);
        const propertyId = urlParams.get('id');
        
        if (propertyId) {
            fetch(`/api/properties/${encodeURIComponent(propertyId)}`)
                .then(res => res.json())
                .then(property => {
                    document.getElementById('propertyDetail').innerHTML = `
                        <div class="property-detail-content">
                            <img src="${escapeHtml(property.image || '/frontend/assets/images/default-property.jpg')}" alt="${escapeHtml(property.title)}">
                            <h2>${escapeHtml(property.title)}</h2>
                            <p class="price">$${property.price.toLocaleString()}</p>
                            <p class="location">${escapeHtml(property.location)}</p>
                            <div class="property-info">
                                <span>${property.bedrooms} Bedrooms</span>
                                <span>${property.bathrooms} Bathrooms</span>
                                <span>${property.area} sqft</span>
                                <span>${property.type === 'sale' ? 'For Sale' : 'For Rent'}</span>
                            </div>
                            <p class="description">${escapeHtml(property.description)}</p>
                            <a href="/" class="btn">Back to Listings</a>
                        </div>
                    `;
                });
        }
    </script>
</body>
</html>
