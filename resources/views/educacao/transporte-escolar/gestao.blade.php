@extends('layouts.app')

@section('title', 'Transporte Escolar - Gestao')

@section('content')
    <div class="container-fluid mt-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h3 class="mb-0">Gestao do Transporte Escolar</h3>
                <div class="text-muted">Cadastro de linhas, veiculos, vinculos de frota e alunos</div>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('transportescolar.web.index') }}" class="btn btn-outline-secondary">Voltar ao painel</a>
                <a href="{{ route('transportescolar.web.export.csv') }}" class="btn btn-outline-primary">Exportar CSV</a>
            </div>
        </div>

        @if(session('status'))
            <div class="alert alert-success">{{ session('status') }}</div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <div class="row g-3">
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        {{ $linhaSelecionada ? 'Editar Linha' : 'Nova Linha' }}
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('transportescolar.web.gestao.linhas.store') }}" class="row g-3">
                            @csrf
                            <input type="hidden" name="id" value="{{ old('id', $linhaSelecionada ? $linhaSelecionada->id : null) }}">

                            <div class="col-md-4">
                                <label class="form-label">Codigo</label>
                                <input type="text" name="codigo" class="form-control" maxlength="30" required value="{{ old('codigo', $linhaSelecionada ? $linhaSelecionada->codigo : '') }}">
                            </div>
                            <div class="col-md-8">
                                <label class="form-label">Nome</label>
                                <input type="text" name="nome" class="form-control" maxlength="180" required value="{{ old('nome', $linhaSelecionada ? $linhaSelecionada->nome : '') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Tipo de servico</label>
                                <select name="tipo_servico" class="form-select" required>
                                    @php($tipoLinha = old('tipo_servico', $linhaSelecionada ? $linhaSelecionada->tipo_servico : 'proprio'))
                                    <option value="proprio" {{ $tipoLinha === 'proprio' ? 'selected' : '' }}>Proprio</option>
                                    <option value="terceirizado" {{ $tipoLinha === 'terceirizado' ? 'selected' : '' }}>Terceirizado</option>
                                    <option value="transporte_publico" {{ $tipoLinha === 'transporte_publico' ? 'selected' : '' }}>Transporte publico coletivo</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Saida</label>
                                <input type="text" name="horario_saida" class="form-control" maxlength="20" value="{{ old('horario_saida', $linhaSelecionada ? $linhaSelecionada->horario_saida : '') }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Retorno</label>
                                <input type="text" name="horario_retorno" class="form-control" maxlength="20" value="{{ old('horario_retorno', $linhaSelecionada ? $linhaSelecionada->horario_retorno : '') }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Custo mensal</label>
                                <input type="number" name="custo_mensal" class="form-control" min="0" step="0.01" value="{{ old('custo_mensal', $linhaSelecionada ? $linhaSelecionada->custo_mensal : 0) }}">
                            </div>
                            <div class="col-md-8">
                                <label class="form-label">Unidade escolar</label>
                                <input type="text" name="unidade_escolar" class="form-control" maxlength="180" value="{{ old('unidade_escolar', $linhaSelecionada ? $linhaSelecionada->unidade_escolar : '') }}">
                            </div>
                            <div class="col-12">
                                <label class="form-label">Descricao da rota</label>
                                <textarea name="rota_descricao" class="form-control" rows="3">{{ old('rota_descricao', $linhaSelecionada ? $linhaSelecionada->rota_descricao : '') }}</textarea>
                            </div>
                            @if($linhaSelecionada && !empty($linhaSelecionada->roteiro_resumido))
                                <div class="col-12">
                                    <div class="alert alert-light border mb-0">
                                        <div class="fw-bold">Roteiro consolidado</div>
                                        <div class="small text-muted text-break">{{ $linhaSelecionada->roteiro_resumido }}</div>
                                        <div class="small text-muted">Pontos ativos: {{ $linhaSelecionada->pontos_total ?? 0 }}</div>
                                        @if(!empty($linhaSelecionada->roteiro_detalhado))
                                            <ul class="small text-muted mb-0 mt-2">
                                                @foreach($linhaSelecionada->roteiro_detalhado as $item)
                                                    <li>{{ $item }}</li>
                                                @endforeach
                                            </ul>
                                        @endif
                                    </div>
                                </div>
                            @endif
                            <div class="col-12 form-check ms-2">
                                <input type="checkbox" class="form-check-input" name="ativo" value="1" id="linha_ativa" {{ (bool) old('ativo', $linhaSelecionada ? $linhaSelecionada->ativo : true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="linha_ativa">Linha ativa</label>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-success">
                                    {{ $linhaSelecionada ? 'Atualizar linha' : 'Salvar linha' }}
                                </button>
                            </div>
                        </form>

                        <hr>

                        <div class="table-responsive">
                            <table class="table table-striped table-bordered align-middle mb-0">
                                <thead class="table-light">
                                <tr>
                                    <th>Código</th>
                                    <th>Nome</th>
                                    <th>Tipo</th>
                                    <th>Horarios</th>
                                    <th>Roteiro</th>
                                    <th>Ações</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse($linhas as $linha)
                                    <tr>
                                        <td>{{ $linha['codigo'] }}</td>
                                        <td>{{ $linha['nome'] }}</td>
                                        <td>{{ $linha['tipo_servico'] }}</td>
                                        <td>{{ $linha['horario_saida'] }} / {{ $linha['horario_retorno'] }}</td>
                                        <td>
                                            <div class="small text-muted text-break">
                                                {{ $linha['roteiro_resumido'] ?? ($linha['rota_descricao'] ?? 'Sem pontos cadastrados') }}
                                            </div>
                                            <div class="small text-muted">Pontos: {{ $linha['pontos_total'] ?? 0 }}</div>
                                        </td>
                                        <td class="text-nowrap">
                                            <a class="btn btn-sm btn-outline-primary" href="{{ route('transportescolar.web.gestao', ['linha_id' => $linha['id']]) }}">Editar</a>
                                            <form method="POST" action="{{ route('transportescolar.web.gestao.linhas.destroy', ['id' => $linha['id']]) }}" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Remover esta linha?')">Excluir</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6">Nenhuma linha cadastrada.</td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="card mt-3">
                    <div class="card-header">
                        {{ $pontoSelecionado ? 'Editar Ponto de Rota' : 'Novo Ponto de Rota' }}
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('transportescolar.web.gestao.pontos.store') }}" class="row g-3">
                            @csrf
                            <input type="hidden" name="id" value="{{ old('id', $pontoSelecionado ? $pontoSelecionado->id : null) }}">

                            <div class="col-md-6">
                                <label class="form-label">Linha</label>
                                <select name="linha_id" class="form-select" required>
                                    <option value="">Selecione</option>
                                    @foreach($linhas as $linha)
                                        <option value="{{ $linha['id'] }}" {{ (string) old('linha_id', $pontoSelecionado ? $pontoSelecionado->linha_id : null) === (string) $linha['id'] ? 'selected' : '' }}>
                                            {{ $linha['codigo'] }} - {{ $linha['nome'] }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Nome do ponto</label>
                                <input type="text" name="nome" class="form-control" maxlength="180" required value="{{ old('nome', $pontoSelecionado ? $pontoSelecionado->nome : '') }}">
                            </div>
                            <div class="col-md-8">
                                <label class="form-label">Endereco</label>
                                <input type="text" name="endereco" class="form-control" maxlength="255" value="{{ old('endereco', $pontoSelecionado ? $pontoSelecionado->endereco : '') }}">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Ordem</label>
                                <input type="number" name="ordem" class="form-control" min="0" value="{{ old('ordem', $pontoSelecionado ? $pontoSelecionado->ordem : 0) }}">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Tipo</label>
                                @php($tipoPonto = old('tipo_ponto', $pontoSelecionado ? $pontoSelecionado->tipo_ponto : 'parada'))
                                <select name="tipo_ponto" class="form-select" required>
                                    <option value="parada" {{ $tipoPonto === 'parada' ? 'selected' : '' }}>Parada</option>
                                    <option value="embarque" {{ $tipoPonto === 'embarque' ? 'selected' : '' }}>Embarque</option>
                                    <option value="desembarque" {{ $tipoPonto === 'desembarque' ? 'selected' : '' }}>Desembarque</option>
                                    <option value="terminal" {{ $tipoPonto === 'terminal' ? 'selected' : '' }}>Terminal</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Observacao</label>
                                <textarea name="observacao" class="form-control" rows="2">{{ old('observacao', $pontoSelecionado ? $pontoSelecionado->observacao : '') }}</textarea>
                            </div>
                            <div class="col-12 form-check ms-2">
                                <input type="checkbox" class="form-check-input" name="ativo" value="1" id="ponto_ativo" {{ (bool) old('ativo', $pontoSelecionado ? $pontoSelecionado->ativo : true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="ponto_ativo">Ponto ativo</label>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-success">
                                    {{ $pontoSelecionado ? 'Atualizar ponto' : 'Salvar ponto' }}
                                </button>
                            </div>
                        </form>

                        <hr>

                        <div class="table-responsive">
                            <table class="table table-striped table-bordered align-middle mb-0">
                                <thead class="table-light">
                                <tr>
                                    <th>Linha</th>
                                    <th>Nome</th>
                                    <th>Endereco</th>
                                    <th>Tipo</th>
                                    <th>Ordem</th>
                                    <th>Acoes</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse($pontos as $ponto)
                                    <tr>
                                        <td>{{ $ponto['linha_codigo'] ?? $ponto['linha_id'] }}</td>
                                        <td>{{ $ponto['nome'] }}</td>
                                        <td>{{ $ponto['endereco'] ?? '-' }}</td>
                                        <td>{{ $ponto['tipo_ponto'] }}</td>
                                        <td>{{ $ponto['ordem'] }}</td>
                                        <td class="text-nowrap">
                                            <a class="btn btn-sm btn-outline-primary" href="{{ route('transportescolar.web.gestao', ['ponto_id' => $ponto['id']]) }}">Editar</a>
                                            <form method="POST" action="{{ route('transportescolar.web.gestao.pontos.destroy', ['id' => $ponto['id']]) }}" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Remover este ponto?')">Excluir</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6">Nenhum ponto cadastrado.</td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="card mt-3">
                    <div class="card-header">
                        {{ $vinculoSelecionado ? 'Editar Vinculo Linha x Veiculo' : 'Novo Vinculo Linha x Veiculo' }}
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('transportescolar.web.gestao.vinculos.store') }}" class="row g-3">
                            @csrf
                            <input type="hidden" name="id" value="{{ old('id', $vinculoSelecionado ? $vinculoSelecionado->id : null) }}">

                            <div class="col-md-6">
                                <label class="form-label">Linha</label>
                                <select name="linha_id" class="form-select" required>
                                    <option value="">Selecione</option>
                                    @foreach($linhas as $linha)
                                        <option value="{{ $linha['id'] }}" {{ (string) old('linha_id', $vinculoSelecionado ? $vinculoSelecionado->linha_id : null) === (string) $linha['id'] ? 'selected' : '' }}>
                                            {{ $linha['codigo'] }} - {{ $linha['nome'] }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Veiculo</label>
                                <select name="veiculo_id" class="form-select" required>
                                    <option value="">Selecione</option>
                                    @foreach($veiculos as $veiculo)
                                        <option value="{{ $veiculo['id'] }}" {{ (string) old('veiculo_id', $vinculoSelecionado ? $vinculoSelecionado->veiculo_id : null) === (string) $veiculo['id'] ? 'selected' : '' }}>
                                            {{ $veiculo['placa'] }} - {{ $veiculo['modelo'] }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Inicio</label>
                                <input type="date" name="data_inicio" class="form-control" value="{{ old('data_inicio', $vinculoSelecionado && $vinculoSelecionado->data_inicio ? $vinculoSelecionado->data_inicio->format('Y-m-d') : null) }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Fim</label>
                                <input type="date" name="data_fim" class="form-control" value="{{ old('data_fim', $vinculoSelecionado && $vinculoSelecionado->data_fim ? $vinculoSelecionado->data_fim->format('Y-m-d') : null) }}">
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Observacao</label>
                                <textarea name="observacao" class="form-control" rows="2">{{ old('observacao', $vinculoSelecionado ? $vinculoSelecionado->observacao : '') }}</textarea>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-success">
                                    {{ $vinculoSelecionado ? 'Atualizar vinculo' : 'Salvar vinculo' }}
                                </button>
                            </div>
                        </form>

                        <hr>

                        <div class="table-responsive">
                            <table class="table table-striped table-bordered align-middle mb-0">
                                <thead class="table-light">
                                <tr>
                                    <th>Linha</th>
                                    <th>Veiculo</th>
                                    <th>Periodo</th>
                                    <th>Observacao</th>
                                    <th>Acoes</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse($vinculos as $vinculo)
                                    <tr>
                                        <td>{{ $vinculo['linha_codigo'] ?? $vinculo['linha_id'] }}</td>
                                        <td>{{ $vinculo['veiculo_placa'] ?? $vinculo['veiculo_id'] }}</td>
                                        <td>{{ $vinculo['data_inicio'] ?? '-' }} / {{ $vinculo['data_fim'] ?? '-' }}</td>
                                        <td>{{ $vinculo['observacao'] ?? '-' }}</td>
                                        <td class="text-nowrap">
                                            <a class="btn btn-sm btn-outline-primary" href="{{ route('transportescolar.web.gestao', ['vinculo_id' => $vinculo['id']]) }}">Editar</a>
                                            <form method="POST" action="{{ route('transportescolar.web.gestao.vinculos.destroy', ['id' => $vinculo['id']]) }}" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Remover este vinculo?')">Excluir</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5">Nenhum vinculo cadastrado.</td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        {{ $veiculoSelecionado ? 'Editar Veiculo' : 'Novo Veiculo' }}
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('transportescolar.web.gestao.veiculos.store') }}" class="row g-3">
                            @csrf
                            <input type="hidden" name="id" value="{{ old('id', $veiculoSelecionado ? $veiculoSelecionado->id : null) }}">

                            <div class="col-md-4">
                                <label class="form-label">Placa</label>
                                <input type="text" name="placa" class="form-control" maxlength="20" required value="{{ old('placa', $veiculoSelecionado ? $veiculoSelecionado->placa : '') }}">
                            </div>
                            <div class="col-md-8">
                                <label class="form-label">Modelo</label>
                                <input type="text" name="modelo" class="form-control" maxlength="180" required value="{{ old('modelo', $veiculoSelecionado ? $veiculoSelecionado->modelo : '') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Motorista</label>
                                <input type="text" name="motorista_nome" class="form-control" maxlength="180" value="{{ old('motorista_nome', $veiculoSelecionado ? $veiculoSelecionado->motorista_nome : '') }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Capacidade</label>
                                <input type="number" name="capacidade" class="form-control" min="1" value="{{ old('capacidade', $veiculoSelecionado ? $veiculoSelecionado->capacidade : '') }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Situacao</label>
                                <input type="text" name="situacao" class="form-control" maxlength="40" required value="{{ old('situacao', $veiculoSelecionado ? $veiculoSelecionado->situacao : 'disponivel') }}">
                            </div>
                            <div class="col-12">
                                <label class="form-label">Observacao</label>
                                <textarea name="observacao" class="form-control" rows="3">{{ old('observacao', $veiculoSelecionado ? $veiculoSelecionado->observacao : '') }}</textarea>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-success">
                                    {{ $veiculoSelecionado ? 'Atualizar veiculo' : 'Salvar veiculo' }}
                                </button>
                            </div>
                        </form>

                        <hr>

                        <div class="table-responsive">
                            <table class="table table-striped table-bordered align-middle mb-0">
                                <thead class="table-light">
                                <tr>
                                    <th>Placa</th>
                                    <th>Modelo</th>
                                    <th>Motorista</th>
                                    <th>Situacao</th>
                                    <th>Acoes</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse($veiculos as $veiculo)
                                    <tr>
                                        <td>{{ $veiculo['placa'] }}</td>
                                        <td>{{ $veiculo['modelo'] }}</td>
                                        <td>{{ $veiculo['motorista_nome'] ?? '-' }}</td>
                                        <td>{{ $veiculo['situacao'] }}</td>
                                        <td class="text-nowrap">
                                            <a class="btn btn-sm btn-outline-primary" href="{{ route('transportescolar.web.gestao', ['veiculo_id' => $veiculo['id']]) }}">Editar</a>
                                            <form method="POST" action="{{ route('transportescolar.web.gestao.veiculos.destroy', ['id' => $veiculo['id']]) }}" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Remover este veiculo?')">Excluir</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5">Nenhum veiculo cadastrado.</td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="card mt-3">
                    <div class="card-header">
                        {{ $alunoSelecionado ? 'Editar Aluno' : 'Novo Aluno' }}
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('transportescolar.web.gestao.alunos.store') }}" class="row g-3" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="id" value="{{ old('id', $alunoSelecionado ? $alunoSelecionado->id : null) }}">

                            <div class="col-md-6">
                                <label class="form-label">Linha</label>
                                <select name="linha_id" class="form-select">
                                    <option value="">Sem linha</option>
                                    @foreach($linhas as $linha)
                                        <option value="{{ $linha['id'] }}" {{ (string) old('linha_id', $alunoSelecionado ? $alunoSelecionado->linha_id : null) === (string) $linha['id'] ? 'selected' : '' }}>
                                            {{ $linha['codigo'] }} - {{ $linha['nome'] }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Nome do aluno</label>
                                <input type="text" name="aluno_nome" class="form-control" maxlength="180" required value="{{ old('aluno_nome', $alunoSelecionado ? $alunoSelecionado->aluno_nome : '') }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">CPF</label>
                                <input type="text" name="aluno_cpf" class="form-control" maxlength="20" value="{{ old('aluno_cpf', $alunoSelecionado ? $alunoSelecionado->aluno_cpf : '') }}">
                            </div>
                            <div class="col-md-8">
                                <label class="form-label">Escola</label>
                                <input type="text" name="escola_nome" class="form-control" maxlength="180" value="{{ old('escola_nome', $alunoSelecionado ? $alunoSelecionado->escola_nome : '') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Local de embarque</label>
                                <input type="text" name="local_embarque" class="form-control" maxlength="180" value="{{ old('local_embarque', $alunoSelecionado ? $alunoSelecionado->local_embarque : '') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Motivo de uso</label>
                                <input type="text" name="motivo_uso" class="form-control" maxlength="180" value="{{ old('motivo_uso', $alunoSelecionado ? $alunoSelecionado->motivo_uso : '') }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Periodo</label>
                                <input type="text" name="periodo_uso" class="form-control" maxlength="80" value="{{ old('periodo_uso', $alunoSelecionado ? $alunoSelecionado->periodo_uso : '') }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Situacao matricula</label>
                                <input type="text" name="situacao_matricula" class="form-control" maxlength="40" value="{{ old('situacao_matricula', $alunoSelecionado ? $alunoSelecionado->situacao_matricula : '') }}">
                            </div>
                            <div class="col-md-4 form-check mt-4 pt-2">
                                <input type="checkbox" class="form-check-input" name="utiliza_transporte" value="1" id="aluno_transporte" {{ (bool) old('utiliza_transporte', $alunoSelecionado ? $alunoSelecionado->utiliza_transporte : true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="aluno_transporte">Utiliza transporte</label>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Foto do aluno</label>
                                <input type="file" name="foto_aluno" class="form-control" accept="image/*">
                            </div>
                            @if(!empty($alunoSelecionado) && !empty($alunoSelecionado->foto_path))
                                <div class="col-md-6">
                                    <label class="form-label">Foto atual</label>
                                    <div class="border rounded p-2 text-center">
                                        <img src="{{ asset('storage/' . $alunoSelecionado->foto_path) }}" alt="Foto atual" style="max-width: 160px; max-height: 200px;">
                                    </div>
                                </div>
                            @endif
                            <div class="col-12">
                                <button type="submit" class="btn btn-success">
                                    {{ $alunoSelecionado ? 'Atualizar aluno' : 'Salvar aluno' }}
                                </button>
                            </div>
                        </form>

                        <hr>

                        <div class="table-responsive">
                            <table class="table table-striped table-bordered align-middle mb-0">
                                <thead class="table-light">
                                <tr>
                                    <th>Aluno</th>
                                    <th>Linha</th>
                                    <th>Escola</th>
                                    <th>Embarque</th>
                                    <th>Acoes</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse($alunos as $aluno)
                                    <tr>
                                        <td>{{ $aluno['aluno_nome'] }}</td>
                                        <td>{{ $aluno['linha_codigo'] ?? '-' }}</td>
                                        <td>{{ $aluno['escola_nome'] ?? '-' }}</td>
                                        <td>{{ $aluno['local_embarque'] ?? '-' }}</td>
                                        <td class="text-nowrap">
                                            <a class="btn btn-sm btn-outline-success" href="{{ route('transportescolar.web.gestao.alunos.carteira', ['id' => $aluno['id']]) }}" target="_blank">Carteira</a>
                                            <a class="btn btn-sm btn-outline-primary" href="{{ route('transportescolar.web.gestao', ['aluno_id' => $aluno['id']]) }}">Editar</a>
                                            <form method="POST" action="{{ route('transportescolar.web.gestao.alunos.destroy', ['id' => $aluno['id']]) }}" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Remover este aluno?')">Excluir</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5">Nenhum aluno cadastrado.</td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
