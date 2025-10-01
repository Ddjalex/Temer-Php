let editingId = null;

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
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

loadProperties();
