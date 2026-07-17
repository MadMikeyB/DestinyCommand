@extends('layout')

@section('title', 'DestinyCommand | Destiny 2 bot commands and stats')
@section('meta_description', 'DestinyCommand provides Destiny 2 command syntax, bot installation snippets, and player stat lookups for supported stream and community bots.')

@section('content')
    <div class="space-y-4 text-[15px]">
        <section class="space-y-3">
            <details class="overflow-hidden rounded-lg border border-stone-300 bg-stone-50 shadow-sm dark:border-zinc-800 dark:bg-zinc-900" open>
                <summary class="cursor-pointer list-none border-b border-stone-200 px-4 py-3 text-base font-medium text-zinc-800 marker:hidden dark:border-zinc-800 dark:text-zinc-100">What is DestinyCommand.com?</summary>
                <div class="docs-prose prose prose-sm prose-slate max-w-none px-4 py-4 prose-a:text-zinc-800 prose-a:decoration-stone-400 prose-a:underline-offset-2 prosezinc-900t-zinc-900 prose-headings:text-zinc-800 prose-table:text-sm dark:prose-invert dark:prose-a:text-zinc-100 dark:prose-a:decoration-zinc-600 dark:prose-code:text-zinc-100 dark:prose-headings:text-zinc-100">
                    <p>The Destiny Command is an app/command you can add to your chat bot that allows you and your viewers to check their stats across Destiny 2 as a whole. Whether it be Trials stats, K/D, loadout or just checking the amount of times you've achieved a certain medal.</p>
                    <p>We support all platforms, but mainly focus on Twitch. There are bots supporting the command on Twitch, Youtube, Discord, Slack and Mixer.</p>
                    <p>Installation is simple, choose your bot below and follow the instructions. For most bots a simple copy-paste in your chat is enough!</p>
                </div>
            </details>

            <details class="overflow-hidden rounded-lg border border-stone-300 bg-stone-50 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                <summary class="cursor-pointer list-none border-b border-stone-200 px-4 py-3 text-base font-medium text-zinc-800 marker:hidden dark:border-zinc-800 dark:text-zinc-100">Command List</summary>
                <div id="commands" class="docs-prose prose prose-sm prose-slate max-w-none px-4 py-4 prose-a:text-zinc-800 prose-a:decoration-stone-400 prose-a:underline-offset-2 prosezinc-900t-zinc-900 prose-headings:text-zinc-800 prose-table:text-sm dark:prose-invert dark:prose-a:text-zinc-100 dark:prose-a:decoration-zinc-600 dark:prose-code:text-zinc-100 dark:prose-headings:text-zinc-100">
                    <h3>Usage:</h3>
                    <p><code>!destiny &lt;action&gt; &lt;user&gt; &lt;platform&gt;</code></p>
                    <p>Example: <code>!destiny primary a_dmg04#7777 xbox</code></p>

                    <h3>Loadout:</h3>
                    <ul>
                        <li>primary (Primary/Kinetic weapon info)</li>
                        <li>secondary (Secondary/energy weapon info)</li>
                        <li>heavy (Heavy/power weapon info)</li>
                        <li>helmet (Helmet info)</li>
                        <li>gauntlet (Gauntlet info)</li>
                        <li>legs (Legs info)</li>
                        <li>helmet (Helmet info)</li>
                        <li>vehicle (Vehicle info)</li>
                        <li>ship (Ship info)</li>
                        <li>classitem (Classitem info)</li>
                        <li>emote (Emote info)</li>
                        <li>chest (Chest info)</li>
                        <li>aura (aura info)</li>
                        <li>weapons (Will show all weapons, without perks)</li>
                        <li>gear (Will show all gear, without perks)</li>
                    </ul>

                    <h3>Stats:</h3>
                    <p>Basic info: By default the stat command will show an account overall stat, if you want character specific stats add a <code>c</code> in front of the stat.</p>
                    <p>For example:</p>
                    <p><code>!destiny kd a_dmg04#7777</code> - Will show account overall kd.</p>
                    <p><code>!destiny ckd a_dmg04#7777</code> - Will show kd per character.</p>
                    <p>By default the stat command will grab pvp stats, if you want to specify a specific playlist add the playlist in front of the command.</p>
                    <p>For example:</p>
                    <p><code>!destiny pvekd a_dmg04#7777</code> - Will show account overall kd in PvE.</p>
                    <p><code>!destiny cpvekd a_dmg04#7777</code> - Will show kd per character in PvE.</p>
                    <p>The following stats can be checked:</p>

                    <ul>
                        <li>kd (Kills/Deaths ratio)</li>
                        <li>kda ((Kills+(Assists/2))/Deaths ratio)</li>
                        <li>wins (Games won)</li>
                        <li>wl (Wins/Losses ratio)</li>
                        <li>wins (Games won)</li>
                        <li>time (Time played)</li>
                        <li>deaths (Total deaths)</li>
                        <li>kills (Total kills)</li>
                        <li>assists (Total assists)</li>
                        <li>cr (Combat rating)</li>
                        <li>bestwep (Best weapon)</li>
                        <li>tdd (Total death distance)</li>
                        <li>avgdd (Average death distance)</li>
                        <li>tkd (Total kill distance)</li>
                        <li>avgkd (Average kill distance)</li>
                        <li>score (Total score)</li>
                        <li>avgspk (Average score per kill)</li>
                        <li>avgspl (Average score per life)</li>
                        <li>mk (Most kill in one game)</li>
                        <li>bestscore (Best single game score)</li>
                        <li>pkills (Precision kills)</li>
                        <li>akills (Ability kills)</li>
                        <li>suicides (Total suicides)</li>
                        <li>lks (Longest killing spree)</li>
                        <li>lsl (Longest single life)</li>
                        <li>fusion (Fusion rifle kills)</li>
                        <li>auto (Auto rifle kills)</li>
                        <li>machinegun (Machinegun kills)</li>
                        <li>pulse (Pulse rifle kills)</li>
                        <li>rocket (Rocket launcher kills)</li>
                        <li>handcannon (Handcannon kills)</li>
                        <li>scout (Scout rifle kills)</li>
                        <li>shotgun (Shotgun kills)</li>
                        <li>sniper (Sniper rifle kills)</li>
                        <li>smg (SMG kills)</li>
                        <li>sidearm (Sidearm kills)</li>
                        <li>sword (Sword kills)</li>
                        <li>grenadelauncher (Grenade launcher kills)</li>
                        <li>grenade (Grenade kills)</li>
                    </ul>

                    <p>The following medals can be checked:</p>

                    <table>
                        <tr><th>Command code</th><th>Medal name</th><th>Medal description</th></tr>
                        <tr><td>hurricane</td><td>Hurricane</td><td>Defeat 3 opponents in a single Arc Staff activation</td></tr>
                        <tr><td>handfullofbullets</td><td>Handfull of Bullets</td><td>Defeat 3 opponents in a single Golden Gun activation</td></tr>
                        <tr><td>lethalinstinct</td><td>Lethal Instinct</td><td>Defeat an opponent within 2 seconds of activating Golden Gun</td></tr>
                        <tr><td>lightningstorm</td><td>Lighting Storm</td><td>Defeat two or more opponents in a single Stormtrance activation</td></tr>
                        <tr><td>bloodforblood</td><td>Blood for Blood</td><td>Defeat an opponent who just defeated an ally</td></tr>
                        <tr><td>iliveherenow</td><td>I live her now</td><td>Hold two or more zones for at least 1 minute</td></tr>
                        <tr><td>flagbearer</td><td>Flag Bearer</td><td>Complete a Control match with the most combined Advantage and Power Play kills</td></tr>
                        <tr><td>gangsallhere</td><td>Gangs All Here</td><td>Win a round with your entire team alive</td></tr>
                        <tr><td>thecycle</td><td>The Cycle</td><td>In a single match, land at least one final blow with each class of weapon (Kinetic, Energy, Power) and ability (Melee, Grenade, Super)</td></tr>
                        <tr><td>dodgethis</td><td>Dodge this</td><td>Defeat a Hunter attempting to dodge</td></tr>
                        <tr><td>barricadebreaker</td><td>Barricade Breaker</td><td>Defeat a Titan within 3 seconds of their deploying a Barricade</td></tr>
                        <tr><td>riftbreaker</td><td>Rift Breaker</td><td>Defeat a Warlock while they are within their active Rift</td></tr>
                        <tr><td>notonmywatch</td><td>Not on My Watch</td><td>Land a final blow on an opponent who has damaged an ally</td></tr>
                        <tr><td>crushedthem</td><td>Crushed Them</td><td>Win a match with a large margin of victory</td></tr>
                        <tr><td>fightme</td><td>Fight Me!</td><td>Deal the most total damage to opponents in a single match</td></tr>
                        <tr><td>timeandahalf</td><td>Time and a Half</td><td>Win a match in overtime</td></tr>
                        <tr><td>undefeated</td><td>Undefeated</td><td>Complete a match in which you are never defeated by an opponent</td></tr>
                        <tr><td>doubleplay</td><td>Double Play</td><td>Rapidly defeat 2 opposing Guardians</td></tr>
                        <tr><td>tripleplay</td><td>Triple Play</td><td>Rapidly defeat 3 opposing Guardians</td></tr>
                        <tr><td>lightsout</td><td>Lights Out</td><td>Rapidly defeat 4 opposing Guardians</td></tr>
                        <tr><td>annihilation</td><td>Annihilation</td><td>Land final blows on the entire enemy team before any of them respawn</td></tr>
                        <tr><td>bestservedcold</td><td>Payback</td><td>Land the final blow on the Guardian who last defeated you</td></tr>
                        <tr><td>quickstrike</td><td>Quickstrike</td><td>Quickly defeat an opponent with Arc Staff within 3 seconds of activation</td></tr>
                        <tr><td>unyielding</td><td>Unyielding</td><td>In a single life, defeat 10 opposing Guardians</td></tr>
                        <tr><td>ruthless</td><td>Ruthless</td><td>In a single life, defeat 5 opposing Guardians</td></tr>
                        <tr><td>weranoutofmedals</td><td>We Ran Out of Medals</td><td>In a single life, defeat 20 opposing Guardians</td></tr>
                        <tr><td>combinedfire</td><td>Combined Fire</td><td>In a single life, defeat 3 opposing Guardians while assisting or assisted by your teammates</td></tr>
                        <tr><td>shutdown</td><td>Shutdown</td><td>Shut down an opponent's streak</td></tr>
                        <tr><td>wreckingcrew</td><td>Wrecking Crew</td><td>As a team, defeat 7 opposing Guardians without any of your team dying</td></tr>
                        <tr><td>notsofastmyfriend</td><td>Not So Fast My Friend</td><td>Defeat an opposing Guardian using your Super while their Super is active</td></tr>
                        <tr><td>mycrestismyown</td><td>My Crest Is My Own</td><td>Complete a match in which your crest is never collected by an opponent</td></tr>
                        <tr><td>safeandsecured</td><td>Safe and Secured</td><td>Secure three opposing crests in a single life)</td></tr>
                        <tr><td>survivor</td><td>Survivor</td><td>Win a Survival round without being defeated</td></tr>
                        <tr><td>assaultspecialist</td><td>Assualt Specialist</td><td>In a single match, defeat 7 opponents with Auto Rifle final blows</td></tr>
                        <tr><td>coldfusion</td><td>Cold Fusion</td><td>In a single life, defeat two opponents with a Fusion Rifle</td></tr>
                        <tr><td>directhit</td><td>Direct Hit</td><td>Defeat two opponents with direct grenade hits without switching weapons or reloading</td></tr>
                        <tr><td>hawkeye</td><td>Hawkeye</td><td>In a single life, defeat two opponents with precision Hand Cannon final blows</td></tr>
                        <tr><td>lethalcadence</td><td>Lethal Cadence</td><td>In a single match, defeat 7 opponents with Pulse Rifle final blows</td></tr>
                        <tr><td>splashdamage</td><td>Splash Damage</td><td>Defeat two or more opponents with a single rocket</td></tr>
                        <tr><td>fieldscout</td><td>Field Scout</td><td>In a single match, defeat 5 opponents at long range with Scout Rifle final blows</td></tr>
                        <tr><td>closeencounters</td><td>Close Encounters</td><td>Defeat two opponents at close range with a Shotgun without switching weapons or reloading</td></tr>
                        <tr><td>submachinist</td><td>Sub Machinist</td><td>In a single life, defeat 2 opponents with Submachine Gun final blows</td></tr>
                        <tr><td>regent</td><td>Regent</td><td>Defeat two opponents with a sword without switching weapons</td></tr>
                        <tr><td>neverindoubt</td><td>Never In Doubt</td><td>Doubt Win a match in which your team never trailed</td></tr>
                        <tr><td>fromthejawsofdefeat</td><td>From the Jaws of Defeat</td><td>Win a match after having trailed by a significant margin</td></tr>
                        <tr><td>fallingstar</td><td>Falling Star</td><td>Defeat an opponent with Brimstone while Daybreak is active</td></tr>
                        <tr><td>defyinggravity</td><td>Defying Gravity</td><td>In a single Daybreak activation, defeat two or more opponents without touching the ground</td></tr>
                        <tr><td>singularity</td><td>Singularity</td><td>Defeat an opponent with a Nova Bomb Vortex</td></tr>
                        <tr><td>fromdowntown</td><td>From Downtown</td><td>Defeat two or more opponents with a Nova Bomb that was in the air for at least 5 seconds</td></tr>
                        <tr><td>thunderstruck</td><td>Thunderstruck</td><td>Defeat an opponent with Landfall while casting Stormtrance</td></tr>
                        <tr><td>lightningstrike</td><td>Lightning Strike</td><td>Defeat an opponent within 3 seconds of activating Arc Staff</td></tr>
                        <tr><td>entangled</td><td>Entangled</td><td>Defeat a tethered opponent within 5 seconds of casting Shadowshot</td></tr>
                        <tr><td>longbow</td><td>Longbow</td><td>Defeat an opponent with Shadowshot at a distance greater than 30 meters</td></tr>
                        <tr><td>perfectguard</td><td>Perfect Guard</td><td>Block fatal damage within 2 seconds of activating Ward of Dawn</td></tr>
                        <tr><td>flyingfortress</td><td>Flying Fortress</td><td>Defeat an opponent with a Shield Rush within 3 seconds of defeating an opponent with a Sentinel Shield melee</td></tr>
                        <tr><td>absoluteforce</td><td>Absolute Force</td><td>Defeat two or more opponents in a single Fists of Havoc slam</td></tr>
                        <tr><td>strikerspecial</td><td>Striker Special</td><td>In a single activation, defeat two opponents with Shoulder Charge, then a third with Fists of Havoc</td></tr>
                        <tr><td>pitchperfect</td><td>Pitch Perfect</td><td>Defeat an opponent with Hammer of Sol at a distance greater than 30 meters</td></tr>
                        <tr><td>everythinglookslikeanail</td><td>Everything Looks Like a Nail</td><td>Defeat three opponents within a single Hammer of Sol activation</td></tr>
                        <tr><td>counterattack</td><td>Counter Attack</td><td>Defeat an opponent within 5 seconds of them setting a charge</td></tr>
                        <tr><td>pyrotechnics</td><td>Pyrotechnics</td><td>Set a charge that successfully detonates</td></tr>
                        <tr><td>bombswhatbombs</td><td>Bombs? What Bombs?</td><td>Defuse multiple charges in a single match</td></tr>
                        <tr><td>laststand</td><td>Last Stand</td><td>Defuse the charge as the last Guardian standing</td></tr>
                        <tr><td>perfectgame</td><td>Perfect Game</td><td>Win a Countdown match in which your opponent never scores and never sets a charge</td></tr>
                        <tr><td>lonegun</td><td>Lone Gun</td><td>Win a round as the last surviving Guardian on your team</td></tr>
                        <tr><td>minutetowinit</td><td>Minute to Win It</td><td>As a team, win a round of Survival within 1 minute</td></tr>
                        <tr><td>undertaker</td><td>Undertaker</td><td>Land all knockout blows on the opposing team in a single round</td></tr>
                        <tr><td>accordingtoplan</td><td>According to Plan</td><td>Win a Survival round despite being scoreless on Match Point</td></tr>
                        <tr><td>untouchable</td><td>Untouchable</td><td>Win a Survival match where no one on your team is defeated across all rounds</td></tr>
                        <tr><td>reclaimer</td><td>Reclaimer</td><td>Recapture a zone within 15 seconds of it being captured by your opponents</td></tr>
                        <tr><td>dominantadvantage</td><td>Dominant Advantage</td><td>Score 5 advantage or Power Play kills before the opponent recaptures a zone</td></tr>
                        <tr><td>poweroverwhelming</td><td>Power Overwhelming</td><td>As a team, defeat all 4 opposing Guardians at least once during a single Power Play</td></tr>
                        <tr><td>firstsecure</td><td>First Secure</td><td>Secure the first crest in a match</td></tr>
                        <tr><td>steadfastally</td><td>Steadfast Ally</td><td>Recover three allied crests in a single life</td></tr>
                        <tr><td>crestfallen</td><td>Crestfallen</td><td>In a single life, create 5 consecutive crests that are secured by your teammates</td></tr>
                        <tr><td>acrownofcrests</td><td>A Crown of Crests</td><td>Complete a Supremacy match with the most crests created and a 100% secure rate</td></tr>
                        <tr><td>lightemup</td><td>Light 'Em Up</td><td>Cast the first super of the match</td></tr>
                        <tr><td>fireinthehole</td><td>Fire in the Hole!</td><td>In a single life, land 5 grenade final blows</td></tr>
                        <tr><td>punchandpie</td><td>Punch and Pie</td><td>In a single life, land 3 melee final blows</td></tr>
                        <tr><td>superstar</td><td>Superstar</td><td>In a single life, cast 3 supers</td></tr>
                        <tr><td>byourpowerscombined</td><td>By Our Powers Combined</td><td>As a team, rapidly cast all 4 of your supers</td></tr>
                        <tr><td>totalmayhem</td><td>Total Mayhem</td><td>As a team, land 10 super final blows without anyone on your team being defeated</td></tr>
                        <tr><td>polyarmory</td><td>Polyarmory</td><td>In a single round, both you and your partner must land one final blow each with Kinetic, Energy, and Power weapons</td></tr>
                        <tr><td>thirdwheel</td><td>Third Wheel</td><td>Rapidly defeat both your opponents while your partner is down</td></tr>
                        <tr><td>brokenup</td><td>Broken Up</td><td>As a pair, defeat both your opponents within 3 seconds while they are separated from each other</td></tr>
                        <tr><td>heartbreaker</td><td>Heartbreaker</td><td>Win a Crimson Days match in sudden death</td></tr>
                        <tr><td>bestinclass</td><td>Best in Class</td><td>In a single life, defeat at least one Hunter, one Titan, and one Warlock</td></tr>
                        <tr><td>assassin</td><td>Assassin</td><td>In a single life, land 3 unassisted final blows without taking any damage in between</td></tr>
                        <tr><td>pickpocket</td><td>Pickpocket</td><td>In a single life, steal 5 final blows from your opponents</td></tr>
                        <tr><td>podiumfinish</td><td>Podium Finish</td><td>Finish in the top 3 in a Rumble match</td></tr>
                        <tr><td>roundrobin</td><td>Round Robin</td><td>In a single life, defeat each opposing player at least once</td></tr>
                        <tr><td>thesumofalltears</td><td>The Sum of All Tears</td><td>Win a Rumble match with a score greater than the sum of all opponents' scores</td></tr>
                        <tr><td>slayer</td><td>Slayer</td><td>Rapidly defeat 5 opposing Guardians</td></tr>
                        <tr><td>reaper</td><td>Reaper</td><td>Rapidly defeat 6 opposing Guardians</td></tr>
                        <tr><td>seventhcolumn</td><td>Seventh Column</td><td>Rapidly defeat 7 opposing Guardians</td></tr>
                        <tr><td>localmaxima</td><td>Local Maxima</td><td>Defeat the most opponents in a single round</td></tr>
                        <tr><td>denialofservice</td><td>Denial of Service</td><td>As a team, collect 3 consecutive ammo crates in a single round</td></tr>
                        <tr><td>clawingback</td><td>Clawing Back</td><td>Within a round, retake the lead after trailing by 5 points</td></tr>
                        <tr><td>whenthedustclears</td><td>When the Dust Clears</td><td>Win a Final Showdown in which your entire team survives</td></tr>
                        <tr><td>werenotdoneyet</td><td>We're Not Done Yet</td><td>Force a Final Showdown round after trailing 0-2</td></tr>
                        <tr><td>invincible</td><td>Invincible</td><td>Win a match in which no one on your team is defeated</td></tr>
                        <tr><td>totalmedals</td><td>Total medals</td><td>Total medals</td></tr>
                    </table>
                </div>
            </details>

            <details class="overflow-hidden rounded-lg border border-stone-300 bg-stone-50 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                <summary class="cursor-pointer list-none border-b border-stone-200 px-4 py-3 text-base font-medium text-zinc-800 marker:hidden dark:border-zinc-800 dark:text-zinc-100">Account linking</summary>
                <div class="docs-prose prose prose-sm prose-slate max-w-none px-4 py-4 prose-a:text-zinc-800 prose-a:decoration-stone-400 prose-a:underline-offset-2 prosezinc-900t-zinc-900 prose-headings:text-zinc-800 prose-table:text-sm dark:prose-invert dark:prose-a:text-zinc-100 dark:prose-a:decoration-zinc-600 dark:prose-code:text-zinc-100 dark:prose-headings:text-zinc-100">
                    <p>Account linking is available for Nightbot users. Tired of typing your gamertag/platform for each command? Use <code>!destiny setplayer username#1234 platform</code> to link your Twitch/Youtube/Discord account to your Destiny account. After linking your account you can use all !destiny commands without having to type your username/platform, for example: <code>!destiny primary</code>.</p>
                </div>
            </details>
        </section>

        <section class="space-y-3">
            <h2 class="px-4 text-3xl font-normal text-zinc-800 dark:text-zinc-100">Install</h2>

            <details class="overflow-hidden rounded-lg border border-stone-300 bg-stone-50 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                <summary class="cursor-pointer list-none border-b border-stone-200 px-4 py-3 text-base font-medium text-zinc-800 marker:hidden dark:border-zinc-800 dark:text-zinc-100">Nightbot (Twitch / Youtube / Discord)</summary>
                <div id="install" class="docs-prose prose prose-sm prose-slate max-w-none px-4 py-4 prose-a:text-zinc-800 prose-a:decoration-stone-400 prose-a:underline-offset-2 prosezinc-900t-zinc-900 prose-headings:text-zinc-800 prose-table:text-sm dark:prose-invert dark:prose-a:text-zinc-100 dark:prose-a:decoration-zinc-600 dark:prose-code:text-zinc-100 dark:prose-headings:text-zinc-100">
                    <p><code>!commands add !destiny $(urlfetch https://destinycommand.com/api/command?query=$(querystring)&amp;default_console=xbox)</code></p>
                    <p>Optional: the <b>default_console</b> parameter can be changed to either <b>pc, xbox or ps</b>. This is the main console which will be chosen if no console is provided.</p>
                </div>
            </details>

            <details class="overflow-hidden rounded-lg border border-stone-300 bg-stone-50 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                <summary class="cursor-pointer list-none border-b border-stone-200 px-4 py-3 text-base font-medium text-zinc-800 marker:hidden dark:border-zinc-800 dark:text-zinc-100">Streamlabs</summary>
                <div class="docs-prose prose prose-sm prose-slate max-w-none px-4 py-4 prose-a:text-zinc-800 prose-a:decoration-stone-400 prose-a:underline-offset-2 prosezinc-900t-zinc-900 prose-headings:text-zinc-800 prose-table:text-sm dark:prose-invert dark:prose-a:text-zinc-100 dark:prose-a:decoration-zinc-600 dark:prose-code:text-zinc-100 dark:prose-headings:text-zinc-100">
                    <p><code>!addcommand !destiny {readapi.https://destinycommand.com/api/command?query={1:3}&amp;bot=streamlabs&amp;user={user.name}&amp;channel={channel.name}&amp;default_console=xbox}</code></p>
                    <p>Optional: the <b>default_console</b> parameter can be changed to either <b>pc, xbox or ps</b>. This is the main console which will be chosen if no console is provided.</p>
                </div>
            </details>

            <details class="overflow-hidden rounded-lg border border-stone-300 bg-stone-50 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                <summary class="cursor-pointer list-none border-b border-stone-200 px-4 py-3 text-base font-medium text-zinc-800 marker:hidden dark:border-zinc-800 dark:text-zinc-100">Streamelements (Twitch / Youtube)</summary>
                <div class="docs-prose prose prose-sm prose-slate max-w-none px-4 py-4 prose-a:text-zinc-800 prose-a:decoration-stone-400 prose-a:underline-offset-2 prosezinc-900t-zinc-900 prose-headings:text-zinc-800 prose-table:text-sm dark:prose-invert dark:prose-a:text-zinc-100 dark:prose-a:decoration-zinc-600 dark:prose-code:text-zinc-100 dark:prose-headings:text-zinc-100">
                    <p><code>!command add !destiny ${customapi.https://destinycommand.com/api/command?query=$(queryencode $(1:))&amp;bot=streamelements&amp;user=$(queryencode ${user})&amp;channel=$(queryencode ${channel})&amp;default_console=xbox}</code></p>
                    <p>Optional: the <b>default_console</b> parameter can be changed to either <b>pc, xbox or ps</b>. This is the main console which will be chosen if no console is provided.</p>
                </div>
            </details>

            <details class="overflow-hidden rounded-lg border border-stone-300 bg-stone-50 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                <summary class="cursor-pointer list-none border-b border-stone-200 px-4 py-3 text-base font-medium text-zinc-800 marker:hidden dark:border-zinc-800 dark:text-zinc-100">Phantombot (Twitch)</summary>
                <div class="docs-prose prose prose-sm prose-slate max-w-none px-4 py-4 prose-a:text-zinc-800 prose-a:decoration-stone-400 prose-a:underline-offset-2 prosezinc-900t-zinc-900 prose-headings:text-zinc-800 prose-table:text-sm dark:prose-invert dark:prose-a:text-zinc-100 dark:prose-a:decoration-zinc-600 dark:prose-code:text-zinc-100 dark:prose-headings:text-zinc-100">
                    <p><code>!addcom !destiny (customapi https://destinycommand.com/live/api/command?user=(sender)&amp;channel=(channelname)&amp;bot=phantombot&amp;default_console=xbox&amp;query=(encodeurlparam (echo)))</code></p>
                    <p>Optional: the <b>default_console</b> parameter can be changed to either <b>pc, xbox or ps</b>. This is the main console which will be chosen if no console is provided.</p>
                </div>
            </details>

            <details class="overflow-hidden rounded-lg border border-stone-300 bg-stone-50 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                <summary class="cursor-pointer list-none border-b border-stone-200 px-4 py-3 text-base font-medium text-zinc-800 marker:hidden dark:border-zinc-800 dark:text-zinc-100">Charlemagne (Discord)</summary>
                <div class="docs-prose prose prose-sm prose-slate max-w-none px-4 py-4 prose-a:text-zinc-800 prose-a:decoration-stone-400 prose-a:underline-offset-2 prosezinc-900t-zinc-900 prose-headings:text-zinc-800 prose-table:text-sm dark:prose-invert dark:prose-a:text-zinc-100 dark:prose-a:decoration-zinc-600 dark:prose-code:text-zinc-100 dark:prose-headings:text-zinc-100">
                    <p>Charlemagne is a fast Discord bot that provides detailed access to Destiny information. Besides that Charlemagne also responds to all your !destiny commands. More information about Charlemagne at: <a href="https://warmind.io/" target="_blank" rel="noreferrer">warmind.io</a>.</p>
                    <p>To install Charlemagne to your Discord go to <a href="https://warmind.io/" target="_blank" rel="noreferrer">warmind.io</a> and click "add Charlemagne to your server". The following page will pop up:</p>
                    <p><img src="https://2g.be/twitch/destiny/images/dc80127a83287139963e485a97cdc095b.png" alt="Charlemagne install example"></p>
                    <p>Select your own, or a server you manage and hit Authorize.</p>
                    <p>That's it, you can close the confirmation page and jump in Discord to play around with Charlemagne.</p>
                </div>
            </details>
        </section>
    </div>
@endsection
