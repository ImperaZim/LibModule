# EasyModuleDiagnosticsExample

Plugin feito para testar falhas controladas do sistema de modules.

Copie este plugin apenas quando quiser testar diagnostico. Ele foi feito para aparecer com alguns modulos em estado de erro/espera.

## Modulos

```txt
examples:diagnostic-ok
examples:diagnostic-missing-module
examples:diagnostic-missing-service
examples:diagnostic-disabled
```

## O que cada modulo faz

- `examples:diagnostic-ok`: modulo funcional, registra `/examplediag`.
- `examples:diagnostic-missing-module`: depende de `examples:not-installed`.
- `examples:diagnostic-missing-service`: depende de `examples:not-installed-service`.
- `examples:diagnostic-disabled`: esta com `enabled: false` no `module.yml`.

## Comandos de diagnostico

```txt
/easymodule failures
/easymodule doctor
/easymodule why examples:diagnostic-missing-module
/easymodule why examples:diagnostic-missing-service
/easymodule why examples:diagnostic-disabled
/easymodule resources examples:diagnostic-ok
/examplediag
```

O comportamento esperado e: os modulos problematicos aparecem no diagnostico, mas o modulo OK continua funcionando.
