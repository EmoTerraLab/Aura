<!DOCTYPE html>
<html lang="<?= \App\Core\Lang::current() ?>" class="light">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="csrf-token" content="<?= \App\Core\Csrf::generateToken() ?>">
    
    <title><?= $title ?? (\App\Core\Config::get('school_name', 'Prisma') . ' — Convivencia Escolar') ?></title>
    
    <!-- Favicon & Touch Icons -->
    <link rel="icon" type="image/png" href="<?= BASE_URL ?>assets/prisma-symbol.jpeg">
    <link rel="apple-touch-icon" href="<?= BASE_URL ?>assets/prisma-symbol.jpeg">

    <!-- ═══════════════════════════════════════════════════════════════════
         PWA: Web App Manifest + iOS Meta Tags
         ═══════════════════════════════════════════════════════════════════ -->
    <link rel="manifest" href="<?= BASE_URL ?>manifest.json" crossorigin="use-credentials">
    <meta name="theme-color" content="#1E2433">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="application-name" content="Prisma">

    <!-- iOS: Experiencia nativa a pantalla completa en iPhone/iPad -->
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="Prisma">

    <!-- Google Fonts: Manrope (Display/Headings) + Inter (Body/Forms) -->
    <link href="https://fonts.googleapis.com" rel="preconnect"/>
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect"/>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        // Brand Prisma
                        "prisma-mint": "#79E0CC",
                        "prisma-lavender": "#C9A7FF",
                        "prisma-sky": "#A6E4FF",
                        "prisma-cloud": "#F4F7FD",
                        "prisma-charcoal": "#1E2433",

                        // Semantic
                        "success": "#2FBF9F",
                        "success-bg": "#EAFBF6",
                        "info": "#62B9F3",
                        "info-bg": "#EDF8FF",
                        "warning": "#F2B84B",
                        "warning-bg": "#FFF8E7",
                        "danger": "#E86B73",
                        "danger-bg": "#FFF0F1",
                        "error": "#E86B73",
                        "error-container": "#FFF0F1",

                        // Neutrals
                        "gray-50": "#F8FAFC",
                        "gray-100": "#F1F5F9",
                        "gray-200": "#E2E8F0",
                        "gray-300": "#CBD5E1",
                        "gray-500": "#64748B",
                        "gray-700": "#334155",
                        "gray-900": "#0F172A",

                        // System Aliases
                        "bg-app": "#F7F9FD",
                        "bg-surface": "#FFFFFF",
                        "bg-subtle": "#F4F7FD",
                        "text-primary": "#1E2433",
                        "text-secondary": "#64748B",
                        "text-muted": "#94A3B8",
                        "border-subtle": "#E2E8F0",
                        "border-strong": "#CBD5E1",

                        // Legacy / Dynamic Compatibility
                        "primary": "<?= \App\Core\Config::get('app_primary_color', '#1E2433') ?>",
                        "secondary": "<?= \App\Core\Config::get('app_accent_color', '#79E0CC') ?>",
                        "surface": "#FFFFFF",
                        "background": "#F7F9FD",
                        "on-surface": "#1E2433",
                        "on-surface-variant": "#64748B",
                        "surface-variant": "#F1F5F9",
                        "surface-container": "#F4F7FD",
                        "surface-container-low": "#F8FAFC",
                        "surface-container-lowest": "#FFFFFF",
                        "primary-container": "#1E2433",
                        "on-primary": "#FFFFFF",
                        "on-primary-container": "#79E0CC",
                        "secondary-container": "#EDF8FF",
                        "on-secondary-container": "#1E2433",
                        "surface-tint": "#2FBF9F",
                        "outline": "#CBD5E1",
                        "outline-variant": "#E2E8F0"
                    },
                    borderRadius: {
                        "sm": "8px",
                        "md": "12px",
                        "lg": "16px",
                        "xl": "20px",
                        "2xl": "24px",
                        "prisma": "16px",
                        "full": "9999px"
                    },
                    boxShadow: {
                        "sm": "0 1px 2px rgba(30, 36, 51, 0.06)",
                        "md": "0 6px 18px rgba(30, 36, 51, 0.08)",
                        "lg": "0 14px 36px rgba(30, 36, 51, 0.10)",
                        "ambient": "0 10px 40px -10px rgba(30, 36, 51, 0.08)"
                    },
                    spacing: {
                        "margin-page": "32px",
                        "card-padding": "24px",
                        "gutter": "24px",
                        "unit": "8px",
                        "stack-gap": "20px"
                    },
                    fontFamily: {
                        "display": ["Manrope", "sans-serif"],
                        "sans": ["Inter", "sans-serif"],
                        "manrope": ["Manrope", "sans-serif"],
                        "inter": ["Inter", "sans-serif"],
                        "body-md": ["Inter", "sans-serif"],
                        "body-lg": ["Inter", "sans-serif"],
                        "h1": ["Manrope", "sans-serif"],
                        "h2": ["Manrope", "sans-serif"],
                        "h3": ["Manrope", "sans-serif"],
                        "label-caps": ["Manrope", "sans-serif"]
                    },
                    fontSize: {
                        "display": ["40px", { lineHeight: "1.1", fontWeight: "700" }],
                        "h1": ["32px", { lineHeight: "1.2", letterSpacing: "-0.02em", fontWeight: "700" }],
                        "h2": ["24px", { lineHeight: "1.25", letterSpacing: "-0.01em", fontWeight: "700" }],
                        "h3": ["20px", { lineHeight: "1.3", fontWeight: "650" }],
                        "body-lg": ["18px", { lineHeight: "1.55", fontWeight: "400" }],
                        "body-md": ["16px", { lineHeight: "1.55", fontWeight: "400" }],
                        "body-sm": ["14px", { lineHeight: "1.5", fontWeight: "400" }],
                        "label": ["13px", { lineHeight: "1.3", fontWeight: "600" }],
                        "caption": ["12px", { lineHeight: "1.4", fontWeight: "500" }],
                        "label-caps": ["12px", { lineHeight: "1.0", letterSpacing: "0.06em", fontWeight: "600" }]
                    }
                }
            }
        }
    </script>
    <style>
        /* Base Reset & Design System Tokens */
        :root {
            --prisma-mint: #79E0CC;
            --prisma-lavender: #C9A7FF;
            --prisma-sky: #A6E4FF;
            --prisma-cloud: #F4F7FD;
            --prisma-charcoal: #1E2433;
            --white: #FFFFFF;
            --gray-50: #F8FAFC;
            --gray-100: #F1F5F9;
            --gray-200: #E2E8F0;
            --gray-300: #CBD5E1;
            --gray-500: #64748B;
            --gray-700: #334155;
            --gray-900: #0F172A;
            --success: #2FBF9F;
            --success-bg: #EAFBF6;
            --info: #62B9F3;
            --info-bg: #EDF8FF;
            --warning: #F2B84B;
            --warning-bg: #FFF8E7;
            --danger: #E86B73;
            --danger-bg: #FFF0F1;
            --bg-app: #F7F9FD;
            --bg-surface: #FFFFFF;
            --bg-subtle: #F4F7FD;
            --text-primary: #1E2433;
            --text-secondary: #64748B;
            --text-muted: #94A3B8;
            --border-subtle: #E2E8F0;
            --border-strong: #CBD5E1;
            --radius-sm: 8px;
            --radius-md: 12px;
            --radius-lg: 16px;
            --radius-xl: 20px;
            --radius-pill: 9999px;
            --shadow-sm: 0 1px 2px rgba(30, 36, 51, 0.06);
            --shadow-md: 0 6px 18px rgba(30, 36, 51, 0.08);
            --shadow-lg: 0 14px 36px rgba(30, 36, 51, 0.10);
            --prisma-gradient: linear-gradient(90deg, #79E0CC 0%, #A6E4FF 45%, #C9A7FF 100%);
            --ease-prisma: cubic-bezier(0.2, 0.8, 0.2, 1);
        }

        * { box-sizing: border-box; }
        html, body { 
            margin: 0; 
            padding: 0; 
            width: 100%;
            min-height: 100%;
            overflow-x: hidden; 
            font-size: 16px;
            font-family: "Inter", sans-serif;
            background-color: var(--bg-app);
            color: var(--text-primary);
            -webkit-text-size-adjust: 100%;
        }

        h1, h2, h3, h4, h5, h6 {
            font-family: "Manrope", sans-serif;
            color: var(--text-primary);
        }

        /* Form Controls Standard (44px height, 10-12px radius, focus ring) */
        input[type="text"], input[type="email"], input[type="password"], input[type="number"], input[type="search"], select, textarea {
            font-family: "Inter", sans-serif;
            font-size: 15px !important;
            border: 1px solid var(--border-strong);
            border-radius: var(--radius-md);
            background-color: var(--white);
            color: var(--text-primary);
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }

        input[type="text"]:focus, input[type="email"]:focus, input[type="password"]:focus, input[type="number"]:focus, input[type="search"]:focus, select:focus, textarea:focus {
            outline: 3px solid rgba(166, 228, 255, 0.45) !important;
            border-color: #62B9F3 !important;
            box-shadow: none !important;
        }

        /* Scrollbars */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: rgba(30, 36, 51, 0.12); border-radius: 9999px; }
        ::-webkit-scrollbar-thumb:hover { background: rgba(30, 36, 51, 0.25); }
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }

        .prisma-gradient-bg {
            background: var(--prisma-gradient);
        }
        .prisma-card {
            background-color: var(--white);
            border: 1px solid var(--border-subtle);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-sm);
        }
        .prisma-card:hover {
            box-shadow: var(--shadow-md);
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(8px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .table-container {
            width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
    </style>
    <script>
        /**
         * fetchJson - Versión Blindada (Solución Definitiva)
         * - Cabeceras AJAX automáticas para evitar redirecciones de middleware.
         * - Time-out integrado para evitar bloqueos por red.
         * - Sistema de guardián para limpiar spinners infinitos.
         */
        async function fetchJson(url, opts = {}) {
            const controller = new AbortController();
            const timeoutId = setTimeout(() => controller.abort(), 10000); // 10s timeout

            try {
                const token = document.querySelector('meta[name="csrf-token"]').content;
                const headers = {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': token
                };
                
                if (opts.headers) { Object.assign(headers, opts.headers); }

                const res = await fetch(url, {
                    ...opts,
                    headers: headers,
                    signal: controller.signal,
                    body: opts.body ? JSON.stringify(opts.body) : undefined
                });

                clearTimeout(timeoutId);

                if (res.status === 503) {
                    const maintenance = await res.json();
                    window.location.reload(); // Recargar para ver pantalla de mantenimiento
                    return { error: 'Sistema en mantenimiento' };
                }

                if (!res.ok && res.status !== 400 && res.status !== 403) {
                    throw new Error(`Error del servidor: ${res.status}`);
                }

                return await res.json();
            } catch (err) {
                clearTimeout(timeoutId);
                console.error("Fetch error:", err);
                
                // Si el error es por timeout o red, avisar al usuario
                if (err.name === 'AbortError') {
                    return { error: "La petición ha tardado demasiado tiempo. Revisa tu conexión." };
                }
                return { error: "Error de conexión con el servidor: " + err.message };
            }
        }
    </script>
</head>
<body class="<?= $bodyClass ?? 'bg-background text-on-surface font-body-md text-body-md antialiased min-h-screen' ?>">
    <?= $content ?>
    <?php if (isset($scripts)): ?>
        <?= $scripts ?>
    <?php endif; ?>

    <!-- ═══════════════════════════════════════════════════════════════════
         PWA: Registro del Service Worker (diferido para no bloquear render)
         ═══════════════════════════════════════════════════════════════════ -->
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js', { scope: '/' })
                    .then((registration) => {
                        console.log('[Aura PWA] Service Worker registrado. Scope:', registration.scope);

                        // Detectar actualizaciones del Service Worker
                        registration.addEventListener('updatefound', () => {
                            const newWorker = registration.installing;
                            newWorker.addEventListener('statechange', () => {
                                if (newWorker.state === 'installed' && navigator.serviceWorker.controller) {
                                    // Hay una nueva versión disponible
                                    console.log('[Aura PWA] Nueva versión disponible');
                                    // Activar nuevo SW inmediatamente
                                    newWorker.postMessage({ action: 'SKIP_WAITING' });
                                }
                            });
                        });
                    })
                    .catch((error) => {
                        console.warn('[Aura PWA] Error al registrar Service Worker:', error);
                    });

                // Recargar la página cuando el nuevo SW tome el control
                let refreshing = false;
                navigator.serviceWorker.addEventListener('controllerchange', () => {
                    if (!refreshing) {
                        refreshing = true;
                        window.location.reload();
                    }
                });
            });
        }
    </script>
</body>
</html>

