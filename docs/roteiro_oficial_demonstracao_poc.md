# Roteiro Oficial de Demonstracao - PoC Licitacao

Objetivo:
- conduzir demonstracao padronizada, objetiva e auditavel para decisao de atendimento dos requisitos obrigatorios.

## 1. Dados gerais da sessao

- Data: ____/____/____
- Inicio: ____:____
- Fim: ____:____
- Ambiente: [ ] Homologacao [ ] Producao controlada
- Responsavel tecnico: ______________________
- Avaliador funcional (contratante): ______________________

## 2. Sequencia de demonstracao (ordem recomendada)

1. Acesso e seguranca
- login por CPF;
- MFA em perfil obrigatorio;
- evidencia de evento de autenticacao.

2. Auditoria e sessoes
- listagem de sessoes ativas;
- encerramento remoto de sessao;
- confirmacao de bloqueio da sessao revogada.

3. Backup e restore
- evidencia de backup gerado com checksum;
- politica de retencao configurada;
- simulacao de restore controlado;
- (quando disponivel) prova de protecao por A3 para download.

4. Observabilidade e SLA
- consulta em `/api/health/live`;
- consulta em `/api/health/ready`;
- evidencia de coleta (`ops:health-snapshot`);
- evidencia de relatorio (`ops:sla-report`).

5. Acessibilidade
- contraste alto;
- ajuste de fonte;
- filtros de daltonismo;
- validacao de persistencia de preferencia.

6. Modulos funcionais prioritarios da licitacao
- contabilidade publica;
- orcamentario (PPA/LDO/LOA);
- tesouraria;
- tributacao/tributos web/issqn;
- compras e licitacoes;
- RH e portal do servidor;
- demais sistemas priorizados no TR.

## 3. Matriz de execucao por bloco

| Bloco | Passou | Falhou | Evidencia | Observacao |
|---|---|---|---|---|
| Acesso e seguranca | [ ] | [ ] |  |  |
| Auditoria e sessoes | [ ] | [ ] |  |  |
| Backup e restore | [ ] | [ ] |  |  |
| Observabilidade e SLA | [ ] | [ ] |  |  |
| Acessibilidade | [ ] | [ ] |  |  |
| Modulos funcionais | [ ] | [ ] |  |  |

## 4. Criterio de aceite da sessao

Considerar sessao aprovada quando:
1. todos os blocos obrigatorios estiverem com status `Passou`;
2. cada bloco tiver pelo menos uma evidencia objetiva;
3. avaliador funcional registrar aceite formal.

## 5. Assinaturas

- Responsavel tecnico: __________________________________
- Avaliador funcional: __________________________________
- Data do aceite: ____/____/____
