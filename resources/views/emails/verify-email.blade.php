<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Подтвердите email</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #2d2d2d;">
<h1>Подтвердите email</h1>
<p>
    Привет, {{ $user->full_name ?? 'пользователь' }}!
</p>
<p>
    Чтобы подтвердить вашу почту и получить доступ к оформлению заказов, нажмите кнопку ниже:
</p>
<p>
    <a href="{{ $url }}"
       style="display:inline-block;background:#1d4ed8;color:#ffffff;text-decoration:none;padding:10px 16px;border-radius:6px;">
        Подтвердить email
    </a>
</p>
<p style="font-size: 12px; color: #6b7280;">
    Если кнопка не работает, откройте ссылку в браузере: {{ $url }}
</p>
</body>
</html>
