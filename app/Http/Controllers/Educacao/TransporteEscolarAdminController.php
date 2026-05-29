<?php

namespace App\Http\Controllers\Educacao;

use App\Http\Controllers\Controller;
use App\Services\Educacao\TransporteEscolar\TransporteEscolarAdminService;
use App\Services\Educacao\TransporteEscolar\TransporteEscolarCarteiraService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Mpdf\Mpdf;
use Mpdf\Output\Destination;
use Illuminate\View\View;
use InvalidArgumentException;

class TransporteEscolarAdminController extends Controller
{
    public function index(Request $request, TransporteEscolarAdminService $service): View
    {
        $dados = $service->listagem();

        $linhaSelecionada = null;
        if ($request->filled('linha_id')) {
            try {
                $linhaSelecionada = $service->obterLinha((int) $request->query('linha_id'));
                foreach ($service->resumoLinha($linhaSelecionada) as $chave => $valor) {
                    $linhaSelecionada->setAttribute($chave, $valor);
                }
            } catch (InvalidArgumentException $e) {
                $linhaSelecionada = null;
            }
        }

        $veiculoSelecionado = null;
        if ($request->filled('veiculo_id')) {
            try {
                $veiculoSelecionado = $service->obterVeiculo((int) $request->query('veiculo_id'));
            } catch (InvalidArgumentException $e) {
                $veiculoSelecionado = null;
            }
        }

        $alunoSelecionado = null;
        if ($request->filled('aluno_id')) {
            try {
                $alunoSelecionado = $service->obterAluno((int) $request->query('aluno_id'));
            } catch (InvalidArgumentException $e) {
                $alunoSelecionado = null;
            }
        }

        $vinculoSelecionado = null;
        if ($request->filled('vinculo_id')) {
            try {
                $vinculoSelecionado = $service->obterVinculo((int) $request->query('vinculo_id'));
            } catch (InvalidArgumentException $e) {
                $vinculoSelecionado = null;
            }
        }

        $pontoSelecionado = null;
        if ($request->filled('ponto_id')) {
            try {
                $pontoSelecionado = $service->obterPonto((int) $request->query('ponto_id'));
            } catch (InvalidArgumentException $e) {
                $pontoSelecionado = null;
            }
        }

        return view('educacao.transporte-escolar.gestao', array_merge($dados, [
            'linhaSelecionada' => $linhaSelecionada,
            'veiculoSelecionado' => $veiculoSelecionado,
            'alunoSelecionado' => $alunoSelecionado,
            'vinculoSelecionado' => $vinculoSelecionado,
            'pontoSelecionado' => $pontoSelecionado,
        ]));
    }

    public function storeLinha(Request $request, TransporteEscolarAdminService $service): RedirectResponse
    {
        $dados = $request->validate([
            'id' => ['nullable', 'integer', 'min:1'],
            'codigo' => ['required', 'string', 'max:30'],
            'nome' => ['required', 'string', 'max:180'],
            'tipo_servico' => ['required', 'string', 'in:proprio,terceirizado,transporte_publico'],
            'horario_saida' => ['nullable', 'string', 'max:20'],
            'horario_retorno' => ['nullable', 'string', 'max:20'],
            'custo_mensal' => ['nullable', 'numeric', 'min:0'],
            'unidade_escolar' => ['nullable', 'string', 'max:180'],
            'rota_descricao' => ['nullable', 'string'],
            'ativo' => ['nullable', 'boolean'],
        ]);

        $dados['ativo'] = (bool) ($dados['ativo'] ?? false);
        $dados['custo_mensal'] = (float) ($dados['custo_mensal'] ?? 0);

        try {
            $linha = $service->salvarLinha($dados);
            return redirect()
                ->route('transportescolar.web.gestao', ['linha_id' => $linha->id])
                ->with('status', 'Linha salva com sucesso.');
        } catch (InvalidArgumentException $e) {
            return redirect()
                ->route('transportescolar.web.gestao')
                ->with('error', $e->getMessage());
        }
    }

    public function storeVeiculo(Request $request, TransporteEscolarAdminService $service): RedirectResponse
    {
        $dados = $request->validate([
            'id' => ['nullable', 'integer', 'min:1'],
            'placa' => ['required', 'string', 'max:20'],
            'modelo' => ['required', 'string', 'max:180'],
            'motorista_nome' => ['nullable', 'string', 'max:180'],
            'capacidade' => ['nullable', 'integer', 'min:1'],
            'situacao' => ['required', 'string', 'max:40'],
            'observacao' => ['nullable', 'string'],
        ]);

        try {
            $veiculo = $service->salvarVeiculo($dados);
            return redirect()
                ->route('transportescolar.web.gestao', ['veiculo_id' => $veiculo->id])
                ->with('status', 'Veiculo salvo com sucesso.');
        } catch (InvalidArgumentException $e) {
            return redirect()
                ->route('transportescolar.web.gestao')
                ->with('error', $e->getMessage());
        }
    }

    public function storeAluno(Request $request, TransporteEscolarAdminService $service): RedirectResponse
    {
        $dados = $request->validate([
            'id' => ['nullable', 'integer', 'min:1'],
            'linha_id' => ['nullable', 'integer', 'min:1'],
            'aluno_nome' => ['required', 'string', 'max:180'],
            'aluno_cpf' => ['nullable', 'string', 'max:20'],
            'escola_nome' => ['nullable', 'string', 'max:180'],
            'local_embarque' => ['nullable', 'string', 'max:180'],
            'motivo_uso' => ['nullable', 'string', 'max:180'],
            'periodo_uso' => ['nullable', 'string', 'max:80'],
            'situacao_matricula' => ['nullable', 'string', 'max:40'],
            'foto_aluno' => ['nullable', 'image', 'max:5120'],
            'utiliza_transporte' => ['nullable', 'boolean'],
        ]);

        $dados['utiliza_transporte'] = (bool) ($dados['utiliza_transporte'] ?? false);

        $arquivoFoto = $request->file('foto_aluno');
        if ($arquivoFoto !== null) {
            $dados['foto_path'] = $arquivoFoto->store('transporte-escolar/alunos', 'public');
        }

        try {
            $aluno = $service->salvarAluno($dados);
            return redirect()
                ->route('transportescolar.web.gestao', ['aluno_id' => $aluno->id])
                ->with('status', 'Aluno salvo com sucesso.');
        } catch (InvalidArgumentException $e) {
            return redirect()
                ->route('transportescolar.web.gestao')
                ->with('error', $e->getMessage());
        }
    }

    public function storeVinculo(Request $request, TransporteEscolarAdminService $service): RedirectResponse
    {
        $dados = $request->validate([
            'id' => ['nullable', 'integer', 'min:1'],
            'linha_id' => ['required', 'integer', 'min:1'],
            'veiculo_id' => ['required', 'integer', 'min:1'],
            'data_inicio' => ['nullable', 'date'],
            'data_fim' => ['nullable', 'date'],
            'observacao' => ['nullable', 'string'],
        ]);

        try {
            $vinculo = $service->salvarVinculo($dados);
            return redirect()
                ->route('transportescolar.web.gestao', ['vinculo_id' => $vinculo->id])
                ->with('status', 'Vinculo salvo com sucesso.');
        } catch (InvalidArgumentException $e) {
            return redirect()
                ->route('transportescolar.web.gestao')
                ->with('error', $e->getMessage());
        }
    }

    public function storePonto(Request $request, TransporteEscolarAdminService $service): RedirectResponse
    {
        $dados = $request->validate([
            'id' => ['nullable', 'integer', 'min:1'],
            'linha_id' => ['required', 'integer', 'min:1'],
            'nome' => ['required', 'string', 'max:180'],
            'endereco' => ['nullable', 'string', 'max:255'],
            'tipo_ponto' => ['required', 'string', 'max:40'],
            'ordem' => ['nullable', 'integer', 'min:0'],
            'observacao' => ['nullable', 'string'],
            'ativo' => ['nullable', 'boolean'],
        ]);

        $dados['ativo'] = (bool) ($dados['ativo'] ?? false);
        $dados['ordem'] = (int) ($dados['ordem'] ?? 0);

        try {
            $ponto = $service->salvarPonto($dados);
            return redirect()
                ->route('transportescolar.web.gestao', ['ponto_id' => $ponto->id])
                ->with('status', 'Ponto salvo com sucesso.');
        } catch (InvalidArgumentException $e) {
            return redirect()
                ->route('transportescolar.web.gestao')
                ->with('error', $e->getMessage());
        }
    }

    public function destroyLinha(int $id, TransporteEscolarAdminService $service): RedirectResponse
    {
        try {
            $service->removerLinha($id);
            return redirect()->route('transportescolar.web.gestao')->with('status', 'Linha removida.');
        } catch (InvalidArgumentException $e) {
            return redirect()->route('transportescolar.web.gestao')->with('error', $e->getMessage());
        }
    }

    public function destroyVeiculo(int $id, TransporteEscolarAdminService $service): RedirectResponse
    {
        try {
            $service->removerVeiculo($id);
            return redirect()->route('transportescolar.web.gestao')->with('status', 'Veiculo removido.');
        } catch (InvalidArgumentException $e) {
            return redirect()->route('transportescolar.web.gestao')->with('error', $e->getMessage());
        }
    }

    public function destroyAluno(int $id, TransporteEscolarAdminService $service): RedirectResponse
    {
        try {
            $service->removerAluno($id);
            return redirect()->route('transportescolar.web.gestao')->with('status', 'Aluno removido.');
        } catch (InvalidArgumentException $e) {
            return redirect()->route('transportescolar.web.gestao')->with('error', $e->getMessage());
        }
    }

    public function destroyVinculo(int $id, TransporteEscolarAdminService $service): RedirectResponse
    {
        try {
            $service->removerVinculo($id);
            return redirect()->route('transportescolar.web.gestao')->with('status', 'Vinculo removido.');
        } catch (InvalidArgumentException $e) {
            return redirect()->route('transportescolar.web.gestao')->with('error', $e->getMessage());
        }
    }

    public function destroyPonto(int $id, TransporteEscolarAdminService $service): RedirectResponse
    {
        try {
            $service->removerPonto($id);
            return redirect()->route('transportescolar.web.gestao')->with('status', 'Ponto removido.');
        } catch (InvalidArgumentException $e) {
            return redirect()->route('transportescolar.web.gestao')->with('error', $e->getMessage());
        }
    }

    public function carteiraAluno(int $id, TransporteEscolarCarteiraService $service): View
    {
        try {
            return view('educacao.transporte-escolar.carteira', $service->payload($id));
        } catch (ModelNotFoundException $e) {
            abort(404);
        }
    }

    public function carteiraAlunoPdf(int $id, TransporteEscolarCarteiraService $service)
    {
        try {
            $dados = $service->payload($id);
        } catch (ModelNotFoundException $e) {
            abort(404);
        }

        $html = view('educacao.transporte-escolar.carteira-pdf', $dados)->render();
        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'margin_left' => 8,
            'margin_right' => 8,
            'margin_top' => 8,
            'margin_bottom' => 8,
        ]);
        $mpdf->SetTitle('Carteira do Estudante - ' . $dados['aluno']->aluno_nome);
        $mpdf->WriteHTML($html);

        $conteudo = $mpdf->Output('', Destination::STRING_RETURN);
        $arquivo = 'carteira-transporte-escolar-' . $dados['aluno']->id . '.pdf';

        return response($conteudo, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $arquivo . '"',
        ]);
    }
}
