# Sprint 1 - Checklist Executável (Segurança de Acesso)

Base:
- `docs/plano_execucao_sprints_poc.md`
- `docs/matriz_gap_poc_100.md`

Objetivo da sprint:
- fechar os requisitos críticos de autenticação (`1.3.14`, `1.3.15.d`) sem trocar stack.

Escopo técnico principal:
- identidade unificada por CPF;
- MFA obrigatório por perfil;
- trilha de autenticação e sessões;
- endurecimento mínimo de login e recuperação de senha.

## Arquivos/Componentes-Chave

- `config/auth.php`
- `app/Providers/Auth/LegacyUserProvider.php`
- `app/Models/User.php`
- `app/Http/Middleware/AuthEcidadeUser.php`
- `app/Http/Kernel.php`
- `app/Http/Controllers/Auth/LoginController.php`
- `app/Http/Controllers/Auth/ForgotPasswordController.php`
- `app/Http/Controllers/Auth/ResetPasswordController.php`
- `routes/web.php`
- `routes/api.php`

## Checklist (S1-xx)

## Dia 1-2: Arquitetura e Contratos

- [ ] `S1-01` Definir desenho de identidade unificada (CPF como chave principal de autenticação).
  Entregável: documento curto em `docs/` com fluxo de login e mapeamento login->CPF.
  Aceite: fluxo aprovado para implementação sem ambiguidade.

- [ ] `S1-02` Definir estratégia de MFA por perfil (obrigatório para perfis críticos).
  Entregável: matriz de política MFA por perfil.
  Aceite: regras explícitas de quando bloquear acesso sem 2º fator.

- [ ] `S1-03` Definir estratégia de integração externa (Google/GOVBR/A1/A3) em fases.
  Entregável: plano faseado (MVP PoC + expansão).
  Aceite: fase 1 executável dentro da sprint.

## Dia 2-4: Modelo e Provider

- [x] `S1-04` Ajustar modelo de usuário para suportar campos de CPF e estado MFA.
  Arquivo alvo: `app/Models/User.php`.
  Aceite: atributo de CPF disponível para autenticação e validações básicas aplicadas.

- [x] `S1-05` Evoluir provider legado para resolver usuário também por CPF.
  Arquivo alvo: `app/Providers/Auth/LegacyUserProvider.php`.
  Aceite: autenticação funciona por login e por CPF sem regressão de hash legado.

- [x] `S1-06` Garantir rehash seguro de senha legado e bloquear formatos inválidos.
  Arquivo alvo: `app/Providers/Auth/LegacyUserProvider.php`.
  Aceite: autenticação com senha antiga migra para hash atual com sucesso.

## Dia 4-6: Fluxo de Login + MFA

- [x] `S1-07` Implementar etapa MFA no fluxo de login.
  Arquivo alvo: `app/Http/Controllers/Auth/LoginController.php`.
  Aceite: login de perfil com MFA só conclui após 2º fator válido.

- [x] `S1-08` Implementar middleware de enforcement de MFA em rotas protegidas.
  Arquivos alvo: `app/Http/Kernel.php`, `routes/web.php`, `routes/api.php`.
  Aceite: acesso bloqueado em rota protegida quando MFA pendente.

- [x] `S1-09` Registrar eventos de autenticação (sucesso/falha) para auditoria.
  Arquivos alvo: `app/Http/Controllers/Auth/LoginController.php`, `app/Http/Middleware/AuthEcidadeUser.php`.
  Aceite: logs com usuário, timestamp, IP e resultado.

## Dia 6-7: Recuperação de Senha e Hardening

- [x] `S1-10` Revisar fluxo de recuperação de senha para aderência mínima.
  Arquivos alvo: `app/Http/Controllers/Auth/ForgotPasswordController.php`, `app/Http/Controllers/Auth/ResetPasswordController.php`.
  Aceite: fluxo funcionando e sem exposição de informação sensível.

- [x] `S1-11` Definir política de senha e bloqueio progressivo em tentativas inválidas.
  Arquivos alvo: `config/auth.php`, `app/Http/Controllers/Auth/LoginController.php`.
  Aceite: limite de tentativas ativo e validações reforçadas.

## Dia 7-8: Sessões e Segurança Operacional

- [x] `S1-12` Expor informação de sessão ativa para usuário (mínimo para PoC).
  Arquivos alvo: middleware/controlador a definir no módulo web.
  Aceite: usuário visualiza sessão atual e consegue invalidar sessão.

- [x] `S1-13` Alertar usuário sobre falhas recentes de autenticação.
  Arquivo alvo: fluxo de login/notificação.
  Aceite: alerta exibido após login quando houver falhas anteriores.

## Dia 8-10: Testes, Evidências e Fechamento

- [x] `S1-14` Criar/ajustar testes dos cenários críticos de autenticação.
  Escopo: login por CPF, MFA obrigatório, bloqueio por tentativa, rehash legado.
  Aceite: casos críticos cobertos e executáveis no ambiente do projeto.

- [x] `S1-15` Preparar roteiro de demonstração PoC do tema autenticação.
  Entregável: passo a passo reproduzível com evidências (logs/telas).
  Aceite: execução ponta a ponta sem intervenção manual fora do roteiro.

- [x] `S1-16` Atualizar matriz de gaps após entrega da sprint.
  Arquivo alvo: `docs/matriz_gap_poc_100.md`.
  Aceite: status atualizado para cada requisito impactado.

## Comandos de Verificação (quando aplicável)

```bash
php -l app/Models/User.php
php -l app/Providers/Auth/LegacyUserProvider.php
php -l app/Http/Controllers/Auth/LoginController.php
php -l app/Http/Middleware/AuthEcidadeUser.php
php -l app/Http/Kernel.php
composer test
```

Obs.: no estado atual do projeto, a suíte de testes ainda tem pendências de bootstrap; registrar evidência do que passou e do que bloqueou.

## Critério de Pronto da Sprint 1

- requisitos `1.3.14` e `1.3.15.d` com implementação funcional mínima para PoC;
- trilha de autenticação demonstrável (sucesso/falha + IP + horário);
- bloqueios e políticas de senha ativos;
- documentação e roteiro de demonstração atualizados em `docs/`.
