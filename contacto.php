<?php
/**
 * ===================================================================
 * CONTACTO.PHP - Página de Contacto
 * ===================================================================
 */

require_once __DIR__ . '/config/config.php';

// Procesar formulario si se envió
$formSubmitted = false;
$formError = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = isset($_POST['nombre']) ? trim($_POST['nombre']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $telefono = isset($_POST['telefono']) ? trim($_POST['telefono']) : '';
    $mensaje = isset($_POST['mensaje']) ? trim($_POST['mensaje']) : '';
    
    // Validación básica
    if (empty($nombre) || empty($email) || empty($mensaje)) {
        $formError = 'Por favor completa todos los campos requeridos';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $formError = 'Por favor ingresa un email válido';
    } else {
        // Generar mensaje de WhatsApp
        $whatsappMessage = "*Nuevo mensaje de contacto*\n\n";
        $whatsappMessage .= "Nombre: $nombre\n";
        $whatsappMessage .= "Email: $email\n";
        $whatsappMessage .= "Teléfono: $telefono\n\n";
        $whatsappMessage .= "Mensaje:\n$mensaje";
        
        $whatsappLink = 'https://wa.me/' . str_replace(['+', ' ', '-'], '', WHATSAPP_NUMBER) . 
                       '?text=' . urlencode($whatsappMessage);
        
        // Redirect a WhatsApp
        header("Location: $whatsappLink");
        exit;
    }
}

// Configuración de la página
$pageTitle = "Contacto";
$currentPage = "contacto";
$pageCSS = [
    'components/cards.css',
    'pages/contact.css'
];

include __DIR__ . '/public/includes/header.php';
?>

<!-- Page Header -->
<section style="padding:4rem 0 2rem;background:linear-gradient(135deg,var(--primary-color),var(--primary-dark));">
    <div class="container">
        <div style="max-width:700px;margin:0 auto;text-align:center;">
            <h1 style="font-size:var(--text-5xl);font-weight:var(--font-black);color:white;margin-bottom:var(--space-md);">
                Contáctanos
            </h1>
            <p style="font-size:var(--text-lg);color:rgba(255,255,255,0.9);">
                Estamos aquí para ayudarte. Envíanos un mensaje y te responderemos lo antes posible.
            </p>
        </div>
    </div>
</section>

<!-- Contact Section -->
<section class="contact-page">
    <div class="container">
        <div class="contact-grid">
            <!-- Contact Form -->
            <div class="contact-form">
                <h2 style="font-size:var(--text-2xl);font-weight:var(--font-bold);margin-bottom:var(--space-lg);">
                    Envíanos un Mensaje
                </h2>
                
                <?php if ($formError): ?>
                    <div style="background:var(--error-light);color:var(--error-color);padding:var(--space-md);border-radius:var(--radius-md);margin-bottom:var(--space-lg);">
                        <?php echo htmlspecialchars($formError); ?>
                    </div>
                <?php endif; ?>
                
                <form method="POST" action="">
                    <div class="form-group">
                        <label for="nombre" class="form-label">Nombre *</label>
                        <input type="text" 
                               id="nombre" 
                               name="nombre" 
                               class="form-input" 
                               required
                               value="<?php echo isset($_POST['nombre']) ? htmlspecialchars($_POST['nombre']) : ''; ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="email" class="form-label">Email *</label>
                        <input type="email" 
                               id="email" 
                               name="email" 
                               class="form-input" 
                               required
                               value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="telefono" class="form-label">Teléfono</label>
                        <input type="tel" 
                               id="telefono" 
                               name="telefono" 
                               class="form-input"
                               value="<?php echo isset($_POST['telefono']) ? htmlspecialchars($_POST['telefono']) : ''; ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="mensaje" class="form-label">Mensaje *</label>
                        <textarea id="mensaje" 
                                  name="mensaje" 
                                  class="form-textarea" 
                                  rows="5" 
                                  required><?php echo isset($_POST['mensaje']) ? htmlspecialchars($_POST['mensaje']) : ''; ?></textarea>
                    </div>
                    
                    <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;">
                        Enviar Mensaje
                    </button>
                    
                    <p style="font-size:var(--text-sm);color:var(--text-gray);margin-top:var(--space-md);text-align:center;">
                        * Campos requeridos
                    </p>
                </form>
            </div>
            
            <!-- Contact Info -->
            <div class="contact-info">
                <h2 style="font-size:var(--text-2xl);font-weight:var(--font-bold);margin-bottom:var(--space-lg);">
                    Información de Contacto
                </h2>
                
                <div class="contact-item">
                    <div class="contact-icon">
                        📍
                    </div>
                    <div>
                        <h4 style="font-weight:var(--font-semibold);margin-bottom:var(--space-xs);">Ubicación</h4>
                        <p style="color:var(--text-gray);"><?php echo BUSINESS_ADDRESS; ?></p>
                    </div>
                </div>
                
                <div class="contact-item">
                    <div class="contact-icon">
                        📞
                    </div>
                    <div>
                        <h4 style="font-weight:var(--font-semibold);margin-bottom:var(--space-xs);">Teléfono</h4>
                        <p style="color:var(--text-gray);">
                            <a href="tel:<?php echo str_replace(' ', '', CONTACT_PHONE); ?>" 
                               style="color:var(--primary-color);text-decoration:none;">
                                <?php echo CONTACT_PHONE; ?>
                            </a>
                        </p>
                    </div>
                </div>
                
                <div class="contact-item">
                    <div class="contact-icon">
                        ✉️
                    </div>
                    <div>
                        <h4 style="font-weight:var(--font-semibold);margin-bottom:var(--space-xs);">Email</h4>
                        <p style="color:var(--text-gray);">
                            <a href="mailto:<?php echo CONTACT_EMAIL; ?>" 
                               style="color:var(--primary-color);text-decoration:none;">
                                <?php echo CONTACT_EMAIL; ?>
                            </a>
                        </p>
                    </div>
                </div>
                
                <div class="contact-item">
                    <div class="contact-icon">
                        🕐
                    </div>
                    <div>
                        <h4 style="font-weight:var(--font-semibold);margin-bottom:var(--space-xs);">Horario</h4>
                        <p style="color:var(--text-gray);"><?php echo BUSINESS_HOURS; ?></p>
                    </div>
                </div>
                
                <!-- WhatsApp Button -->
                <div style="margin-top:var(--space-2xl);">
                    <a href="https://wa.me/<?php echo str_replace(['+', ' ', '-'], '', WHATSAPP_NUMBER); ?>?text=<?php echo urlencode('Hola, tengo una consulta'); ?>" 
                       target="_blank"
                       class="btn btn-primary"
                       style="width:100%;justify-content:center;background-color:var(--whatsapp-color);">
                        <svg width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                        </svg>
                        Chat por WhatsApp
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Map Section (Google Maps Embed) -->
<section style="padding:0;margin-top:var(--space-4xl);">
    <div style="width:100%;height:450px;background:var(--bg-gray);display:flex;align-items:center;justify-content:center;color:var(--text-gray);">
        <!-- Reemplazar con Google Maps embed real -->
        <div style="text-align:center;padding:2rem;">
            <div style="font-size:3rem;margin-bottom:1rem;">🗺️</div>
            <p style="font-size:var(--text-lg);font-weight:var(--font-semibold);margin-bottom:0.5rem;">
                Encuéntranos en:
            </p>
            <p><?php echo BUSINESS_ADDRESS; ?></p>
            <p style="margin-top:1rem;">
                <a href="https://www.google.com/maps/search/?api=1&query=<?php echo urlencode(BUSINESS_ADDRESS); ?>" 
                   target="_blank"
                   class="btn btn-outline"
                   style="margin-top:1rem;">
                    Ver en Google Maps
                </a>
            </p>
        </div>
        
        <!-- Para activar Google Maps real, usar:
        <iframe 
            src="https://www.google.com/maps/embed?pb=..."
            width="100%" 
            height="450" 
            style="border:0;" 
            allowfullscreen="" 
            loading="lazy" 
            referrerpolicy="no-referrer-when-downgrade">
        </iframe>
        -->
    </div>
</section>

<?php include __DIR__ . '/public/includes/footer.php'; ?>
