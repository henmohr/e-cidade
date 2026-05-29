# Matriz de Reuso - i-Educar Transport Package x e-Cidade

Documento de referência para acelerar o escopo funcional do módulo de Transporte Escolar do e-Cidade usando o pacote `i-educar-transport-package` como benchmark de domínio.

Fontes de referência:
- [README do pacote](https://github.com/portabilis/i-educar-transport-package)
- [Releases do i-Educar](https://github.com/portabilis/i-educar/releases)

## Premissa

O pacote do Portabilis deve ser tratado como referência funcional e de modelagem, não como substituição direta da implementação atual do e-Cidade.

O que mais interessa para reuso rápido:
- cadastros centrais de transporte escolar;
- modelagem de linhas, rotas, pontos, veículos e motoristas;
- relações entre estudante, escola e transporte;
- relatórios operacionais.

## Matriz A1-A8

| Requisito | O que o pacote ajuda a acelerar | O que já existe no e-Cidade | Próximo passo recomendado |
|---|---|---|---|
| A1 Linhas, rotas, horários, veículos e custos | Estrutura de domínio de transporte escolar com foco em itinerário, rota, ponto, veículo e motorista | Painel, cadastro, persistência e filtros em [TransporteEscolarExportService](/home/mohr/git/e-cidade/app/Services/Educacao/TransporteEscolar/TransporteEscolarExportService.php) e [TransporteEscolarWebController](/home/mohr/git/e-cidade/app/Http/Controllers/Educacao/TransporteEscolarWebController.php) | Aproximar a modelagem de rotas/itinerários do vocabulário do pacote e fechar custos por linha |
| A2 Tipos de serviço: próprio, terceirizado, transporte público coletivo | Ajuda a validar a divisão de frota e operação do domínio | Campo `tipo_servico` já existe em [LinhaTransporteEscolar](/home/mohr/git/e-cidade/app/Models/Educacao/TransporteEscolar/LinhaTransporteEscolar.php) | Padronizar os valores e aplicar regra por tipo na gestão |
| A3 Movimentação de veículos; integração com frota | Base funcional para veículo, motorista e relacionamento com rota | Vínculos linha-veículo e CRUD em [TransporteEscolarSeteService](/home/mohr/git/e-cidade/app/Services/Educacao/TransporteEscolar/TransporteEscolarSeteService.php) | Integrar com o módulo de frota legado e expor movimentação operacional |
| A4 Relatórios diversos | Referência de relatórios de transporte e visão operacional | A7 consolidado em [TransporteEscolarRelatorioService](/home/mohr/git/e-cidade/app/Services/Educacao/TransporteEscolar/TransporteEscolarRelatorioService.php) | Reaproveitar a estrutura de relatórios para novos recortes e layouts |
| A5 Uso por estudantes e escolas | Estrutura de vínculo estudante-transporte-escola | Alunos, escola e filtros já cobertos em [TransporteEscolarExportService](/home/mohr/git/e-cidade/app/Services/Educacao/TransporteEscolar/TransporteEscolarExportService.php) | Melhorar a consistência entre cadastro do aluno, linha e unidade escolar |
| A6 Carteira de estudante | Pouco ou nada ajuda diretamente | Carteira web/PDF e foto já existem em [TransporteEscolarAdminController](/home/mohr/git/e-cidade/app/Http/Controllers/Educacao/TransporteEscolarAdminController.php) | Refinar layout e validação pública do QR code, se necessário |
| A7 Relatórios legais | Ajuda como benchmark de relatórios do módulo | Relatórios legais já implementados em [TransporteEscolarRelatorioController](/home/mohr/git/e-cidade/app/Http/Controllers/Educacao/TransporteEscolarRelatorioController.php) | Fechar eventuais relatórios obrigatórios adicionais do termo |
| A8 Importação/exportação SETE | Não há evidência clara de suporte nativo ao SETE | Fluxo SETE já implementado em [TransporteEscolarSeteService](/home/mohr/git/e-cidade/app/Services/Educacao/TransporteEscolar/TransporteEscolarSeteService.php) e documentado em [docs/transporte_escolar_sete.md](/home/mohr/git/e-cidade/docs/transporte_escolar_sete.md) | Ajustar apenas se o município fornecer um layout oficial |

## Foco de reuso

Se a prioridade for acelerar entrega funcional, o pacote deve guiar primeiro:

1. cadastros de A1 e A3;
2. relacionamento escola-aluno-transporte de A5;
3. relatórios operacionais de A4 e A7.

## O que não vale forçar

- Copiar o pacote como extensão pronta para o e-Cidade.
- Recriar a arquitetura do i-Educar dentro do legado.
- Postergar a integração já pronta que existe no módulo atual só para aderir ao pacote.

## Decisão prática

Use o pacote como referência para convergir vocabulário, entidades e relatórios. Mantenha a implementação atual do e-Cidade como base executável.
