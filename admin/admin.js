let editingId = null;
let editingSliderId = null;
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
        const properties = await response.json();
        displayPropertiesTable(properties);
    } catch (error) {
        console.error('Error loading properties:', error);
    }
}

function displayPropertiesTable(properties) {
    const tbody = document.getElementById('propertiesTable');
    
    if (properties.length === 0) {
        tbody.innerHTML = '<tr><td colspan="5" style="text-align: center; padding: 20px;">No properties yet</td></tr>';
        return;
    }
    
    tbody.innerHTML = properties.map(property => `
        <tr data-property-id="${escapeHtml(property.id)}">
            <td>${escapeHtml(property.title)}</td>
            <td>${escapeHtml(property.location)}</td>
            <td>$${property.price.toLocaleString()}</td>
            <td>${property.type === 'sale' ? 'For Sale' : 'For Rent'}</td>
            <td>
                <button class="btn-edit">Edit</button>
                <button class="btn-delete">Delete</button>
            </td>
        </tr>
    `).join('');
    
    tbody.querySelectorAll('.btn-edit').forEach(btn => {
        btn.addEventListener('click', () => {
            const id = btn.closest('tr').dataset.propertyId;
            editProperty(id);
        });
    });
    
    tbody.querySelectorAll('.btn-delete').forEach(btn => {
        btn.addEventListener('click', () => {
            const id = btn.closest('tr').dataset.propertyId;
            deleteProperty(id);
        });
    });
}

async function editProperty(id) {
    try {
        const response = await fetch(`/api/properties/${encodeURIComponent(id)}`);
        const property = await response.json();
        
        document.getElementById('propertyId').value = property.id;
        document.getElementById('title').value = property.title;
        document.getElementById('description').value = property.description;
        document.getElementById('price').value = property.price;
        document.getElementById('location').value = property.location;
        document.getElementById('type').value = property.type;
        document.getElementById('bedrooms').value = property.bedrooms;
        document.getElementById('bathrooms').value = property.bathrooms;
        document.getElementById('area').value = property.area;
        document.getElementById('image').value = property.image || '';
        document.getElementById('featured').checked = property.featured || false;
        
        editingId = id;
        window.scrollTo({ top: 0, behavior: 'smooth' });
    } catch (error) {
        console.error('Error loading property:', error);
    }
}

async function deleteProperty(id) {
    if (!confirm('Are you sure you want to delete this property?')) {
        return;
    }
    
    try {
        await fetch(`/api/properties/${encodeURIComponent(id)}`, {
            method: 'DELETE'
        });
        loadProperties();
    } catch (error) {
        console.error('Error deleting property:', error);
    }
}

function resetForm() {
    document.getElementById('propertyForm').reset();
    document.getElementById('propertyId').value = '';
    editingId = null;
}

document.getElementById('propertyForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const propertyData = {
        title: document.getElementById('title').value,
        description: document.getElementById('description').value,
        price: parseFloat(document.getElementById('price').value),
        location: document.getElementById('location').value,
        type: document.getElementById('type').value,
        bedrooms: parseInt(document.getElementById('bedrooms').value),
        bathrooms: parseInt(document.getElementById('bathrooms').value),
        area: parseInt(document.getElementById('area').value),
        image: document.getElementById('image').value,
        featured: document.getElementById('featured').checked
    };
    
    try {
        if (editingId) {
            await fetch(`/api/properties/${encodeURIComponent(editingId)}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(propertyData)
            });
        } else {
            await fetch('/api/properties', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(propertyData)
            });
        }
        
        resetForm();
        loadProperties();
    } catch (error) {
        console.error('Error saving property:', error);
    }
});

async function loadSliders() {
    try {
        const response = await fetch('/api/sliders');
        const sliders = await response.json();
        displaySlidersTable(sliders);
        updateHeroSlider(sliders);
    } catch (error) {
        console.error('Error loading sliders:', error);
    }
}

function displaySlidersTable(sliders) {
    const tbody = document.getElementById('slidersTable');
    
    if (sliders.length === 0) {
        tbody.innerHTML = '<tr><td colspan="5" style="text-align: center; padding: 20px;">No sliders yet</td></tr>';
        return;
    }
    
    tbody.innerHTML = sliders.map(slider => `
        <tr data-slider-id="${escapeHtml(slider.id)}">
            <td>${escapeHtml(slider.title)}</td>
            <td>${escapeHtml(slider.subtitle || '')}</td>
            <td>${slider.display_order}</td>
            <td>${slider.active ? 'Yes' : 'No'}</td>
            <td>
                <button class="btn-edit">Edit</button>
                <button class="btn-delete">Delete</button>
            </td>
        </tr>
    `).join('');
    
    tbody.querySelectorAll('.btn-edit').forEach(btn => {
        btn.addEventListener('click', () => {
            const id = btn.closest('tr').dataset.sliderId;
            editSlider(id);
        });
    });
    
    tbody.querySelectorAll('.btn-delete').forEach(btn => {
        btn.addEventListener('click', () => {
            const id = btn.closest('tr').dataset.sliderId;
            deleteSlider(id);
        });
    });
}

function updateHeroSlider(sliders) {
    const activeSliders = sliders.filter(s => s.active);
    if (activeSliders.length === 0) return;
    
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

async function editSlider(id) {
    try {
        const response = await fetch(`/api/sliders/${encodeURIComponent(id)}`);
        const slider = await response.json();
        
        document.getElementById('sliderId').value = slider.id;
        document.getElementById('sliderTitle').value = slider.title;
        document.getElementById('sliderSubtitle').value = slider.subtitle || '';
        document.getElementById('sliderImage').value = slider.image || '';
        document.getElementById('sliderOrder').value = slider.display_order;
        document.getElementById('sliderActive').checked = slider.active;
        
        if (slider.image) {
            document.getElementById('sliderImagePreview').innerHTML = 
                `<img src="${escapeHtml(slider.image)}" style="max-width: 200px; max-height: 150px; border-radius: 4px;">`;
        }
        
        editingSliderId = id;
        window.scrollTo({ top: 0, behavior: 'smooth' });
    } catch (error) {
        console.error('Error loading slider:', error);
    }
}

async function deleteSlider(id) {
    if (!confirm('Are you sure you want to delete this slider?')) {
        return;
    }
    
    try {
        await fetch(`/api/sliders/${encodeURIComponent(id)}`, {
            method: 'DELETE'
        });
        loadSliders();
    } catch (error) {
        console.error('Error deleting slider:', error);
    }
}

function resetSliderForm() {
    document.getElementById('sliderForm').reset();
    document.getElementById('sliderId').value = '';
    document.getElementById('sliderImagePreview').innerHTML = '';
    editingSliderId = null;
}

document.getElementById('sliderImageFile').addEventListener('change', async (e) => {
    const file = e.target.files[0];
    if (!file) return;
    
    const formData = new FormData();
    formData.append('image', file);
    formData.append('type', 'slider');
    
    try {
        const response = await fetch('/api/upload', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            document.getElementById('sliderImage').value = result.url;
            document.getElementById('sliderImagePreview').innerHTML = 
                `<img src="${escapeHtml(result.url)}" style="max-width: 200px; max-height: 150px; border-radius: 4px;">`;
        } else {
            alert('Upload failed: ' + (result.error || 'Unknown error'));
        }
    } catch (error) {
        console.error('Error uploading image:', error);
        alert('Upload failed');
    }
});

document.getElementById('sliderForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const sliderData = {
        title: document.getElementById('sliderTitle').value,
        subtitle: document.getElementById('sliderSubtitle').value,
        image: document.getElementById('sliderImage').value,
        display_order: parseInt(document.getElementById('sliderOrder').value),
        active: document.getElementById('sliderActive').checked ? 1 : 0
    };
    
    try {
        if (editingSliderId) {
            await fetch(`/api/sliders/${encodeURIComponent(editingSliderId)}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(sliderData)
            });
        } else {
            await fetch('/api/sliders', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(sliderData)
            });
        }
        
        resetSliderForm();
        loadSliders();
    } catch (error) {
        console.error('Error saving slider:', error);
    }
});

initSlider();
loadSliders();
loadProperties();
