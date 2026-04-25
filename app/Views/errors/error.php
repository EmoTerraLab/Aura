<!DOCTYPE html>
<html class="light" lang="es">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title><?= $code ?? '404' ?> - <?= $title ?? 'Página no encontrada' ?> | Aura</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&amp;display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    "colors": {
                        "on-tertiary-fixed-variant": "#6c3a10",
                        "on-secondary-container": "#3e687c",
                        "on-background": "#181c1d",
                        "primary-fixed": "#a2eff9",
                        "on-tertiary": "#ffffff",
                        "secondary-fixed": "#c0e8ff",
                        "error-container": "#ffdad6",
                        "on-primary-fixed": "#001f23",
                        "secondary-container": "#bbe6fd",
                        "on-error": "#ffffff",
                        "on-tertiary-container": "#ffd0b1",
                        "on-primary-fixed-variant": "#004f56",
                        "tertiary": "#6d3a10",
                        "surface-container": "#eceeef",
                        "on-tertiary-fixed": "#301400",
                        "primary": "#004f56",
                        "inverse-on-surface": "#eef1f1",
                        "outline": "#6f797a",
                        "secondary-fixed-dim": "#a2cde3",
                        "error": "#ba1a1a",
                        "surface-tint": "#056972",
                        "surface-dim": "#d8dadb",
                        "on-secondary": "#ffffff",
                        "tertiary-container": "#895126",
                        "surface-container-low": "#f1f4f4",
                        "inverse-primary": "#86d3dd",
                        "surface-container-high": "#e6e9e9",
                        "on-secondary-fixed-variant": "#1f4c5f",
                        "tertiary-fixed-dim": "#ffb785",
                        "on-secondary-fixed": "#001f2b",
                        "surface-container-highest": "#e0e3e3",
                        "primary-container": "#066972",
                        "background": "#f7fafa",
                        "surface-bright": "#f7fafa",
                        "on-error-container": "#93000a",
                        "surface-container-lowest": "#ffffff",
                        "on-primary-container": "#99e6f0",
                        "on-surface-variant": "#3f484a",
                        "tertiary-fixed": "#ffdcc6",
                        "surface": "#f7fafa",
                        "secondary": "#3a6478",
                        "inverse-surface": "#2d3132",
                        "surface-variant": "#e0e3e3",
                        "primary-fixed-dim": "#86d3dd",
                        "on-primary": "#ffffff",
                        "on-surface": "#181c1d",
                        "outline-variant": "#bec8ca"
                    },
                    "borderRadius": {
                        "DEFAULT": "1rem",
                        "lg": "2rem",
                        "xl": "3rem",
                        "full": "9999px"
                    },
                    "spacing": {
                        "stack-gap": "16px",
                        "unit": "8px",
                        "margin-page": "32px",
                        "card-padding": "24px",
                        "gutter": "24px"
                    },
                    "fontFamily": {
                        "body-md": ["Manrope"],
                        "h2": ["Manrope"],
                        "h1": ["Manrope"],
                        "body-lg": ["Manrope"],
                        "label-caps": ["Manrope"]
                    },
                    "fontSize": {
                        "body-md": ["16px", {"lineHeight": "1.6", "letterSpacing": "0.01em", "fontWeight": "400"}],
                        "h2": ["24px", {"lineHeight": "1.3", "letterSpacing": "-0.01em", "fontWeight": "600"}],
                        "h1": ["32px", {"lineHeight": "1.2", "letterSpacing": "-0.02em", "fontWeight": "700"}],
                        "body-lg": ["18px", {"lineHeight": "1.6", "letterSpacing": "0.01em", "fontWeight": "400"}],
                        "label-caps": ["12px", {"lineHeight": "1.0", "letterSpacing": "0.08em", "fontWeight": "600"}]
                    }
                },
            },
        }
    </script>
    <style>
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }
        .organic-blob {
            background-color: #dfe4dc;
            filter: blur(40px);
            opacity: 0.6;
            border-radius: 60% 40% 70% 30% / 40% 50% 60% 40%;
        }
        .text-soft-teal {
            color: #066972;
        }
        .bg-soft-teal {
            background-color: #066972;
        }
    </style>
</head>
<body class="bg-background font-body-md text-on-background min-h-screen flex flex-col">
    <!-- TopAppBar -->
    <header class="fixed top-0 w-full flex justify-between items-center px-6 py-4 z-50 bg-transparent font-['Manrope'] antialiased">
        <div class="text-2xl font-bold tracking-tight text-teal-700">
            Aura
        </div>
        <div class="flex items-center gap-4">
            <a href="/" class="p-2 rounded-full hover:bg-teal-50/50 transition-colors text-teal-700">
                <span class="material-symbols-outlined" data-icon="account_circle">account_circle</span>
            </a>
        </div>
    </header>
    <!-- Main Content Canvas -->
    <main class="flex-grow flex flex-col items-center justify-center px-margin-page relative overflow-hidden">
        <!-- Background Organic Shape (Digital Sanctuary Aesthetic) -->
        <div class="absolute inset-0 flex items-center justify-center -z-10 pointer-events-none">
            <div class="organic-blob w-[320px] h-[320px] md:w-[480px] md:h-[480px]"></div>
        </div>
        <!-- 404 Visual Content -->
        <div class="max-w-2xl w-full text-center space-y-stack-gap z-10">
            <!-- Icon/Visual Anchor -->
            <div class="mb-unit flex justify-center">
                <span class="material-symbols-outlined text-[80px] md:text-[120px] text-teal-700/20" style="font-variation-settings: 'wght' 200;"><?= $icon ?? 'explore_off' ?></span>
            </div>
            <!-- Typography Hierarchy -->
            <h1 class="font-h1 text-h1 text-on-background max-w-lg mx-auto">
                <?= $message ?? 'Vaya, parece que te has alejado un poco del camino.' ?>
            </h1>
            <p class="font-body-lg text-body-lg text-on-surface-variant max-w-md mx-auto leading-relaxed">
                <?= $description ?? 'No te preocupes, este sigue siendo un espacio seguro. Pulsa el botón de abajo para volver a tu dashboard.' ?>
            </p>
            <!-- Action Area -->
            <div class="pt-gutter">
                <?php
                $homeLink = '/login';
                if (\App\Core\Auth::check()) {
                    $role = \App\Core\Auth::role();
                    if ($role === 'alumno') $homeLink = '/alumno/dashboard';
                    elseif ($role === 'admin') $homeLink = '/admin';
                    else $homeLink = '/staff/inbox';
                }
                ?>
                <a class="inline-flex items-center justify-center px-10 py-4 bg-primary-container text-white font-semibold rounded-full shadow-[0_8px_30px_rgb(6,105,114,0.12)] hover:opacity-90 active:scale-95 transition-all text-body-md" href="<?= $homeLink ?>">
                    Volver al Inicio
                </a>
            </div>
            <!-- Breadcrumb-like indicator (minimalist) -->
            <div class="pt-margin-page opacity-40">
                <span class="font-label-caps text-label-caps uppercase tracking-widest text-on-surface-variant">Código de estado: <?= $code ?? '404' ?></span>
            </div>
        </div>
    </main>
    <!-- Footer -->
    <footer class="flex justify-center items-center w-full py-8 mt-auto opacity-50 font-['Manrope'] text-[10px] tracking-wide text-slate-400">
        <div class="flex flex-col items-center gap-2">
            <p>Aura powered by EmoTerraLab (emoterralab.com)</p>
            <div class="flex gap-4">
                <a class="hover:text-teal-500 underline transition-colors" href="#">Soporte</a>
                <a class="hover:text-teal-500 underline transition-colors" href="#">Privacidad</a>
            </div>
        </div>
    </footer>
</body>
</html>
