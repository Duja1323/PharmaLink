@extends('layouts.app')

@section('content')
<div class="pharmacy-container">
    <!-- En-tête avec titre élégant -->
    <div class="pharmacy-header text-center mb-5">
        <h1 class="display-4 fw-bold text-primary">
            <i class="fas fa-pills me-3"></i>Nos Médicaments
        </h1>
        <p class="lead text-muted">Découvrez notre gamme complète de produits pharmaceutiques</p>
    </div>

    <!-- Barre de recherche améliorée -->
    <form method="GET" action="{{ route('purchase.page') }}" class="mb-5">
        <div class="search-bar input-group shadow-lg rounded-pill">
            <span class="input-group-text bg-white border-0">
                <i class="fas fa-search text-primary"></i>
            </span>
            <input type="text" name="search" class="form-control border-0 py-3" 
                   placeholder="Rechercher un médicament..." value="{{ request('search') }}">
            <button type="submit" class="btn btn-primary rounded-pill px-4">
                <i class="fas fa-search me-2"></i>Rechercher
            </button>
        </div>
    </form>

    <!-- Grille de produits améliorée -->
    <div class="row g-4">
        @foreach($medications as $medication)
        <div class="col-lg-4 col-md-6">
            <div class="product-card card h-100 border-0 shadow-sm rounded-4 overflow-hidden">
                <!-- Image du produit -->
                @if($medication->image)
                <div class="product-image-container">
                    <img src="{{ asset('storage/' . $medication->image) }}" 
                         alt="{{ $medication->name }}" 
                         class="product-image img-fluid">
                </div>
                @else
                <div class="product-image-container">
                    <img src="{{ asset('images/' . ($loop->index % 5 == 0 ? 'paracetamol.jpg' : ($loop->index % 5 == 1 ? 'VeinUp.png' : ($loop->index % 5 == 2 ? 'g.jpg' : ($loop->index % 5 == 3 ? 'unnamed.jpg' : 'pharmacie.jpg'))))) }}" 
                         alt="{{ $medication->name }}" 
                         class="product-image img-fluid">
                </div>
                @endif
                
                <!-- Corps de la carte -->
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <h3 class="product-title h5 mb-0">
                            <i class="fas fa-capsules text-primary me-2"></i>{{ $medication->name }}
                        </h3>
                        <span class="badge bg-primary rounded-pill">
                            {{ $medication->price }} DH
                        </span>
                    </div>
                    
                    <p class="product-description text-muted mb-4">
                        {{ $medication->description }}
                    </p>
                    
                    <div class="stock-info mb-4">
                        <span class="d-block mb-1">
                            <i class="fas fa-boxes me-2"></i>
                            <strong>Stock :</strong> 
                            @if($medication->quantity > 0)
                                <span class="text-success" data-stock-id="{{ $medication->id }}" data-stock="{{ $medication->quantity }}">{{ $medication->quantity }} disponibles</span>
                            @else
                                <span class="text-danger" data-stock-id="{{ $medication->id }}" data-stock="0">En rupture de stock</span>
                            @endif
                        </span>
                    </div>
                    
                    <!-- Contrôles de quantité et ajout au panier -->
                    @if($medication->quantity > 0)
                    <div class="product-controls">
                        <div class="input-group mb-3">
                            <button class="btn btn-outline-secondary quantity-decrement" type="button" data-id="{{ $medication->id }}">
                                <i class="fas fa-minus"></i>
                            </button>
                            <input type="number" min="1" max="{{ $medication->quantity }}" 
                                   id="quantity-{{ $medication->id }}" 
                                   class="form-control text-center quantity-input" 
                                   value="1">
                            <button class="btn btn-outline-secondary quantity-increment" type="button" data-id="{{ $medication->id }}">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                        
                        <div class="mb-3 text-center">
                            <small class="text-muted item-total" data-id="{{ $medication->id }}" data-price="{{ $medication->price }}">
                                Total: <span>{{ number_format($medication->price, 2) }}</span> DH
                            </small>
                        </div>
                        
                        <button class="btn btn-success w-100 add-to-cart rounded-pill"
                                data-id="{{ $medication->id }}" 
                                data-name="{{ $medication->name }}" 
                                data-price="{{ $medication->price }}">
                            <i class="fas fa-cart-plus me-2"></i>Ajouter au panier
                        </button>
                    </div>
                    @else
                    <button class="btn btn-outline-danger w-100 rounded-pill disabled">
                        <i class="fas fa-times-circle me-2"></i>Rupture de stock
                    </button>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Pagination centrée -->
    <div class="d-flex justify-content-center mt-5">
        <nav aria-label="Pagination">
            {{ $medications->links('pagination::bootstrap-5') }}
        </nav>
    </div>

    <!-- Panier d'achat -->
    <div class="shopping-cart mt-5">
        <div class="card shadow-lg border-0 rounded-4">
            <div class="card-header bg-primary text-white rounded-top-4 py-3">
                <h3 class="mb-0">
                    <i class="fas fa-shopping-cart me-2"></i>Votre Panier
                </h3>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Médicament</th>
                                <th>Quantité</th>
                                <th>Prix Unitaire</th>
                                <th>Total</th>
                                <th class="pe-4"></th>
                            </tr>
                        </thead>
                        <tbody id="cart-body">
                            <!-- Les éléments du panier seront insérés ici par JavaScript -->
                        </tbody>
                        <tfoot id="cart-footer" class="table-light">
                            <!-- Le total sera calculé par JavaScript -->
                        </tfoot>
                    </table>
                </div>
            </div>
            <div class="card-footer bg-white text-end rounded-bottom-4">
                <button id="checkout-btn" class="btn btn-lg rounded-pill px-5">
                    <i class="fas fa-shopping-cart me-2"></i>Passer la commande
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modale de confirmation de commande -->
<div class="modal fade" id="confirmOrderModal" tabindex="-1" aria-labelledby="confirmOrderModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="confirmOrderModalLabel"><i class="fas fa-shopping-cart me-2"></i>Confirmation de commande</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="text-center mb-4">
          <i class="fas fa-check-circle text-primary" style="font-size: 3rem;"></i>
        </div>
        <p class="text-center fs-5 mb-4">Êtes-vous sûr de vouloir passer cette commande?</p>
        
        <div class="card bg-light mb-3">
          <div class="card-body">
            <div class="d-flex justify-content-between mb-2">
              <span>Nombre d'articles:</span>
              <span class="fw-bold" id="modalTotalItems">0</span>
            </div>
            <div class="d-flex justify-content-between">
              <span>Total:</span>
              <span class="fw-bold fs-5 text-primary" id="modalTotalPrice">0.00 DH</span>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer justify-content-center">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
          <i class="fas fa-times me-2"></i>Annuler
        </button>
        <button type="button" class="btn btn-primary" id="confirmOrderBtn">
          <i class="fas fa-check me-2"></i>Confirmer
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Styles CSS améliorés -->
<style>
    .pharmacy-container {
        padding: 2rem 0 4rem;
    }
    
    .pharmacy-header {
        padding: 2rem 0;
        background: linear-gradient(135deg, rgba(13, 110, 253, 0.05) 0%, rgba(255, 255, 255, 1) 100%);
        border-radius: 1rem;
        margin-bottom: 2rem;
    }
    
    .search-bar {
        max-width: 800px;
        margin: 0 auto;
        overflow: hidden;
    }
    
    .search-bar .form-control {
        height: 50px;
        font-size: 1.1rem;
    }
    
    .product-card {
        transition: all 0.3s ease;
        border: 1px solid rgba(0, 0, 0, 0.05);
    }
    
    .product-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        border-color: rgba(13, 110, 253, 0.2);
    }
    
    .product-image-container {
        height: 200px;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: #f8f9fa;
        overflow: hidden;
    }
    
    .product-image {
        max-height: 100%;
        width: auto;
        object-fit: contain;
        transition: transform 0.3s ease;
    }
    
    .product-card:hover .product-image {
        transform: scale(1.05);
    }
    
    .product-placeholder {
        height: 200px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .product-title {
        color: #212529;
        font-weight: 600;
    }
    
    .product-description {
        min-height: 50px;
    }
    
    .quantity-input {
        text-align: center;
        font-weight: 500;
    }
    
    .quantity-decrement, .quantity-increment {
        width: 40px;
    }
    
    /* Styles pour le panier */
    .shopping-cart {
        margin-top: 3rem;
    }
    
    .shopping-cart th {
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.85rem;
        letter-spacing: 0.5px;
    }
    
    /* Styles pour les notifications */
    .pharmacy-notification {
        position: fixed;
    top: 80px; /* بدلاً من 20px لتجنب التداخل مع الشريط العلوي */
    right: 20px;
    z-index: 9999;
    min-width: 300px;
    padding: 15px 20px;
    border-radius: 10px;
    color: white;
    font-weight: 500;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.15);
    display: flex;
    align-items: center;
    justify-content: space-between;
    }
    
    .notification-success {
        background: linear-gradient(135deg, #28a745, #218838);
    }
    
    .notification-error {
        background: linear-gradient(135deg, #dc3545, #c82333);
    }
    
    .notification-warning {
        background: linear-gradient(135deg, #ffc107, #e0a800);
    }
    
    .notification-close {
        background: none;
        border: none;
        color: white;
        font-size: 1.2rem;
        cursor: pointer;
        margin-left: 15px;
    }
    
    @keyframes slideIn {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
    
    @keyframes fadeOut {
        from { opacity: 1; }
        to { opacity: 0; }
    }
    
    /* Responsive design */
    @media (max-width: 992px) {
        .product-card {
            margin-bottom: 1.5rem;
        }
    }
    @media (max-width: 768px) {
    .pharmacy-notification {
        top: 70px;
        right: 10px;
        left: 10px;
        min-width: auto;
        width: calc(100% - 20px);
    }
}
    @media (max-width: 768px) {
        .pharmacy-header h1 {
            font-size: 2rem;
        }
        
        .search-bar {
            flex-direction: column;
            border-radius: 0.5rem !important;
        }
        
        .search-bar input {
            border-radius: 0.5rem !important;
            margin-bottom: 0.5rem;
        }
        
        .search-bar button {
            width: 100%;
            border-radius: 0.5rem !important;
        }
    }
    #sidebar {
        display: none;
    }
    #main-content {
        margin-left: 0;
        width: 100%;
    }
    .table-striped tbody tr:last-child {
        border-top: 2px solid #dee2e6;
    }
    
    /* Styles pour les modales */
    .modal-content {
        border: none;
        border-radius: 1rem;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }
    
    .modal-header {
        background: linear-gradient(135deg, #0d6efd 0%, #0b5ed7 100%);
        border-bottom: none;
        padding: 1.5rem;
    }
    
    .modal-title {
        font-weight: 600;
    }
    
    .modal-body {
        padding: 2rem;
    }
    
    .modal-footer {
        border-top: none;
        padding: 1rem 2rem 2rem;
    }
    
    #confirmOrderBtn {
        background: linear-gradient(135deg, #0d6efd 0%, #0b5ed7 100%);
        border: none;
        padding: 0.75rem 2rem;
        font-weight: 600;
        border-radius: 50px;
        min-width: 150px;
        box-shadow: 0 4px 15px rgba(13, 110, 253, 0.2);
        transition: all 0.3s ease;
    }
    
    #confirmOrderBtn:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(13, 110, 253, 0.3);
    }
    
    .btn-outline-secondary {
        border-radius: 50px;
        padding: 0.75rem 2rem;
        min-width: 150px;
    }
    
    /* Animation pour l'icône de confirmation */
    .modal .fa-check-circle {
        animation: scaleIn 0.5s ease;
    }
    
    @keyframes scaleIn {
        0% { transform: scale(0); opacity: 0; }
        70% { transform: scale(1.2); opacity: 1; }
        100% { transform: scale(1); opacity: 1; }
    }
    
    /* Style pour la carte récapitulative */
    .modal .card {
        border-radius: 0.75rem;
        border: 1px solid rgba(13, 110, 253, 0.1);
    }
    
    /* Style pour le bouton de paiement principal */
    #checkout-btn {
        background: linear-gradient(135deg, #26d07c 0%, #1bb76e 100%);
        color: white;
        border: none;
        border-radius: 50px;
        padding: 0.75rem 2rem;
        font-weight: 600;
        font-size: 1.1rem;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(38, 208, 124, 0.2);
        position: relative;
        overflow: hidden;
    }
    
    #checkout-btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(38, 208, 124, 0.3);
    }
    
    #checkout-btn::after {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
        transition: 0.5s;
    }
    
    #checkout-btn:hover::after {
        left: 100%;
    }
</style>

<!-- Script JavaScript amélioré -->
<script>
    // Panier et fonctions utilitaires
    let cart = JSON.parse(localStorage.getItem('pharmacy_cart')) || [];
    
    // Fonction pour vérifier la disponibilité du stock
    function checkStockAvailability(medicationId, requestedQuantity) {
        // Récupérer la quantité actuellement dans le panier pour ce médicament
        const cartItem = cart.find(item => item.id === medicationId);
        const cartQuantity = cartItem ? cartItem.quantity : 0;
        
        // Récupérer la quantité disponible en stock
        const stockElement = document.querySelector(`[data-stock-id="${medicationId}"]`);
        if (!stockElement) return true; // Si l'élément n'existe pas, on autorise l'ajout
        
        const availableStock = parseInt(stockElement.dataset.stock);
        
        // Calculer la quantité totale (panier + nouvelle demande)
        const totalRequestedQuantity = cartQuantity + requestedQuantity;
        
        // Vérifier si la quantité totale demandée est disponible
        return availableStock >= totalRequestedQuantity;
    }
    
    // Afficher une notification
    function showNotification(message, type = 'success') {
        const existingNotifications = document.querySelectorAll('.pharmacy-notification');
        existingNotifications.forEach(notif => notif.remove());
        
        const notification = document.createElement('div');
        notification.className = `pharmacy-notification notification-${type}`;
        notification.innerHTML = `
            <span>${message}</span>
            <button class="notification-close">&times;</button>
        `;
        
        document.body.appendChild(notification);
        
        notification.querySelector('.notification-close').addEventListener('click', () => {
            notification.remove();
        });
        
        setTimeout(() => notification.remove(), 3000);
    }
    
    // Sauvegarder le panier dans le localStorage
    function saveCart() {
        localStorage.setItem('pharmacy_cart', JSON.stringify(cart));
    }
    
    // Mettre à jour les indicateurs de stock sur la page
    function updateStockDisplay() {
        // Pour chaque médicament dans le panier, mettre à jour l'affichage du stock disponible
        cart.forEach(item => {
            const stockElement = document.querySelector(`[data-stock-id="${item.id}"]`);
            if (stockElement) {
                const totalStock = parseInt(stockElement.dataset.originalStock);
                const remainingStock = totalStock - item.quantity;
                
                // Mettre à jour le texte affiché
                stockElement.textContent = remainingStock > 0 
                    ? `${remainingStock} disponibles (${item.quantity} dans votre panier)` 
                    : "En rupture de stock";
                
                stockElement.classList.toggle('text-success', remainingStock > 0);
                stockElement.classList.toggle('text-danger', remainingStock <= 0);
                
                // Mettre à jour la quantité max des inputs
                const quantityInput = document.getElementById(`quantity-${item.id}`);
                if (quantityInput) {
                    quantityInput.max = remainingStock;
                    if (parseInt(quantityInput.value) > remainingStock) {
                        quantityInput.value = remainingStock > 0 ? remainingStock : 0;
                    }
                }
                
                // Désactiver le bouton d'ajout si le stock est épuisé
                const addBtn = document.querySelector(`.add-to-cart[data-id="${item.id}"]`);
                if (addBtn) {
                    addBtn.disabled = remainingStock <= 0;
                    if (remainingStock <= 0) {
                        addBtn.classList.replace('btn-success', 'btn-outline-danger');
                        addBtn.innerHTML = '<i class="fas fa-times-circle me-2"></i>Rupture de stock';
                    }
                }
            }
        });
    }
    
    // Mettre à jour l'affichage du panier
    function updateCartDisplay() {
        const cartBody = document.getElementById('cart-body');
        const cartFooter = document.getElementById('cart-footer');
        
        cartBody.innerHTML = '';
        
        if (cart.length === 0) {
            cartBody.innerHTML = `
                <tr>
                    <td colspan="5" class="text-center py-4 text-muted">
                        <i class="fas fa-shopping-cart fa-2x mb-3"></i>
                        <p class="mb-0">Votre panier est vide</p>
                    </td>
                </tr>
            `;
            cartFooter.innerHTML = '';
            return;
        }
        
        let total = 0;
        
        cart.forEach(item => {
            const itemTotal = item.price * item.quantity;
            total += itemTotal;
            
            const row = `
                <tr>
                    <td class="ps-4 align-middle">
                        <strong>${item.name}</strong>
                    </td>
                    <td class="align-middle">
                        <div class="d-flex align-items-center">
                            <button class="btn btn-sm btn-outline-secondary cart-decrement" 
                                    data-id="${item.id}">
                                <i class="fas fa-minus"></i>
                            </button>
                            <input type="number" min="1" value="${item.quantity}" 
                                   class="form-control form-control-sm text-center mx-2 cart-quantity"
                                   style="width: 60px;"
                                   data-id="${item.id}">
                            <button class="btn btn-sm btn-outline-secondary cart-increment" 
                                    data-id="${item.id}">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </td>
                    <td class="align-middle">${item.price.toFixed(2)} DH</td>
                    <td class="align-middle">${itemTotal.toFixed(2)} DH</td>
                    <td class="pe-4 align-middle">
                        <button class="btn btn-sm btn-danger rounded-circle cart-remove"
                                data-id="${item.id}">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
            `;
            
            cartBody.innerHTML += row;
        });
        
        cartFooter.innerHTML = `
            <tr>
                <td colspan="3" class="text-end fw-bold">Total :</td>
                <td class="fw-bold">${total.toFixed(2)} DH</td>
                <td></td>
            </tr>
        `;
        
        // Ajouter les événements
        document.querySelectorAll('.cart-quantity').forEach(input => {
            input.addEventListener('change', function() {
                const id = this.dataset.id;
                const newQuantity = parseInt(this.value);
                
                if (newQuantity > 0) {
                    const item = cart.find(item => item.id === id);
                    if (item) {
                        // Vérifier si la nouvelle quantité est disponible
                        const stockElement = document.querySelector(`[data-stock-id="${id}"]`);
                        if (stockElement) {
                            const originalStock = parseInt(stockElement.dataset.originalStock);
                            const currentInCart = item.quantity;
                            
                            if (newQuantity > currentInCart) {
                                // On augmente la quantité, vérifier le stock
                                const additionalQty = newQuantity - currentInCart;
                                if (!checkStockAvailability(id, additionalQty)) {
                                    showNotification(`Stock insuffisant pour ${item.name}`, 'error');
                                    this.value = item.quantity; // Restaurer la valeur précédente
                                    return;
                                }
                            }
                        }
                        
                        // Mettre à jour la quantité
                        item.quantity = newQuantity;
                        saveCart();
                        updateCartDisplay();
                        updateStockDisplay();
                    }
                }
            });
        });
        
        document.querySelectorAll('.cart-increment').forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.dataset.id;
                const item = cart.find(item => item.id === id);
                if (item) {
                    // Vérifier le stock avant d'incrémenter
                    if (checkStockAvailability(id, 1)) {
                        item.quantity++;
                        saveCart();
                        updateCartDisplay();
                        updateStockDisplay();
                    } else {
                        showNotification(`Stock insuffisant pour ${item.name}`, 'error');
                    }
                }
            });
        });
        
        document.querySelectorAll('.cart-decrement').forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.dataset.id;
                const item = cart.find(item => item.id === id);
                if (item && item.quantity > 1) {
                    item.quantity--;
                    saveCart();
                    updateCartDisplay();
                    updateStockDisplay();
                }
            });
        });
        
        document.querySelectorAll('.cart-remove').forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.dataset.id;
                const item = cart.find(item => item.id === id);
                if (item) {
                    cart = cart.filter(item => item.id !== id);
                    saveCart();
                    updateCartDisplay();
                    updateStockDisplay();
                    showNotification(`${item.name} a été retiré du panier`, 'error');
                }
            });
        });
    }
    
    // Gestion des boutons d'ajout au panier
    document.querySelectorAll('.add-to-cart').forEach(button => {
        button.addEventListener('click', function() {
            const id = this.dataset.id;
            const name = this.dataset.name;
            const price = parseFloat(this.dataset.price);
            const quantityInput = document.getElementById(`quantity-${id}`);
            const quantity = parseInt(quantityInput.value);
            
            // Vérifier la disponibilité du stock
            if (!checkStockAvailability(id, quantity)) {
                showNotification(`Stock insuffisant pour ${name}`, 'error');
                return;
            }
            
            const existingItem = cart.find(item => item.id === id);
            
            if (existingItem) {
                existingItem.quantity += quantity;
            } else {
                cart.push({ 
                    id: id, 
                    name: name, 
                    price: price, 
                    quantity: quantity 
                });
            }
            
            saveCart();
            updateCartDisplay();
            updateStockDisplay();
            showNotification(`${quantity} ${name} ajouté(s) au panier`);
            
            // Réinitialiser la quantité
            quantityInput.value = 1;
        });
    });
    
    // Gestion des boutons de quantité
    document.querySelectorAll('.quantity-increment').forEach(button => {
        button.addEventListener('click', function() {
            const medicationId = this.dataset.id;
            const input = document.getElementById(`quantity-${medicationId}`);
            const max = parseInt(input.max);
            if (parseInt(input.value) < max) {
                input.value = parseInt(input.value) + 1;
                updateItemTotal(medicationId);
            }
        });
    });
    
    document.querySelectorAll('.quantity-decrement').forEach(button => {
        button.addEventListener('click', function() {
            const medicationId = this.dataset.id;
            const input = document.getElementById(`quantity-${medicationId}`);
            if (parseInt(input.value) > 1) {
                input.value = parseInt(input.value) - 1;
                updateItemTotal(medicationId);
            }
        });
    });
    
    // Mettre à jour le total lors de la saisie manuelle
    document.querySelectorAll('.quantity-input').forEach(input => {
        input.addEventListener('change', function() {
            const medicationId = this.id.replace('quantity-', '');
            updateItemTotal(medicationId);
        });
    });
    
    // Fonction pour mettre à jour le prix total d'un article
    function updateItemTotal(medicationId) {
        const quantityInput = document.getElementById(`quantity-${medicationId}`);
        const totalElement = document.querySelector(`.item-total[data-id="${medicationId}"] span`);
        
        if (quantityInput && totalElement) {
            const quantity = parseInt(quantityInput.value);
            const price = parseFloat(document.querySelector(`.item-total[data-id="${medicationId}"]`).dataset.price);
            const total = quantity * price;
            
            totalElement.textContent = total.toFixed(2);
        }
    }
    
    document.getElementById('checkout-btn').addEventListener('click', function(e) {
        e.preventDefault();
        
        if (cart.length === 0) {
            showNotification('Votre panier est vide', 'warning');
            return;
        }
        
        const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
        const totalPrice = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0).toFixed(2);
        
        // Mettre à jour les valeurs dans la modale
        document.getElementById('modalTotalItems').textContent = totalItems;
        document.getElementById('modalTotalPrice').textContent = totalPrice + ' DH';
        
        // Afficher la modale au lieu d'utiliser confirm()
        const confirmModal = new bootstrap.Modal(document.getElementById('confirmOrderModal'));
        confirmModal.show();
    });
    
    // Gestionnaire pour le bouton de confirmation dans la modale
    document.getElementById('confirmOrderBtn').addEventListener('click', function() {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '/prepare-checkout';
            form.style.display = 'none';
            
            const csrfField = document.createElement('input');
            csrfField.type = 'hidden';
            csrfField.name = '_token';
            csrfField.value = '{{ csrf_token() }}';
            form.appendChild(csrfField);
            
            const cartField = document.createElement('input');
            cartField.type = 'hidden';
            cartField.name = 'cart_data';
            cartField.value = JSON.stringify(cart);
            form.appendChild(cartField);
            
            document.body.appendChild(form);
        
        // Ajouter une animation de chargement au bouton
        this.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Traitement...';
        this.disabled = true;
        
        // Soumettre le formulaire après un court délai pour montrer l'animation
        setTimeout(() => {
            form.submit();
        }, 500);
    });
    
    document.addEventListener('DOMContentLoaded', function() {
        // Initialiser les données de stock originales
        document.querySelectorAll('[data-stock-id]').forEach(element => {
            const stockText = element.textContent.trim();
            const stockMatch = stockText.match(/(\d+)/);
            if (stockMatch) {
                const stockAmount = parseInt(stockMatch[1]);
                element.dataset.originalStock = stockAmount;
            }
        });
        
        updateCartDisplay();
        updateStockDisplay();
        
        if (new URLSearchParams(window.location.search).has('success')) {
            showNotification('Commande passée avec succès !', 'success');
            localStorage.removeItem('pharmacy_cart');
            cart = [];
            updateCartDisplay();
            
            history.replaceState(null, '', window.location.pathname);
        }
    });
</script>
@endsection