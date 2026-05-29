<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <title>{{ $titulo }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 11px; color: #222; }
        h1, h2, h3 { margin: 0 0 8px 0; }
        .muted { color: #666; }
        .box { border: 1px solid #ccc; padding: 10px; margin-bottom: 12px; }
        .grid { width: 100%; border-collapse: collapse; }
        .grid th, .grid td { border: 1px solid #ccc; padding: 6px; vertical-align: top; }
        .grid th { background: #f3f5f7; text-align: left; }
        .cards { width: 100%; }
        .cards td { width: 25%; vertical-align: top; }
        .card { border: 1px solid #d6d6d6; padding: 8px; min-height: 70px; }
        .label { font-size: 9px; color: #666; text-transform: uppercase; letter-spacing: .03em; }
        .value { font-size: 22px; font-weight: bold; margin: 4px 0; }
        .page-break { page-break-after: always; }
    </style>
</head>
<body>
    <h1>{{ $titulo }}</h1>
    <div class="muted">{{ $subtitulo }}</div>
    <div class="muted">Gerado em: {{ $gerado_em }}</div>

    <div class="box">
        <strong>Status do escopo:</strong> {{ $status }}
        <div class="muted">A7 consolidado para controle da area e atendimento aos relatorios obrigatorios.</div>
        <div class="muted">Filtros: {{ $filtro_descricao }}</div>
        @if(!empty($linha_selecionada))
            <div class="muted">Linha selecionada: {{ $linha_selecionada['codigo'] }} - {{ $linha_selecionada['nome'] }}</div>
        @endif
    </div>

    @if(!empty($filtros_aplicados))
        <div class="box">
            <strong>Filtros aplicados</strong>
            <ul style="margin: 6px 0 0 18px;">
                @foreach($filtros_aplicados as $item)
                    <li>{{ $item['label'] }}: {{ $item['valor'] }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <table class="cards" cellspacing="0" cellpadding="0">
        <tr>
            @foreach($resumo as $item)
                <td>
                    <div class="card">
                        <div class="label">{{ $item['titulo'] }}</div>
                        <div class="value">{{ $item['valor'] }}</div>
                        <div class="muted">{{ $item['detalhe'] }}</div>
                    </div>
                </td>
            @endforeach
        </tr>
    </table>

    <h2>Checklist legal A7</h2>
    <table class="grid" cellspacing="0" cellpadding="0">
        <thead>
        <tr>
            <th style="width: 70px;">Codigo</th>
            <th>Titulo</th>
            <th>Evidencia</th>
            <th style="width: 90px;">Status</th>
        </tr>
        </thead>
        <tbody>
        @foreach($checklist_legal as $item)
            <tr>
                <td>{{ $item['codigo'] }}</td>
                <td>{{ $item['titulo'] }}</td>
                <td>{{ $item['evidencia'] }}</td>
                <td>{{ $item['status'] }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <div class="page-break"></div>

    <h2>Relatorios obrigatorios</h2>
    <table class="grid" cellspacing="0" cellpadding="0">
        <thead>
        <tr>
            <th>Nome</th>
            <th>Finalidade</th>
            <th>Status</th>
        </tr>
        </thead>
        <tbody>
        @foreach($relatorios_obrigatorios as $relatorio)
            <tr>
                <td>{{ $relatorio['nome'] }}</td>
                <td>{{ $relatorio['finalidade'] }}</td>
                <td>{{ $relatorio['status'] }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <h2>Alunos</h2>
    <table class="grid" cellspacing="0" cellpadding="0">
        <thead>
        <tr>
            <th>Aluno</th>
            <th>Escola</th>
            <th>Unidade escolar</th>
            <th>Periodo</th>
            <th>Linha</th>
            <th>Embarque</th>
        </tr>
        </thead>
        <tbody>
        @forelse($alunos as $aluno)
            <tr>
                <td>{{ $aluno['nome'] }}</td>
                <td>{{ $aluno['escola'] }}</td>
                <td>{{ $aluno['unidade_escolar'] ?? '-' }}</td>
                <td>{{ $aluno['periodo_uso'] ?? '-' }}</td>
                <td>{{ $aluno['linha'] }}</td>
                <td>{{ $aluno['embarque'] }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="6">Nenhum aluno encontrado para os filtros aplicados.</td>
            </tr>
        @endforelse
        </tbody>
    </table>

    <h2 style="margin-top: 14px;">Pendencias documentais</h2>
    <ul>
        @foreach($pendencias as $pendencia)
            <li>{{ $pendencia }}</li>
        @endforeach
    </ul>

    <h2>Linhas</h2>
    <table class="grid" cellspacing="0" cellpadding="0">
        <thead>
        <tr>
            <th>Codigo</th>
            <th>Nome</th>
            <th>Tipo</th>
            <th>Horario</th>
            <th>Custo</th>
            <th>Roteiro</th>
        </tr>
        </thead>
        <tbody>
        @foreach($linhas as $linha)
            <tr>
                <td>{{ $linha['codigo'] }}</td>
                <td>{{ $linha['nome'] }}</td>
                <td>{{ $linha['tipo'] }}</td>
                <td>{{ $linha['horario'] }}</td>
                <td>{{ $linha['custo'] }}</td>
                <td>
                    <div>{{ $linha['roteiro_resumido'] ?? ($linha['rota_descricao'] ?? 'Sem pontos cadastrados') }}</div>
                    <div class="muted">Pontos: {{ $linha['pontos_total'] ?? 0 }}</div>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <h2>Veiculos</h2>
    <table class="grid" cellspacing="0" cellpadding="0">
        <thead>
        <tr>
            <th>Placa</th>
            <th>Modelo</th>
            <th>Motorista</th>
            <th>Status</th>
        </tr>
        </thead>
        <tbody>
        @foreach($veiculos as $veiculo)
            <tr>
                <td>{{ $veiculo['placa'] }}</td>
                <td>{{ $veiculo['modelo'] }}</td>
                <td>{{ $veiculo['motorista'] }}</td>
                <td>{{ $veiculo['status'] }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
</body>
</html>
