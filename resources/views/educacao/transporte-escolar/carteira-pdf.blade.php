<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            color: #13253d;
            font-size: 11pt;
        }

        .card {
            border: 2px solid #153b63;
            border-radius: 16px;
            overflow: hidden;
        }

        .header {
            background: #153b63;
            color: #fff;
            padding: 14px 18px;
        }

        .header .title {
            font-size: 18pt;
            font-weight: bold;
            margin: 0;
        }

        .header .sub {
            font-size: 9pt;
            margin-top: 3px;
        }

        .content {
            width: 100%;
            border-collapse: collapse;
        }

        .content td {
            vertical-align: top;
            padding: 18px;
        }

        .foto {
            width: 140px;
            height: 180px;
            border: 1px dashed #91a9c9;
            text-align: center;
            color: #4c6484;
            font-weight: bold;
            font-size: 12pt;
        }

        .foto div {
            padding-top: 65px;
        }

        .foto img {
            width: 140px;
            height: 180px;
            object-fit: cover;
        }

        .qr {
            width: 210px;
            text-align: center;
            border-left: 1px solid #d6e0eb;
        }

        .qr img {
            width: 180px;
            height: 180px;
        }

        .dados {
            width: 100%;
        }

        .dados h1 {
            font-size: 20pt;
            margin: 0 0 6px 0;
        }

        .tag {
            display: inline-block;
            background: #e9f2fb;
            color: #153b63;
            padding: 4px 10px;
            border-radius: 999px;
            font-size: 9pt;
            font-weight: bold;
            margin-bottom: 12px;
        }

        .field {
            margin-bottom: 9px;
        }

        .field .label {
            font-size: 8pt;
            color: #5b6b7f;
            text-transform: uppercase;
        }

        .field .value {
            font-size: 11pt;
            font-weight: bold;
        }

        .footer {
            border-top: 1px solid #d6e0eb;
            padding: 12px 18px 16px;
            font-size: 9pt;
            color: #54657c;
        }

        .footer-table {
            width: 100%;
            border-collapse: collapse;
        }

        .footer-table td {
            width: 50%;
            vertical-align: top;
        }
    </style>
</head>
<body>
<div class="card">
    <div class="header">
        <div class="title">Carteira de Transporte Escolar</div>
        <div class="sub">Codigo da carteira: {{ $codigo_carteira }}</div>
    </div>

    <table class="content">
        <tr>
            <td style="width: 155px;">
                <div class="foto">
                    @if(!empty($foto_data_uri))
                        <img src="{{ $foto_data_uri }}" alt="Foto do aluno">
                    @else
                        <div>FOTO</div>
                    @endif
                </div>
            </td>
            <td>
                <div class="dados">
                    <h1>{{ $aluno->aluno_nome }}</h1>
                    <div class="tag">{{ $linha ? $linha->codigo : 'SEM LINHA DEFINIDA' }}</div>

                    <div class="field">
                        <div class="label">CPF</div>
                        <div class="value">{{ $aluno->aluno_cpf ?: '-' }}</div>
                    </div>
                    <div class="field">
                        <div class="label">Escola</div>
                        <div class="value">{{ $aluno->escola_nome ?: '-' }}</div>
                    </div>
                    <div class="field">
                        <div class="label">Linha</div>
                        <div class="value">{{ $linha ? $linha->nome : '-' }}</div>
                    </div>
                    <div class="field">
                        <div class="label">Local de embarque</div>
                        <div class="value">{{ $aluno->local_embarque ?: '-' }}</div>
                    </div>
                    <div class="field">
                        <div class="label">Periodo de uso</div>
                        <div class="value">{{ $aluno->periodo_uso ?: '-' }}</div>
                    </div>
                    <div class="field">
                        <div class="label">Situacao</div>
                        <div class="value">{{ $aluno->situacao_matricula ?: '-' }}</div>
                    </div>
                </div>
            </td>
            <td class="qr">
                <img src="{{ $qr_code }}" alt="QRCode">
                <div style="margin-top: 8px;">Leitura para validacao interna</div>
            </td>
        </tr>
    </table>

    <div class="footer">
        <table class="footer-table">
            <tr>
                <td>
                    <strong>Emitido em:</strong> {{ $emitido_em }}<br>
                    <strong>Validade impressa:</strong> {{ $validade_texto }}
                </td>
                <td style="text-align: right;">
                    <strong>Utiliza transporte:</strong> {{ $aluno->utiliza_transporte ? 'Sim' : 'Nao' }}<br>
                    <strong>Identificador:</strong> #{{ $aluno->id }}
                </td>
            </tr>
        </table>
    </div>
</div>
</body>
</html>
