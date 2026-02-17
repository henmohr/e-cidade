<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sessoes Ativas - e-Cidade</title>
</head>
<body>
    <h1>Sessoes Ativas</h1>
    <p>Gerencie suas sessoes abertas e encerre acessos antigos.</p>

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

    @if (empty($sessions))
        <p>Nenhuma sessao ativa registrada.</p>
    @else
        <table border="1" cellpadding="6" cellspacing="0">
            <thead>
                <tr>
                    <th>Sessao</th>
                    <th>Inicio</th>
                    <th>Ultimo acesso</th>
                    <th>IP</th>
                    <th>User-Agent</th>
                    <th>Acao</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($sessions as $entry)
                    <tr>
                        <td>
                            {{ $entry['session_id'] ?? '-' }}
                            @if (($entry['session_id'] ?? '') === $currentSessionId)
                                <strong>(atual)</strong>
                            @endif
                        </td>
                        <td>{{ $entry['started_at'] ?? '-' }}</td>
                        <td>{{ $entry['last_seen_at'] ?? '-' }}</td>
                        <td>{{ $entry['ip'] ?? '-' }}</td>
                        <td>{{ $entry['user_agent'] ?? '-' }}</td>
                        <td>
                            @if (($entry['session_id'] ?? '') !== $currentSessionId)
                                <form method="post" action="{{ route('sessions.revoke') }}">
                                    @csrf
                                    <input type="hidden" name="session_id" value="{{ $entry['session_id'] }}">
                                    <button type="submit">Encerrar</button>
                                </form>
                            @else
                                Sessao atual
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</body>
</html>
