/**
 * Session Timer System
 * Manages session timeout with automatic warnings and logout
 */

class SessionManager {
    constructor() {
        this.sessionDuration = 30 * 60 * 1000; // 30 minutes in milliseconds
        this.warningTime = 5 * 60 * 1000; // 5 minutes warning in milliseconds
        this.startTime = this.getSessionStartTime();
        this.timerInterval = null;
        this.warningShown = false;
        
        this.init();
    }
    
    init() {
        // Check if session has already expired
        const elapsed = Date.now() - this.startTime;
        if (elapsed >= this.sessionDuration) {
            this.handleSessionExpired();
            return;
        }
        
        this.createTimerDisplay();
        this.createLogoutModal();
        this.startTimer();
        this.bindEvents();
        
        // Check if we should show warning immediately
        const remaining = this.sessionDuration - elapsed;
        if (remaining <= this.warningTime) {
            this.warningShown = true;
            setTimeout(() => this.showLogoutModal(), 1000);
        }
    }
    
    createTimerDisplay() {
        // Add timer display to sidebar bottom
        const sidebar = document.querySelector('.sidebar-wrapper');
        if (!sidebar) return;
        
        // Calculate initial remaining time
        const elapsed = Date.now() - this.startTime;
        const remaining = Math.max(0, this.sessionDuration - elapsed);
        const minutes = Math.floor(remaining / 60000);
        const seconds = Math.floor((remaining % 60000) / 1000);
        const initialDisplay = `${minutes}:${seconds.toString().padStart(2, '0')}`;
        
        const timerContainer = document.createElement('div');
        timerContainer.className = 'session-timer-container';
        timerContainer.innerHTML = `
            <div class="session-timer p-3 border-top border-secondary mt-auto">
                <div class="d-flex align-items-center text-light">
                    <i class="bi bi-clock me-2"></i>
                    <div>
                        <div class="small text-muted">Session expires in</div>
                        <div class="session-time fw-bold" id="sessionTimer">${initialDisplay}</div>
                    </div>
                </div>
            </div>
        `;
        
        sidebar.appendChild(timerContainer);
        
        // Add CSS for timer styling
        this.addTimerStyles();
    }
    
    addTimerStyles() {
        const style = document.createElement('style');
        style.textContent = `
            .session-timer-container {
                position: absolute;
                bottom: 0;
                left: 0;
                right: 0;
                background: var(--bs-body-bg);
            }
            
            .session-timer {
                background: rgba(0, 0, 0, 0.1);
                border-top: 1px solid rgba(255, 255, 255, 0.1) !important;
            }
            
            .session-time.warning {
                color: #ffc107 !important;
                animation: pulse 1s infinite;
            }
            
            .session-time.danger {
                color: #dc3545 !important;
                animation: pulse 0.5s infinite;
            }
            
            @keyframes pulse {
                0% { opacity: 1; }
                50% { opacity: 0.5; }
                100% { opacity: 1; }
            }
            
            .session-expiry-modal .modal-header {
                background-color: #ffc107;
                color: #000;
            }
        `;
        document.head.appendChild(style);
    }
    
    createLogoutModal() {
        const modalHtml = `
            <div class="modal fade session-expiry-modal" id="sessionExpiryModal" tabindex="-1" 
                 aria-labelledby="sessionExpiryModalLabel" aria-hidden="true" data-bs-backdrop="static">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header bg-warning">
                            <h5 class="modal-title text-dark" id="sessionExpiryModalLabel">
                                <i class="bi bi-exclamation-triangle me-2"></i>
                                Session Expiring Soon
                            </h5>
                        </div>
                        <div class="modal-body">
                            <div class="text-center">
                                <div class="mb-3">
                                    <i class="bi bi-clock-history display-4 text-warning"></i>
                                </div>
                                <p class="mb-3">Your session will expire in <strong><span id="modalTimer">0:00</span></strong>.</p>
                                <p class="text-muted">You will be automatically logged out for security reasons.</p>
                            </div>
                        </div>
                        <div class="modal-footer justify-content-center">
                            <button type="button" class="btn btn-success" id="renewBtn">
                                <i class="bi bi-arrow-clockwise me-1"></i>
                                Renew Session
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        document.body.insertAdjacentHTML('beforeend', modalHtml);
    }
    
    startTimer() {
        this.timerInterval = setInterval(() => {
            this.updateTimer();
        }, 1000);
    }
    
    updateTimer() {
        const elapsed = Date.now() - this.startTime;
        const remaining = this.sessionDuration - elapsed;
        
        if (remaining <= 0) {
            this.handleSessionExpired();
            return;
        }
        
        // Show warning at 5 minutes
        if (remaining <= this.warningTime && !this.warningShown) {
            this.showLogoutModal();
        }
        
        this.updateTimerDisplay(remaining);
    }
    
    updateTimerDisplay(remaining) {
        const minutes = Math.floor(remaining / 60000);
        const seconds = Math.floor((remaining % 60000) / 1000);
        const display = `${minutes}:${seconds.toString().padStart(2, '0')}`;
        
        const timerElement = document.getElementById('sessionTimer');
        const modalTimerElement = document.getElementById('modalTimer');
        
        if (timerElement) {
            timerElement.textContent = display;
            
            // Add visual warnings
            timerElement.classList.remove('warning', 'danger');
            if (remaining <= this.warningTime) {
                if (remaining <= 120000) { // 2 minutes
                    timerElement.classList.add('danger');
                } else {
                    timerElement.classList.add('warning');
                }
            }
        }
        
        if (modalTimerElement && this.warningShown) {
            const warningRemaining = remaining;
            const warningMinutes = Math.floor(warningRemaining / 60000);
            const warningSeconds = Math.floor((warningRemaining % 60000) / 1000);
            modalTimerElement.textContent = `${warningMinutes}:${warningSeconds.toString().padStart(2, '0')}`;
        }
    }
    
    showLogoutModal() {
        this.warningShown = true;
        
        // Calculate and set initial modal timer display
        const elapsed = Date.now() - this.startTime;
        const remaining = Math.max(0, this.sessionDuration - elapsed);
        const minutes = Math.floor(remaining / 60000);
        const seconds = Math.floor((remaining % 60000) / 1000);
        const modalTimerElement = document.getElementById('modalTimer');
        if (modalTimerElement) {
            modalTimerElement.textContent = `${minutes}:${seconds.toString().padStart(2, '0')}`;
        }
        
        const modal = new bootstrap.Modal(document.getElementById('sessionExpiryModal'));
        modal.show();
        
        // Play notification sound if available
        this.playNotificationSound();
    }
    
    playNotificationSound() {
        try {
            // Create a simple beep sound
            const audioContext = new (window.AudioContext || window.webkitAudioContext)();
            const oscillator = audioContext.createOscillator();
            const gainNode = audioContext.createGain();
            
            oscillator.connect(gainNode);
            gainNode.connect(audioContext.destination);
            
            oscillator.frequency.value = 800;
            oscillator.type = 'sine';
            
            gainNode.gain.setValueAtTime(0.3, audioContext.currentTime);
            gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.5);
            
            oscillator.start(audioContext.currentTime);
            oscillator.stop(audioContext.currentTime + 0.5);
        } catch (e) {
            console.log('Audio notification not available');
        }
    }
    
    getSessionStartTime() {
        // Try to get existing session start time from localStorage
        const storedStartTime = localStorage.getItem('adminSessionStartTime');
        if (storedStartTime) {
            const startTime = parseInt(storedStartTime);
            const elapsed = Date.now() - startTime;
            
            // If less than session duration has passed, use stored time
            if (elapsed < this.sessionDuration) {
                console.log(`Session continuing - ${Math.floor((this.sessionDuration - elapsed) / 60000)} minutes remaining`);
                return startTime;
            } else {
                console.log('Stored session expired, starting new session');
                localStorage.removeItem('adminSessionStartTime');
            }
        }
        
        // Create new session start time
        const newStartTime = Date.now();
        localStorage.setItem('adminSessionStartTime', newStartTime.toString());
        console.log('New session started - 30 minutes duration');
        return newStartTime;
    }
    
    bindEvents() {
        // Renew session button
        document.addEventListener('click', (e) => {
            if (e.target.id === 'renewBtn' || e.target.closest('#renewBtn')) {
                e.preventDefault();
                this.renewSession();
            }
        });
        
        // Note: User activity tracking disabled to prevent unexpected timer resets
        // The session timer will run for the full duration without automatic extensions
        // Users can manually renew the session when the warning appears
    }
    

    
    renewSession() {
        // Reset the session timer
        this.startTime = Date.now();
        localStorage.setItem('adminSessionStartTime', this.startTime.toString());
        this.warningShown = false;
        this.hideLogoutModal();
        
        const timerElement = document.getElementById('sessionTimer');
        if (timerElement) {
            timerElement.classList.remove('warning', 'danger');
        }
        
        // Show success message
        this.showSuccessMessage('Session renewed for 30 minutes!');
    }
    
    resetTimer() {
        this.startTime = Date.now();
        localStorage.setItem('adminSessionStartTime', this.startTime.toString());
        this.warningShown = false;
        this.hideLogoutModal();
        
        const timerElement = document.getElementById('sessionTimer');
        if (timerElement) {
            timerElement.classList.remove('warning', 'danger');
        }
    }
    
    hideLogoutModal() {
        const modal = bootstrap.Modal.getInstance(document.getElementById('sessionExpiryModal'));
        if (modal) {
            modal.hide();
        }
    }
    
    handleSessionExpired() {
        clearInterval(this.timerInterval);
        this.showErrorMessage('Session expired. Logging out...');
        setTimeout(() => this.logout(), 2000);
    }
    
    logout() {
        // Clear any existing intervals
        if (this.timerInterval) {
            clearInterval(this.timerInterval);
        }
        
        // Clear session start time from localStorage
        localStorage.removeItem('adminSessionStartTime');
        
        // Redirect to logout (which will destroy the session)
        window.location.href = 'logout.php?logout=1';
    }
    
    showSuccessMessage(message) {
        if (typeof toastr !== 'undefined') {
            toastr.success(message);
        } else if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: message,
                timer: 3000,
                showConfirmButton: false
            });
        } else {
            alert(message);
        }
    }
    
    showErrorMessage(message) {
        if (typeof toastr !== 'undefined') {
            toastr.error(message);
        } else if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: message,
                timer: 3000,
                showConfirmButton: false
            });
        } else {
            alert(message);
        }
    }

}

// Initialize session manager when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    // Only initialize if we're in the admin area and user is logged in
    if (document.body.classList.contains('layout-fixed') && 
        document.querySelector('.app-sidebar')) {
        window.sessionManager = new SessionManager();
    }
});

// Add spinning animation for loading states
const spinStyle = document.createElement('style');
spinStyle.textContent = `
    .spin {
        animation: spin 1s linear infinite;
    }
    
    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
`;
document.head.appendChild(spinStyle);