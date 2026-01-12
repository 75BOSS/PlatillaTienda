<?php
/**
 * Script para configurar automáticamente Supabase Storage
 * Accede a: /admin/setup-supabase.php
 */

require_once '../../config/config.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setup Supabase - <?php echo APP_NAME; ?></title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; }
        .step { margin: 20px 0; padding: 20px; border: 1px solid #ddd; border-radius: 8px; }
        .success { background: #d4edda; border-color: #c3e6cb; color: #155724; }
        .error { background: #f8d7da; border-color: #f5c6cb; color: #721c24; }
        .warning { background: #fff3cd; border-color: #ffeaa7; color: #856404; }
        .info { background: #d1ecf1; border-color: #bee5eb; color: #0c5460; }
        button { padding: 10px 20px; margin: 10px 5px; cursor: pointer; background: #007bff; color: white; border: none; border-radius: 4px; }
        button:hover { background: #0056b3; }
        .code { background: #f8f9fa; padding: 10px; border-radius: 4px; font-family: monospace; margin: 10px 0; }
        .progress { display: none; }
    </style>
</head>
<body>
    <h1>🚀 Setup Automático de Supabase Storage</h1>
    
    <div class="step info">
        <h3>📋 Información del Proyecto</h3>
        <p><strong>URL:</strong> <?php echo SUPABASE_URL; ?></p>
        <p><strong>Bucket:</strong> <?php echo SUPABASE_BUCKET; ?></p>
        <p><strong>Clave configurada:</strong> ✅ Sí</p>
    </div>

    <div class="step">
        <h3>1️⃣ Verificar Conexión</h3>
        <button onclick="step1_testConnection()">Probar Conexión</button>
        <div id="step1Result"></div>
    </div>

    <div class="step">
        <h3>2️⃣ Verificar/Crear Bucket</h3>
        <button onclick="step2_checkBucket()">Verificar Bucket</button>
        <button onclick="step2_createBucket()" style="background: #28a745;">Crear Bucket</button>
        <div id="step2Result"></div>
    </div>

    <div class="step">
        <h3>3️⃣ Crear Carpetas</h3>
        <button onclick="step3_createFolders()">Crear Carpetas (products, categories)</button>
        <div id="step3Result"></div>
    </div>

    <div class="step">
        <h3>4️⃣ Configurar Políticas RLS</h3>
        <div class="warning">
            <p><strong>⚠️ Importante:</strong> Las políticas RLS deben configurarse manualmente en el dashboard de Supabase.</p>
            <p>Ve a: <a href="https://supabase.com/dashboard/project/wlaxhnfvtcdgcybsvlby/storage/policies" target="_blank">Storage Policies</a></p>
        </div>
        
        <h4>Política para INSERTAR archivos:</h4>
        <div class="code">
CREATE POLICY "Allow public uploads" ON storage.objects
FOR INSERT WITH CHECK (bucket_id = 'imagenes');
        </div>
        
        <h4>Política para LEER archivos:</h4>
        <div class="code">
CREATE POLICY "Allow public access" ON storage.objects
FOR SELECT USING (bucket_id = 'imagenes');
        </div>
        
        <button onclick="step4_testPolicies()">Probar Políticas</button>
        <div id="step4Result"></div>
    </div>

    <div class="step">
        <h3>5️⃣ Test Final</h3>
        <input type="file" id="finalTestFile" accept="image/*">
        <button onclick="step5_finalTest()">Subir Imagen de Prueba</button>
        <div id="step5Result"></div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/@supabase/supabase-js@2"></script>
    <script>
        const supabaseUrl = '<?php echo SUPABASE_URL; ?>';
        const supabaseKey = '<?php echo SUPABASE_ANON_KEY; ?>';
        const bucketName = '<?php echo SUPABASE_BUCKET; ?>';
        
        let supabaseClient = null;

        function showResult(elementId, message, type = 'success') {
            const element = document.getElementById(elementId);
            element.innerHTML = `<div class="${type} step" style="margin-top: 10px;">${message}</div>`;
        }

        async function step1_testConnection() {
            try {
                console.log('🔗 Testing connection...');
                supabaseClient = supabase.createClient(supabaseUrl, supabaseKey);
                
                const { data, error } = await supabaseClient.storage.listBuckets();
                
                if (error) throw error;
                
                showResult('step1Result', `✅ Conexión exitosa! Buckets encontrados: ${data.length}`);
                console.log('Buckets disponibles:', data);
                
            } catch (error) {
                console.error('❌ Connection error:', error);
                showResult('step1Result', `❌ Error: ${error.message}`, 'error');
            }
        }

        async function step2_checkBucket() {
            if (!supabaseClient) {
                showResult('step2Result', '❌ Primero ejecuta el paso 1', 'error');
                return;
            }

            try {
                const { data, error } = await supabaseClient.storage.getBucket(bucketName);
                
                if (error) {
                    if (error.message.includes('not found')) {
                        showResult('step2Result', `⚠️ Bucket "${bucketName}" no existe. Haz clic en "Crear Bucket"`, 'warning');
                    } else {
                        throw error;
                    }
                } else {
                    showResult('step2Result', `✅ Bucket "${bucketName}" existe y está configurado como: ${data.public ? 'PÚBLICO' : 'PRIVADO'}`);
                }
                
            } catch (error) {
                showResult('step2Result', `❌ Error: ${error.message}`, 'error');
            }
        }

        async function step2_createBucket() {
            if (!supabaseClient) {
                showResult('step2Result', '❌ Primero ejecuta el paso 1', 'error');
                return;
            }

            try {
                const { data, error } = await supabaseClient.storage.createBucket(bucketName, {
                    public: true,
                    allowedMimeTypes: ['image/jpeg', 'image/png', 'image/webp', 'image/gif'],
                    fileSizeLimit: 5242880 // 5MB
                });
                
                if (error) {
                    if (error.message.includes('already exists')) {
                        showResult('step2Result', `✅ Bucket "${bucketName}" ya existe`, 'success');
                    } else {
                        throw error;
                    }
                } else {
                    showResult('step2Result', `✅ Bucket "${bucketName}" creado exitosamente como PÚBLICO`);
                }
                
            } catch (error) {
                showResult('step2Result', `❌ Error creando bucket: ${error.message}`, 'error');
            }
        }

        async function step3_createFolders() {
            if (!supabaseClient) {
                showResult('step3Result', '❌ Primero ejecuta el paso 1', 'error');
                return;
            }

            try {
                // Crear archivo placeholder en cada carpeta
                const folders = ['products', 'categories'];
                const results = [];
                
                for (const folder of folders) {
                    const { data, error } = await supabaseClient.storage
                        .from(bucketName)
                        .upload(`${folder}/.placeholder`, new Blob(['placeholder']), {
                            upsert: true
                        });
                    
                    if (error && !error.message.includes('already exists')) {
                        results.push(`❌ ${folder}: ${error.message}`);
                    } else {
                        results.push(`✅ ${folder}: Creada`);
                    }
                }
                
                showResult('step3Result', results.join('<br>'));
                
            } catch (error) {
                showResult('step3Result', `❌ Error: ${error.message}`, 'error');
            }
        }

        async function step4_testPolicies() {
            if (!supabaseClient) {
                showResult('step4Result', '❌ Primero ejecuta el paso 1', 'error');
                return;
            }

            try {
                // Test de lectura
                const { data: listData, error: listError } = await supabaseClient.storage
                    .from(bucketName)
                    .list('', { limit: 1 });
                
                if (listError) {
                    showResult('step4Result', `❌ Error de lectura: ${listError.message}<br>
                        <strong>Solución:</strong> Configura las políticas RLS manualmente en el dashboard`, 'error');
                    return;
                }
                
                // Test de escritura con archivo pequeño
                const testBlob = new Blob(['test'], { type: 'text/plain' });
                const { data: uploadData, error: uploadError } = await supabaseClient.storage
                    .from(bucketName)
                    .upload(`test/policy_test_${Date.now()}.txt`, testBlob);
                
                if (uploadError) {
                    showResult('step4Result', `❌ Error de escritura: ${uploadError.message}<br>
                        <strong>Solución:</strong> Configura las políticas RLS manualmente en el dashboard`, 'error');
                } else {
                    showResult('step4Result', `✅ Políticas funcionando correctamente!<br>
                        ✅ Lectura: OK<br>
                        ✅ Escritura: OK`);
                }
                
            } catch (error) {
                showResult('step4Result', `❌ Error: ${error.message}`, 'error');
            }
        }

        async function step5_finalTest() {
            const fileInput = document.getElementById('finalTestFile');
            const file = fileInput.files[0];
            
            if (!file) {
                showResult('step5Result', '❌ Selecciona una imagen primero', 'error');
                return;
            }

            if (!supabaseClient) {
                showResult('step5Result', '❌ Primero ejecuta el paso 1', 'error');
                return;
            }

            try {
                const fileName = `test/${Date.now()}_${file.name}`;
                
                showResult('step5Result', '🔄 Subiendo archivo...', 'info');
                
                const { data, error } = await supabaseClient.storage
                    .from(bucketName)
                    .upload(fileName, file);
                
                if (error) throw error;
                
                const { data: publicUrlData } = supabaseClient.storage
                    .from(bucketName)
                    .getPublicUrl(fileName);
                
                showResult('step5Result', `🎉 ¡ÉXITO TOTAL!<br>
                    <strong>Archivo subido:</strong> ${fileName}<br>
                    <strong>URL pública:</strong> <a href="${publicUrlData.publicUrl}" target="_blank">Ver imagen</a><br>
                    <img src="${publicUrlData.publicUrl}" style="max-width: 200px; margin-top: 10px; border-radius: 8px;">`);
                
            } catch (error) {
                showResult('step5Result', `❌ Error final: ${error.message}`, 'error');
            }
        }
    </script>
</body>
</html>