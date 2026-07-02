# Review — sulu-block-swiper

Vollständiges Code-Review vom 2026-07-02. Jeder Punkt ist als Checkbox abarbeitbar
und über seine Nummer (z. B. „1.3“) eindeutig referenzierbar.
Stand: Branch `main` (eb845ae).

---

## 1. Funktionale Probleme (hohe Priorität)

- [x] **1.1 — `block--swiper` ignoriert seine komplette Slider-Konfiguration**
  `Resources/config/blocks/block--swiper.xml` (Zeilen 31–129) definiert `effect`, `loop`,
  `autoplay`, `autoplay_delay`, `speed`, `show_navigation`, `show_pagination` — aber
  `Resources/views/includes/blocks/block--swiper.html.twig` gibt kein einziges
  `data-*`-Attribut dafür aus und rendert Navigation/Pagination bedingungslos.
  Editor-Einstellungen sind auf der Website wirkungslos.
  → Template analog zu `block--swiper-bg.html.twig:14-23` verdrahten.

- [x] **1.2 — `block--swiper-3-image` ignoriert die Hälfte seiner Einstellungen**
  Das XML definiert `show_navigation`, `show_pagination`, `slides_per_view`, `centered`,
  aber `block--swiper-3-image.html.twig` gibt nur `data-loop` und `data-speed` aus und
  rendert keine `.swiper-button-*`- oder `.swiper-pagination`-Elemente.
  → Fehlende `data-*`-Attribute und Navigations-/Pagination-Markup ergänzen.

- [x] **1.3 — `speed` in `block--swiper-3-image.xml` ist `text_line` statt `number`**
  (`block--swiper-3-image.xml:30`) — Freitext landet ungeprüft in `data-speed`.
  Alle anderen Blöcke nutzen `type="number"` mit `min`/`max`.
  → Auf `number` umstellen (Achtung: Bestandsdaten prüfen).

- [x] **1.4 — Falscher Fallback bei `block--swiper-hero`**
  `block--swiper-hero.html.twig:20-21` nutzt `content.show_navigation ? 'true' : 'false'`
  ohne `?? true`-Fallback (anders als Zeilen 4–7). Bestandsinhalte mit `null` rendern
  `data-show-navigation="false"`, obwohl der XML-Default `true` ist.
  → `content.show_navigation ?? true` bzw. `content.show_pagination ?? true` verwenden.

- [x] **1.5 — `block--swiper-slide` ist verwaist**
  Kein Container referenziert den Typ (`block--swiper` erlaubt nur
  `block--swiper-slide-facts`), er steht nicht in `_slots.yaml`.
  → Entschieden: als Slide-Typ in `block--swiper` registriert; Root-Element des
  Templates trägt jetzt die `swiper-slide`-Klasse.

- [x] **1.6 — Video-Medien erzeugen kaputte Ausgabe in `block--swiper-slide-facts`**
  `block--swiper-slide-facts.xml:20` erlaubt `types="image,video"`, das Template rendert
  aber immer `<img>` mit `image.thumbnails['content-image']`.
  → Entschieden: `types="image"` eingeschränkt (Videos wurden vom Template nie
  unterstützt). Bestandsinhalte mit bereits ausgewähltem Video ggf. manuell prüfen.

- [x] **1.7 — Ungültiger `loading`-Wert „auto“**
  `block--swiper-slide-facts.xml:82`: Die Option `auto` ist kein gültiger Wert des
  HTML-`loading`-Attributs (nur `lazy`/`eager`).
  → Option entfernt (`config_image.xml`-Fragment war nicht betroffen). Template
  normalisiert gespeicherte Altwerte auf `lazy`.

- [x] **1.8 — Robustheit in `block--swiper-slide.html.twig`**
  - Zeile 3: `content.attr_class` ohne `|default('')` (einziges Template ohne Guard).
    → mit 1.5 erledigt.
  - Zeile 4: `view.link.target` ohne Default — leeres `target=""` bzw. Fehler bei
    `strict_variables`. → `target` wird nur noch gerendert, wenn gesetzt.
  - Bei `target="_blank"` fehlt `rel="noopener"`. → `rel="noopener noreferrer"`
    wird bei `_blank` automatisch gesetzt.

- [x] **1.9 — Externe Abhängigkeit `block--hero-content` unklar**
  `block--swiper-hero.xml:20` referenziert `block--hero-content` — folgt nicht dem
  Namensschema von `sulu-block-content` (`block--content-*`) und kommt aus keinem der
  deklarierten Composer-Pakete erkennbar.
  → Geklärt: stammt aus `depa/sulu-block-hero`. Als require + VCS-Repository in
  composer.json aufgenommen, in EXTERNAL_TYPES des Konsistenz-Tests und im README
  dokumentiert.

## 2. Inkonsistenzen zwischen den Blöcken (mittlere Priorität)

- [ ] **2.1 — Übersetzungsschlüssel vereinheitlichen** *(wartet auf Projekt-Abgleich)*
  `block--swiper-bg.html.twig:60-62` nutzt `website.page.swiper.autoplay.*`,
  `block--swiper-hero.html.twig:39-41` nutzt `website.swiper.autoplay.*`.
  → Auf einen Namespace einigen; erwägen, Translations im Bundle mitzuliefern.
  **Todo:** Vor der Umsetzung in den Projekten prüfen, welcher Namespace dort
  bereits übersetzt ist (`website.swiper.*` oder `website.page.swiper.*`).
  Geplante Umsetzung danach: (1) betroffenes Template auf den gemeinsamen
  Namespace umstellen, (2) `Resources/translations/messages.{de,en}.yaml`
  im Bundle mitliefern (App-Übersetzungen überschreiben Bundle-Defaults),
  (3) verwaiste Alt-Schlüssel in den Projekten aufräumen, (4) dabei auch die
  hartkodiert englischen `aria-label="Next slide"/"Previous slide"` in
  `block--swiper` und `block--swiper-hero` auf `|trans`-Schlüssel umstellen
  (aus Review 3.1, Variante 2).

- [x] **2.2 — Asset-Loading in `block--swiper.html.twig` angleichen**
  Lädt als einziges Template `swiper-lib.js` ohne `defer: true` und kein
  `swiper-lib.css` (nur ein eigenes `block--swiper.css`).
  → `swiper-lib.css` ergänzt, `swiper-lib.js` lädt mit `defer` wie überall sonst.

- [x] **2.3 — Autoplay-Toggle-Verhalten angleichen**
  `block--swiper-bg` rendert den Pause-Button nur bei `autoplay == true` (Zeile 56),
  `block--swiper-hero` rendert ihn immer.
  → Hero an das bg-Muster angeglichen: Toggle nur bei aktivem Autoplay
  (WCAG 2.2.2 — Pause-Mechanismus nur nötig, wenn sich etwas bewegt).

- [x] **2.4 — `loop`-Shadowing beseitigen**
  In `block--swiper-3-image.html.twig:3`, `block--swiper-bg.html.twig:4` und
  `block--swiper-hero.html.twig:4` überschreibt `{% set loop = content.loop ?? true %}`
  Twigs reservierte `loop`-Variable. Funktioniert aktuell (Twig-`loop` gewinnt in der
  `for`-Schleife), ist aber eine Stolperfalle.
  → Umbenennen in z. B. `loop_enabled`.
  → In allen vier Templates umbenannt (`swiper` und `3-image` bereits mit 1.1/1.2,
  `bg` und `hero` jetzt).

- [ ] **2.5 — Checkbox-Definitionen in `block--swiper-3-image.xml` angleichen**
  Bei `loop`/`show_navigation`/`show_pagination` fehlen die `toggler`/`default_value`-
  Params, die alle anderen Blöcke haben.

- [x] **2.6 — `config`-Sektion (attr_class) in `block--swiper-3-image.xml` ergänzen**
  Das Template referenziert `content.attr_class`, das XML definiert die Property nicht
  (durch `|default('')` derzeit unschädlich).
  → `config`-Sektion mit `attr_class`-XInclude ergänzt (inkl. fehlendem
  `xmlns:xi`-Namespace am Root).

- [ ] **2.7 — Übrige Block-Assets ins Bundle verlagern**
  `block--swiper.js` liegt inzwischen im Bundle (`Resources/public/js/`, ausgeliefert
  unter `/bundles/sulublockswiper/`). Die Stylesheets sowie das JS der übrigen Blöcke
  (`block--swiper-bg.js`, `block--swiper-hero.js`, `block--swiper-3-image.js`,
  `block--swiper*.css`) werden weiterhin unter `/website/…` aus der App erwartet.
  → Aus der App ins Bundle übernehmen und Asset-Pfade in den Templates umstellen.
  **Teilweise erledigt:** Swiper.js 14.0.1 selbst liegt jetzt im Bundle
  (`Resources/public/vendor/swiper/`, MIT-Lizenz beigelegt); alle vier Templates
  laden es von dort statt `/website/…/swiper-lib.*`. Beim Rollout in den Projekten:
  App-Kopien von `swiper-lib.*` entfernen (sonst Doppel-Laden) und die drei
  App-Block-Scripts gegen Swiper 14 verifizieren. Offen: block-spezifisches CSS/JS.

- [ ] **2.8 — Bundle-`block--swiper.js` mit dem App-Original abgleichen**
  `Resources/public/js/block--swiper.js` wurde neu geschrieben (Standard-Swiper-Init
  auf Basis der `data-*`-Attribute), da das bisherige `/website/js/block--swiper.js`
  aus der App hier nicht zugänglich war. Eventuelle Besonderheiten des Originals
  (Breakpoints, Events, Custom-Verhalten) fehlen daher möglicherweise.
  → Mit dem App-Original vergleichen, Abweichungen ins Bundle übernehmen und das
  alte Script aus der App entfernen (sonst konkurrieren zwei Initialisierungen).

## 3. Barrierefreiheit (bewusst entscheiden)

- [x] **3.1 — `aria-hidden`-Strategie in `block--swiper-bg` dokumentieren oder vereinheitlichen**
  `block--swiper-bg.html.twig` setzt `aria-hidden="true"` auf den gesamten
  `swiper-wrapper` sowie Navigation/Pagination (`tabindex="-1"`) — Slider ist für
  Screenreader/Tastatur unsichtbar. Vertretbar bei rein dekorativen Hintergründen
  (Text liegt außerhalb des Wrappers), aber: dokumentieren, und die Linie ist
  uneinheitlich — die anderen Slider haben diese Attribute nicht.
  → Entschieden: Verhalten ist Absicht (dekorative Hintergründe). Als Kommentar im
  Template und README-Abschnitt „Accessibility“ dokumentiert. Übersetzbare
  aria-labels in 2.1 (4) aufgenommen; vollständiges Karussell-Muster als 3.2.

- [ ] **3.2 — Einheitliches Karussell-A11y-Muster für die inhaltstragenden Slider**
  `block--swiper`, `block--swiper-hero` und `block--swiper-3-image` folgen nicht dem
  W3C-ARIA-Karussell-Muster (`aria-roledescription="carousel"`, Slides mit
  `role="group"` und „Slide x von y“-Beschriftung, `aria-live` beim manuellen
  Blättern). Vieles davon kann Swiper.js über sein `a11y`-Modul erzeugen.
  → Zusammen mit der Verlagerung der Block-JS ins Bundle (2.7/2.8) umsetzen,
  da es primär Swiper-Konfiguration betrifft.

## 4. Doku, Tests, CI (niedrige Priorität)

- [x] **4.1 — README: `depa/sulu-block-content` als Requirement ergänzen**
  Ist Pflichtabhängigkeit (composer.json), fehlt aber unter „Requirements“; das
  `bundles.php`-Beispiel registriert das ContentBundle ebenfalls nicht.
  → Requirements um `sulu-block-content` und `sulu-block-hero` ergänzt,
  `bundles.php`-Beispiel registriert jetzt alle vier Bundles.

- [x] **4.2 — README: benötigte Bild-Formate dokumentieren**
  Templates setzen `content-image`, `card-lg`/`card-md`/`card-xs` und
  `1920x`/`1400x`/`1200x`/`992x`/`767x` voraus — nirgends dokumentiert.
  → README-Abschnitt „Required image formats“ mit Tabelle (Block → Formate →
  Verwendung/Breakpoints) ergänzt.

- [x] **4.3 — Konsistenz-Tests ergänzen**
  - XML-`<key>` == Dateiname für alle Block-XMLs
  - jeder Eintrag in `_slots.yaml` existiert als XML
  - jede referenzierte Kind-Block-Type ist auflösbar (paketintern oder deklarierte
    Abhängigkeit)
  → Umgesetzt als `tests/Unit/Config/BlockConfigConsistencyTest.php`; zusätzlich:
  jeder Block hat ein Twig-Template, und jeder `default-type` ist in den `<types>`
  seines Blocks deklariert. Externe Typen sind in `EXTERNAL_TYPES` mit
  Herkunftspaket gepflegt (`block--hero-content` dort als ungeklärt markiert,
  vgl. 1.9). `symfony/yaml` in require-dev.

- [x] **4.4 — CI: Lint-Steps ergänzen**
  `xmllint` (inkl. `--xinclude`) und `twig lint` als eigener Job/Step.
  → XML-Lint als eigener CI-Job `lint-xml`. Twig-Lint als PHPUnit-Test
  (`tests/Unit/Templates/TwigTemplateLintTest.php`, `twig/twig` in require-dev),
  läuft damit in der bestehenden Matrix — `bin/console lint:twig` bräuchte
  eine Symfony-App, die das Bundle nicht hat.

- [x] **4.5 — CI: PHP 8.4 in die Matrix aufnehmen**
  `composer.json` erlaubt `^8.2`, Matrix endet bei 8.3.
  → 8.4 ergänzt. Ergebnis beim ersten CI-Lauf (PR/Push auf main) prüfen.

- [x] **4.6 — CI: Composer-Auth für private VCS-Repos prüfen**
  Falls `sulu-block-helper`/`sulu-block-content` privat sind, braucht
  `composer install` in Actions ein Token — im Workflow ist keines konfiguriert.
  → Geprüft: CI auf `main` läuft grün (zuletzt Run 23, 2026-06-18), `composer
  install` funktioniert in Actions ohne Token — kein Handlungsbedarf. Erneut
  prüfen, falls die Abhängigkeits-Repos später auf privat umgestellt werden.

---

## Positiv (kein Handlungsbedarf)

- Schlanke, korrekte Extension-Ableitung von `AbstractBlockExtension`
- Konsequentes `declare(strict_types=1)`, PHPStan Level 8 über `src` und `tests`
- XInclude-Fragmente reduzieren Duplikation in den XMLs sinnvoll
- Zweisprachige Labels inkl. `info_text`
- `fetchpriority`/`is_decorative`/Retina-Optionen im `config_image`-Fragment
- `is_first → eager loading`-Muster in `block--swiper-bg-slide` (gut für LCP)
