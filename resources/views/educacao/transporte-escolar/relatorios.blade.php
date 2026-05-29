@extends('layouts.app')

@section('title', 'Transporte Escolar - Relatorios Legais')

@section('content')
    <div class="container-fluid mt-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h3 class="mb-0">{{ $titulo }}</h3>
                <div class="text-muted">{{ $subtitulo }}</div>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('transportescolar.web.relatorios.csv', $filtros) }}" class="btn btn-outline-primary">Exportar CSV</a>
                <a href="{{ route('transportescolar.web.relatorios.pdf', $filtros) }}" class="btn btn-success">Baixar PDF</a>
                <a href="{{ route('transportescolar.web.index') }}" class="btn btn-outline-secondary">Voltar ao painel</a>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header">Filtros do relatorio</div>
            <div class="card-body">
                <form method="GET" action="{{ route('transportescolar.web.relatorios') }}" class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label">Linha</label>
                        <select name="linha" class="form-select">
                            <option value="">Todas</option>
                            @foreach($linhas_disponiveis as $linha)
                                <option value="{{ $linha['codigo'] }}" {{ (isset($filtros['linha']) && $filtros['linha'] === $linha['codigo']) ? 'selected' : '' }}>
                                    {{ $linha['codigo'] }} - {{ $linha['nome'] }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Escola</label>
                        <select name="escola" class="form-select">
                            <option value="">Todas</option>
                            @foreach($escolas_disponiveis as $escola)
                                <option value="{{ $escola }}" {{ (isset($filtros['escola']) && $filtros['escola'] === $escola) ? 'selected' : '' }}>{{ $escola }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Periodo</label>
                        <select name="periodo" class="form-select">
                            <option value="">Todos</option>
                            @foreach($periodos_disponiveis as $periodo)
                                <option value="{{ $periodo }}" {{ (isset($filtros['periodo']) && $filtros['periodo'] === $periodo) ? 'selected' : '' }}>{{ $periodo }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 d-flex gap-2">
                        <button type="submit" class="btn btn-primary">Aplicar filtros</button>
                        <a href="{{ route('transportescolar.web.relatorios') }}" class="btn btn-outline-secondary">Limpar</a>
                    </div>
                </form>
                @if(!empty($filtros_aplicados))
                    <div class="mt-3 d-flex flex-wrap gap-2">
                        @foreach($filtros_aplicados as $item)
                            <span class="badge bg-info text-dark">{{ $item['label'] }}: {{ $item['valor'] }}</span>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <div class="alert alert-info">
            A7 consolidado para controle da area e atendimento aos relatorios obrigatorios. {{ $filtro_descricao }}. A integracao documental permanece em fechamento.
        </div>

        <div class="row g-3 mb-3">
            @foreach($resumo as $item)
                <div class="col-md-6 col-xl-3">
                    <div class="card h-100">
                        <div class="card-body">
                            <div class="text-muted small">{{ $item['titulo'] }}</div>
                            <div class="display-6">{{ $item['valor'] }}</div>
                            <div class="small text-muted">{{ $item['detalhe'] }}</div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="row g-3">
            <div class="col-xl-8">
                <div class="card mb-3">
                    <div class="card-header">Checklist legal A7</div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered align-middle">
                                <thead class="table-light">
                                <tr>
                                    <th style="width: 80px;">Codigo</th>
                                    <th>Titulo</th>
                                    <th>Evidencia</th>
                                    <th style="width: 140px;">Status</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($checklist_legal as $item)
                                    <tr>
                                        <td class="fw-bold">{{ $item['codigo'] }}</td>
                                        <td>{{ $item['titulo'] }}</td>
                                        <td>{{ $item['evidencia'] }}</td>
                                        <td>
                                            @if($item['status'] === 'disponivel')
                                                <span class="badge bg-success">disponivel</span>
                                            @else
                                                <span class="badge bg-warning text-dark">em implantacao</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="card mb-3">
                    <div class="card-header">Relatorios obrigatorios</div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered align-middle">
                                <thead class="table-light">
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
                                        <td>
                                            @if($relatorio['status'] === 'disponivel')
                                                <span class="badge bg-success">disponivel</span>
                                            @else
                                                <span class="badge bg-warning text-dark">em implantacao</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-4">
                <div class="card mb-3">
                    <div class="card-header">Situacao da linha</div>
                    <div class="card-body">
                        <ul class="mb-0">
                            @foreach($status_linhas as $status => $total)
                                <li><strong>{{ $status }}</strong>: {{ $total }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>

                <div class="card mb-3">
                    <div class="card-header">Situacao da frota</div>
                    <div class="card-body">
                        <ul class="mb-0">
                            @foreach($status_veiculos as $status => $total)
                                <li><strong>{{ $status }}</strong>: {{ $total }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>

                <div class="card mb-3">
                    <div class="card-header">Pendencias documentais</div>
                    <div class="card-body">
                        <ul class="mb-0">
                            @foreach($pendencias as $pendencia)
                                <li>{{ $pendencia }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">Integracoes previstas</div>
                    <div class="card-body">
                        <ul class="mb-0">
                            @foreach($integracoes as $item)
                                <li>{{ $item }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3 mt-1">
            <div class="col-12">
                <div class="card h-100">
                    <div class="card-header">Alunos em evidencia</div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm table-striped table-bordered align-middle mb-0">
                                <thead class="table-light">
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
                                        <td colspan="6" class="text-center text-muted">Nenhum aluno encontrado para os filtros aplicados.</td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-header">Linhas em evidencia</div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm table-striped table-bordered align-middle mb-0">
                                <thead class="table-light">
                                <tr>
                                    <th>Codigo</th>
                                    <th>Nome</th>
                                    <th>Tipo</th>
                                    <th>Roteiro</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($linhas as $linha)
                                    <tr>
                                        <td>{{ $linha['codigo'] }}</td>
                                        <td>{{ $linha['nome'] }}</td>
                                        <td>{{ $linha['tipo'] }}</td>
                                        <td>
                                            <div class="small text-muted text-break">
                                                {{ $linha['roteiro_resumido'] ?? ($linha['rota_descricao'] ?? 'Sem pontos cadastrados') }}
                                            </div>
                                            <div class="small text-muted">Pontos: {{ $linha['pontos_total'] ?? 0 }}</div>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-header">Veiculos em evidencia</div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm table-striped table-bordered align-middle mb-0">
                                <thead class="table-light">
                                <tr>
                                    <th>Placa</th>
                                    <th>Modelo</th>
                                    <th>Status</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($veiculos as $veiculo)
                                    <tr>
                                        <td>{{ $veiculo['placa'] }}</td>
                                        <td>{{ $veiculo['modelo'] }}</td>
                                        <td>{{ $veiculo['status'] }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
