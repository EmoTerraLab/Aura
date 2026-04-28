<!DOCTYPE html>
<html lang="<?= \App\Core\Lang::getLocale() ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= \App\Core\Lang::t('auth.reset_new_password') ?> - Aura PDP</title>
    <link rel="stylesheet" href="/css/app.css">
    <style>
        body { display: flex; align-items: center; justify-content: center; height: 100vh; }
        .login-card { width: 100%; max-width: 400px; text-align: center; }
    </style>
</head>
<body>
    <div class="card login-card">
        <h1><?= \App\Core\Lang::t('auth.reset_new_password') ?></h1>
        
        <?php if (!empty($errors)): ?>
            <div class="text-error mb-4" style="text-align: left;">
                <ul style="margin-left: 1.5rem;">
                    <?php foreach ($errors as $error): ?>
                        <li><?= $error ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="POST" action="/password/reset">
            <input type="hidden" name="csrf_token" value="<?= \App\Core\Csrf::generateToken() ?>">
            <input type="hidden" name="token" value="<?= htmlspecialchars($token ?? $token_val ?? '') ?>">
            
            <div class="form-group" style="text-align: left;">
                <label for="password"><?= \App\Core\Lang::t('auth.reset_new_password') ?></label>
                <input type="password" id="password" name="password" required autofocus minlength="8">
            </div>
            
            <div class="form-group" style="text-align: left;">
                <label for="password_confirm"><?= \App\Core\Lang::t('auth.reset_confirm_password') ?></label>
                <input type="password" id="password_confirm" name="password_confirm" required minlength="8">
            </div>
            
            <button type="submit" class="btn btn-primary" style="width: 100%;"><?= \App\Core\Lang::t('auth.reset_submit') ?></button>
        </form>
    </div>
</body>
</html>
