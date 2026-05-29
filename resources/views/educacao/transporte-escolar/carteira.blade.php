@extends('layouts.app')

@section('title', 'Carteira do Estudante - Transporte Escolar')

@section('content')
    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: #fff !important; }
            .card { border: 0 !important; box-shadow: none !important; }
            .container-fluid { padding: 0 !important; }
        }

        .carteira-wrap {
            max-width: 980px;
            margin: 0 auto;
        }

        .carteira-card {
            border: 3px solid #153b63;
            border-radius: 20px;
            overflow: hidden;
            background: linear-gradient(180deg, #ffffff 0%, #f4f8fc 100%);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
        }

        .carteira-header {
            background: linear-gradient(90deg, #153b63 0%, #2162a7 100%);
            color: #fff;
            padding: 18px 22px;
        }

        .carteira-body {
            display: grid;
            grid-template-columns: 180px 1fr 240px;
            gap: 20px;
            padding: 22px;
            align-items: center;
        }

        .foto-placeholder {
            width: 160px;
            height: 200px;
            border: 2px dashed #91a9c9;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: #4c6484;
            font-weight: 600;
            background: rgba(33, 98, 167, 0.05);
        }

        .foto-img {
            width: 160px;
            height: 200px;
            border-radius: 16px;
            object-fit: cover;
            border: 2px solid #91a9c9;
            background: #fff;
        }

        .dados-aluno h2 {
            font-size: 1.5rem;
            margin-bottom: 6px;
        }

        .campo {
            margin-bottom: 12px;
        }

        .campo label {
            display: block;
            font-size: 0.78rem;
            text-transform: uppercase;
            color: #5b6b7f;
            margin-bottom: 2px;
        }

        .campo div {
            font-size: 1rem;
            font-weight: 600;
            color: #13253d;
        }

        .qr-box {
            text-align: center;
            border-left: 1px solid #d6e0eb;
            padding-left: 20px;
        }

        .qr-box img {
            width: 220px;
            height: 220px;
            image-rendering: pixelated;
        }

        .rodape-carteira {
            display: flex;
            justify-content: space-between;
            gap: 16px;
            padding: 0 22px 22px;
            color: #54657c;
            font-size: 0.9rem;
        }

        .chip {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 999px;
            background: #e9f2fb;
            color: #153b63;
            font-weight: 700;
            font-size: 0.8rem;
        }
    </style>

    <div class="container-fluid mt-4 carteira-wrap">
        <div class="d-flex justify-content-between align-items-center mb-3 no-print">
            <div>
                <h3 class="mb-0">Carteira do Estudante</h3>
                <div class="text-muted">Impressao da identificacao do transporte escolar</div>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('transportescolar.web.gestao.alunos.carteira.pdf', ['id' => $aluno->id]) }}" class="btn btn-outline-success">Baixar PDF</a>
                <button type="button" class="btn btn-primary" onclick="window.print()">Imprimir</button>
                <a href="{{ route('transportescolar.web.gestao', ['aluno_id' => $aluno->id]) }}" class="btn btn-outline-secondary">Voltar</a>
            </div>
        </div>

        <div class="carteira-card">
            <div class="carteira-header">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div>
                        <div class="small text-uppercase opacity-75">Municipio</div>
                        <h4 class="mb-0">Carteira de Transporte Escolar</h4>
                    </div>
                    <div class="text-end">
                        <div class="small text-uppercase opacity-75">Codigo da carteira</div>
                        <div class="fw-bold fs-5">{{ $codigo_carteira }}</div>
                    </div>
                </div>
            </div>

            <div class="carteira-body">
                <div class="foto-placeholder">
                    @if(!empty($foto_data_uri))
                        <img class="foto-img" src="{{ $foto_data_uri }}" alt="Foto do aluno">
                    @else
                        <div>
                            <div class="fs-1">Foto</div>
                            <div>Espaco para fotografia do aluno</div>
                        </div>
                    @endif
                </div>

                <div class="dados-aluno">
                    <h2>{{ $aluno->aluno_nome }}</h2>
                    <div class="chip mb-3">{{ $linha ? $linha->codigo : 'SEM LINHA DEFINIDA' }}</div>

                    <div class="row">
                        <div class="col-md-6 campo">
                            <label>CPF</label>
                            <div>{{ $aluno->aluno_cpf ?: '-' }}</div>
                        </div>
                        <div class="col-md-6 campo">
                            <label>Escola</label>
                            <div>{{ $aluno->escola_nome ?: '-' }}</div>
                        </div>
                        <div class="col-md-6 campo">
                            <label>Linha</label>
                            <div>{{ $linha ? $linha->nome : '-' }}</div>
                        </div>
                        <div class="col-md-6 campo">
                            <label>Local de embarque</label>
                            <div>{{ $aluno->local_embarque ?: '-' }}</div>
                        </div>
                        <div class="col-md-6 campo">
                            <label>Periodo de uso</label>
                            <div>{{ $aluno->periodo_uso ?: '-' }}</div>
                        </div>
                        <div class="col-md-6 campo">
                            <label>Situacao</label>
                            <div>{{ $aluno->situacao_matricula ?: '-' }}</div>
                        </div>
                    </div>
                </div>

                <div class="qr-box">
                    <img src="{{ $qr_code }}" alt="QRCode da carteira">
                    <div class="mt-2 small text-muted">Leitura para validacao interna</div>
                </div>
            </div>

            <div class="rodape-carteira">
                <div>
                    <strong>Emitido em:</strong> {{ $emitido_em }}<br>
                    <strong>Validade impressa:</strong> {{ $validade_texto }}
                </div>
                <div class="text-end">
                    <strong>Utiliza transporte:</strong> {{ $aluno->utiliza_transporte ? 'Sim' : 'Nao' }}<br>
                    <strong>Identificador:</strong> #{{ $aluno->id }}
                </div>
            </div>
        </div>
    </div>
@endsection
