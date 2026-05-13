# EasyModuleBasicExample

Plugin minimo para testar um modulo externo da EasyLibrary.

## O que ele testa

- descoberta por `easylibrary-modules`;
- `module.yml` externo;
- `BaseModule`;
- `ModuleContext`;
- logger por modulo;
- config propria do modulo;
- command/listener/task registrados pelo lifecycle;
- cleanup automatico;
- service registry;
- API publica por modulo;
- health check.

## Comandos

```txt
/examplehello
/examplehello api
/examplehello service
/examplehello config
```

## Diagnostico recomendado

```txt
/easymodule info examples:hello
/easymodule resources examples:hello
/easymodule health examples:hello
/easymodule services
/easymodule capabilities
/easymodule reload examples:hello
```
