# Stijlgids — Tim van der Kloet | Webdevelopment & Techniek

Versie 1.0 — augustus 2026
Deze gids beschrijft hoe kleur, typografie en UI-elementen worden ingezet op alle nieuwe sites, serverpagina's en mails die onder deze huisstijl vallen. Alles is afgeleid van het logo (`<TK/>`).

---

## 1. Uitgangspunt

Er zijn **twee toepassingen** van dezelfde merkbasis:

| Context | Doel | Toon |
|---|---|---|
| **Website / portfolio** | Vertrouwen wekken, werk presenteren | Rustig, professioneel, technisch maar toegankelijk |
| **Server- en statuspagina's / mails** | In 1 seconde duidelijk maken: werkt het wel of niet | Functioneel, ondubbelzinnig, geen ruis |

Beide gebruiken dezelfde merkkleuren (navy + teal) voor branding, header en links. Het verschil zit in een **extra semantische laag** die alleen op status-gerelateerde pagina's wordt gebruikt.

---

## 2. Kleurenpalet

### 2.1 Merkkern — overal gebruikt

| Naam | Hex | RGB | Gebruik |
|---|---|---|---|
| Ink Navy | `#1F2933` | 31, 41, 51 | Primaire tekstkleur, koppen, logo, footer-achtergrond |
| Signal Teal | `#0B7F89` | 11, 127, 137 | Primaire accentkleur: links, knoppen, actieve states, iconen |
| Teal Licht | `#3FAFB8` | 63, 175, 184 | Hover-states, lichte accenten, badges, grafieklijnen |

**Regel:** navy is voor lezen, teal is voor klikken. Gebruik teal nooit voor lopende tekst — alleen voor interactieve of geaccentueerde elementen. Gebruik navy nooit als achtergrondkleur van een knop die "ga verder"-achtig is; dat is teal's rol.

### 2.2 Neutralen — website

| Naam | Hex | Gebruik |
|---|---|---|
| Paper (achtergrond) | `#F7F9FA` | Pagina-achtergrond, secties die zich onderscheiden van wit |
| Wit | `#FFFFFF` | Cards, content-vlakken op Paper-achtergrond |
| Rand / lijn | `#E3E7EA` | Dividers, kaartranden, tabellijnen |
| Secundaire tekst | `#5B6B76` | Bijschriften, metadata, placeholder-tekst |
| Muted tekst | `#8A9AA5` | Timestamps, disabled labels |

**Regel:** nooit puur zwart (`#000`) gebruiken voor tekst — altijd Ink Navy. Dat houdt de hele site visueel consistent met het logo.

### 2.3 Semantisch — server-, statuspagina's en mails

Alleen gebruiken op plekken waar een systeemstatus wordt getoond (uptime-dashboard, incidentmail, serverbeheer-UI). Deze kleuren zijn losgekoppeld van de merkkleuren zodat status nooit met branding verward wordt.

| Status | Hex | Wanneer |
|---|---|---|
| 🟢 Up / operationeel | `#1B8A5A` | Dienst draait normaal |
| 🟠 Degraded | `#C97F0E` | Verhoogde latency, deels beschikbaar, waarschuwing |
| 🔴 Down | `#C1382F` | Storing, service onbereikbaar |
| ⚪ Onderhoud / onbekend | `#6B7280` | Gepland onderhoud, geen data, gepauzeerde check |

**Belangrijke regel:** gebruik **nooit** Signal Teal als "up"-kleur, ook al ligt de kleur dicht bij groen. Teal = merk, groen = status. Als je ze mengt, kan een lezer in een mail niet meer snel zien of iets een merkelement of een statusindicator is.

**Contrastvarianten** (voor achtergrond-tinten van badges/banners, WCAG-AA-veilig met bijbehorende tekstkleur):

| Status | Achtergrond-tint | Tekstkleur op tint |
|---|---|---|
| Up | `#E7F5EE` | `#0F5C3B` |
| Degraded | `#FBEEDC` | `#8A5709` |
| Down | `#FBEAE8` | `#82251E` |
| Onderhoud | `#EEF0F2` | `#495057` |

---

## 3. Typografie

Het logo gebruikt een zware, geometrische sans voor "TK" en de naam — dat vertaalt zich als volgt:

| Rol | Font | Gewicht | Gebruik |
|---|---|---|---|
| Koppen (H1/H2) | **Poppins** of **Montserrat** | 700–800 (Bold/ExtraBold) | Titels, hero-teksten, logo-achtige uitingen |
| Subkoppen (H3+) | Poppins / Montserrat | 600 (SemiBold) | Sectietitels |
| Body-tekst | **Inter** of **system-ui** | 400 (Regular) | Lopende tekst, UI-labels — leesbaarheid boven karakter |
| Technisch / code / server-logs | **JetBrains Mono** of **Fira Code** | 400–500 | Statuspagina's, logs, serverdata, IP's, versienummers |

**Regel:** gebruik de zware koppenfont spaarzaam — alleen voor titels, nooit voor alinea's. Statuspagina's mogen de monospace-font gebruiken voor waarden (uptime %, response time, IP) zodat cijfers uitlijnen en "technisch" aanvoelen, passend bij "Techniek" in de tagline.

**Schaal (indicatief):**
- H1: 40–48px / 700
- H2: 28–32px / 700
- H3: 20–22px / 600
- Body: 16px / 400, line-height 1.6
- Small / caption: 13px / 400, `--text-secondary`

---

## 4. Logo & merkgebruik

- Minimale vrije ruimte rondom het logo: gelijk aan de hoogte van de "T" in "TK".
- Op donkere achtergronden: gebruik de witte/lichte variant van het logo (navy-vorm inverteren naar wit, teal blijft teal — teal heeft genoeg contrast op navy).
- Gebruik het `<TK/>`-icoon (zonder naam) als favicon, app-icoon en als laad-/statusindicator op serverpagina's.
- Nooit het logo herkleuren buiten navy/teal, nooit schaduw of gradient toevoegen.

---

## 5. UI-componenten — website

| Component | Regel |
|---|---|
| Primaire knop | Achtergrond Signal Teal, tekst wit, hover → Teal Licht |
| Secundaire knop | Transparant/wit met navy rand en navy tekst, hover → lichte teal-tint achtergrond |
| Links (inline) | Signal Teal, underline bij hover |
| Kaarten | Wit op Paper-achtergrond, 1px rand `#E3E7EA`, 8–12px radius, geen zware schaduw |
| Badges/tags (niet-status) | Teal-tint achtergrond (`#E1F5EE`-achtig), donkere teal-tekst |
| Footer | Ink Navy achtergrond, tekst wit/lichtgrijs, teal voor links |
| Formuliervelden | Witte achtergrond, grijze rand, focus-ring in Teal |

---

## 6. UI-componenten — server-, statuspagina's & mails

| Component | Regel |
|---|---|
| Statusindicator (bolletje/badge) | Altijd één van de 4 semantische kleuren uit §2.3 — nooit merkkleuren |
| Uptime-percentage / grafiek | Lijn in Signal Teal voor "normaal", datapunten inkleuren met status-kleur bij incident |
| Incident-banner (bovenaan pagina) | Achtergrond = contrastvariant-tint van de status, tekst = bijbehorende donkere tekstkleur (zie tabel §2.3) |
| Header van statuspagina/mail | Ink Navy of wit met logo — blijft merk-branding, gescheiden van de statuskleuren eronder |
| "Alles operationeel"-melding | Groene tint-banner, géén teal, om verwarring met merk-elementen te voorkomen |
| Serverbeheer-UI (dashboards) | Neutrale grijzen + navy voor structuur, semantische kleuren uitsluitend voor status/metrics, teal voor interactieve elementen (knoppen, filters) |
| E-mail (up/down notificaties) | Onderwerpregel en header-balk in status-kleur (rood bij down, groen bij herstel), logo/footer in merkkleuren, body-tekst in Ink Navy op wit |

**Vuistregel voor mails:** de eerste 2 seconden moeten duidelijk maken "up" of "down" via kleur — pas daarna komt de merkidentiteit (logo, footer) in beeld. Status wint van branding in hiërarchie, branding wint van status in rust.

---

## 7. Donkere modus

Voor dashboards/serverpagina's die vaak 's nachts bekeken worden (monitoring):

| Element | Light | Dark |
|---|---|---|
| Achtergrond | `#F7F9FA` | `#12181F` |
| Kaarten | `#FFFFFF` | `#1B232C` |
| Tekst primair | `#1F2933` | `#E8EDF0` |
| Tekst secundair | `#5B6B76` | `#8FA0AB` |
| Teal accent | `#0B7F89` | `#3FAFB8` (lichter, voor contrast) |
| Status-kleuren | zoals §2.3 | zelfde hue, ~10% lichter voor leesbaarheid op donker |

---

## 8. Do's en don'ts

**Wel doen:**
- Teal reserveren voor interactieve elementen en accenten.
- Statuskleuren strikt scheiden van merkkleuren.
- Navy als basis-tekstkleur op elke pagina, ook op statuspagina's.
- Monospace gebruiken voor technische waarden (IP's, uptime %, versies, logs).

**Niet doen:**
- Geen teal gebruiken als "alles is oké"-indicator — dat is groen.
- Geen felle, extra kleuren toevoegen buiten dit palet zonder aanleiding (geen paars, geen extra blauw).
- Geen zwart (`#000000`) — altijd Ink Navy.
- Geen gradients of schaduweffecten op het logo.

---

## 9. Referentie: CSS custom properties

```css
:root {
  /* merkkern */
  --tk-navy: #1F2933;
  --tk-teal: #0B7F89;
  --tk-teal-light: #3FAFB8;

  /* website neutralen */
  --tk-bg: #F7F9FA;
  --tk-surface: #FFFFFF;
  --tk-border: #E3E7EA;
  --tk-text-secondary: #5B6B76;
  --tk-text-muted: #8A9AA5;

  /* status (server/mail) */
  --tk-status-up: #1B8A5A;
  --tk-status-degraded: #C97F0E;
  --tk-status-down: #C1382F;
  --tk-status-maintenance: #6B7280;

  --tk-status-up-bg: #E7F5EE;
  --tk-status-up-text: #0F5C3B;
  --tk-status-degraded-bg: #FBEEDC;
  --tk-status-degraded-text: #8A5709;
  --tk-status-down-bg: #FBEAE8;
  --tk-status-down-text: #82251E;
  --tk-status-maintenance-bg: #EEF0F2;
  --tk-status-maintenance-text: #495057;

  /* typografie */
  --tk-font-heading: 'Poppins', 'Montserrat', sans-serif;
  --tk-font-body: 'Inter', system-ui, sans-serif;
  --tk-font-mono: 'JetBrains Mono', 'Fira Code', monospace;
}

[data-theme="dark"] {
  --tk-bg: #12181F;
  --tk-surface: #1B232C;
  --tk-text-primary: #E8EDF0;
  --tk-text-secondary: #8FA0AB;
  --tk-teal: #3FAFB8;
}
```
