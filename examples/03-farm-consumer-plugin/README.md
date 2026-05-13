# EasyModuleFarmConsumerExample

Plugin de exemplo que consome um service/capability fornecido por outro plugin.

Ele foi feito para ser usado junto com:

```txt
02-economy-provider-plugin
```

## Modulo

```txt
examples:farm
```

## Dependencias

O modulo exige:

```txt
service: examples:economy-service
capability: economy
plugin: EasyModuleEconomyProviderExample
```

E tenta usar opcionalmente:

```txt
module: examples:hello
```

## Comandos

```txt
/examplefarm
/examplefarm harvest
/examplefarm provider
```

## Testes

Com provider instalado:

```txt
/easymodule why examples:farm
/easymodule services
/easymodule capabilities
/examplefarm harvest
/exampleeco balance
```

Sem provider instalado:

```txt
/easymodule why examples:farm
/easymodule failures
/easymodule doctor
```

O esperado e o modulo ficar aguardando dependencia, sem derrubar os outros modulos.
