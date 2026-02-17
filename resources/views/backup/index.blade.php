<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Backups - e-Cidade</title>
    <link rel="stylesheet" href="{{ asset('assets/css/accessibility.css') }}">
</head>
<body>
    <h1>Backups da PoC</h1>
    <p>Download protegido por certificado A3: <strong>{{ $a3Required ? 'sim' : 'nao' }}</strong></p>

    @if (!$downloadEnabled)
        <p style="color: #b00020;">Download de backup desabilitado.</p>
    @endif

    @if ($errors->any())
        <div style="color: #b00020;">
            {{ $errors->first() }}
        </div>
    @endif

    @if (session('download_url'))
        <div style="color: #1b5e20;">
            Link temporario gerado:
            <a href="{{ session('download_url') }}">{{ session('download_url') }}</a>
        </div>
    @endif

    @foreach (['active' => 'Ativos (<=15 dias)', 'archive' => 'Arquivo (16-35 dias)'] as $tier => $label)
        <h2>{{ $label }}</h2>
        @if (empty($files[$tier]))
            <p>Sem arquivos neste nivel.</p>
        @else
            <table border="1" cellpadding="6" cellspacing="0">
                <thead>
                    <tr>
                        <th>Arquivo</th>
                        <th>Tamanho (bytes)</th>
                        <th>Modificado em</th>
                        <th>Acao</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($files[$tier] as $entry)
                        <tr>
                            <td>{{ $entry['name'] }}</td>
                            <td>{{ $entry['size_bytes'] }}</td>
                            <td>{{ $entry['modified_at'] }}</td>
                            <td>
                                <form method="post" action="{{ route('backup.link') }}">
                                    @csrf
                                    <input type="hidden" name="tier" value="{{ $tier }}">
                                    <input type="hidden" name="file" value="{{ $entry['name'] }}">
                                    <button type="submit">Gerar link</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    @endforeach

    @include('layouts.accessibility-toolbar')
    <script src="{{ asset('assets/js/accessibility.js') }}"></script>
</body>
</html>
