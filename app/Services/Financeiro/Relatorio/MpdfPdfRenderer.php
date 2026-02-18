<?php

namespace App\Services\Financeiro\Relatorio;

use Mpdf\Mpdf;
use Mpdf\Output\Destination;
use Throwable;

class MpdfPdfRenderer implements PdfRendererInterface
{
    public function render(string $html): string
    {
        try {
            $mpdf = new Mpdf([
                'tempDir' => sys_get_temp_dir(),
            ]);
            $mpdf->WriteHTML($html);

            return (string) $mpdf->Output('', Destination::STRING_RETURN);
        } catch (Throwable $exception) {
            return $html;
        }
    }
}
