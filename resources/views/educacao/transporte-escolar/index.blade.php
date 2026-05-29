@extends('layouts.app')

@section('title', 'Transporte Escolar')

@section('content')
    <div class="container-fluid mt-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h3 class="mb-0">Transporte Escolar</h3>
                <div class="text-muted">Modulo A - linhas, veiculos, rotas, relatorios e exportacao SETE</div>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('transportescolar.web.gestao') }}" class="btn btn-outline-secondary">Gestao</a>
                <a href="{{ route('transportescolar.web.export', $filtros) }}" class="btn btn-outline-primary">Exportar base SETE</a>
                <a href="{{ route('transportescolar.web.export.csv', $filtros) }}" class="btn btn-outline-primary">Exportar CSV</a>
                <a href="{{ route('transportescolar.web.export.sete.json', $filtros) }}" class="btn btn-outline-primary">Exportar SETE JSON</a>
                <a href="{{ route('transportescolar.web.relatorios') }}" class="btn btn-outline-success">Relatorios legais</a>
                <a href="{{ route('welcome') }}" class="btn btn-outline-secondary">Voltar</a>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header">Filtros da base</div>
            <div class="card-body">
                <form method="GET" action="{{ route('transportescolar.web.index') }}" class="row g-3 align-items-end">
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
                        <a href="{{ route('transportescolar.web.index') }}" class="btn btn-outline-secondary">Limpar</a>
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

        @if(session('status'))
            <div class="alert alert-success">{{ session('status') }}</div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <div class="alert alert-info">
            Painel inicial publicado para o modulo de transporte escolar. A camada moderna cobre o inventario funcional do termo e prepara a integracao com legado, frota e secretaria. {{ $filtro_descricao ?? 'Sem filtros aplicados' }}.
        </div>

        <div class="row g-3 mb-3">
            @foreach($indicadores as $indicador)
                <div class="col-md-6 col-xl-3">
                    <div class="card h-100">
                        <div class="card-body">
                            <div class="text-muted small">{{ $indicador['titulo'] }}</div>
                            <div class="display-6">{{ $indicador['valor'] }}</div>
                            <div class="small text-muted">{{ $indicador['contexto'] }}</div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="row g-3">
            <div class="col-xl-8">
                <div class="card mb-3">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span>Requisitos do Termo</span>
                        <span class="badge bg-secondary">A1-A8</span>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered align-middle">
                                <thead class="table-light">
                                <tr>
                                    <th style="width: 80px;">Codigo</th>
                                    <th>Titulo</th>
                                    <th>Descricao</th>
                                    <th style="width: 140px;">Status</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($requisitos as $requisito)
                                    <tr>
                                        <td class="fw-bold">{{ $requisito['codigo'] }}</td>
                                        <td>{{ $requisito['titulo'] }}</td>
                                        <td>
                                            <div>{{ $requisito['descricao'] }}</div>
                                            <div class="small text-muted">{{ $requisito['evidencia'] }}</div>
                                        </td>
                                        <td>
                                            @if($requisito['status'] === 'disponivel')
                                                <span class="badge bg-success">disponivel</span>
                                            @elseif($requisito['status'] === 'em_implantacao')
                                                <span class="badge bg-warning text-dark">em implantacao</span>
                                            @else
                                                <span class="badge bg-secondary">{{ $requisito['status'] }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-lg-6">
                        <div class="card h-100">
                            <div class="card-header">Linhas e Rotas</div>
                            <div class="card-body">
                                <ul class="list-group list-group-flush">
                                    @foreach($linhas as $linha)
                                        <li class="list-group-item px-0">
                                            <div class="d-flex justify-content-between">
                                                <strong>{{ $linha['codigo'] }} - {{ $linha['nome'] }}</strong>
                                                <span class="badge bg-light text-dark">{{ $linha['tipo'] }}</span>
                                            </div>
                                            <div class="small text-muted">{{ $linha['horario'] }} | {{ $linha['custo'] }}</div>
                                            @if(!empty($linha['unidade_escolar']))
                                                <div class="small text-muted">Unidade escolar: {{ $linha['unidade_escolar'] }}</div>
                                            @endif
                                            <div class="small text-muted text-break">
                                                Roteiro: {{ $linha['roteiro_resumido'] ?? ($linha['rota_descricao'] ?? 'Sem pontos cadastrados') }}
                                            </div>
                                            <div class="small text-muted">
                                                Pontos: {{ $linha['pontos_total'] ?? 0 }}
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3">
                        <div class="card h-100">
                            <div class="card-header">Veiculos Vinculados</div>
                            <div class="card-body">
                                <ul class="list-group list-group-flush">
                                    @foreach($veiculos as $veiculo)
                                        <li class="list-group-item px-0">
                                            <div class="d-flex justify-content-between">
                                                <strong>{{ $veiculo['placa'] }}</strong>
                                                <span class="badge bg-light text-dark">{{ $veiculo['status'] }}</span>
                                            </div>
                                            <div>{{ $veiculo['modelo'] }}</div>
                                            <div class="small text-muted">Motorista: {{ $veiculo['motorista'] }}</div>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3">
                        <div class="card h-100">
                            <div class="card-header">Pontos de rota</div>
                            <div class="card-body">
                                <ul class="list-group list-group-flush">
                                    @forelse($pontos as $ponto)
                                        <li class="list-group-item px-0">
                                            <div class="d-flex justify-content-between">
                                                <strong>{{ $ponto['nome'] }}</strong>
                                                <span class="badge bg-light text-dark">{{ $ponto['tipo_ponto'] }}</span>
                                            </div>
                                            <div class="small text-muted">
                                                Linha: {{ $ponto['linha_codigo'] ?? '-' }} | Ordem: {{ $ponto['ordem'] }}
                                            </div>
                                            @if(!empty($ponto['endereco']))
                                                <div class="small text-muted">{{ $ponto['endereco'] }}</div>
                                            @endif
                                        </li>
                                    @empty
                                        <li class="list-group-item px-0 text-muted">Nenhum ponto cadastrado.</li>
                                    @endforelse
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mt-3">
                    <div class="card-header">Alunos no recorte</div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm table-striped table-bordered align-middle mb-0">
                                <thead class="table-light">
                                <tr>
                                    <th>Aluno</th>
                                    <th>Escola</th>
                                    <th>Periodo</th>
                                    <th>Linha</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse($alunos as $aluno)
                                    <tr>
                                        <td>{{ $aluno['nome'] }}</td>
                                        <td>{{ $aluno['escola'] }}</td>
                                        <td>{{ $aluno['periodo_uso'] ?? '-' }}</td>
                                        <td>{{ $aluno['linha'] ?? '-' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted">Nenhum aluno encontrado para os filtros aplicados.</td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-4">
                <div class="card mb-3">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span>Relatorios disponiveis</span>
                        <a href="{{ route('transportescolar.web.relatorios') }}" class="btn btn-sm btn-outline-success">Abrir A7</a>
                    </div>
                    <div class="card-body">
                        <ul class="mb-0">
                            @foreach($relatorios as $relatorio)
                                <li>{{ $relatorio }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>

                <div class="card mb-3">
                    <div class="card-header">Integracoes previstas</div>
                    <div class="card-body">
                        <ul class="mb-0">
                            @foreach($integracoes as $integracao)
                                <li>{{ $integracao }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>

                <div class="card mb-3">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span>SETE</span>
                        <a href="{{ route('transportescolar.web.export.sete.json', $filtros) }}" class="btn btn-sm btn-outline-primary">Baixar JSON</a>
                    </div>
                    <div class="card-body">
                        <p class="small text-muted mb-3">Exportacao e importacao do banco municipal no formato SETE JSON estruturado.</p>
                        <form method="POST" action="{{ route('transportescolar.web.import.sete') }}" enctype="multipart/form-data" class="row g-2">
                            @csrf
                            <div class="col-12">
                                <label class="form-label">Arquivo SETE JSON</label>
                                <input type="file" name="arquivo_sete" class="form-control" accept=".json,application/json" required>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-success w-100">Importar SETE</button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="card mb-3">
                    <div class="card-header">Legado relacionado</div>
                    <div class="card-body">
                        <ul class="mb-0">
                            @foreach($legado as $item)
                                <li>
                                    <div class="fw-bold">{{ $item['arquivo'] }}</div>
                                    <div class="small text-muted">{{ $item['finalidade'] }}</div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">Proximas acoes tecnicas</div>
                    <div class="card-body">
                        <ul class="mb-0">
                            @foreach($acoes as $acao)
                                <li>{{ $acao }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
