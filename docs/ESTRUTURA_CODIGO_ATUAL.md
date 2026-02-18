# Estrutura Atual do Código - e-Cidade

Data: 2026-02-18
Escopo: visão técnica da base atual, diretórios, módulos e organização por camadas.

## 1. Visão Geral da Arquitetura

O projeto está em arquitetura **híbrida**:
- Camada moderna em Laravel/PHP (`app/`, `src/`, `routes/`, `config/`, `database/`).
- Camada legada volumosa em PHP procedural/gerado (`classes/`, `model/`, `forms/`, `funcoes/`, `resources/legacy/`).
- Convivência entre rotas/fluxos modernos e legados no mesmo repositório.

Resumo de volume (aprox.):
- `app/`: 979 arquivos PHP
- `src/`: 557 arquivos PHP
- `classes/`: 6438 arquivos PHP
- `model/`: 1585 arquivos PHP
- `db/`: 1059 arquivos PHP
- `resources/`: 15710 arquivos PHP (inclui grande volume legado)

## 2. Estrutura Raiz (Diretórios Principais)

- `app/`: código moderno Laravel (controllers, services, repositories, models, console, tests).
- `src/`: domínio legado-modernizado em namespace `ECidade\...` por área de negócio.
- `classes/`: classes legadas de acesso a dados e regras antigas (muito acopladas ao banco).
- `model/`: modelos/serviços legados por módulo funcional.
- `routes/`: rotas modernas por módulos.
- `api/`: API v1 legada (Silex/estrutura antiga).
- `config/`: configurações Laravel e configs específicas do sistema.
- `database/`: migrations/factories/seeders da camada moderna.
- `db/`: scripts e migrations legadas/históricas.
- `resources/`: views/assets e grande acervo de código legado em `resources/legacy`.
- `legacy/` e `legacy_config/`: ponte e configuração de compatibilidade com o legado.
- `docs/`: documentação funcional, técnica e de sprint.
- `docker/`: ambiente local/infra de containers.

## 3. Estrutura da Camada Moderna (`app/`)

Principais subpastas:
- `app/Http`: controllers, middleware e requests.
- `app/Services`: regras de aplicação por domínio (financeiro, patrimonial, tributário etc.).
- `app/Repositories`: acesso a dados na camada moderna.
- `app/Models`: models Eloquent (incluindo tabelas legadas e modernas).
- `app/Console/Commands`: comandos operacionais e scripts de suporte.
- `app/Tests`: testes unitários (núcleo atual de cobertura automatizada).
- `app/Application`, `app/Domain`, `app/Support`: camadas auxiliares de organização.

## 4. Estrutura do Domínio em `src/`

Subdomínios principais:
- `src/Financeiro`: Contabilidade, Empenho, Tesouraria, EFD-Reinf.
- `src/Patrimonial`: Acordos, Licitação, Protocolo.
- `src/Tributario`: Água, Arrecadação, Dívida, Jurídico, Integrações.
- `src/RecursosHumanos`: RH, Pessoal, eSocial.
- `src/Educacao`, `src/Saude`, `src/PortalTransparencia`.
- `src/V3` e `src/Core`: infraestrutura técnica e componentes de plataforma.

## 5. Estrutura Legada Relevante

- `classes/`: principal núcleo legado, com milhares de `db_*_classe.php` e regras históricas.
- `model/`: módulos antigos por área de negócio, ainda usados por fluxos críticos.
- `forms/` e `funcoes/`: formulários, scripts e funções utilitárias legadas.
- `resources/legacy/`: telas e componentes antigos integrados ao fluxo atual.

Observação:
- Alterações nessas áreas exigem cuidado com impacto transversal e regressão funcional.

## 6. Módulos Funcionais do Sistema

O sistema possui catálogo de módulos em:
- `docs/MODULOS_IMPLEMENTADOS.md`

Categorias principais já documentadas:
- Financeiro e Contabilidade
- Compras, Licitações e Contratos
- Recursos Humanos e Pessoal
- Saúde Pública
- Educação
- Tributação e Arrecadação
- Patrimônio e Infraestrutura
- Atendimento e Serviços Online
- Configuração e Administração
- Outros módulos transversais

## 7. Fluxo de Entrada e Roteamento

- Entrada principal HTTP: `FrontController.php` (ponte modern/legacy).
- Rotas modernas: `routes/` + controllers em `app/Http/Controllers`.
- APIs:
  - `api/v1`: legado.
  - rotas modernas de API: `routes/` e `app/Http`.

## 8. Banco e Persistência

- Modo híbrido de persistência:
  - Eloquent/Repositories modernos.
  - SQL direto e classes geradas legadas (`classes/`, `db/`, `model/`).
- Evolução de esquema:
  - `database/migrations` (moderno).
  - `db/migrations` e versões históricas (`db/v2.*`) no legado.

## 9. Testes e Qualidade (Estado Atual)

- Testes automatizados concentrados em `app/Tests/Unit`.
- Cobertura ainda parcial para o tamanho total da base.
- Coexistência de padrões modernos e legados exige estratégia incremental de validação.

## 10. Onde Evoluir Sem Quebrar

Para novas entregas/sprints, priorizar:
1. `app/Services`, `app/Repositories`, `app/Http`, `src/*` (camada moderna).
2. Integração gradual com legado via guardas, comandos e validações.
3. Alterações no legado (`classes/`, `model/`) somente em pontos centrais e com evidência de teste.

## 11. Referências Internas

- `docs/MODULOS_IMPLEMENTADOS.md`
- `docs/PLANO_MODERNIZACAO_ECIDADE_2026.md`
- `docs/sprint7_checklist_execucao.md`
- `docs/MODERN_LEGACY_ROUTING.md`

## 12. Matriz de Módulos x Diretórios (Estado Atual)

Legenda de status arquitetural:
- `Moderno`: predominância em `app/` e `src/`.
- `Híbrido`: coexistência relevante de moderno e legado.
- `Legado`: predominância em `classes/`, `model/`, `forms/`, `resources/legacy`.

| Módulo | Diretórios principais | Arquivos de referência (exemplos) | Status |
|---|---|---|---|
| Contabilidade Pública | `app/Services/Financeiro/Contabilidade`, `app/Repositories/Financeiro/Contabilidade`, `src/Financeiro/Contabilidade`, `classes/`, `model/contabilidade` | `app/Repositories/Financeiro/Contabilidade/ContaPlanoRepository.php`, `app/Repositories/Contabilidade/OrcdotacaoRepository.php` | Híbrido |
| Orçamento (PPA/LDO/LOA) e Execução Orçamentária | `app/Services/Financeiro/ExecucaoOrcamentaria`, `app/Repositories/Financeiro/ExecucaoOrcamentaria`, `app/Services/Orcamento`, `app/Services/OrcParametro`, `classes/`, `model/` | `app/Services/Financeiro/ExecucaoOrcamentaria/CicloDespesaService.php`, `app/Repositories/Financeiro/ExecucaoOrcamentaria/CicloDespesaRepository.php`, `config/execucao_orcamentaria.php` | Híbrido |
| Tesouraria e Fluxo de Caixa | `app/Services/Financeiro/Tesouraria`, `app/Repositories/Financeiro/Tesouraria`, `src/Financeiro/Tesouraria`, `classes/`, `model/caixa` | `app/Repositories/Financeiro/Tesouraria/ContaBancariaRepository.php`, `app/Repositories/Financeiro/Tesouraria/SaltesRepository.php` | Híbrido |
| Compras e Licitações | `routes/modules/patrimonial/compras`, `routes/modules/patrimonial/licitacoes`, `app/Services/Patrimonial/Licitacao`, `app/Repositories/Patrimonial/Licitacao`, `src/Patrimonial/Licitacao`, `classes/` | `src/Patrimonial/Licitacao/PNCP/BasePNCP.php`, `app/Services/ParecerLicitacao/UpdateParecerLicitacaoService.php` | Híbrido |
| Contratos Administrativos | `routes/modules/patrimonial/contratos`, `app/Services/Patrimonial/Aditamento`, `app/Repositories/Patrimonial`, `src/Patrimonial/Licitacao/Licitacon`, `classes/` | `src/Patrimonial/Licitacao/Licitacon/Regra/Emissao/Contrato.php` | Híbrido |
| Almoxarifado e Materiais | `routes/modules/patrimonial/material`, `app/Repositories/Patrimonial/Materiais`, `app/Services/Patrimonial/compras`, `classes/`, `model/` | `routes/modules/patrimonial/material/material.php`, `classes/requisicaoMaterial.model.php` | Legado/Híbrido |
| Patrimônio e Bens | `routes/modules/patrimonial/patrimonio`, `app/Models/Patrimonial`, `app/Services/Patrimonial`, `src/Patrimonial`, `classes/` | `classes/db_bens_classe.php`, `app/Services/Patrimonial/Veiculo/EmpVeiculosService.php` | Híbrido |
| Frota (Veículos) | `routes/modules/patrimonial/veiculos`, `app/ViewModel/Veiculo`, `app/Services/Patrimonial/Veiculo`, `classes/` | `routes/modules/patrimonial/veiculos/procedimentos`, `app/ViewModel/Veiculo/Abastecimento` | Híbrido |
| Obras | `routes/modules/patrimonial/obras`, `classes/`, `model/` | `classes/db_licobras302025_classe.php`, `classes/db_licobrasmedicao_classe.php` | Legado |
| Protocolo e Processo Digital | `routes/modules/patrimonial/protocolo`, `app/Repositories/Patrimonial/Protocolo`, `src/Patrimonial/Protocolo`, `model/protocolo`, `classes/` | `src/Patrimonial/Protocolo/Repositorio/ProcessoRepositorio.php`, `model/processoProtocolo.model.php` | Híbrido |
| Tributação Municipal (IPTU/Arrecadação/Dívida) | `src/Tributario/*`, `app/Services/Tributario/*`, `app/Repositories/Tributario/*`, `model/`, `classes/` | `src/Tributario/Cadastro/Iptu/Recadastramento/Processamento.php`, `src/Tributario/Arrecadacao/EmissaoGeral/EmissaoGeral.php` | Híbrido |
| ISSQN / NFS-e / Tributos Web | `app/Services/Tributario/ISSQN`, `app/Repositories/Tributario/ISSQN`, `model/issqn`, `classes/` | `model/issqn/NotaFiscalISSQN.model.php`, `model/issqn/webservice/CancelamentoISSQNVariavelWebService.model.php` | Híbrido |
| ITBI Online | `app/Services/Tributario/Itbi`, `model/itbi`, `classes/` | `model/itbi/Itbi.model.php`, `model/itbi/ArrecadItbi.model.php` | Híbrido |
| Alvará Online | `model/issqn/alvara`, `model/issqn`, `classes/` | `model/issqn/alvara/Alvara.model.php`, `model/issqn/AlvaraMovimentacao.model.php` | Legado/Híbrido |
| Recursos Humanos, Folha, eSocial | `src/RecursosHumanos/*`, `model/pessoal`, `model/esocial`, `app/Services`, `classes/` | `model/pessoal/CalculoFolha13o.model.php`, `model/esocial/FilaESocialTask.model.php` | Híbrido |
| Ponto Eletrônico e Portal do Servidor | `classes/` (núcleo ponto), `model/pontoFolha.model.php`, `model/recursosHumanos` | `classes/db_pontoeletronicoconfiguracoesgerais_classe.php`, `classes/db_regraponto_classe.php` | Legado |
| Saúde Municipal / UPA / Hospitalar | `src/Saude/Agendamento`, `classes/` (prefixo `db_sau_` e `db_vac_`), `model/` | `classes/db_sau_parametrosagendamento_classe.php`, `classes/db_movimentacaoprontuario_classe.php` | Legado/Híbrido |
| Educação | `src/Educacao/*`, `model/educacao/*`, `classes/` | `model/educacao/Matricula.model.php`, `classes/db_diarioclasse_classe.php` | Híbrido |
| Portal da Transparência / Diário / Publicidade | `src/PortalTransparencia`, `model/integracao/transparencia`, `classes/`, `routes/modules/patrimonial/ouvidoria` | `model/integracao/transparencia/IntegracaoPortalTransparencia.model.php`, `src/PortalTransparencia/Licitacao/Documentos/Outros.php` | Híbrido |
| Configuração, Workflow, Formulários | `src/Configuracao/*`, `routes/modules/configuracao/*`, `app/Application/Configuracao`, `classes/` | `src/Configuracao/Workflow/Workflow.php`, `src/Configuracao/Formulario/Model/Formulario.php` | Híbrido |

## 13. Observações Práticas para a Sprint

1. Entradas mais seguras para evolução: `app/Services/*`, `app/Repositories/*`, `src/*` e novos testes em `app/Tests/Unit`.
2. Alterações em `classes/` e `model/` devem ser acompanhadas por teste de regressão de fluxo funcional.
3. Rotas em `routes/modules/patrimonial/*` ainda concentram operação crítica legada de compras, contratos, patrimônio, protocolo e obras.
4. Para cada módulo novo/refatorado, registrar no mínimo: caminho da regra (`app/Services` ou `src`), caminho de persistência (`app/Repositories` ou legado) e evidência de teste (`app/Tests/Unit/...`).
