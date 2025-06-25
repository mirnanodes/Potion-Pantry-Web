// Potion Pantry - Main Application JavaScript

// Global variables
let sidebarOpen = false;
let currentProductId = null;

// Initialize app when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    initializeApp();
});

// Initialize application
function initializeApp() {
    setupSidebar();
    setupProductCards();
    setupForms();
    setupTooltips();
    setupModals();
    
    // Add fade-in animation to content
    document.body.classList.add('fade-in');
    
    // Auto-dismiss alerts after 5 seconds
    setTimeout(function() {
        const alerts = document.querySelectorAll('.alert:not(.alert-permanent)');
        alerts.forEach(alert => {
            if (alert.classList.contains('show')) {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            }
        });
    }, 5000);
}

// Sidebar functionality
function setupSidebar() {
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    
    if (sidebarToggle && sidebar) {
        sidebarToggle.addEventListener('click', toggleSidebar);
    }
    
    if (overlay) {
        overlay.addEventListener('click', closeSidebar);
    }
    
    // Close sidebar on escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && sidebarOpen) {
            closeSidebar();
        }
    });
    
    // Auto-close sidebar on mobile when clicking nav links
    const navLinks = document.querySelectorAll('.sidebar .nav-link');
    navLinks.forEach(link => {
        link.addEventListener('click', function() {
            if (window.innerWidth < 768) {
                setTimeout(closeSidebar, 100);
            }
        });
    });
}

function toggleSidebar() {
    if (sidebarOpen) {
        closeSidebar();
    } else {
        openSidebar();
    }
}

function openSidebar() {
    const sidebar = document.getElementById('sidebar');
    const mainContent = document.querySelector('.main-content');
    const overlay = document.getElementById('sidebarOverlay');
    
    if (sidebar) {
        sidebar.classList.add('show');
    }
    
    if (mainContent && window.innerWidth >= 768) {
        mainContent.classList.add('shifted');
    }
    
    if (overlay) {
        overlay.style.display = 'block';
    }
    
    sidebarOpen = true;
}

function closeSidebar() {
    const sidebar = document.getElementById('sidebar');
    const mainContent = document.querySelector('.main-content');
    const overlay = document.getElementById('sidebarOverlay');
    
    if (sidebar) {
        sidebar.classList.remove('show');
    }
    
    if (mainContent) {
        mainContent.classList.remove('shifted');
    }
    
    if (overlay) {
        overlay.style.display = 'none';
    }
    
    sidebarOpen = false;
}

// Product card interactions
function setupProductCards() {
    const productCards = document.querySelectorAll('.product-card');
    
    productCards.forEach(card => {
        card.addEventListener('click', function(e) {
            // Don't trigger if clicking on buttons
            if (!e.target.closest('button')) {
                const productId = this.dataset.productId;
                if (productId) {
                    showProductDetail(productId);
                }
            }
        });
    });
}

// Form enhancements
function setupForms() {
    // Add loading states to form submissions
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const submitBtn = this.querySelector('button[type="submit"]');
            if (submitBtn && !submitBtn.disabled) {
                submitBtn.disabled = true;
                const originalText = submitBtn.innerHTML;
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status"></span>Loading...';
                
                // Re-enable after 5 seconds as fallback
                setTimeout(() => {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                }, 5000);
            }
        });
    });
    
    // Auto-format price inputs
    const priceInputs = document.querySelectorAll('input[type="number"][name="price"]');
    priceInputs.forEach(input => {
        input.addEventListener('input', function() {
            if (this.value) {
                // Remove any non-numeric characters
                this.value = this.value.replace(/[^0-9]/g, '');
            }
        });
    });
    
    // Image preview
    const imageInputs = document.querySelectorAll('input[type="file"][accept*="image"]');
    imageInputs.forEach(input => {
        input.addEventListener('change', function() {
            previewImage(this);
        });
    });
}

// Image preview functionality
function previewImage(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        const previewContainer = document.getElementById('imagePreview') || createImagePreviewContainer(input);
        
        reader.onload = function(e) {
            previewContainer.innerHTML = `
                <img src="${e.target.result}" class="img-fluid rounded" style="max-height: 200px;" alt="Preview">
                <button type="button" class="btn btn-sm btn-outline-danger mt-2" onclick="clearImagePreview('${input.id}')">
                    Remove Preview
                </button>
            `;
            previewContainer.style.display = 'block';
        };
        
        reader.readAsDataURL(input.files[0]);
    }
}

function createImagePreviewContainer(input) {
    const container = document.createElement('div');
    container.id = 'imagePreview';
    container.className = 'mt-3';
    container.style.display = 'none';
    input.parentNode.appendChild(container);
    return container;
}

function clearImagePreview(inputId) {
    const input = document.getElementById(inputId);
    const preview = document.getElementById('imagePreview');
    
    if (input) input.value = '';
    if (preview) preview.style.display = 'none';
}

// Tooltip setup
function setupTooltips() {
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    const tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
}

// Modal setup
function setupModals() {
    // Auto-focus first input in modals
    const modals = document.querySelectorAll('.modal');
    modals.forEach(modal => {
        modal.addEventListener('shown.bs.modal', function() {
            const firstInput = this.querySelector('input, textarea, select');
            if (firstInput) {
                firstInput.focus();
            }
        });
    });
}

// Product Management Functions
function logUsage(productId, timeOfDay = null) {
    if (!timeOfDay) {
        showUsageModal(productId);
        return;
    }
    
    const formData = new FormData();
    formData.append('product_id', productId);
    formData.append('time_of_day', timeOfDay);
    formData.append('usage_date', new Date().toISOString().split('T')[0]);
    formData.append('notes', '');
    
    showLoading();
    
    fetch('../api/usage.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        hideLoading();
        if (data.success) {
            showSuccess('Usage logged successfully!');
            setTimeout(() => location.reload(), 1500);
        } else {
            showError(data.message || 'Failed to log usage');
        }
    })
    .catch(error => {
        hideLoading();
        console.error('Error:', error);
        showError('An error occurred while logging usage');
    });
}

function editProduct(productId) {
    // For now, just redirect to edit page (you can create this page)
    window.location.href = `edit_product.php?id=${productId}`;
}

function deleteProduct(productId) {
    if (confirm('Are you sure you want to delete this product? This action cannot be undone.')) {
        const formData = new FormData();
        formData.append('product_id', productId);
        formData.append('action', 'delete');
        
        showLoading();
        
        fetch('../api/products.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            hideLoading();
            if (data.success) {
                showSuccess('Product deleted successfully!');
                
                // Remove the card from UI with animation
                const productCard = document.querySelector(`[data-product-id="${productId}"]`);
                if (productCard) {
                    productCard.style.animation = 'fadeOut 0.3s ease-out';
                    setTimeout(() => {
                        productCard.remove();
                        // Check if no products left
                        const remainingCards = document.querySelectorAll('.product-card');
                        if (remainingCards.length === 0) {
                            location.reload();
                        }
                    }, 300);
                }
            } else {
                showError(data.message || 'Failed to delete product');
            }
        })
        .catch(error => {
            hideLoading();
            console.error('Error:', error);
            showError('An error occurred while deleting product');
        });
    }
}

function showProductDetail(productId) {
    currentProductId = productId;
    
    // For now, just show a simple alert with product info
    // You can enhance this to show a detailed modal
    const productCard = document.querySelector(`[data-product-id="${productId}"]`);
    if (productCard) {
        const productName = productCard.querySelector('.card-title').textContent;
        const productBrand = productCard.querySelector('.card-text').textContent;
        
        alert(`Product Details:\nName: ${productName}\nBrand: ${productBrand}\n\nFeature coming soon: Detailed product view with ingredients, usage history, and more!`);
    }
}

// Usage Modal Functions
function showUsageModal(productId) {
    // Remove existing modal if any
    const existingModal = document.getElementById('usageModal');
    if (existingModal) {
        existingModal.remove();
    }
    
    const modalHTML = `
        <div class="modal fade" id="usageModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Log Product Usage</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Time of Day</label>
                            <select class="form-select" id="timeOfDay">
                                <option value="Morning">Morning</option>
                                <option value="Evening">Evening</option>
                                <option value="Both">Both</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Notes (Optional)</label>
                            <textarea class="form-control" id="usageNotes" rows="3" 
                                      placeholder="How did the product work for you today?"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-primary" onclick="submitUsage(${productId})">
                            Log Usage
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // Add modal to body
    document.body.insertAdjacentHTML('beforeend', modalHTML);
    
    // Show modal
    const modal = new bootstrap.Modal(document.getElementById('usageModal'));
    modal.show();
    
    // Set default time based on current time
    const currentHour = new Date().getHours();
    const defaultTime = currentHour < 12 ? 'Morning' : 'Evening';
    document.getElementById('timeOfDay').value = defaultTime;
}

function submitUsage(productId) {
    const timeOfDay = document.getElementById('timeOfDay').value;
    const notes = document.getElementById('usageNotes').value.trim();
    
    const formData = new FormData();
    formData.append('product_id', productId);
    formData.append('time_of_day', timeOfDay);
    formData.append('usage_date', new Date().toISOString().split('T')[0]);
    formData.append('notes', notes);
    
    // Disable submit button
    const submitBtn = document.querySelector('#usageModal .btn-primary');
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Logging...';
    
    fetch('../api/usage.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Close modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('usageModal'));
            modal.hide();
            
            showSuccess('Usage logged successfully!');
            
            // Refresh page after delay
            setTimeout(() => location.reload(), 1500);
        } else {
            showError(data.message || 'Failed to log usage');
            submitBtn.disabled = false;
            submitBtn.innerHTML = 'Log Usage';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showError('An error occurred while logging usage');
        submitBtn.disabled = false;
        submitBtn.innerHTML = 'Log Usage';
    });
}

// Notification Functions
function showSuccess(message) {
    showNotification(message, 'success');
}

function showError(message) {
    showNotification(message, 'error');
}

function showNotification(message, type = 'success') {
    // Remove existing notifications
    const existing = document.querySelectorAll('.notification-toast');
    existing.forEach(toast => toast.remove());
    
    const bgColor = type === 'success' ? 'bg-success' : 'bg-danger';
    const icon = type === 'success' ? '✅' : '❌';
    
    const toastHTML = `
        <div class="notification-toast position-fixed top-0 end-0 m-3 p-3 ${bgColor} text-white rounded shadow-lg" 
             style="z-index: 1050; min-width: 300px;">
            <div class="d-flex align-items-center">
                <span class="me-2" style="font-size: 1.2rem;">${icon}</span>
                <span class="flex-grow-1">${message}</span>
                <button type="button" class="btn-close btn-close-white ms-2" onclick="this.parentElement.parentElement.remove()"></button>
            </div>
        </div>
    `;
    
    document.body.insertAdjacentHTML('beforeend', toastHTML);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        const toast = document.querySelector('.notification-toast');
        if (toast) {
            toast.style.animation = 'fadeOut 0.3s ease-out';
            setTimeout(() => toast.remove(), 300);
        }
    }, 5000);
}

// Loading Functions
function showLoading() {
    const loader = document.getElementById('loadingOverlay') || createLoadingOverlay();
    loader.style.display = 'flex';
}

function hideLoading() {
    const loader = document.getElementById('loadingOverlay');
    if (loader) {
        loader.style.display = 'none';
    }
}

function createLoadingOverlay() {
    const overlay = document.createElement('div');
    overlay.id = 'loadingOverlay';
    overlay.innerHTML = `
        <div class="d-flex align-items-center justify-content-center position-fixed top-0 start-0 w-100 h-100" 
             style="background: rgba(255,255,255,0.8); z-index: 9999;">
            <div class="text-center">
                <div class="spinner-border text-primary mb-3" role="status" style="width: 3rem; height: 3rem;">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <div class="text-muted">Please wait...</div>
            </div>
        </div>
    `;
    overlay.style.display = 'none';
    document.body.appendChild(overlay);
    return overlay;
}

// Utility Functions
function formatPrice(price) {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0
    }).format(price);
}

function formatDate(dateString) {
    return new Date(dateString).toLocaleDateString('id-ID', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    });
}

function calculateDaysUntilExpiration(expirationDate) {
    const today = new Date();
    const expiry = new Date(expirationDate);
    const diffTime = expiry - today;
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
    return diffDays;
}

// Search and Filter Functions
function filterProducts(searchTerm) {
    const productCards = document.querySelectorAll('.product-card');
    
    productCards.forEach(card => {
        const productName = card.querySelector('.card-title').textContent.toLowerCase();
        const productBrand = card.querySelector('.card-text').textContent.toLowerCase();
        
        if (productName.includes(searchTerm.toLowerCase()) || 
            productBrand.includes(searchTerm.toLowerCase())) {
            card.parentElement.style.display = 'block';
        } else {
            card.parentElement.style.display = 'none';
        }
    });
}

// Keyboard Shortcuts
document.addEventListener('keydown', function(e) {
    // Ctrl/Cmd + K to open search
    if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
        e.preventDefault();
        const searchInput = document.getElementById('searchInput');
        if (searchInput) {
            searchInput.focus();
        }
    }
    
    // Ctrl/Cmd + N to add new product
    if ((e.ctrlKey || e.metaKey) && e.key === 'n') {
        e.preventDefault();
        window.location.href = '../pages/add_product.php';
    }
});

// Form Validation Enhancement
function validateForm(form) {
    let isValid = true;
    const requiredFields = form.querySelectorAll('[required]');
    
    requiredFields.forEach(field => {
        if (!field.value.trim()) {
            field.classList.add('is-invalid');
            isValid = false;
        } else {
            field.classList.remove('is-invalid');
        }
    });
    
    return isValid;
}

// Auto-save functionality for forms
function setupAutoSave() {
    const forms = document.querySelectorAll('form[data-autosave]');
    
    forms.forEach(form => {
        const inputs = form.querySelectorAll('input, textarea, select');
        
        inputs.forEach(input => {
            input.addEventListener('input', function() {
                saveFormData(form);
            });
        });
        
        // Load saved data on page load
        loadFormData(form);
    });
}

function saveFormData(form) {
    const formData = new FormData(form);
    const data = {};
    
    for (let [key, value] of formData.entries()) {
        data[key] = value;
    }
    
    localStorage.setItem(`form_${form.id}`, JSON.stringify(data));
}

function loadFormData(form) {
    const savedData = localStorage.getItem(`form_${form.id}`);
    
    if (savedData) {
        const data = JSON.parse(savedData);
        
        Object.keys(data).forEach(key => {
            const field = form.querySelector(`[name="${key}"]`);
            if (field) {
                field.value = data[key];
            }
        });
    }
}

function clearSavedFormData(formId) {
    localStorage.removeItem(`form_${formId}`);
}

// Add CSS animations
const style = document.createElement('style');
style.textContent = `
    @keyframes fadeOut {
        from { opacity: 1; }
        to { opacity: 0; }
    }
    
    @keyframes slideInRight {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    .notification-toast {
        animation: slideInRight 0.3s ease-out;
    }
    
    .is-invalid {
        border-color: #dc3545 !important;
        box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25) !important;
    }
`;
document.head.appendChild(style);

// Initialize auto-save on page load
document.addEventListener('DOMContentLoaded', function() {
    setupAutoSave();
});

// Export functions for use in other scripts
window.PotionPantry = {
    logUsage,
    editProduct,
    deleteProduct,
    showProductDetail,
    showSuccess,
    showError,
    showLoading,
    hideLoading,
    formatPrice,
    formatDate,
    calculateDaysUntilExpiration,
    filterProducts,
    validateForm,
    toggleSidebar,
    openSidebar,
    closeSidebar
};