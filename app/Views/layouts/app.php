<!DOCTYPE html>
<html lang="<?= \App\Core\Lang::current() ?>" class="light">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="csrf-token" content="<?= \App\Core\Csrf::generateToken() ?>">
    
    <title><?= $title ?? \App\Core\Config::get('school_name', 'Aura') ?></title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?= BASE_URL ?>favicon.ico">

    <link href="https://fonts.googleapis.com" rel="preconnect"/>
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect"/>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "on-secondary": "#ffffff",
                        "surface-dim": "#d8dadb",
                        "on-tertiary-container": "#ffd0b1",
                        "on-background": "#181c1d",
                        "surface-container-low": "#f1f4f4",
                        "tertiary-fixed-dim": "#ffb785",
                        "background": "#f7fafa",
                        "on-primary-fixed": "#001f23",
                        "on-primary-container": "#99e6f0",
                        "on-error": "#ffffff",
                        "error": "#ba1a1a",
                        "on-secondary-fixed-variant": "#1f4c5f",
                        "on-surface-variant": "#3f484a",
                        "primary-fixed-dim": "#86d3dd",
                        "inverse-on-surface": "#eef1f1",
                        "on-secondary-fixed": "#001f2b",
                        "on-tertiary-fixed-variant": "#6c3a10",
                        "surface-tint": "#056972",
                        "surface-container-highest": "#e0e3e3",
                        "primary-fixed": "#a2eff9",
                        "inverse-primary": "#86d3dd",
                        "secondary": "<?= \App\Core\Config::get('app_accent_color', '#3a6478') ?>",
                        "surface-container-high": "#e6e9e9",
                        "outline-variant": "#bec8ca",
                        "surface-bright": "#f7fafa",
                        "secondary-container": "#bbe6fd",
                        "on-tertiary": "#ffffff",
                        "surface-container-lowest": "#ffffff",
                        "on-surface": "#181c1d",
                        "on-secondary-container": "#3e687c",
                        "outline": "#6f797a",
                        "primary-container": "#066972",
                        "inverse-surface": "#2d3132",
                        "secondary-fixed-dim": "#a2cde3",
                        "tertiary-fixed": "#ffdcc6",
                        "tertiary-container": "#895126",
                        "surface-variant": "#e0e3e3",
                        "primary": "<?= \App\Core\Config::get('app_primary_color', '#004f56') ?>",
                        "on-error-container": "#93000a",
                        "on-primary": "#ffffff",
                        "on-primary-fixed-variant": "#004f56",
                        "surface-container": "#eceeef",
                        "surface": "#f7fafa",
                        "tertiary": "#6d3a10",
                        "error-container": "#ffdad6",
                        "secondary-fixed": "#c0e8ff",
                        "on-tertiary-fixed": "#301400"
                    },
                    borderRadius: {
                        "DEFAULT": "1rem",
                        "lg": "2rem",
                        "xl": "3rem",
                        "full": "9999px"
                    },
                    spacing: {
                        "margin-page": "32px",
                        "card-padding": "24px",
                        "gutter": "24px",
                        "unit": "8px",
                        "stack-gap": "16px"
                    },
                    fontFamily: {
                        "body-md": ["Manrope"],
                        "h2": ["Manrope"],
                        "h1": ["Manrope"],
                        "body-lg": ["Manrope"],
                        "label-caps": ["Manrope"]
                    },
                    fontSize: {
                        "body-md": ["16px", { lineHeight: "1.6", letterSpacing: "0.01em", fontWeight: "400" }],
                        "h2": ["24px", { lineHeight: "1.3", letterSpacing: "-0.01em", fontWeight: "600" }],
                        "h1": ["32px", { lineHeight: "1.2", letterSpacing: "-0.02em", fontWeight: "700" }],
                        "body-lg": ["18px", { lineHeight: "1.6", letterSpacing: "0.01em", fontWeight: "400" }],
                        "label-caps": ["12px", { lineHeight: "1.0", letterSpacing: "0.08em", fontWeight: "600" }]
                    }
                }
            }
        }
    </script>
    <style>
        /* Base Reset & Scale Fixes */
        * { box-sizing: border-box; }
        html, body { 
            margin: 0; 
            padding: 0; 
            width: 100%;
            height: 100%;
            overflow-x: hidden; 
            font-size: 16px; /* Essential for mobile scale */
            -webkit-text-size-adjust: 100%;
        }

        body { background-color: theme('colors.surface'); color: theme('colors.on-surface'); }
        
        /* Form elements zoom fix for iOS */
        input[type="text"], input[type="email"], input[type="password"], input[type="number"], input[type="search"], select, textarea {
            font-size: 16px !important; /* Prevents auto-zoom on focus in iOS */
        }

        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: rgba(0,0,0,0.1); border-radius: 9999px; }
        ::-webkit-scrollbar-thumb:hover { background: rgba(0,0,0,0.2); }
        
        input:-webkit-autofill,
        input:-webkit-autofill:hover, 
        input:-webkit-autofill:focus, 
        input:-webkit-autofill:active{
            -webkit-box-shadow: 0 0 0 30px #e0e3e3 inset !important;
            -webkit-text-fill-color: #181c1d !important;
        }
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
        .ambient-shadow { box-shadow: 0 10px 40px -10px rgba(6, 105, 114, 0.08); }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Fluid Typography */
        h1 { font-size: clamp(1.5rem, 5vw, 2rem) !important; }
        h2 { font-size: clamp(1.25rem, 4vw, 1.5rem) !important; }

        /* Responsive Table */
        .table-container {
            width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
    </style>
    <script>
        async function fetchJson(url, opts = {}) {
            const token = document.querySelector('meta[name="csrf-token"]').content;
            const headers = { 
                'Content-Type': 'application/json', 
                'X-CSRF-TOKEN': token 
            };
            if (opts.headers) { Object.assign(headers, opts.headers); }
            const res = await fetch(url, {
                ...opts,
                headers: headers,
                body: opts.body ? JSON.stringify(opts.body) : undefined
            });
            return res.json();
        }
    </script>
</head>
<body class="<?= $bodyClass ?? 'bg-background text-on-surface font-body-md text-body-md antialiased min-h-screen' ?>">
    <?= $content ?>
    <?php if (isset($scripts)): ?>
        <?= $scripts ?>
    <?php endif; ?>
</body>
</html>
