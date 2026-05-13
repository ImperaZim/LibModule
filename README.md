# EasyLibrary Modules

Este README documenta o sistema de módulos da EasyLibrary. Ele foi colocado temporariamente dentro de `src/imperazim/module/` para ficar perto do código enquanto a API ainda está evoluindo. Depois, ele pode ser movido ou resumido para o README raiz.

## Objetivo

O sistema de módulos permite dividir plugins grandes em partes pequenas, carregáveis, diagnosticáveis e reutilizáveis.

Ele foi pensado para PocketMine-MP 5 e para plugins que precisam de:

- módulos internos dentro do próprio plugin;
- módulos de plugins diferentes conversando entre si;
- dependências fortes e opcionais;
- serviços compartilhados entre módulos;
- provedores por capacidade, como `economy`, `database`, `window`, `farm`, `ranking`;
- reload, enable e disable individual;
- diagnóstico via comando;
- limpeza automática de comandos, listeners, eventos, tasks, components, services e callbacks;
- storage separado por módulo;
- debug por módulo.

## Exemplos temporarios

Esta pasta agora inclui exemplos completos em:

```txt
src/imperazim/module/examples/
```

Eles sao plugins de teste prontos para copiar para a pasta `plugins/` do servidor. Os exemplos cobrem modulo basico, service provider, consumer entre plugins diferentes, capabilities e diagnosticos de falha controlada.

Leia primeiro:

```txt
src/imperazim/module/examples/README.md
```

Plugins inclusos:

```txt
01-basic-plugin
02-economy-provider-plugin
03-farm-consumer-plugin
04-diagnostics-plugin
```

## Conceitos principais

### ModuleManager

`ModuleManager` é o gerenciador global dos módulos. Em runtime, ele pode ser acessado por:

```php
$manager = \Library::modules();
```

Ele descobre módulos, calcula ordem de carregamento, ativa módulos pendentes, desativa dependentes, registra serviços, resolve capacidades e expõe diagnósticos.

### BaseModule

Todo módulo normalmente deve estender:

```php
use imperazim\module\BaseModule;

final class FarmModule extends BaseModule {
  protected function onEnable(\imperazim\module\ModuleManager $manager): void {
    // inicialização
  }

  protected function onDisable(\imperazim\module\ModuleManager $manager): void {
    // encerramento opcional
  }
}
```

`BaseModule` já oferece helpers para listeners, commands, tasks, services, APIs, configs, data folder e logger.

### ModuleContext

Cada módulo recebe um contexto enquanto está ativo. Dentro de `BaseModule`, use:

```php
$context = $this->context();
$plugin = $context->getPlugin();
$manager = $context->getManager();
$services = $context->getServices();
$logger = $context->getLogger();
```

O contexto evita depender diretamente de variáveis globais ou de chamadas espalhadas para `Library::modules()`.

### module.yml

Cada módulo é descoberto por um arquivo `module.yml` ou `module.yaml`.

Exemplo simples:

```yaml
id: statemc:farm
name: Farm System
version: 1.0.0
loader: FarmModule
namespace: statemc\farmsystem\module
load: POSTWORLD
enabled: true
```

Campos obrigatórios:

```yaml
id: vendor:module-id
loader: ModuleClass
version: 1.0.0
```

Campos opcionais comuns:

```yaml
name: Nome bonito
namespace: Vendor\Plugin\Modules\Farm
load: STARTUP|POSTWORLD
enabled: true
```

## Estrutura recomendada

```text
MyPlugin/
├─ plugin.yml
└─ src/
   ├─ Main.php
   └─ modules/
      ├─ farm/
      │  ├─ module.yml
      │  ├─ FarmModule.php
      │  ├─ api/
      │  │  └─ FarmApi.php
      │  └─ listener/
      │     └─ FarmListener.php
      └─ ranking/
         ├─ module.yml
         └─ RankingModule.php
```

No `plugin.yml`, declare onde a EasyLibrary deve procurar módulos:

```yaml
easylibrary-modules:
  - path: src/modules
    namespace: myplugin\modules
```

Também é aceito formato simples:

```yaml
easylibrary-modules: src/modules
```

Se o módulo já informa `namespace` no próprio `module.yml`, o namespace do `plugin.yml` pode ser omitido.

## Dependências entre módulos

### Dependência obrigatória

```yaml
dependencies:
  - statemc:database
  - statemc:economy
```

O módulo só será ativado depois dessas dependências estarem ativas.

### Dependência com versão

```yaml
dependencies:
  statemc:economy: ">=1.2.0"
  statemc:database: ">=2.0.0 <3.0.0"
```

Se a versão não bater, o módulo entra em estado de falha de dependência.

### Dependência opcional

```yaml
soft-dependencies:
  - statemc:ranking
```

Se o módulo existir, ele será carregado antes. Se não existir, o módulo atual ainda pode ativar.

### load-before

```yaml
load-before:
  - statemc:farm-ui
```

Use quando um módulo precisa carregar antes de outro sem o outro declarar dependência direta.

## Dependências de plugin

Use quando o módulo depende de outro plugin PocketMine ativo:

```yaml
plugin-dependencies:
  - EconomyAPI
  - MyExternalPlugin
```

Se o plugin não existir ou estiver desativado, o módulo fica aguardando.

## Serviços

Serviços são objetos expostos por um módulo para outros módulos.

### Fornecendo serviço

```php
interface EconomyService {
  public function getMoney(string $player): float;
  public function addMoney(string $player, float $amount): void;
}

final class EconomyModule extends BaseModule implements EconomyService {
  protected function onEnable(ModuleManager $manager): void {
    $this->provideService('statemc:economy', $this, EconomyService::class, $this->getVersion());
  }

  public function getMoney(string $player): float {
    return 0.0;
  }

  public function addMoney(string $player, float $amount): void {
    // ...
  }
}
```

### Consumindo serviço

```php
/** @var EconomyService $economy */
$economy = $this->getTypedService('statemc:economy', EconomyService::class);
$money = $economy->getMoney($player->getName());
```

Sem contrato tipado:

```php
$service = $this->getService('statemc:economy');
```

Com fallback opcional:

```php
$service = $this->getOptionalService('statemc:economy');
if ($service !== null) {
  // integração opcional
}
```

### Declarando dependência de serviço no module.yml

```yaml
service-dependencies:
  - statemc:economy
```

Ou no formato agrupado:

```yaml
requires:
  services:
    - statemc:economy
```

## Capacidades

Capacidade é um conceito mais abstrato que serviço. Ela responde: "qual módulo fornece economia?", "qual módulo fornece database?", "qual módulo fornece ranking?".

Um serviço é uma API concreta. Uma capacidade é uma categoria/função.

### Declarando capacidade fornecida

Formato curto:

```yaml
capabilities:
  - economy
  - money
```

Formato explícito:

```yaml
provides-capabilities:
  - economy
  - money
```

Formato agrupado:

```yaml
provides:
  capabilities:
    - economy
  services:
    - statemc:economy
```

### Declarando capacidade exigida

```yaml
capability-dependencies:
  - economy
```

Ou:

```yaml
requires:
  capabilities:
    - economy
```

O módulo só ativa quando existe algum módulo ativo fornecendo essa capacidade.

### Resolvendo provider por capacidade

```php
$provider = $this->getCapabilityProvider('economy');
if ($provider !== null) {
  $api = $provider->getApi();
}
```

### Provider preferido

Se dois módulos fornecem a mesma capacidade, o primeiro ativo é usado por padrão. Para fixar o provider preferido:

```text
/easymodule provider economy statemc:economy
```

Para voltar ao automático:

```text
/easymodule provider economy clear
```

## APIs públicas de módulo

Por padrão, `BaseModule::getApi()` retorna o próprio módulo.

Para esconder implementação interna, retorne um objeto API dedicado:

```php
final class FarmApi {
  public function __construct(private FarmModule $module) {}

  public function isInFarm(Player $player): bool {
    return $this->module->isInFarm($player);
  }
}

final class FarmModule extends BaseModule {
  private FarmApi $api;

  public function __construct(Plugin $plugin, array $config) {
    parent::__construct($plugin, $config);
    $this->api = new FarmApi($this);
  }

  public function getApi(): object {
    return $this->api;
  }
}
```

Consumindo:

```php
$api = $this->getApi('statemc:farm');
```

Opcional:

```php
$api = $this->getOptionalApi('statemc:farm');
```

## Recursos registrados pelo módulo

Use os helpers do `BaseModule` para que a EasyLibrary limpe tudo automaticamente no disable/reload.

### Listener

```php
$this->addListener(FarmListener::class);
```

O listener deve aceitar o padrão:

```php
final class FarmListener implements Listener {
  public function __construct(
    private Plugin $plugin,
    private FarmModule $module,
    private ModuleManager $manager
  ) {}
}
```

### Evento direto

```php
$this->addEvent(PlayerJoinEvent::class, function(PlayerJoinEvent $event): void {
  // ...
});
```

### Command

```php
$this->addCommand(FarmCommand::class);
```

### Tasks

```php
$this->scheduleDelayedTask(new MyTask(), 20);
$this->scheduleRepeatingTask(new MyTask(), 20);
```

### Cleanup customizado

```php
$this->addCleanup(function(): void {
  // fechar conexão, limpar cache, salvar algo
});
```

## Storage por módulo

Cada módulo possui pasta própria em:

```text
plugin_data/modules/<module_id_sanitizado>/
```

Exemplo:

```php
$config = $this->getModuleConfig('config.yml', [
  'enabled' => true,
  'limit' => 10
]);

$limit = (int) $config->get('limit', 10);
```

Caminho absoluto:

```php
$folder = $this->context()->getDataFolder();
$file = $this->context()->getPath('cache.json');
```

## Logger por módulo

```php
$this->logger()->info('Farm loaded.');
$this->logger()->warning('Farm spawn is not configured.');
$this->logger()->debug('Verbose debug only when debug is enabled.');
```

Ativar debug de um módulo:

```text
/easymodule debug statemc:farm on
```

Desativar:

```text
/easymodule debug statemc:farm off
```

## Health check

Um módulo pode sobrescrever `getHealth()`:

```php
use imperazim\module\health\ModuleHealthReport;

public function getHealth(): ModuleHealthReport {
  if ($this->database === null) {
    return ModuleHealthReport::error(['Database is not connected.']);
  }

  if ($this->cacheSize > 10000) {
    return ModuleHealthReport::warning(['Cache is very large.'], [
      'cacheSize' => $this->cacheSize
    ]);
  }

  return ModuleHealthReport::ok([
    'cacheSize' => $this->cacheSize
  ]);
}
```

Ver no jogo/console:

```text
/easymodule health statemc:farm
```

## Comandos administrativos

```text
/easymodule list
/easymodule info <id>
/easymodule why <id>
/easymodule resources <id>
/easymodule health <id>
/easymodule failures
/easymodule services
/easymodule capabilities
/easymodule provider <capability> [module|clear]
/easymodule graph
/easymodule doctor
/easymodule dependents <id>
/easymodule enable <id>
/easymodule disable <id>
/easymodule disable-runtime <id>
/easymodule reload <id>
/easymodule refresh
/easymodule debug <id> <on|off>
```

### `why`

Mostra por que um módulo não ativou:

- dependência obrigatória ausente;
- versão inválida;
- plugin dependency ausente;
- service dependency ausente;
- capability dependency ausente;
- módulo desativado no manifest;
- módulo desativado persistentemente;
- último erro de lifecycle.

### `resources`

Mostra recursos rastreados:

- commands;
- listeners;
- eventos;
- tasks;
- components;
- cleanups;
- services.

Ajuda a descobrir vazamento de listener/task após reload.

### `doctor`

Mostra visão geral do sistema:

- quantidade de módulos;
- módulos ativos;
- sources;
- services;
- módulos desativados persistentemente;
- dependências faltando;
- versões inválidas;
- services faltando;
- capabilities faltando;
- plugins faltando.

## Ciclo de vida

Fluxo de startup:

1. EasyLibrary registra sources de módulos.
2. Lê todos os `module.yml`.
3. Resolve classes loader.
4. Registra módulos no registry.
5. Calcula ordem por dependências fortes, soft dependencies e `load-before`.
6. Ativa módulos em múltiplas passagens para permitir providers de capability/service que não sejam dependência direta.
7. Módulos que ainda não possuem dependências ficam em estado `waiting_*`.

Fluxo de disable:

1. Desativa dependentes.
2. Chama `onDisable()`.
3. Remove services fornecidos pelo módulo.
4. Cancela tasks registradas.
5. Remove listeners/eventos.
6. Remove commands.
7. Executa cleanups.
8. Marca como disabled.

Fluxo de reload:

1. Desativa dependentes ativos.
2. Desativa o módulo alvo.
3. Remove recursos antigos.
4. Recarrega manifest.
5. Recria classe do módulo.
6. Reativa pendentes.

## Comunicação entre dois plugins

Sim. Se dois plugins usam EasyLibrary e ambos declaram seus módulos, eles conseguem se comunicar como módulos normais.

Plugin A:

```yaml
id: plugin-a:economy
version: 1.0.0
loader: EconomyModule
provides-capabilities:
  - economy
provides:
  - plugin-a:economy-service
```

Plugin B:

```yaml
id: plugin-b:shop
version: 1.0.0
loader: ShopModule
requires:
  capabilities:
    - economy
  services:
    - plugin-a:economy-service
soft-dependencies:
  - plugin-a:economy
```

No `ShopModule`:

```php
$economyProvider = $this->getCapabilityProvider('economy');
$economyService = $this->getOptionalService('plugin-a:economy-service');
```

A vantagem sobre plugin dependency comum é que você não precisa depender sempre de um plugin específico. Pode depender da capacidade `economy` e permitir que outro módulo forneça isso.

## Padrões recomendados

### IDs

Use IDs com vendor:

```text
statemc:farm
statemc:farm-ui
statemc:economy
imperazim:window
```

Evite IDs genéricos como:

```text
farm
core
main
api
```

### Serviços

Use IDs de serviço também com vendor:

```text
statemc:economy
statemc:database
imperazim:window
```

### Capabilities

Capabilities podem ser mais genéricas:

```text
economy
database
window
farm
ranking
permissions
profile
```

### API pública

Prefira expor uma classe API pequena em vez de retornar o módulo inteiro.

Bom:

```php
public function getApi(): object {
  return $this->api;
}
```

Evite obrigar outros módulos a chamarem métodos internos do módulo.

### Dependências

Use `dependencies` quando precisa de um módulo específico.

Use `requires.capabilities` quando aceita qualquer provider compatível.

Use `service-dependencies` quando precisa de um objeto concreto registrado.

Use `soft-dependencies` para integrações opcionais.

## Exemplo completo

### `src/modules/economy/module.yml`

```yaml
id: example:economy
name: Example Economy
version: 1.0.0
loader: EconomyModule
namespace: example\modules\economy
provides:
  capabilities:
    - economy
  services:
    - example:economy
```

### `src/modules/economy/EconomyService.php`

```php
<?php

declare(strict_types=1);

namespace example\modules\economy;

interface EconomyService {
  public function getMoney(string $player): float;
  public function addMoney(string $player, float $amount): void;
}
```

### `src/modules/economy/EconomyModule.php`

```php
<?php

declare(strict_types=1);

namespace example\modules\economy;

use imperazim\module\BaseModule;
use imperazim\module\ModuleManager;
use imperazim\module\health\ModuleHealthReport;

final class EconomyModule extends BaseModule implements EconomyService {

  /** @var array<string, float> */
  private array $money = [];

  protected function onEnable(ModuleManager $manager): void {
    $this->provideService('example:economy', $this, EconomyService::class, $this->getVersion());
    $this->logger()->info('Economy service registered.');
  }

  public function getMoney(string $player): float {
    return $this->money[strtolower($player)] ?? 0.0;
  }

  public function addMoney(string $player, float $amount): void {
    $key = strtolower($player);
    $this->money[$key] = ($this->money[$key] ?? 0.0) + $amount;
  }

  public function getHealth(): ModuleHealthReport {
    return ModuleHealthReport::ok([
      'accounts' => count($this->money)
    ]);
  }
}
```

### `src/modules/shop/module.yml`

```yaml
id: example:shop
name: Example Shop
version: 1.0.0
loader: ShopModule
namespace: example\modules\shop
requires:
  capabilities:
    - economy
  services:
    - example:economy
soft-dependencies:
  - example:economy
```

### `src/modules/shop/ShopModule.php`

```php
<?php

declare(strict_types=1);

namespace example\modules\shop;

use example\modules\economy\EconomyService;
use imperazim\module\BaseModule;
use imperazim\module\ModuleManager;

final class ShopModule extends BaseModule {

  protected function onEnable(ModuleManager $manager): void {
    $economy = $this->getTypedService('example:economy', EconomyService::class);
    $this->logger()->info('Shop connected to economy service.');
  }
}
```

## Checklist antes de considerar estável

- Testar dois plugins diferentes fornecendo e consumindo módulos.
- Testar duas capabilities iguais e provider preferido.
- Testar reload de módulo com dependentes ativos.
- Testar disable persistente e enable persistente.
- Testar services removidos após disable/reload.
- Testar tasks/listeners/commands removidos após reload.
- Testar `doctor`, `why`, `resources`, `failures`, `capabilities`.
- Testar plugin disable/enable de um plugin dono de módulos.
- Testar erro em `onEnable()` e confirmar que recursos parciais são limpos.

## Boas práticas para plugins usando módulos

- Não registre listener/task/command manualmente se existe helper do módulo.
- Não acesse módulo de outro plugin por classe concreta sem necessidade.
- Prefira service, API ou capability.
- Dê IDs com vendor.
- Evite `reload` em produção para módulos que manipulam estado crítico sem salvar antes.
- Use `getHealth()` para expor problemas reais do módulo.
- Use `logger()->debug()` para logs verbosos e deixe desligado por padrão.
- Use `requires.capabilities` para integrações flexíveis.
- Use `dependencies` quando precisa exatamente daquele módulo.

## Estados possíveis

```text
discovered
waiting_owner
waiting_dependency
waiting_service
enabling
enabled
disabling
disabled
reloading
failed
failed_dependency
```

`waiting_service` também pode ser usado quando está faltando capability, porque capability é resolvida por provider ativo.

## Arquivo persistente da EasyLibrary

As preferências globais ficam em:

```text
plugin_data/EasyLibrary/modules.yml
```

Atualmente pode conter:

```yaml
disabled:
  - example:shop

debug:
  example:economy: true

providers:
  economy: example:economy
```

- `disabled`: módulos desativados persistentemente.
- `debug`: debug por módulo.
- `providers`: provider preferido por capability.

