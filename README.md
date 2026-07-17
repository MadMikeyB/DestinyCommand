# DestinyCommand

Destiny Command is an app/command you can add to your chat bot that allows you and your viewers to check their stats across Destiny 2 as a whole. Whether it be Trials stats, K/D, loadout or just checking the amount of times you've achieved a certain medal.

We support all platforms, but mainly focus on Twitch. There are bots supporting the command on Twitch, Youtube, Discord and Slack.

Installation is simple, choose your bot below and follow the instructions. For most bots a simple copy-paste in your chat is enough!

## Table of Contents

- [Credits](#credits)
- [Getting Started](#getting-started)
  - [Streamers](#streamers)
    - [Nightbot (Twitch / Youtube / Discord)](#nightbot-twitch--youtube--discord)
    - [Streamlabs](#streamlabs)
    - [Streamelements (Twitch / Youtube)](#streamelements-twitch--youtube)
    - [Phantombot (Twitch)](#phantombot-twitch)
  - [Chatters](#chatters)
    - [Loadout Commands](#loadout-commands)
    - [Stat Commands](#stat-commands)
    - [Medal Commands](#medal-commands)
    - [Account Linking](#account-linking)
- [Local Development](#local-development)
  - [Requirements](#requirements)
- [Local Set Up Instructions](#local-set-up-instructions)
- [Contributing](#contributing)
- [License](#license)

## Credits

This repository is a modernization of the archived `xgerhard/DestinyCommand` project. Thank you to xgerhard for tirelessly maintaining destinycommand.com over the years. Consider donating: https://paypal.me/xgerhard. 

## Getting Started

### Streamers

#### Nightbot (Twitch / Youtube / Discord)

```text
!commands add !destiny $(urlfetch https://destinycommand.com/api/command?query=$(querystring)&default_console=ps)
```

Optional: the `default_console` parameter can be changed to either `pc`, `xbox`, or `ps`. This is the main console which will be chosen if no console is provided.

#### Streamlabs

```text
!addcommand !destiny {readapi.https://destinycommand.com/api/command?query={1:3}&bot=streamlabs&user={user.name}&channel={channel.name}&default_console=ps}
```

Optional: the `default_console` parameter can be changed to either `pc`, `xbox`, or `ps`. This is the main console which will be chosen if no console is provided.

#### Streamelements (Twitch / Youtube)

```text
!command add !destiny ${customapi.https://destinycommand.com/api/command?query=$(queryencode $(1:))&bot=streamelements&user=$(queryencode ${user})&channel=$(queryencode ${channel})&default_console=ps}
```

Optional: the `default_console` parameter can be changed to either `pc`, `xbox`, or `ps`. This is the main console which will be chosen if no console is provided.

#### Phantombot (Twitch)

```text
!addcom !destiny (customapi https://destinycommand.com/live/api/command?user=(sender)&channel=(channelname)&bot=phantombot&default_console=ps&query=(encodeurlparam (echo)))
```

Optional: the `default_console` parameter can be changed to either `pc`, `xbox`, or `ps`. This is the main console which will be chosen if no console is provided.

### Chatters

Command syntax:

`!destiny <action> <user> <platform>`

Example:

`!destiny primary a_dmg04#7777 ps`

#### Loadout Commands

| Command | Description |
| --- | --- |
| `primary` | Primary / Kinetic weapon info |
| `secondary` | Secondary / Energy weapon info |
| `heavy` | Heavy / Power weapon info |
| `helmet` | Helmet info |
| `gauntlet` | Gauntlet info |
| `legs` | Legs info |
| `vehicle` | Vehicle info |
| `ship` | Ship info |
| `classitem` | Class item info |
| `emote` | Emote info |
| `chest` | Chest info |
| `aura` | Aura info |
| `weapons` | Show all weapons, without perks |
| `gear` | Show all gear, without perks |

#### Stat Commands

Basic info:

- By default the stat command shows account overall stats.
- Add `c` in front of the stat for character specific stats.
- By default the stat command grabs PvP stats.
- Add the playlist in front of the command to target a specific playlist.

Examples:

| Example | Result |
| --- | --- |
| `!destiny kd a_dmg04#7777` | Show account overall K/D |
| `!destiny ckd a_dmg04#7777` | Show K/D per character |
| `!destiny pvekd a_dmg04#7777` | Show account overall K/D in PvE |
| `!destiny cpvekd a_dmg04#7777` | Show K/D per character in PvE |

Available stat commands:

| Command | Description |
| --- | --- |
| `kd` | Kills / Deaths ratio |
| `kda` | (Kills + (Assists / 2)) / Deaths ratio |
| `wins` | Games won |
| `wl` | Wins / Losses ratio |
| `time` | Time played |
| `deaths` | Total deaths |
| `kills` | Total kills |
| `assists` | Total assists |
| `cr` | Combat rating |
| `bestwep` | Best weapon |
| `tdd` | Total death distance |
| `avgdd` | Average death distance |
| `tkd` | Total kill distance |
| `avgkd` | Average kill distance |
| `score` | Total score |
| `avgspk` | Average score per kill |
| `avgspl` | Average score per life |
| `mk` | Most kills in one game |
| `bestscore` | Best single game score |
| `pkills` | Precision kills |
| `akills` | Ability kills |
| `suicides` | Total suicides |
| `lks` | Longest killing spree |
| `lsl` | Longest single life |
| `fusion` | Fusion rifle kills |
| `auto` | Auto rifle kills |
| `machinegun` | Machine gun kills |
| `pulse` | Pulse rifle kills |
| `rocket` | Rocket launcher kills |
| `handcannon` | Hand cannon kills |
| `scout` | Scout rifle kills |
| `shotgun` | Shotgun kills |
| `sniper` | Sniper rifle kills |
| `smg` | SMG kills |
| `sidearm` | Sidearm kills |
| `sword` | Sword kills |
| `grenadelauncher` | Grenade launcher kills |
| `grenade` | Grenade kills |

#### Medal Commands

| Command | Medal | Description |
| --- | --- | --- |
| `hurricane` | Hurricane | Defeat 3 opponents in a single Arc Staff activation |
| `handfullofbullets` | Handfull of Bullets | Defeat 3 opponents in a single Golden Gun activation |
| `lethalinstinct` | Lethal Instinct | Defeat an opponent within 2 seconds of activating Golden Gun |
| `lightningstorm` | Lighting Storm | Defeat two or more opponents in a single Stormtrance activation |
| `bloodforblood` | Blood for Blood | Defeat an opponent who just defeated an ally |
| `iliveherenow` | I live her now | Hold two or more zones for at least 1 minute |
| `flagbearer` | Flag Bearer | Complete a Control match with the most combined Advantage and Power Play kills |
| `gangsallhere` | Gangs All Here | Win a round with your entire team alive |
| `thecycle` | The Cycle | In a single match, land at least one final blow with each class of weapon (Kinetic, Energy, Power) and ability (Melee, Grenade, Super) |
| `dodgethis` | Dodge this | Defeat a Hunter attempting to dodge |
| `barricadebreaker` | Barricade Breaker | Defeat a Titan within 3 seconds of their deploying a Barricade |
| `riftbreaker` | Rift Breaker | Defeat a Warlock while they are within their active Rift |
| `notonmywatch` | Not on My Watch | Land a final blow on an opponent who has damaged an ally |
| `crushedthem` | Crushed Them | Win a match with a large margin of victory |
| `fightme` | Fight Me! | Deal the most total damage to opponents in a single match |
| `timeandahalf` | Time and a Half | Win a match in overtime |
| `undefeated` | Undefeated | Complete a match in which you are never defeated by an opponent |
| `doubleplay` | Double Play | Rapidly defeat 2 opposing Guardians |
| `tripleplay` | Triple Play | Rapidly defeat 3 opposing Guardians |
| `lightsout` | Lights Out | Rapidly defeat 4 opposing Guardians |
| `annihilation` | Annihilation | Land final blows on the entire enemy team before any of them respawn |
| `bestservedcold` | Payback | Land the final blow on the Guardian who last defeated you |
| `quickstrike` | Quickstrike | Quickly defeat an opponent with Arc Staff within 3 seconds of activation |
| `unyielding` | Unyielding | In a single life, defeat 10 opposing Guardians |
| `ruthless` | Ruthless | In a single life, defeat 5 opposing Guardians |
| `weranoutofmedals` | We Ran Out of Medals | In a single life, defeat 20 opposing Guardians |
| `combinedfire` | Combined Fire | In a single life, defeat 3 opposing Guardians while assisting or assisted by your teammates |
| `shutdown` | Shutdown | Shut down an opponent's streak |
| `wreckingcrew` | Wrecking Crew | As a team, defeat 7 opposing Guardians without any of your team dying |
| `notsofastmyfriend` | Not So Fast My Friend | Defeat an opposing Guardian using your Super while their Super is active |
| `mycrestismyown` | My Crest Is My Own | Complete a match in which your crest is never collected by an opponent |
| `safeandsecured` | Safe and Secured | Secure three opposing crests in a single life |
| `survivor` | Survivor | Win a Survival round without being defeated |
| `assaultspecialist` | Assualt Specialist | In a single match, defeat 7 opponents with Auto Rifle final blows |
| `coldfusion` | Cold Fusion | In a single life, defeat two opponents with a Fusion Rifle |
| `directhit` | Direct Hit | Defeat two opponents with direct grenade hits without switching weapons or reloading |
| `hawkeye` | Hawkeye | In a single life, defeat two opponents with precision Hand Cannon final blows |
| `lethalcadence` | Lethal Cadence | In a single match, defeat 7 opponents with Pulse Rifle final blows |
| `splashdamage` | Splash Damage | Defeat two or more opponents with a single rocket |
| `fieldscout` | Field Scout | In a single match, defeat 5 opponents at long range with Scout Rifle final blows |
| `closeencounters` | Close Encounters | Defeat two opponents at close range with a Shotgun without switching weapons or reloading |
| `submachinist` | Sub Machinist | In a single life, defeat 2 opponents with Submachine Gun final blows |
| `regent` | Regent | Defeat two opponents with a sword without switching weapons |
| `neverindoubt` | Never In Doubt | Win a match in which your team never trailed |
| `fromthejawsofdefeat` | From the Jaws of Defeat | Win a match after having trailed by a significant margin |
| `fallingstar` | Falling Star | Defeat an opponent with Brimstone while Daybreak is active |
| `defyinggravity` | Defying Gravity | In a single Daybreak activation, defeat two or more opponents without touching the ground |
| `singularity` | Singularity | Defeat an opponent with a Nova Bomb Vortex |
| `fromdowntown` | From Downtown | Defeat two or more opponents with a Nova Bomb that was in the air for at least 5 seconds |
| `thunderstruck` | Thunderstruck | Defeat an opponent with Landfall while casting Stormtrance |
| `lightningstrike` | Lightning Strike | Defeat an opponent within 3 seconds of activating Arc Staff |
| `entangled` | Entangled | Defeat a tethered opponent within 5 seconds of casting Shadowshot |
| `longbow` | Longbow | Defeat an opponent with Shadowshot at a distance greater than 30 meters |
| `perfectguard` | Perfect Guard | Block fatal damage within 2 seconds of activating Ward of Dawn |
| `flyingfortress` | Flying Fortress | Defeat an opponent with a Shield Rush within 3 seconds of defeating an opponent with a Sentinel Shield melee |
| `absoluteforce` | Absolute Force | Defeat two or more opponents in a single Fists of Havoc slam |
| `strikerspecial` | Striker Special | In a single activation, defeat two opponents with Shoulder Charge, then a third with Fists of Havoc |
| `pitchperfect` | Pitch Perfect | Defeat an opponent with Hammer of Sol at a distance greater than 30 meters |
| `everythinglookslikeanail` | Everything Looks Like a Nail | Defeat three opponents within a single Hammer of Sol activation |
| `counterattack` | Counter Attack | Defeat an opponent within 5 seconds of them setting a charge |
| `pyrotechnics` | Pyrotechnics | Set a charge that successfully detonates |
| `bombswhatbombs` | Bombs? What Bombs? | Defuse multiple charges in a single match |
| `laststand` | Last Stand | Defuse the charge as the last Guardian standing |
| `perfectgame` | Perfect Game | Win a Countdown match in which your opponent never scores and never sets a charge |
| `lonegun` | Lone Gun | Win a round as the last surviving Guardian on your team |
| `minutetowinit` | Minute to Win It | As a team, win a round of Survival within 1 minute |
| `undertaker` | Undertaker | Land all knockout blows on the opposing team in a single round |
| `accordingtoplan` | According to Plan | Win a Survival round despite being scoreless on Match Point |
| `untouchable` | Untouchable | Win a Survival match where no one on your team is defeated across all rounds |
| `reclaimer` | Reclaimer | Recapture a zone within 15 seconds of it being captured by your opponents |
| `dominantadvantage` | Dominant Advantage | Score 5 advantage or Power Play kills before the opponent recaptures a zone |
| `poweroverwhelming` | Power Overwhelming | As a team, defeat all 4 opposing Guardians at least once during a single Power Play |
| `firstsecure` | First Secure | Secure the first crest in a match |
| `steadfastally` | Steadfast Ally | Recover three allied crests in a single life |
| `crestfallen` | Crestfallen | In a single life, create 5 consecutive crests that are secured by your teammates |
| `acrownofcrests` | A Crown of Crests | Complete a Supremacy match with the most crests created and a 100% secure rate |
| `lightemup` | Light 'Em Up | Cast the first super of the match |
| `fireinthehole` | Fire in the Hole! | In a single life, land 5 grenade final blows |
| `punchandpie` | Punch and Pie | In a single life, land 3 melee final blows |
| `superstar` | Superstar | In a single life, cast 3 supers |
| `byourpowerscombined` | By Our Powers Combined | As a team, rapidly cast all 4 of your supers |
| `totalmayhem` | Total Mayhem | As a team, land 10 super final blows without anyone on your team being defeated |
| `polyarmory` | Polyarmory | In a single round, both you and your partner must land one final blow each with Kinetic, Energy, and Power weapons |
| `thirdwheel` | Third Wheel | Rapidly defeat both your opponents while your partner is down |
| `brokenup` | Broken Up | As a pair, defeat both your opponents within 3 seconds while they are separated from each other |
| `heartbreaker` | Heartbreaker | Win a Crimson Days match in sudden death |
| `bestinclass` | Best in Class | In a single life, defeat at least one Hunter, one Titan, and one Warlock |
| `assassin` | Assassin | In a single life, land 3 unassisted final blows without taking any damage in between |
| `pickpocket` | Pickpocket | In a single life, steal 5 final blows from your opponents |
| `podiumfinish` | Podium Finish | Finish in the top 3 in a Rumble match |
| `roundrobin` | Round Robin | In a single life, defeat each opposing player at least once |
| `thesumofalltears` | The Sum of All Tears | Win a Rumble match with a score greater than the sum of all opponents' scores |
| `slayer` | Slayer | Rapidly defeat 5 opposing Guardians |
| `reaper` | Reaper | Rapidly defeat 6 opposing Guardians |
| `seventhcolumn` | Seventh Column | Rapidly defeat 7 opposing Guardians |
| `localmaxima` | Local Maxima | Defeat the most opponents in a single round |
| `denialofservice` | Denial of Service | As a team, collect 3 consecutive ammo crates in a single round |
| `clawingback` | Clawing Back | Within a round, retake the lead after trailing by 5 points |
| `whenthedustclears` | When the Dust Clears | Win a Final Showdown in which your entire team survives |
| `werenotdoneyet` | We're Not Done Yet | Force a Final Showdown round after trailing 0-2 |
| `invincible` | Invincible | Win a match in which no one on your team is defeated |
| `totalmedals` | Total medals | Total medals |

#### Account Linking

Account linking is available for Nightbot users.

Use:

`!destiny setplayer username#1234 platform`

After linking your Twitch / YouTube / Discord account to your Destiny account, you can use commands without typing your username and platform every time, for example:

`!destiny primary`

## Local Development

### Requirements

- PHP 8.5+
- Composer 2+
- A database supported by Laravel

## Local Set Up Instructions

Follow these steps to set up the Laravel/Inertia.js application locally:

### 1. Clone the Repository
```bash
git clone https://github.com/MadMikeyB/DestinyCommand.git
cd DestinyCommand
```

### 2. Set Up a Local PHP Development Environment
You can use [php.new](https://php.new) to quickly spin up a local PHP environment or set up one manually:

- **Using php.new**:
    1. Visit [php.new](https://php.new) and follow the instructions to set up a local PHP environment.
    2. Ensure you have Composer installed globally.
    3. Install a local database server (MySQL, PostgreSQL, or use [SQLite](https://laravel.com/docs/master/database#sqlite-configuration)).
        - You can use [DBNgin](https://dbngin.com/) to get a database server set up (I have only tested this on macOS)
    4. Ensure Redis is installed (Pretty sure DBNgin can do this too.)

- **Manual Setup**:
    1. Install PHP (version 8.5 or higher) and Composer.
    2. Install a database server (e.g., MySQL or MariaDB).
    3. Set up a web server (e.g., Apache or Nginx).
    4. Ensure Redis is installed.

### 3. Install Dependencies
```bash
composer install
npm install
```

### 4. Set Up Environment Variables
1. Copy the `.env.example` file to `.env`:
```bash
cp .env.example .env
```
2. Update the `.env` file with your local database credentials and other required configurations.

### 5. Generate Application Key
```bash
php artisan key:generate
```

### 6. Set Up the Database
1. Create a new database for the application.
2. Run migrations and seeders:
```bash
php artisan migrate --seed
```

### 7. Obtain a Bungie API Key
1. Visit the [Bungie Developer Portal](https://www.bungie.net/en/Application).
2. Log in with your Bungie account and create a new application.
3. Note down the API key and client secret.
4. Update the API key and secret in your `.env` file:
```env
BUNGIE_API_KEY=your_api_key
BUNGIE_CLIENT_ID=your_client_id
BUNGIE_CLIENT_SECRET=your_client_secret
```
### 8. Start the Development Server
```bash
composer run dev
```
Visit `https://localhost:8000` in your browser to access the application.

> [!NOTE]
> `composer run dev` will do the following for you in a terminal window: 
>  - Start the php development server via `php artisan serve`
>  - Start the queue listeners via `php artisan queue:listen`
>  - Start Pail (Laravel's "tail" for all app log files) via `php artisan pail`
>  - Run `npm run dev` to run the front end server.
r the landing page and tools

## Contributing

See [CONTRIBUTING.md](CONTRIBUTING.md).

## License

This fork retains the upstream MIT licensing position from the original project metadata. See [LICENSE](LICENSE).
