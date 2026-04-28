<!DOCTYPE html>
<html lang="<?= \App\Core\Lang::getLocale() ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= \App\Core\Lang::t('auth.reset_link_invalid') ?> - Aura PDP</title>
    <link rel="stylesheet" href="/css/app.css">
    <style>
        body { display: flex; align-items: center; justify-content: center; height: 100vh; }
        .login-card { width: 100%; max-width: 400px; text-align: center; }
    </style>
</head>
<body>
    <div class="card login-card">
        <h1 class="text-error mb-4"><?= \App\Core\Lang::t('auth.reset_link_invalid') ?></h1>
        <p class="text-muted mb-8"><?= \App\Core\Lang::t('auth.reset_link_expired_desc') ?></p>
        
        <a href="/password/forgot" class="btn btn-primary" style="width: 100%;"><?= \App\Core\Lang::t('auth.forgot_password_title') ?></a>
        <div class="mt-4">
            <a href="/login" class="text-muted small">Volver al inicio de sesión</a>
        </div>
    </div>
</body>
</html>
