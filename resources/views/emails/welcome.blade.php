<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Добро пожаловать</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #2d2d2d;">
<h1>Привет, {{ $user->full_name }}!</h1>
<p>
    Спасибо, что зарегистрировались на сайте <strong>{{ config('app.name', 'Laravel Shop') }}</strong>.
    Мы подготовили для вас личный кабинет, где вы можете следить за заказами и редактировать профиль.
</p>
<p>
    Перейдите по ссылке ниже, чтобы дополнить данные и посмотреть историю покупок:
</p>
<p>
    <a href="{{ route('profile.form') }}" style="color: #1d4ed8;">Открыть профиль</a>
</p>
<p>Желаем приятных покупок!</p>
</body>
</html>
