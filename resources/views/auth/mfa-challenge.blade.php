<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>MFA - e-Cidade</title>
</head>
<body>
    <h1>Validação MFA</h1>
    <p>Informe o código de verificação enviado.</p>

    @if ($errors->any())
        <div style="color: #b00020;">
            {{ $errors->first() }}
        </div>
    @endif

    @if (session('status'))
        <div style="color: #1b5e20;">
            {{ session('status') }}
        </div>
    @endif

    <form method="post" action="{{ route('mfa.verify') }}">
        @csrf
        <label for="code">Código</label>
        <input id="code" name="code" type="text" maxlength="8" required>
        <button type="submit">Validar</button>
    </form>

    <form method="post" action="{{ route('mfa.resend') }}" style="margin-top: 12px;">
        @csrf
        <button type="submit">Reenviar código</button>
    </form>
</body>
</html>

