let allProperties = [];

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

async function loadProperties() {
    try {
        const response = await fetch('/api/properties');
        allProperties = await response.json();
        displayProperties(allProperties);
    } catch (error) {
        console.error('Error loading properties:', error);
    }
}

function displayProperties(properties) {
    const container = document.getElementById('propertiesList');
    
    if (properties.length === 0) {
        container.innerHTML = '<p style="text-align: center; padding: 40px; color: #666;">No properties found</p>';
        return;
    }
    
    container.innerHTML = properties.map(property => `
        <div class="property-card" data-property-id="${escapeHtml(property.id)}">
            <img src="${escapeHtml(property.image || '/frontend/assets/images/default-property.jpg')}" alt="${escapeHtml(property.title)}">
            <div class="property-card-content">
                <h3>${escapeHtml(property.title)}</h3>
                <p class="price">$${property.price.toLocaleString()}</p>
                <p class="location">${escapeHtml(property.location)}</p>
                <div class="property-info">
                    <span>${property.bedrooms} Beds</span>
                    <span>${property.bathrooms} Baths</span>
                    <span>${property.area} sqft</span>
                    <span>${property.type === 'sale' ? 'For Sale' : 'For Rent'}</span>
                </div>
            </div>
        </div>
    `).join('');
    
    document.querySelectorAll('.property-card').forEach(card => {
        card.addEventListener('click', () => {
            viewProperty(card.dataset.propertyId);
        });
    });
}

function viewProperty(id) {
    window.location.href = `/property?id=${encodeURIComponent(id)}`;
}

function filterProperties() {
    const type = document.getElementById('typeFilter').value;
    const minPrice = parseFloat(document.getElementById('minPrice').value) || 0;
    const maxPrice = parseFloat(document.getElementById('maxPrice').value) || Infinity;
    const location = document.getElementById('locationFilter').value.toLowerCase();
    
    const filtered = allProperties.filter(property => {
        if (type && property.type !== type) return false;
        if (property.price < minPrice) return false;
        if (property.price > maxPrice) return false;
        if (location && !property.location.toLowerCase().includes(location)) return false;
        return true;
    });
    
    displayProperties(filtered);
}

loadProperties();
