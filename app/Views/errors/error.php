<!DOCTYPE html>
<html class="light" lang="<?= \App\Core\Lang::current() ?>">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title><?= $code ?? '404' ?> - <?= \App\Core\Lang::t($title_key ?? 'error.404_title') ?> | Prisma</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Manrope:wght@500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        primary: '#1E2433',
                        'primary-container': '#283247',
                        'on-primary': '#FFFFFF',
                        surface: '#F4F7FD',
                        'on-surface': '#1E2433',
                        'on-surface-variant': '#5A6478',
                        'surface-container-lowest': '#FFFFFF',
                        'surface-container-low': '#EDF2FA',
                        'surface-container-high': '#E2E8F4',
                        'prisma-mint': '#79E0CC',
                        'prisma-lavender': '#C9A7FF',
                        'prisma-sky': '#A6E4FF',
                        error: '#D94841',
                    },
                    fontFamily: {
                        display: ['Manrope', 'sans-serif'],
                        body: ['Inter', 'sans-serif']
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-surface font-body text-on-surface min-h-screen flex flex-col">
    <!-- TopAppBar -->
    <header class="fixed top-0 w-full flex justify-between items-center px-6 py-4 z-50 bg-surface/80 backdrop-blur-md font-display">
        <div class="flex items-center gap-2.5">
            <img src="<?= BASE_URL ?>assets/prisma-symbol.jpeg" alt="Prisma" class="h-8 w-8 rounded-lg object-contain shadow-xs">
            <span class="text-lg font-bold tracking-tight text-primary">Prisma</span>
        </div>
        <div class="flex items-center gap-3">
            <?= \App\Core\Lang::renderSelector() ?>
        </div>
    </header>

    <!-- Main Content Canvas -->
    <main class="flex-grow flex flex-col items-center justify-center px-6 relative overflow-hidden font-display pt-16">
        <div class="max-w-md w-full text-center space-y-5 z-10">
            <!-- Icon -->
            <div class="w-20 h-20 bg-primary/5 rounded-2xl flex items-center justify-center mx-auto text-primary">
                <span class="material-symbols-outlined text-4xl"><?= $icon ?? 'explore_off' ?></span>
            </div>
            
            <h1 class="font-bold text-2xl md:text-3xl text-primary tracking-tight">
                <?= \App\Core\Lang::t($message_key ?? 'error.404_message') ?>
            </h1>
            <p class="font-body text-xs md:text-sm text-on-surface-variant max-w-sm mx-auto leading-relaxed">
                <?= \App\Core\Lang::t($description_key ?? 'error.404_desc') ?>
            </p>
            
            <!-- Action Area -->
            <div class="pt-2">
                <?php
                $homeLink = '/login';
                if (\App\Core\Auth::check()) {
                    $role = \App\Core\Auth::role();
                    if ($role === 'alumno') $homeLink = '/alumno/dashboard';
                    elseif ($role === 'admin') $homeLink = '/admin';
                    else $homeLink = '/staff/inbox';
                }
                ?>
                <a class="inline-flex items-center justify-center h-11 px-8 bg-primary text-on-primary font-bold rounded-xl shadow-xs hover:bg-primary/90 active:scale-95 transition-all text-xs" href="<?= $homeLink ?>">
                    <?= \App\Core\Lang::t('error.back_to_start') ?>
                </a>
            </div>
            
            <div class="pt-4 opacity-50">
                <span class="text-[10px] font-bold uppercase tracking-widest text-on-surface-variant"><?= \App\Core\Lang::t('error.status_code') ?>: <?= $code ?? '404' ?></span>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="flex justify-center items-center w-full py-6 mt-auto font-display text-[11px] text-on-surface-variant/70 border-t border-surface-variant/30">
        <p><?= \App\Core\Lang::t('footer.powered_by') ?></p>
    </footer>
</body>
</html>
