let allProperties = [];
let currentSlide = 0;
let slideInterval;

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function initSlider() {
    const slides = document.querySelectorAll('.slide');
    if (slides.length === 0) return;
    
    showSlide(0);
    startAutoSlide();
}

function showSlide(index) {
    const slides = document.querySelectorAll('.slide');
    const dots = document.querySelectorAll('.dot');
    
    if (slides.length === 0) return;
    
    if (index >= slides.length) {
        currentSlide = 0;
    } else if (index < 0) {
        currentSlide = slides.length - 1;
    } else {
        currentSlide = index;
    }
    
    slides.forEach(slide => slide.classList.remove('active'));
    slides[currentSlide]?.classList.add('active');
    
    if (dots.length > 0) {
        dots.forEach(dot => dot.classList.remove('active'));
        dots[currentSlide]?.classList.add('active');
    }
}

function changeSlide(direction) {
    showSlide(currentSlide + direction);
    resetAutoSlide();
}

function goToSlide(index) {
    showSlide(index);
    resetAutoSlide();
}

function startAutoSlide() {
    slideInterval = setInterval(() => {
        showSlide(currentSlide + 1);
    }, 5000);
}

function resetAutoSlide() {
    if (document.querySelectorAll('.slide').length > 0) {
        clearInterval(slideInterval);
        startAutoSlide();
    }
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
    
    if (!container) return;
    
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

async function loadSliders() {
    try {
        const response = await fetch('/api/sliders');
        const sliders = await response.json();
        updateHeroSlider(sliders);
    } catch (error) {
        console.error('Error loading sliders:', error);
        initSlider();
    }
}

function updateHeroSlider(sliders) {
    const activeSliders = sliders.filter(s => s.active);
    if (activeSliders.length === 0) {
        initSlider();
        return;
    }
    
    const sliderContainer = document.querySelector('.slider-container');
    const dotsContainer = document.querySelector('.slider-dots');
    
    if (!sliderContainer || !dotsContainer) return;
    
    sliderContainer.innerHTML = activeSliders.map(slider => `
        <div class="slide" ${slider.image ? `style="background-image: url('${escapeHtml(slider.image)}')"` : ''}>
            <div class="slide-content">
                <h2>${escapeHtml(slider.title)}</h2>
                <p>${escapeHtml(slider.subtitle || '')}</p>
            </div>
        </div>
    `).join('');
    
    dotsContainer.innerHTML = activeSliders.map((_, index) => 
        `<span class="dot ${index === 0 ? 'active' : ''}" onclick="goToSlide(${index})"></span>`
    ).join('');
    
    initSlider();
}

loadSliders();
if (document.getElementById('propertiesList')) {
    loadProperties();
}
