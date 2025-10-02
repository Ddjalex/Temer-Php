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
        const data = await response.json();
        
        if (data.error) {
            console.error('API error:', data.error);
            allProperties = [];
        } else if (Array.isArray(data)) {
            allProperties = data;
        } else {
            console.error('Invalid data format:', data);
            allProperties = [];
        }
        
        displayProperties(allProperties);
    } catch (error) {
        console.error('Error loading properties:', error);
        displayProperties([]);
    }
}

let contactPhone = '';

async function displayProperties(properties) {
    const container = document.getElementById('propertiesList');
    
    if (!container) return;
    
    if (!Array.isArray(properties) || properties.length === 0) {
        container.innerHTML = '<p style="text-align: center; padding: 40px; color: #666;">No properties found</p>';
        return;
    }
    
    container.innerHTML = properties.map(property => {
        const phone = contactPhone.replace(/\D/g, '');
        const whatsappMessage = encodeURIComponent(`Hi, I'm interested in ${property.title} at ${property.location}`);
        
        return `
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
                <div class="property-actions">
                    <a href="/property?id=${encodeURIComponent(property.id)}" class="view-details-btn">View Details</a>
                </div>
            </div>
        </div>
    `;
    }).join('');
    
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

async function loadContactPhone() {
    try {
        const response = await fetch('/api/settings');
        const settings = await response.json();
        contactPhone = settings.contact_phone || '';
        updateSocialMediaLinks(settings);
    } catch (error) {
        console.error('Error loading contact phone:', error);
    }
}

function updateSocialMediaLinks(settings) {
    const socialLinks = document.querySelector('.social-links');
    if (!socialLinks) return;
    
    const links = [
        { url: settings.facebook_url, label: 'Facebook', icon: 'M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z' },
        { url: settings.instagram_url, label: 'Instagram', icon: 'M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z' },
        { url: settings.twitter_url, label: 'Twitter', icon: 'M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z' },
        { url: settings.linkedin_url, label: 'LinkedIn', icon: 'M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z' }
    ];
    
    const validLinks = links.filter(link => link.url && link.url.trim() !== '');
    
    if (validLinks.length > 0) {
        socialLinks.innerHTML = validLinks.map(link => `
            <a href="${escapeHtml(link.url)}" target="_blank" aria-label="${link.label}">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                    <path d="${link.icon}"/>
                </svg>
            </a>
        `).join('');
    }
}

async function init() {
    loadSliders();
    await loadContactPhone();
    if (document.getElementById('propertiesList')) {
        loadProperties();
    }
}

init();
