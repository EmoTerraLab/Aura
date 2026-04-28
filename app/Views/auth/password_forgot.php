<!DOCTYPE html>
<html lang="<?= \App\Core\Lang::current() ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= \App\Core\Lang::t('auth.forgot_password_title') ?> - Aura PDP</title>
    <link rel="stylesheet" href="/css/app.css">
    <style>
        body { display: flex; align-items: center; justify-content: center; height: 100vh; }
        .login-card { width: 100%; max-width: 400px; text-align: center; }
    </style>
</head>
<body>
    <div class="card login-card">
        <h1><?= \App\Core\Lang::t('auth.forgot_password_title') ?></h1>
        <p class="text-muted mb-4"><?= \App\Core\Lang::t('auth.forgot_password_desc') ?></p>
        
        <?php if (isset($_GET['sent'])): ?>
            <div class="text-success mb-4">
                <?= \App\Core\Lang::t('auth.reset_link_sent') ?>
            </div>
            <a href="/login" class="btn btn-primary" style="width: 100%;">Volver al Login</a>
        <?php else: ?>
            <?php if (isset($_GET['error'])): ?>
                <div class="text-error mb-4">
                    <?= $_GET['error'] === 'invalid_email' ? 'Email no válido' : 'Error al procesar la solicitud' ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="/password/forgot">
                <input type="hidden" name="csrf_token" value="<?= \App\Core\Csrf::generateToken() ?>">
                <div class="form-group" style="text-align: left;">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required autofocus placeholder="tu@email.com">
                </div>
                <button type="submit" class="btn btn-primary" style="width: 100%;"><?= \App\Core\Lang::t('auth.forgot_password_title') ?></button>
                <div class="mt-4">
                    <a href="/login" class="text-muted small">Volver al inicio de sesión</a>
                </div>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>
