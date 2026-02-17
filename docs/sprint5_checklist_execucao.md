# Sprint 5 - Checklist Executavel (Acessibilidade e Operacao Web)

Base:
- `docs/plano_execucao_sprints_poc.md`
- `docs/matriz_gap_poc_100.md`

Objetivo da sprint:
- implementar pacote minimo de acessibilidade exigido para PoC.

Escopo tecnico principal:
- modo de alto contraste;
- ajuste de tamanho de fonte (zoom de leitura);
- filtros de daltonismo;
- persistencia de preferencia no navegador.

## Arquivos/Componentes-Chave

- `public/assets/css/accessibility.css`
- `public/assets/js/accessibility.js`
- `resources/views/layouts/accessibility-toolbar.blade.php`
- `resources/views/layouts/app.blade.php`
- `resources/views/auth/mfa-challenge.blade.php`
- `resources/views/auth/sessions.blade.php`
- `resources/views/backup/index.blade.php`

## Checklist (S5-xx)

## Dia 1-3: Recursos de Acessibilidade

- [x] `S5-01` Implementar toolbar de acessibilidade com contraste, fonte e filtros.
- [x] `S5-02` Persistir preferências do usuário (localStorage).

## Dia 3-6: Cobertura em Telas Web Modernas

- [x] `S5-03` Aplicar recursos no layout principal web.
- [x] `S5-04` Aplicar recursos em telas standalone de autenticação/backup.

## Dia 6-8: Evidências

- [x] `S5-05` Publicar runbook de validação de acessibilidade.
- [x] `S5-06` Atualizar matriz de gaps com status pós-sprint.

## Comandos de Verificação

```bash
php -l routes/web.php
```

## Critério de Pronto da Sprint 5 (incremental)

- contraste alto funcional;
- ajuste de fonte funcional;
- filtros de daltonismo funcionais;
- evidência reproduzível em runbook.
