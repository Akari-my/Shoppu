<img width="2048" height="280" alt="minecraft_title" src="https://github.com/user-attachments/assets/9e2f2df3-02d8-4fa9-9fa7-8c816e5f0c2c" />

**Shoppu** is a shop plugin for **PocketMine-MP 5** that lets you create custom item shops with categories, buy/sell prices, category icons, and full language support.

## ✨ Features

- **📂 Category-based shops** — Organize items into categories with custom names and icons
- **💰 Buy & Sell** — Configure items as buy-only, sell-only, or both
- **🖼️ Category Icons** — Each category shows a Minecraft item texture as its button icon
- **🌍 16 languages** — Fully translatable UI and messages (English, Italian, French, German, Spanish, Portuguese, Dutch, Swedish, Polish, Turkish, Russian, Ukrainian, Japanese, Korean, Chinese, Vietnamese)
- **🏦 Economy support** — Built-in BedrockEconomy integration, extensible via interface
- **💾 Dual storage** — YAML (default) or JSON for shop data

## 📋 Requirements

| Dependency |
|------------|
| PocketMine-MP |
| BedrockEconomy |

## 📥 Installation

1. Download the `Shoppu.phar`
2. Place it in your server's `plugins/` folder
3. Restart the server
4. Edit `plugin_data/Shoppu/shop.yml` to set up your shop

## 🎮 Commands

| Command | Description | Permission |
|---------|-------------|------------|
| `/shop` | Opens the shop UI | `shoppu.command` |

## ⚙️ Configuration

### `config.yml`

```yaml
lang: eng                          # Language code
prefix: "§7[§6Shoppu§7] §r"       # Chat message prefix
storage: yaml                      # Storage type: yaml or json

economy:
  type: bedrockeconomy             # Economy provider
```

### 🌐 Supported Languages

| Code | Language | | Code | Language |
|------|----------|---|------|----------|
| `eng` | English | | `ita` | Italian |
| `fra` | French | | `deu` | German |
| `esp` | Spanish | | `por` | Portuguese |
| `nld` | Dutch | | `swe` | Swedish |
| `pol` | Polish | | `tur` | Turkish |
| `rus` | Russian | | `ukr` | Ukrainian |
| `jpn` | Japanese | | `kor` | Korean |
| `zho` | Chinese (Simplified) | | `vie` | Vietnamese |

> Language files are in `plugin_data/Shoppu/lang/`. Edit them to customize any message.

## 🏪 Creating a Shop

Shops are defined in `plugin_data/Shoppu/shop.yml`.

### Structure

```yaml
categories:
  <category_id>:                # Internal ID (lowercase, no spaces)
    name: "Display Name"        # Shown in the UI
    icon: "stone"               # Minecraft item/block for the button icon (optional)
    items:
      <item_id>:                # Minecraft item/block ID (e.g. stone, apple)
        buy: <price>            # Purchase price — remove for sell-only
        sell: <price>           # Sell price — remove for buy-only
```

### Examples

**Both buy and sell:**
```yaml
stone:
  buy: 10
  sell: 5
```

**Buy-only** (no sell price):
```yaml
diamond:
  buy: 500
```

**Sell-only** (no buy price):
```yaml
gravel:
  sell: 1
```

### 🖼️ Category Icons

Each category can have an `icon` field pointing to a Minecraft item or block ID:

```yaml
food:
  name: "Food"
  icon: "apple"          # Shows the apple texture on the button
  items:
    apple:
      buy: 20
      sell: 10
```

The plugin automatically resolves the correct texture path:
- **Blocks** → `textures/blocks/<id>`
- **Items** → `textures/items/<id>`

If no icon is set, the button appears without a texture.

### 📦 Example Shop

Here's a complete shop with 6 categories covering building blocks, farming, food, ores, mob drops, and redstone components:

```yaml
categories:
  building:
    name: "Building Blocks"
    icon: "stone"
    items:
      stone:
        buy: 10
        sell: 5
      oak_planks:
        buy: 8
        sell: 4
      glass:
        buy: 12
        sell: 6

  farming:
    name: "Farming"
    icon: "wheat"
    items:
      wheat:
        buy: 5
        sell: 2
      carrot:
        buy: 8
        sell: 4
      melon:
        buy: 10
        sell: 5

  food:
    name: "Food"
    icon: "apple"
    items:
      apple:
        buy: 20
        sell: 10
      bread:
        buy: 15
        sell: 7
      cooked_beef:
        buy: 30
        sell: 15
      golden_apple:
        buy: 200
        sell: 100

  ores:
    name: "Ores & Minerals"
    icon: "diamond"
    items:
      iron_ingot:
        buy: 50
        sell: 25
      diamond:
        buy: 500
        sell: 250
      netherite_ingot:
        buy: 2000
        sell: 1000

  mob_drops:
    name: "Mob Drops"
    icon: "bone"
    items:
      ender_pearl:
        buy: 200
        sell: 100
      blaze_rod:
        buy: 150
        sell: 75
      slime_ball:
        buy: 30
        sell: 15

  redstone:
    name: "Redstone & Tech"
    icon: "redstone"
    items:
      piston:
        buy: 40
        sell: 20
      hopper:
        buy: 70
        sell: 35
      rail:
        buy: 15
        sell: 7
```

> Use any valid Minecraft item ID: `stone`, `diamond`, `cooked_beef`, `iron_ingot`, `wheat`, `bone`, etc.

## 🏦 Economy

Supports **BedrockEconomy** out of the box.

### Adding a Custom Economy

Create a class implementing `Shoppu\economy\EconomyProvider`:

```php
namespace Shoppu\economy;

use pocketmine\player\Player;

interface EconomyProvider{
    public function getMoney(Player $player, callable $callback): void;
    public function addMoney(Player $player, float $amount, callable $callback): void;
    public function subtractMoney(Player $player, float $amount, callable $callback): void;
    public function getCurrencySymbol(): string;
}
```

Then register it in `Main::createEconomyProvider()` and set `economy.type` in `config.yml`.

## 🌍 Translating

Language files are in `plugin_data/Shoppu/lang/`. To add a new language:

1. Copy `eng.yml` and rename it (e.g. `ara.yml` for Arabic)
2. Translate the values (keep the keys unchanged)
3. Set `lang: ara` in `config.yml`

Available placeholders: `{player}`, `{item}`, `{price}`, `{quantity}`, `{total}`, `{name}`, `{category}`.

## 💾 Storage

| Type | Description |
|------|-------------|
| `yaml` | Human-readable YAML files (default) |
| `json` | JSON format for shop data |

Set `storage` in `config.yml` to switch.

## ⭐ Contribute

- Found a bug? Open an issue
- Pull requests welcome
- Star the repo if you like the project!

## 📄 License

MIT
