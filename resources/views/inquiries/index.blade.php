@extends('layouts.app')

@section('content')
<div class="inquiries-container">
    <div class="container py-5">       
        <!-- Card principale -->
        <div class="card main-card border-0 shadow-lg animated-card">
            <div class="card-header bg-gradient border-0 text-center py-4">
           
                <h2 class="text-blueimage.png mb-4">
                    <i class="fas fa-envelope me-2"></i>Demandes des utilisateurs
                </h2>
            </div>
            
            <div class="card-body p-4 p-lg-5">
                @if(session('success'))
                    <div class="alert custom-alert-success mb-4 animated-alert">
                        <div class="alert-icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="alert-content">
                            {{ session('success') }}
                        </div>
                    </div>
                @endif

                <div class="table-responsive inquiries-table-container">
                    <table class="table table-hover align-middle">
                        <thead class="table-header">
                            <tr>
                                <th><i class="far fa-calendar-alt me-2"></i>Date</th>
                                <th><i class="fas fa-user me-2"></i>Nom</th>
                                <th><i class="fas fa-envelope me-2"></i>E-mail</th>
                                <th><i class="fas fa-phone me-2"></i>Téléphone</th>
                                <th><i class="fas fa-comment me-2"></i>Message</th>
                                <th><i class="fas fa-eye me-2"></i>Statut</th>
                                <th><i class="fas fa-cogs me-2"></i>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($inquiries as $inquiry)
                            <tr class="inquiry-row @if($inquiry->read) bg-light text-muted @endif">
                                <td class="inquiry-date">{{ $inquiry->created_at->format('d/m/Y H:i') }}</td>
                                <td class="inquiry-name">{{ $inquiry->name }}</td>
                                <td class="inquiry-email">
                                    <a href="mailto:{{ $inquiry->email }}">{{ $inquiry->email }}</a>
                                </td>
                                <td class="inquiry-phone">
                                    @if($inquiry->phone)
                                        <a href="tel:{{ $inquiry->phone }}">{{ $inquiry->phone }}</a>
                                    @else
                                        <span class="text-muted">Indisponible</span>
                                    @endif
                                </td>
                                <td class="inquiry-message">
                                    {{ Str::limit($inquiry->message, 50) }}
                                </td>
                                <td class="inquiry-status">
                                    @if($inquiry->read)
                                        <span class="badge bg-success">Lu</span>
                                    @else
                                        <span class="badge bg-warning">Non lu</span>
                                    @endif
                                </td>
                                <td class="inquiry-actions">
                                    <div class="d-flex gap-2 action-buttons">
                                        <button class="btn btn-sm btn-primary view-message-btn action-btn" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#messageModal"
                                                data-name="{{ $inquiry->name }}"
                                                data-email="{{ $inquiry->email }}"
                                                data-phone="{{ $inquiry->phone ?? 'Non fourni' }}"
                                                data-date="{{ $inquiry->created_at->format('d/m/Y H:i') }}"
                                                data-message="{{ $inquiry->message }}"
                                                title="Voir le message complet">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        
                                        @if(!$inquiry->read)
                                        <form action="{{ route('inquiries.markRead', $inquiry->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-success action-btn" title="Marquer comme lu">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        </form>
                                        @endif
                                        
                                        <form action="{{ route('inquiries.destroy', $inquiry->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger action-btn" 
                                                    onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette demande?')"
                                                    title="Supprimer cette demande">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 no-inquiries">
                                    <i class="fas fa-inbox fa-3x mb-3 text-muted"></i>
                                    <p class="mb-0">Aucune demande pour le moment</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($inquiries->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    <nav aria-label="Page navigation">
                        {{ $inquiries->onEachSide(1)->links('pagination::bootstrap-5') }}
                    </nav>
                </div>
                @endif
                
                <!-- Bouton pour vider toutes les demandes lues -->
                <div class="text-end mt-4">
                    <form action="{{ route('inquiries.clearRead') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn clear-read-btn" onclick="return confirm('Êtes-vous sûr de vouloir supprimer toutes les demandes lues?')">
                            <span class="btn-icon"><i class="fas fa-trash-alt"></i></span>
                            <span class="btn-text">Vider toutes les demandes lues</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal pour afficher le message complet -->
<div class="modal fade" id="messageModal" tabindex="-1" aria-labelledby="messageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="messageModalLabel">
                    <i class="fas fa-envelope-open-text me-2"></i>Détails du message
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="message-details">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <p><strong><i class="fas fa-user me-2"></i>Nom :</strong> <span id="modal-name"></span></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong><i class="far fa-calendar-alt me-2"></i>Date :</strong> <span id="modal-date"></span></p>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <p><strong><i class="fas fa-envelope me-2"></i>Email :</strong> <span id="modal-email"></span></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong><i class="fas fa-phone me-2"></i>Téléphone :</strong> <span id="modal-phone"></span></p>
                        </div>
                    </div>
                    <div class="mb-3">
                        <p class="mb-1"><strong><i class="fas fa-comment me-2"></i>Message :</strong></p>
                        <div class="message-content p-3 bg-light rounded" id="modal-message"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i> Fermer
                </button>
                <a href="#" class="btn btn-primary" id="reply-btn">
                    <i class="fas fa-reply me-1"></i> Répondre
                </a>
            </div>
        </div>
    </div>
</div>

<style>
    .inquiries-container {
        padding-top: 2rem;
        padding-bottom: 4rem;
    }
    
    .main-card {
        border-radius: 15px;
        overflow: hidden;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    
    .main-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
    }
    
    .card-header.bg-gradient {
        background: linear-gradient(135deg, #0d6efd 0%, #0b5ed7 100%);
    }
    
    .table-header {
        background-color: #f8f9fa;
    }
    
    .table-header th {
        font-weight: 600;
        color: #495057;
        border-bottom: 2px solid #dee2e6;
    }
    
    .inquiry-row:hover {
        background-color: rgba(13, 110, 253, 0.05);
    }
    
    .inquiry-email a, .inquiry-phone a {
        color: #0d6efd;
        text-decoration: none;
        transition: color 0.2s;
    }
    
    .inquiry-email a:hover, .inquiry-phone a:hover {
        color: #0b5ed7;
        text-decoration: underline;
    }
    
    .no-inquiries {
        color: #6c757d;
    }
    
    .view-message-btn {
        transition: all 0.2s;
    }
    
    .view-message-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }
    
    .custom-alert-success {
        display: flex;
        align-items: center;
        padding: 1rem;
        border-radius: 8px;
        background-color: #d1e7dd;
        color: #0f5132;
        border-left: 4px solid #0f5132;
    }
    
    .alert-icon {
        margin-right: 1rem;
        font-size: 1.25rem;
    }
    
    .message-content {
        white-space: pre-wrap;
        max-height: 300px;
        overflow-y: auto;
    }
    
    .animated-card {
        animation: fadeInUp 0.5s ease-out;
    }
    
    .animated-alert {
        animation: fadeIn 0.3s ease-out;
    }
    
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    @keyframes fadeIn {
        from {
            opacity: 0;
        }
        to {
            opacity: 1;
        }
    }
    
    /* Style pour la pagination */
    .pagination .page-item.active .page-link {
        background-color: #0d6efd;
        border-color: #0d6efd;
    }
    
    .pagination .page-link {
        color: #0d6efd;
        margin: 0 5px;
        border-radius: 5px;
    }
    
    .pagination .page-link:hover {
        background-color: #e9ecef;
    }
    
    /* Styles pour les boutons d'action */
    .action-buttons {
        display: flex;
        justify-content: center;
        gap: 8px;
    }
    
    .action-btn {
        width: 38px;
        height: 38px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 0;
        box-shadow: 0 3px 8px rgba(0,0,0,0.15);
        transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        position: relative;
        overflow: hidden;
        transform-style: preserve-3d;
    }
    
    .action-btn::after {
        content: '';
        position: absolute;
        width: 100%;
        height: 100%;
        top: 0;
        left: 0;
        background: radial-gradient(circle, rgba(255,255,255,0.4) 0%, transparent 70%);
        opacity: 0;
        transition: all 0.3s ease;
        transform: translateZ(1px);
    }
    
    .action-btn:hover {
        transform: translateY(-4px) scale(1.05);
        box-shadow: 0 8px 15px rgba(0,0,0,0.2);
    }
    
    .action-btn:hover::after {
        opacity: 1;
    }
    
    .action-btn:active {
        transform: translateY(0) scale(0.95);
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
    
    .action-btn i {
        font-size: 1rem;
        transition: all 0.3s ease;
    }
    
    .action-btn:hover i {
        transform: scale(1.1);
    }
    
    .view-message-btn {
        background: linear-gradient(145deg, #0d6efd, #0b5ed7);
        border: none;
    }
    
    .btn-success.action-btn {
        background: linear-gradient(145deg, #198754, #157347);
        border: none;
    }
    
    .btn-danger.action-btn {
        background: linear-gradient(145deg, #dc3545, #bb2d3b);
        border: none;
    }
    
    .clear-read-btn {
        background: linear-gradient(145deg, #ffc107, #e5ac00);
        border: none;
        padding: 12px 24px;
        border-radius: 30px;
        font-weight: 600;
        font-size: 0.95rem;
        letter-spacing: 0.5px;
        transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        box-shadow: 0 5px 15px rgba(255, 193, 7, 0.3);
        position: relative;
        overflow: hidden;
        color: #7a4f01;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    
    .clear-read-btn::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(to right, transparent, rgba(255,255,255,0.4), transparent);
        transform: translateX(-100%);
        transition: all 0.6s ease;
    }
    
    .clear-read-btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 20px rgba(255, 193, 7, 0.4);
        background: linear-gradient(145deg, #e5ac00, #ffc107);
        color: #66400f;
    }
    
    .clear-read-btn:hover::before {
        transform: translateX(100%);
    }
    
    .clear-read-btn:active {
        transform: translateY(0);
        box-shadow: 0 3px 10px rgba(255, 193, 7, 0.2);
    }
    
    .btn-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 24px;
        height: 24px;
        border-radius: 50%;
        background-color: rgba(255,255,255,0.3);
        margin-right: 4px;
        transition: all 0.3s ease;
    }
    
    .clear-read-btn:hover .btn-icon {
        background-color: rgba(255,255,255,0.5);
        transform: rotate(-5deg);
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialiser les tooltips avec options améliorées
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"], [title]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl, {
                delay: { show: 300, hide: 100 },
                placement: 'top',
                animation: true,
                trigger: 'hover'
            });
        });
        
        // Gérer l'affichage du modal
        var messageModal = document.getElementById('messageModal');
        if (messageModal) {
            messageModal.addEventListener('show.bs.modal', function (event) {
                var button = event.relatedTarget;
                
                // Récupérer les données de l'attribut data
                var name = button.getAttribute('data-name');
                var email = button.getAttribute('data-email');
                var phone = button.getAttribute('data-phone');
                var date = button.getAttribute('data-date');
                var message = button.getAttribute('data-message');
                
                // Mettre à jour le contenu du modal
                document.getElementById('modal-name').textContent = name;
                document.getElementById('modal-email').textContent = email;
                document.getElementById('modal-phone').textContent = phone;
                document.getElementById('modal-date').textContent = date;
                document.getElementById('modal-message').textContent = message;
                
                // Mettre à jour le lien de réponse
                document.getElementById('reply-btn').href = 'mailto:' + email + '?subject=Réponse à votre demande';
            });
        }
        
        // Animation des lignes du tableau
        var inquiryRows = document.querySelectorAll('.inquiry-row');
        inquiryRows.forEach(function(row, index) {
            row.style.opacity = '0';
            row.style.transform = 'translateX(-20px)';
            row.style.transition = 'all 0.4s ease-out ' + (index * 0.1) + 's';
            
            setTimeout(function() {
                row.style.opacity = '1';
                row.style.transform = 'translateX(0)';
            }, 100);
        });
        
        // Ajouter des attributs data-bs-toggle pour les boutons d'action
        document.querySelectorAll('.action-btn').forEach(function(btn) {
            if (btn.title) {
                btn.setAttribute('data-bs-toggle', 'tooltip');
                btn.setAttribute('data-bs-placement', 'top');
            }
        });
    });
</script>
@endsection