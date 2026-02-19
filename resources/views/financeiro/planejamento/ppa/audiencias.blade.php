@extends('layouts.app')

@section('title', 'PPA - Audiencias Publicas')

@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center mt-4">
            <div class="col-12 col-xl-11">
                <div class="card mb-3">
                    <div class="card-header">PPA - Audiencias Publicas</div>
                    <div class="card-body">
                        <form method="GET" action="{{ route('planejamento.ppa.audiencias.index') }}" class="row g-3 align-items-end">
                            <div class="col-12 col-md-8">
                                <label for="versao_id" class="form-label">Versao PPA</label>
                                <select id="versao_id" name="versao_id" class="form-select" required>
                                    @foreach($versoes as $versao)
                                        <option value="{{ $versao->id }}" {{ (int) $versaoIdSelecionada === (int) $versao->id ? 'selected' : '' }}>
                                            #{{ $versao->id }} | Plano {{ $versao->plano->codigo ?? '-' }} | Versao {{ $versao->numero_versao }} | {{ $versao->status }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12 col-md-4">
                                <button type="submit" class="btn btn-primary w-100">Carregar Audiencias</button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="card mb-3">
                    <div class="card-header">Registrar Nova Audiencia</div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('planejamento.ppa.audiencias.store') }}" class="row g-3">
                            @csrf
                            <input type="hidden" name="versao_id" value="{{ $versaoIdSelecionada }}">

                            <div class="col-12 col-md-3">
                                <label for="data_realizacao" class="form-label">Data</label>
                                <input id="data_realizacao" name="data_realizacao" type="date" class="form-control" required>
                            </div>
                            <div class="col-12 col-md-3">
                                <label for="status" class="form-label">Status</label>
                                <select id="status" name="status" class="form-select" required>
                                    <option value="recebida">Recebida</option>
                                    <option value="em_analise" selected>Em analise</option>
                                    <option value="deferida">Deferida</option>
                                    <option value="indeferida">Indeferida</option>
                                    <option value="concluida">Concluida</option>
                                </select>
                            </div>
                            <div class="col-12 col-md-6">
                                <label for="bairro_atendido" class="form-label">Bairro a ser atendido</label>
                                <input id="bairro_atendido" name="bairro_atendido" type="text" class="form-control" maxlength="120" required>
                            </div>
                            <div class="col-12 col-md-6">
                                <label for="contato_solicitante" class="form-label">Contato do solicitante</label>
                                <input id="contato_solicitante" name="contato_solicitante" type="text" class="form-control" maxlength="255" required>
                            </div>
                            <div class="col-12 col-md-6">
                                <label for="orgao_responsavel" class="form-label">Orgao responsavel</label>
                                <input id="orgao_responsavel" name="orgao_responsavel" type="text" class="form-control" maxlength="255" required>
                            </div>
                            <div class="col-12">
                                <label for="solicitacoes_comunidade" class="form-label">Solicitacoes da comunidade</label>
                                <textarea id="solicitacoes_comunidade" name="solicitacoes_comunidade" rows="3" class="form-control" required></textarea>
                            </div>
                            <div class="col-12">
                                <label for="observacao" class="form-label">Observacao</label>
                                <textarea id="observacao" name="observacao" rows="2" class="form-control"></textarea>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-success">Salvar Audiencia</button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">Audiencias Cadastradas</div>
                    <div class="card-body">
                        @if($audiencias->isEmpty())
                            <p class="mb-0">Nenhuma audiencia cadastrada para esta versao.</p>
                        @else
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered align-middle">
                                    <thead class="table-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>Data</th>
                                        <th>Bairro</th>
                                        <th>Status</th>
                                        <th>Contato</th>
                                        <th>Orgao</th>
                                        <th>Solicitacoes</th>
                                        <th>Atas</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($audiencias as $audiencia)
                                        <tr>
                                            <td>{{ $audiencia->id }}</td>
                                            <td>{{ $audiencia->data_realizacao }}</td>
                                            <td>{{ $audiencia->bairro_atendido }}</td>
                                            <td>{{ $audiencia->status }}</td>
                                            <td>{{ $audiencia->contato_solicitante }}</td>
                                            <td>{{ $audiencia->orgao_responsavel }}</td>
                                            <td style="min-width: 260px;">{{ $audiencia->solicitacoes_comunidade }}</td>
                                            <td style="min-width: 300px;">
                                                <form method="POST" action="{{ route('planejamento.ppa.audiencias.anexos.store', ['audienciaId' => $audiencia->id]) }}" enctype="multipart/form-data" class="mb-2">
                                                    @csrf
                                                    <input type="hidden" name="versao_id" value="{{ $versaoIdSelecionada }}">
                                                    <div class="input-group input-group-sm">
                                                        <input type="file" name="ata" class="form-control" required>
                                                        <button type="submit" class="btn btn-outline-primary">Anexar</button>
                                                    </div>
                                                </form>

                                                @if($audiencia->anexos->isEmpty())
                                                    <small class="text-muted">Sem anexos</small>
                                                @else
                                                    <ul class="mb-0 ps-3">
                                                        @foreach($audiencia->anexos as $anexo)
                                                            <li>
                                                                <a href="{{ route('planejamento.ppa.audiencias.anexos.download', ['anexoId' => $anexo->id]) }}">
                                                                    {{ $anexo->nome_original }}
                                                                </a>
                                                                <small class="text-muted">({{ number_format(((int) $anexo->tamanho_bytes) / 1024, 1, ',', '.') }} KB)</small>
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
