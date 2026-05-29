<?php

namespace App\Services\Educacao\TransporteEscolar;

use App\Models\Educacao\TransporteEscolar\AlunoTransporteEscolar;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Support\Facades\Storage;

class TransporteEscolarCarteiraService
{
    /**
     * @return array<string, mixed>
     */
    public function payload(int $alunoId): array
    {
        $aluno = AlunoTransporteEscolar::query()
            ->with('linha')
            ->findOrFail($alunoId);

        $codigoCarteira = $this->codigoCarteira($aluno);
        $conteudoQr = implode('|', array_filter([
            'ECIDADE',
            'TRANSPORTE_ESCOLAR',
            (string) $aluno->id,
            (string) $aluno->aluno_nome,
            (string) ($aluno->linha ? $aluno->linha->codigo : ''),
            $codigoCarteira,
        ]));

        $qrCode = Builder::create()
            ->writer(new PngWriter())
            ->writerOptions([])
            ->data($conteudoQr)
            ->encoding(new Encoding('UTF-8'))
            ->size(240)
            ->margin(10)
            ->validateResult(false)
            ->build();

        return [
            'aluno' => $aluno,
            'linha' => $aluno->linha,
            'codigo_carteira' => $codigoCarteira,
            'qr_code' => $qrCode->getDataUri(),
            'foto_data_uri' => $this->fotoDataUri($aluno->foto_path),
            'validade_texto' => date('d/m/Y'),
            'emitido_em' => date('d/m/Y H:i'),
        ];
    }

    private function codigoCarteira(AlunoTransporteEscolar $aluno): string
    {
        return 'TES-' . str_pad((string) $aluno->id, 6, '0', STR_PAD_LEFT);
    }

    private function fotoDataUri(?string $fotoPath): ?string
    {
        if ($fotoPath === null || $fotoPath === '') {
            return null;
        }

        if (!Storage::disk('public')->exists($fotoPath)) {
            return null;
        }

        $conteudo = Storage::disk('public')->get($fotoPath);
        $mime = Storage::disk('public')->mimeType($fotoPath) ?: 'image/jpeg';

        return 'data:' . $mime . ';base64,' . base64_encode($conteudo);
    }
}
