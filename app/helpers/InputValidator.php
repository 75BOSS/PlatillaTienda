<?php
/**
 * ===================================================================
 * INPUT VALIDATOR - VALIDACIÓN ROBUSTA DE DATOS
 * ===================================================================
 * Clase para validar y sanitizar todos los datos de entrada del sistema
 * Implementa validaciones de seguridad y consistencia
 */

class InputValidator {
    
    /**
     * Validar datos de producto
     * @param array $data Datos del formulario
     * @param int|null $excludeId ID a excluir en validaciones de unicidad
     * @return array ['data' => array, 'errors' => array]
     */
    public static function validateProduct($data, $excludeId = null) {
        $errors = [];
        $cleanData = [];
        
        // Validar nombre
        if (empty($data['name'])) {
            $errors['name'] = 'El nombre es requerido';
        } else {
            $cleanData['name'] = self::sanitizeString($data['name']);
            if (strlen($cleanData['name']) < 3) {
                $errors['name'] = 'El nombre debe tener al menos 3 caracteres';
            } elseif (strlen($cleanData['name']) > 200) {
                $errors['name'] = 'El nombre no puede exceder 200 caracteres';
            } elseif (self::containsMaliciousContent($cleanData['name'])) {
                $errors['name'] = 'El nombre contiene caracteres no permitidos';
            }
        }
        
        // Validar precio
        if (!isset($data['price']) || $data['price'] === '') {
            $errors['price'] = 'El precio es requerido';
        } else {
            $cleanData['price'] = filter_var($data['price'], FILTER_VALIDATE_FLOAT);
            if ($cleanData['price'] === false) {
                $errors['price'] = 'El precio debe ser un número válido';
            } elseif ($cleanData['price'] < 0) {
                $errors['price'] = 'El precio no puede ser negativo';
            } elseif ($cleanData['price'] > 999999.99) {
                $errors['price'] = 'El precio excede el máximo permitido (999,999.99)';
            }
        }
        
        // Validar categoría
        if (empty($data['category_id'])) {
            $errors['category_id'] = 'Debes seleccionar una categoría';
        } else {
            $cleanData['category_id'] = filter_var($data['category_id'], FILTER_VALIDATE_INT);
            if ($cleanData['category_id'] === false || $cleanData['category_id'] <= 0) {
                $errors['category_id'] = 'La categoría seleccionada no es válida';
            }
        }
        
        // Validar descripción (opcional)
        if (isset($data['description'])) {
            $cleanData['description'] = self::sanitizeText($data['description']);
            if (strlen($cleanData['description']) > 2000) {
                $errors['description'] = 'La descripción no puede exceder 2000 caracteres';
            } elseif (self::containsMaliciousContent($cleanData['description'])) {
                $errors['description'] = 'La descripción contiene caracteres no permitidos';
            }
        } else {
            $cleanData['description'] = '';
        }
        
        // Validar URL de imagen (opcional)
        if (!empty($data['image_url'])) {
            $cleanData['image_url'] = filter_var(trim($data['image_url']), FILTER_VALIDATE_URL);
            if ($cleanData['image_url'] === false) {
                $errors['image_url'] = 'La URL de la imagen no es válida';
            } elseif (strlen($cleanData['image_url']) > 500) {
                $errors['image_url'] = 'La URL de la imagen es demasiado larga';
            } elseif (!self::isValidImageUrl($cleanData['image_url'])) {
                $errors['image_url'] = 'La URL debe apuntar a una imagen válida (jpg, png, gif, webp)';
            }
        } else {
            $cleanData['image_url'] = '';
        }
        
        // Validar stock (opcional)
        if (isset($data['stock'])) {
            $cleanData['stock'] = filter_var($data['stock'], FILTER_VALIDATE_INT);
            if ($cleanData['stock'] === false) {
                $cleanData['stock'] = 0;
            } elseif ($cleanData['stock'] < 0) {
                $errors['stock'] = 'El stock no puede ser negativo';
            } elseif ($cleanData['stock'] > 999999) {
                $errors['stock'] = 'El stock excede el máximo permitido';
            }
        } else {
            $cleanData['stock'] = 0;
        }
        
        // Validar estado activo
        $cleanData['is_active'] = isset($data['is_active']) ? 1 : 0;
        
        return ['data' => $cleanData, 'errors' => $errors];
    }
    
    /**
     * Validar datos de categoría
     * @param array $data Datos del formulario
     * @param int|null $excludeId ID a excluir en validaciones de unicidad
     * @return array ['data' => array, 'errors' => array]
     */
    public static function validateCategory($data, $excludeId = null) {
        $errors = [];
        $cleanData = [];
        
        // Validar nombre
        if (empty($data['name'])) {
            $errors['name'] = 'El nombre es requerido';
        } else {
            $cleanData['name'] = self::sanitizeString($data['name']);
            if (strlen($cleanData['name']) < 3) {
                $errors['name'] = 'El nombre debe tener al menos 3 caracteres';
            } elseif (strlen($cleanData['name']) > 255) {
                $errors['name'] = 'El nombre no puede exceder 255 caracteres';
            } elseif (self::containsMaliciousContent($cleanData['name'])) {
                $errors['name'] = 'El nombre contiene caracteres no permitidos';
            }
        }
        
        // Validar tipo de producto
        if (!empty($data['product_type'])) {
            $validTypes = ['clothing', 'footwear', 'electronics', 'food', 'furniture', 'health_beauty', 'services'];
            if (in_array($data['product_type'], $validTypes)) {
                $cleanData['product_type'] = $data['product_type'];
            } else {
                $errors['product_type'] = 'El tipo de producto seleccionado no es válido';
            }
        } else {
            $cleanData['product_type'] = 'clothing'; // Valor por defecto
        }
        
        // Validar descripción (opcional)
        if (isset($data['description'])) {
            $cleanData['description'] = self::sanitizeText($data['description']);
            if (strlen($cleanData['description']) > 1000) {
                $errors['description'] = 'La descripción no puede exceder 1000 caracteres';
            } elseif (self::containsMaliciousContent($cleanData['description'])) {
                $errors['description'] = 'La descripción contiene caracteres no permitidos';
            }
        } else {
            $cleanData['description'] = '';
        }
        
        // Validar URL de imagen (opcional)
        if (!empty($data['image_url'])) {
            $cleanData['image_url'] = filter_var(trim($data['image_url']), FILTER_VALIDATE_URL);
            if ($cleanData['image_url'] === false) {
                $errors['image_url'] = 'La URL de la imagen no es válida';
            } elseif (strlen($cleanData['image_url']) > 500) {
                $errors['image_url'] = 'La URL de la imagen es demasiado larga';
            } elseif (!self::isValidImageUrl($cleanData['image_url'])) {
                $errors['image_url'] = 'La URL debe apuntar a una imagen válida (jpg, png, gif, webp)';
            }
        } else {
            $cleanData['image_url'] = '';
        }
        
        // Validar categoría padre (opcional)
        if (!empty($data['parent_id'])) {
            $cleanData['parent_id'] = filter_var($data['parent_id'], FILTER_VALIDATE_INT);
            if ($cleanData['parent_id'] === false || $cleanData['parent_id'] <= 0) {
                $errors['parent_id'] = 'La categoría padre seleccionada no es válida';
            } elseif ($excludeId && $cleanData['parent_id'] == $excludeId) {
                $errors['parent_id'] = 'Una categoría no puede ser su propia categoría padre';
            }
        } else {
            $cleanData['parent_id'] = null;
        }
        
        // Validar orden de clasificación
        if (isset($data['sort_order'])) {
            $cleanData['sort_order'] = filter_var($data['sort_order'], FILTER_VALIDATE_INT);
            if ($cleanData['sort_order'] === false) {
                $cleanData['sort_order'] = 0;
            } elseif ($cleanData['sort_order'] < 0 || $cleanData['sort_order'] > 9999) {
                $errors['sort_order'] = 'El orden debe estar entre 0 y 9999';
            }
        } else {
            $cleanData['sort_order'] = 0;
        }
        
        // Validar estado activo
        $cleanData['is_active'] = isset($data['is_active']) ? 1 : 0;
        
        return ['data' => $cleanData, 'errors' => $errors];
    }
    
    /**
     * Validar campo dinámico de producto
     * @param string $fieldKey Clave del campo
     * @param mixed $value Valor del campo
     * @param array $config Configuración del campo
     * @return true|string true si es válido, mensaje de error si no
     */
    public static function validateDynamicField($fieldKey, $value, $config) {
        $type = $config['type'] ?? 'text';
        $label = $config['label'] ?? $fieldKey;
        
        // Si el campo está vacío, generalmente es válido (campos opcionales)
        if (empty($value)) {
            return true;
        }
        
        // Sanitizar valor
        $value = is_string($value) ? trim($value) : $value;
        
        // Validaciones de seguridad
        if (is_string($value) && self::containsMaliciousContent($value)) {
            return "El campo '$label' contiene contenido no permitido";
        }
        
        switch ($type) {
            case 'number':
                if (!is_numeric($value)) {
                    return "El campo '$label' debe ser un número válido";
                }
                $numValue = (float)$value;
                if ($numValue < 0) {
                    return "El campo '$label' no puede ser negativo";
                }
                if ($numValue > 999999999) {
                    return "El campo '$label' excede el valor máximo permitido";
                }
                break;
                
            case 'text':
                if (strlen($value) < 1) {
                    return "El campo '$label' no puede estar vacío";
                }
                if (strlen($value) > 500) {
                    return "El campo '$label' no puede exceder 500 caracteres";
                }
                break;
                
            case 'textarea':
                if (strlen($value) > 2000) {
                    return "El campo '$label' no puede exceder 2000 caracteres";
                }
                break;
                
            case 'select':
                $options = $config['options'] ?? [];
                if (!empty($options) && !in_array($value, $options)) {
                    $validOptions = implode(', ', array_slice($options, 0, 3));
                    if (count($options) > 3) $validOptions .= '...';
                    return "El valor '$value' no es válido para '$label'. Opciones válidas: $validOptions";
                }
                break;
                
            case 'buttons':
                // Para campos tipo buttons (valores separados por coma)
                if (strlen($value) > 200) {
                    return "El campo '$label' no puede exceder 200 caracteres";
                }
                // Validar formato de valores separados por coma
                $values = array_map('trim', explode(',', $value));
                foreach ($values as $val) {
                    if (empty($val)) {
                        return "El campo '$label' contiene valores vacíos. Separa los valores con comas sin espacios extra";
                    }
                    if (strlen($val) > 50) {
                        return "Cada valor en '$label' no puede exceder 50 caracteres";
                    }
                }
                if (count($values) > 20) {
                    return "El campo '$label' no puede tener más de 20 valores";
                }
                break;
                
            default:
                // Validación genérica para tipos desconocidos
                if (is_string($value) && strlen($value) > 500) {
                    return "El campo '$label' no puede exceder 500 caracteres";
                }
        }
        
        return true;
    }
    
    /**
     * Sanitizar string básico
     * @param string $input
     * @return string
     */
    private static function sanitizeString($input) {
        return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
    }
    
    /**
     * Sanitizar texto largo (permite algunos HTML básicos)
     * @param string $input
     * @return string
     */
    private static function sanitizeText($input) {
        // Permitir algunos tags básicos pero escapar el resto
        $allowedTags = '<p><br><strong><em><ul><ol><li>';
        return strip_tags(trim($input), $allowedTags);
    }
    
    /**
     * Detectar contenido malicioso
     * @param string $value
     * @return bool
     */
    private static function containsMaliciousContent($value) {
        $maliciousPatterns = [
            '/<script[^>]*>.*?<\/script>/is',
            '/javascript:/i',
            '/on\w+\s*=/i',
            '/<iframe/i',
            '/<object/i',
            '/<embed/i',
            '/data:text\/html/i',
            '/vbscript:/i',
            '/expression\s*\(/i',
            '/@import/i'
        ];
        
        foreach ($maliciousPatterns as $pattern) {
            if (preg_match($pattern, $value)) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Validar si una URL apunta a una imagen válida
     * @param string $url
     * @return bool
     */
    private static function isValidImageUrl($url) {
        // Verificar extensión
        if (!preg_match('/\.(jpg|jpeg|png|gif|webp)(\?.*)?$/i', $url)) {
            return false;
        }
        
        // Verificar que no sea una URL sospechosa
        $suspiciousDomains = ['javascript:', 'data:', 'vbscript:'];
        foreach ($suspiciousDomains as $domain) {
            if (stripos($url, $domain) === 0) {
                return false;
            }
        }
        
        return true;
    }
    
    /**
     * Validar email
     * @param string $email
     * @return bool
     */
    public static function validateEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
    
    /**
     * Validar número de teléfono (formato internacional)
     * @param string $phone
     * @return bool
     */
    public static function validatePhone($phone) {
        // Permitir números con +, espacios, guiones y paréntesis
        $cleanPhone = preg_replace('/[^\d+]/', '', $phone);
        return preg_match('/^\+?[1-9]\d{7,14}$/', $cleanPhone);
    }
    
    /**
     * Generar token CSRF
     * @return string
     */
    public static function generateCSRFToken() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        $token = bin2hex(random_bytes(32));
        $_SESSION['csrf_token'] = $token;
        return $token;
    }
    
    /**
     * Validar token CSRF
     * @param string $token
     * @return bool
     */
    public static function validateCSRFToken($token) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }
}