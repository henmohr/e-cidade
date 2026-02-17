# Runbook PoC - Acessibilidade (Zoom, Contraste e Daltonismo)

Objetivo:
- demonstrar recursos minimos de acessibilidade exigidos para a PoC.

## 1. Recursos Disponiveis

Toolbar fixa de acessibilidade com:
1. alternancia de contraste normal/alto;
2. ajuste de fonte (A- e A+);
3. filtros de daltonismo:
   - protanopia
   - deuteranopia
   - tritanopia
4. restauracao para padrao.

## 2. Telas Cobertas

1. telas web com `layouts.app`;
2. `auth/mfa-challenge`;
3. `auth/sessions`;
4. `backup/index`.

## 3. Passos de Validacao

1. Abrir tela web protegida.
2. Ativar contraste alto e confirmar mudanca visual.
3. Aumentar e reduzir fonte, confirmando leitura ampliada.
4. Selecionar cada filtro de daltonismo e validar alteracao visual.
5. Recarregar pagina e confirmar persistencia das preferencias.
6. Usar `Restaurar` e confirmar retorno ao padrao.

## 4. Evidencias para PoC

1. Captura em modo normal e contraste alto.
2. Captura com fonte ampliada.
3. Captura com cada filtro de daltonismo.
4. Captura apos reload mantendo preferencia.

## 5. Limites Conhecidos

- pacote atual cobre camada web moderna e telas novas da PoC;
- extensao para todo legado PHP deve ser feita por fases.
