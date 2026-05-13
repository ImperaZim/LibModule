# EasyModuleEconomyProviderExample

Plugin de exemplo que fornece um service de economia fake em memoria.

## Modulo

```txt
examples:economy
```

## Service fornecido

```txt
examples:economy-service
```

Contract/interface:

```php
easylibraryexamples\economy\modules\economy\api\EconomyApi
```

## Capability fornecida

```txt
economy
```

## Comandos

```txt
/exampleeco balance
/exampleeco give <player> <amount>
/exampleeco take <player> <amount>
```

## Testes

```txt
/easymodule info examples:economy
/easymodule services
/easymodule capabilities
/easymodule health examples:economy
```
