// =============================================================================
// Aura — Service Worker (sw.js)
// Estrategia de caché segura compatible con MFA, CSRF y CSP estricto
// Versión: 2.30.2
// =============================================================================

const CACHE_VERSION = 'aura-v2.30.2';
const STATIC_CACHE = `${CACHE_VERSION}-static`;
const RUNTIME_CACHE = `${CACHE_VERSION}-runtime`;

// ─────────────────────────────────────────────────────────────────────────────
// Recursos estáticos para pre-cachear (App Shell)
// Solo assets inmutables que NO contienen datos dinámicos ni tokens
// ─────────────────────────────────────────────────────────────────────────────
const PRECACHE_URLS = [
  '/offline.html',
  '/assets/images/icons/icon-192x192.png',
  '/assets/images/icons/icon-512x512.png',
  '/favicon.ico',
  '/icono.png',
  '/logo-connombre.png'
];

// ─────────────────────────────────────────────────────────────────────────────
// Rutas que NUNCA deben cachearse (seguridad crítica)
// Incluye: autenticación, tokens CSRF, APIs con datos sensibles, sesiones
// ─────────────────────────────────────────────────────────────────────────────
const NEVER_CACHE_PATTERNS = [
  /\/api\//,                    // Todas las APIs (contienen tokens CSRF)
  /\/login/,                    // Flujos de autenticación
  /\/logout/,                   // Cierre de sesión
  /\/auth\//,                   // 2FA, WebAuthn, TOTP
  /\/password\//,               // Recuperación de contraseña
  /\/admin\/api\//,             // APIs administrativas
  /\/admin\/update/,            // Sistema de actualización
  /\/admin\/update\/check/,      // Verificación de actualizaciones
  /\/admin\/settings/,          // Configuración sensible
  /\/profile\//,                // Perfil y 2FA del usuario
  /\/staff\/reports\/.*\/messages/, // Mensajes (datos en tiempo real)
  /\/alumno\/report/,           // Envío de reportes
  /\/alumno\/reports\/.*\/messages/, // Mensajes del alumno
  /\.php$/,                     // Cualquier PHP directo (por seguridad)
  /\/lang\/switch/,             // Cambio de idioma (POST con CSRF)
  /\/Ceuta2000/,                // Ruta secreta de mantenimiento
  /\?.*csrf/i,                  // Cualquier URL con token CSRF en query
];

// ─────────────────────────────────────────────────────────────────────────────
// Patrones de assets estáticos cacheables (Cache-First)
// ─────────────────────────────────────────────────────────────────────────────
const STATIC_ASSET_PATTERNS = [
  /\.(?:png|jpg|jpeg|gif|svg|ico|webp)$/i,  // Imágenes
  /\.(?:css|js)$/i,                         // CSS y JS estáticos
  /\.(?:woff|woff2|ttf|otf|eot)$/i,         // Fuentes (incl. Material Symbols)
  /fonts\.googleapis\.com/,                 // Google Fonts CSS
  /fonts\.gstatic\.com/,                    // Google Fonts archivos
  /cdn\.tailwindcss\.com/,                  // Tailwind CDN
  /cdn\.jsdelivr\.net/,                     // Bootstrap/CDN
];


// =============================================================================
// INSTALL — Pre-cachear App Shell
// =============================================================================
self.addEventListener('install', (event) => {
  console.log('[Aura SW] Instalando versión:', CACHE_VERSION);

  event.waitUntil(
    caches.open(STATIC_CACHE)
      .then((cache) => {
        console.log('[Aura SW] Pre-cacheando App Shell');
        return cache.addAll(PRECACHE_URLS);
      })
      .then(() => self.skipWaiting()) // Activar inmediatamente
  );
});


// =============================================================================
// ACTIVATE — Limpiar cachés antiguas al actualizar
// =============================================================================
self.addEventListener('activate', (event) => {
  console.log('[Aura SW] Activando versión:', CACHE_VERSION);

  event.waitUntil(
    caches.keys()
      .then((cacheNames) => {
        return Promise.all(
          cacheNames
            .filter((name) => name !== STATIC_CACHE && name !== RUNTIME_CACHE)
            .map((name) => {
              console.log('[Aura SW] Eliminando caché antigua:', name);
              return caches.delete(name);
            })
        );
      })
      .then(() => self.clients.claim()) // Tomar control de todas las pestañas
  );
});


// =============================================================================
// FETCH — Estrategia de intercepción segura
// =============================================================================
self.addEventListener('fetch', (event) => {
  const { request } = event;
  const url = new URL(request.url);

  // ───────────────────────────────────────────────────────────────────────
  // REGLA 1: Solo interceptar GET. POST/PUT/PATCH/DELETE van directo a red.
  // Esto protege TODOS los envíos de formularios, tokens CSRF y mutations.
  // ───────────────────────────────────────────────────────────────────────
  if (request.method !== 'GET') {
    return;
  }

  // ───────────────────────────────────────────────────────────────────────
  // REGLA 2: No interceptar peticiones a dominios externos (excepto CDNs)
  // ───────────────────────────────────────────────────────────────────────
  const isExternalRequest = url.origin !== self.location.origin;
  const isAllowedCDN = STATIC_ASSET_PATTERNS.some((pattern) => pattern.test(url.href));

  if (isExternalRequest && !isAllowedCDN) {
    return;
  }

  // ───────────────────────────────────────────────────────────────────────
  // REGLA 3: NUNCA cachear rutas sensibles (auth, API, CSRF)
  // ───────────────────────────────────────────────────────────────────────
  const isSensitive = NEVER_CACHE_PATTERNS.some((pattern) => pattern.test(url.pathname));

  if (isSensitive) {
    return; // Dejar que el navegador haga la petición normal sin intervención
  }

  // ───────────────────────────────────────────────────────────────────────
  // REGLA 4: Peticiones AJAX (XMLHttpRequest) → siempre red directa
  // Los headers X-Requested-With y Accept: application/json indican AJAX
  // ───────────────────────────────────────────────────────────────────────
  if (
    request.headers.get('X-Requested-With') === 'XMLHttpRequest' ||
    request.headers.get('Accept')?.includes('application/json')
  ) {
    return;
  }

  // ───────────────────────────────────────────────────────────────────────
  // REGLA 5: Assets estáticos → Cache-First (rápido, inmutable)
  // ───────────────────────────────────────────────────────────────────────
  const isStaticAsset = STATIC_ASSET_PATTERNS.some((pattern) => pattern.test(url.href));

  if (isStaticAsset) {
    event.respondWith(cacheFirstStrategy(request));
    return;
  }

  // ───────────────────────────────────────────────────────────────────────
  // REGLA 6: Navegación HTML → Network-First con fallback offline
  // CRÍTICO: Siempre intenta red primero para obtener tokens CSRF frescos
  // ───────────────────────────────────────────────────────────────────────
  if (request.mode === 'navigate') {
    event.respondWith(networkFirstNavigation(request));
    return;
  }

  // ───────────────────────────────────────────────────────────────────────
  // DEFAULT: Network-First para todo lo demás
  // ───────────────────────────────────────────────────────────────────────
  event.respondWith(networkFirstStrategy(request));
});


// =============================================================================
// ESTRATEGIAS DE CACHÉ
// =============================================================================

/**
 * Cache-First: Sirve desde caché si existe, sino va a la red.
 * Ideal para assets estáticos inmutables (imágenes, fuentes, CDNs).
 */
async function cacheFirstStrategy(request) {
  const cachedResponse = await caches.match(request);

  if (cachedResponse) {
    return cachedResponse;
  }

  try {
    const networkResponse = await fetch(request);

    if (networkResponse.ok) {
      const cache = await caches.open(RUNTIME_CACHE);
      cache.put(request, networkResponse.clone());
    }

    return networkResponse;
  } catch (error) {
    console.warn('[Aura SW] Error cargando asset:', request.url);
    // Para imágenes, devolver un placeholder transparente
    if (/\.(png|jpg|jpeg|gif|svg|webp|ico)$/i.test(request.url)) {
      return new Response('', {
        status: 200,
        headers: { 'Content-Type': 'image/svg+xml' }
      });
    }
    return new Response('', { status: 408 });
  }
}

/**
 * Network-First para navegación: Siempre intenta la red.
 * Si falla (offline), muestra la página offline de Aura.
 * NUNCA sirve páginas HTML stale que contengan tokens CSRF caducados.
 */
async function networkFirstNavigation(request) {
  try {
    const networkResponse = await fetch(request);
    return networkResponse;
  } catch (error) {
    console.warn('[Aura SW] Sin conexión, mostrando página offline');
    const offlinePage = await caches.match('/offline.html');

    if (offlinePage) {
      return offlinePage;
    }

    // Fallback mínimo si ni siquiera hay offline.html cacheada
    return new Response(
      '<!DOCTYPE html><html><head><meta charset="utf-8"><title>Aura — Sin Conexión</title></head>' +
      '<body style="font-family:sans-serif;display:flex;align-items:center;justify-content:center;min-height:100vh;background:#f7fafa;">' +
      '<div style="text-align:center;"><h1 style="color:#004f56;">Sin Conexión</h1>' +
      '<p style="color:#6f797a;">Comprueba tu conexión a internet e inténtalo de nuevo.</p></div></body></html>',
      {
        status: 503,
        headers: { 'Content-Type': 'text/html; charset=utf-8' }
      }
    );
  }
}

/**
 * Network-First genérico: Intenta red, cae a caché si falla.
 * Usado para recursos no sensibles que no son navegación ni assets.
 */
async function networkFirstStrategy(request) {
  try {
    const networkResponse = await fetch(request);

    if (networkResponse.ok) {
      const cache = await caches.open(RUNTIME_CACHE);
      cache.put(request, networkResponse.clone());
    }

    return networkResponse;
  } catch (error) {
    const cachedResponse = await caches.match(request);

    if (cachedResponse) {
      return cachedResponse;
    }

    return new Response('', { status: 408 });
  }
}


// =============================================================================
// MENSAJE: Permite forzar limpieza de caché desde la app
// Uso: navigator.serviceWorker.controller.postMessage({ action: 'CLEAR_CACHE' })
// =============================================================================
self.addEventListener('message', (event) => {
  if (event.data?.action === 'CLEAR_CACHE') {
    console.log('[Aura SW] Limpieza manual de caché solicitada');
    event.waitUntil(
      caches.keys().then((names) =>
        Promise.all(names.map((name) => caches.delete(name)))
      ).then(() => {
        console.log('[Aura SW] Caché limpiada completamente');
        // Notificar al cliente que se completó
        if (event.source) {
          event.source.postMessage({ action: 'CACHE_CLEARED' });
        }
      })
    );
  }

  if (event.data?.action === 'SKIP_WAITING') {
    self.skipWaiting();
  }
});
