# EasyLibrary Module Examples

Esta pasta e **temporaria** e foi criada para servir como laboratorio do sistema de modules da EasyLibrary.
Ela contem plugins completos de exemplo, com `plugin.yml`, `module.yml`, classes PHP, commands, listeners, tasks, services, capabilities, health checks e exemplos de dependencia entre plugins diferentes.

> Estes exemplos nao precisam ir para release final da EasyLibrary. Eles existem para testar, documentar e validar o sistema de modules enquanto ele evolui.

## Como usar

1. Copie um dos plugins de exemplo desta pasta para a pasta `plugins/` do seu servidor PocketMine-MP.
2. Garanta que a EasyLibrary esteja instalada e habilitada.
3. Inicie o servidor.
4. Use os comandos de diagnostico:

```txt
/easymodule list
/easymodule doctor
/easymodule services
/easymodule capabilities
/easymodule graph
```

## Ordem recomendada para testar

Copie primeiro:

```txt
01-basic-plugin
```

Depois teste conexao entre plugins copiando juntos:

```txt
02-economy-provider-plugin
03-farm-consumer-plugin
```

Por fim, copie o plugin de diagnostico apenas quando quiser testar falhas controladas:

```txt
04-diagnostics-plugin
```

## Plugins inclusos

### 01-basic-plugin

Mostra o basico de um modulo:

- `plugin.yml` declarando `easylibrary-modules`;
- `module.yml` completo;
- `BaseModule`;
- `ModuleContext`;
- logger por modulo;
- pasta/config propria do modulo;
- command registrado pelo lifecycle;
- listener registrado pelo lifecycle;
- repeating task registrado pelo lifecycle;
- cleanup callback;
- service simples;
- API publica com `getApi()`;
- health check.

Comando de teste:

```txt
/examplehello
/examplehello api
/examplehello service
/examplehello config
```

### 02-economy-provider-plugin

Simula um plugin que fornece uma economia simples para outros modulos.

Ele demonstra:

- service registry;
- service contract/interface;
- API publica por modulo;
- capability `economy`;
- provider de service `examples:economy-service`;
- command para manipular saldo fake em memoria.

Comando de teste:

```txt
/exampleeco balance
/exampleeco give <player> <amount>
/exampleeco take <player> <amount>
```

### 03-farm-consumer-plugin

Simula um plugin de farm que depende do provider de economia.

Ele demonstra:

- dependencia obrigatoria por service;
- dependencia obrigatoria por capability;
- consumo de service de outro plugin;
- uso de interface publica do provider;
- dependencia opcional com o modulo hello;
- command que recompensa o player usando a economia.

Comando de teste:

```txt
/examplefarm
/examplefarm harvest
/examplefarm provider
```

Para funcionar completamente, este plugin deve ser testado junto com:

```txt
02-economy-provider-plugin
```

### 04-diagnostics-plugin

Plugin feito para testar `/easymodule doctor`, `/easymodule why`, `/easymodule failures` e estados de falha.

Ele contem:

- um modulo OK;
- um modulo que depende de modulo inexistente;
- um modulo que depende de service inexistente;
- um modulo desativado no proprio `module.yml`.

Use quando quiser verificar se o sistema continua carregando os modulos bons mesmo quando outros falham.

## Padrao de `plugin.yml`

Exemplo:

```yaml
name: EasyModuleBasicExample
main: easylibraryexamples\basic\BasicExamplePlugin
version: 1.0.0
api: 5.0.0
src-namespace-prefix: easylibraryexamples\basic
depend:
  - EasyLibrary

easylibrary-modules:
  - path: modules
    namespace: easylibraryexamples\basic\modules
```

O campo importante e:

```yaml
easylibrary-modules:
  - path: modules
    namespace: vendor\plugin\modules
```

`path` e a pasta onde ficam os `module.yml`.
`namespace` e o prefixo usado para resolver classes dos modulos quando o `module.yml` usa loader curto.

## Padrao de `module.yml`

Exemplo completo:

```yaml
id: examples:hello
name: Hello Module
version: 1.0.0
loader: HelloModule
namespace: easylibraryexamples\basic\modules\hello
load: POSTWORLD
enabled: true

provides:
  services:
    - examples:hello-service
  capabilities:
    - examples.hello

requires:
  services: []
  capabilities: []

dependencies: []
soft-dependencies: []
plugin-dependencies: []
```

## Checklist de teste

Depois de copiar os exemplos para o servidor:

```txt
/easymodule list
/easymodule info examples:hello
/easymodule resources examples:hello
/easymodule health examples:hello
/easymodule services
/easymodule capabilities
/easymodule doctor
/easymodule graph
/easymodule reload examples:hello
/easymodule disable-runtime examples:hello
/easymodule enable examples:hello
```

Para testar persistencia:

```txt
/easymodule disable examples:hello
```

Reinicie o servidor e confira se continua desativado.
Depois reative:

```txt
/easymodule enable examples:hello
```

## Observacoes importantes

- IDs com prefixo evitam colisao: prefira `vendor:nome`.
- Services devem ter interface/contract quando forem usados por outros plugins.
- Capabilities sao boas quando voce quer depender de uma funcao, nao de um modulo especifico.
- `reload` nao deve ser tratado como hot reload real de codigo PHP. Ele serve para reiniciar instancia/config/lifecycle do modulo, nao para descarregar classes PHP ja carregadas.
- Estes exemplos sao propositalmente verbosos para facilitar estudo e debug.
