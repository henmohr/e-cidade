# Runbook PoC - Sessoes Ativas e Auditoria

Objetivo:
- demonstrar controle de sessoes ativas e encerramento remoto.

## 1. Rotas

- `GET /web/sessions`
- `POST /web/sessions/revoke`

## 2. Fluxo de Demonstracao

1. Autenticar com usuario valido em dois navegadores/dispositivos.
2. Abrir `GET /web/sessions` na sessao principal.
3. Confirmar exibicao das duas sessoes (id, inicio, ultimo acesso, IP, user-agent).
4. Encerrar a sessao secundaria com o botao `Encerrar`.
5. Na sessao secundaria, tentar navegar em rota protegida.

Resultado esperado:
- sessao secundaria perde acesso (401) apos revogacao;
- sessao principal permanece ativa.

## 3. Evidencias para PoC

1. Captura da tela de sessoes ativas.
2. Captura da acao de encerramento remoto.
3. Captura da sessao revogada bloqueada.
4. Trecho de log com evento:
   - `User session revoked`

## 4. Limites Conhecidos

- controle atual focado no escopo web moderno;
- expansao para trilhas legadas especificas pode exigir ajustes adicionais.
