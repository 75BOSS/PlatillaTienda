<?php
/**
 * Barra de Promoción
 */

// Cargar modelo de promociones de forma segura
$activePromo = null;
// Temporalmente deshabilitado para evitar errores
/*
try {
    require_once ROOT_PATH . '/app/models/Promotion.php';
    $promotionModel = new Promotion();
    $activePromo = $promotionModel->getActive();
} catch (Exception $e) {
    // Si hay error (ej: tabla no existe), no mostrar promoción
    error_log("Error en promo-bar: " . $e->getMessage());
    $activePromo = null;
}
*/
?>

<?php if ($activePromo): ?>
<div class="promo-bar" id="promoBar" style="background-color: <?php echo htmlspecialchars($activePromo['background_color']); ?>; color: <?php echo htmlspecialchars($activePromo['text_color']); ?>;">
    <div class="container">
        <div class="promo-content">
            <p class="promo-title"><?php echo htmlspecialchars($activePromo['title']); ?></p>
            <?php if (!empty($activePromo['description'])): ?>
            <p class="promo-description"><?php echo htmlspecialchars($activePromo['description']); ?></p>
            <?php endif; ?>
        </div>
        
        <?php if ($activePromo['show_countdown']): ?>
        <div class="promo-countdown">
            <span class="countdown-icon">⏱</span>
            <div class="countdown-timer" id="countdownTimer" data-end="<?php echo $activePromo['end_date']; ?>">
                <div class="countdown-item">
                    <span class="countdown-value" id="countDays">00</span>
                    <span class="countdown-label">d</span>
                </div>
                <span class="countdown-separator">:</span>
                <div class="countdown-item">
                    <span class="countdown-value" id="countHours">00</span>
                    <span class="countdown-label">h</span>
                </div>
                <span class="countdown-separator">:</span>
                <div class="countdown-item">
                    <span class="countdown-value" id="countMinutes">00</span>
                    <span class="countdown-label">m</span>
                </div>
                <span class="countdown-separator">:</span>
                <div class="countdown-item">
                    <span class="countdown-value" id="countSeconds">00</span>
                    <span class="countdown-label">s</span>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
    
    <button class="promo-close" onclick="closePromoBar()" aria-label="Cerrar">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <line x1="18" y1="6" x2="6" y2="18"></line>
            <line x1="6" y1="6" x2="18" y2="18"></line>
        </svg>
    </button>
</div>

<script>
// Countdown Timer
function initCountdown() {
    const timer = document.getElementById('countdownTimer');
    if (!timer) return;
    
    const endDate = new Date(timer.dataset.end).getTime();
    
    function updateCountdown() {
        const now = new Date().getTime();
        const distance = endDate - now;
        
        if (distance < 0) {
            document.getElementById('promoBar').style.display = 'none';
            return;
        }
        
        const days = Math.floor(distance / (1000 * 60 * 60 * 24));
        const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((distance % (1000 * 60)) / 1000);
        
        document.getElementById('countDays').textContent = String(days).padStart(2, '0');
        document.getElementById('countHours').textContent = String(hours).padStart(2, '0');
        document.getElementById('countMinutes').textContent = String(minutes).padStart(2, '0');
        document.getElementById('countSeconds').textContent = String(seconds).padStart(2, '0');
    }
    
    updateCountdown();
    setInterval(updateCountdown, 1000);
}

// Cerrar barra de promoción
function closePromoBar() {
    document.getElementById('promoBar').style.display = 'none';
    sessionStorage.setItem('promoBarClosed', 'true');
}

// Verificar si ya se cerró
document.addEventListener('DOMContentLoaded', function() {
    if (sessionStorage.getItem('promoBarClosed') === 'true') {
        const promoBar = document.getElementById('promoBar');
        if (promoBar) promoBar.style.display = 'none';
    } else {
        initCountdown();
    }
});
</script>
<?php endif; ?>