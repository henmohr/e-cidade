<?php

namespace App\Services\Financeiro\Relatorio;

interface PdfRendererInterface
{
    public function render(string $html): string;
}
