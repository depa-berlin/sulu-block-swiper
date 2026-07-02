# Review — sulu-block-swiper

Vollständiges Code-Review vom 2026-07-02. Jeder Punkt ist als Checkbox abarbeitbar.
Stand: Branch `main` (eb845ae).

---

## 1. Funktionale Probleme (hohe Priorität)

- [ ] **`block--swiper` ignoriert seine komplette Slider-Konfiguration**
  `Resources/config/blocks/block--swiper.xml` (Zeilen 31–129) definiert `effect`, `loop`,
  `autoplay`, `autoplay_delay`, `speed`, `show_navigation`, `show_pagination` — aber
  `Resources/views/includes/blocks/block--swiper.html.twig` gibt kein einziges
  `data-*`-Attribut dafür aus und rendert Navigation/Pagination bedingungslos.
  Editor-Einstellungen sind auf der Website wirkungslos.
  → Template analog zu `block--swiper-bg.html.twig:14-23` verdrahten.

- [ ] **`block--swiper-3-image` ignoriert die Hälfte seiner Einstellungen**
  Das XML definiert `show_navigation`, `show_pagination`, `slides_per_view`, `centered`,
  aber `block--swiper-3-image.html.twig` gibt nur `data-loop` und `data-speed` aus und
  rendert keine `.swiper-button-*`- oder `.swiper-pagination`-Elemente.
  → Fehlende `data-*`-Attribute und Navigations-/Pagination-Markup ergänzen.

- [ ] **`speed` in `block--swiper-3-image.xml` ist `text_line` statt `number`**
  (`block--swiper-3-image.xml:30`) — Freitext landet ungeprüft in `data-speed`.
  Alle anderen Blöcke nutzen `type="number"` mit `min`/`max`.
  → Auf `number` umstellen (Achtung: Bestandsdaten prüfen).

- [ ] **Falscher Fallback bei `block--swiper-hero`**
  `block--swiper-hero.html.twig:20-21` nutzt `content.show_navigation ? 'true' : 'false'`
  ohne `?? true`-Fallback (anders als Zeilen 4–7). Bestandsinhalte mit `null` rendern
  `data-show-navigation="false"`, obwohl der XML-Default `true` ist.
  → `content.show_navigation ?? true` bzw. `content.show_pagination ?? true` verwenden.

- [ ] **`block--swiper-slide` ist verwaist**
  Kein Container referenziert den Typ (`block--swiper` erlaubt nur
  `block--swiper-slide-facts`), er steht nicht in `_slots.yaml`.
  → Entscheiden: entfernen oder als Slide-Typ in `block--swiper` registrieren.

- [ ] **Video-Medien erzeugen kaputte Ausgabe in `block--swiper-slide-facts`**
  `block--swiper-slide-facts.xml:20` erlaubt `types="image,video"`, das Template rendert
  aber immer `<img>` mit `image.thumbnails['content-image']`.
  → Entweder `types="image"` einschränken oder Video-Rendering im Template ergänzen.

- [ ] **Ungültiger `loading`-Wert „auto“**
  `block--swiper-slide-facts.xml:82`: Die Option `auto` ist kein gültiger Wert des
  HTML-`loading`-Attributs (nur `lazy`/`eager`).
  → Option entfernen (auch im Fragment prüfen, falls andere Pakete es nutzen).

- [ ] **Robustheit in `block--swiper-slide.html.twig`**
  - Zeile 3: `content.attr_class` ohne `|default('')` (einziges Template ohne Guard).
  - Zeile 4: `view.link.target` ohne Default — leeres `target=""` bzw. Fehler bei
    `strict_variables`.
  - Bei `target="_blank"` fehlt `rel="noopener"`.

- [ ] **Externe Abhängigkeit `block--hero-content` unklar**
  `block--swiper-hero.xml:20` referenziert `block--hero-content` — folgt nicht dem
  Namensschema von `sulu-block-content` (`block--content-*`) und kommt aus keinem der
  deklarierten Composer-Pakete erkennbar.
  → Herkunft verifizieren, Abhängigkeit in `composer.json` + README dokumentieren.

## 2. Inkonsistenzen zwischen den Blöcken (mittlere Priorität)

- [ ] **Übersetzungsschlüssel vereinheitlichen**
  `block--swiper-bg.html.twig:60-62` nutzt `website.page.swiper.autoplay.*`,
  `block--swiper-hero.html.twig:39-41` nutzt `website.swiper.autoplay.*`.
  → Auf einen Namespace einigen; erwägen, Translations im Bundle mitzuliefern.

- [ ] **Asset-Loading in `block--swiper.html.twig` angleichen**
  Lädt als einziges Template `swiper-lib.js` ohne `defer: true` und kein
  `swiper-lib.css` (nur ein eigenes `block--swiper.css`).

- [ ] **Autoplay-Toggle-Verhalten angleichen**
  `block--swiper-bg` rendert den Pause-Button nur bei `autoplay == true` (Zeile 56),
  `block--swiper-hero` rendert ihn immer.

- [ ] **`loop`-Shadowing beseitigen**
  In `block--swiper-3-image.html.twig:3`, `block--swiper-bg.html.twig:4` und
  `block--swiper-hero.html.twig:4` überschreibt `{% set loop = content.loop ?? true %}`
  Twigs reservierte `loop`-Variable. Funktioniert aktuell (Twig-`loop` gewinnt in der
  `for`-Schleife), ist aber eine Stolperfalle.
  → Umbenennen in z. B. `loop_enabled`.

- [ ] **Checkbox-Definitionen in `block--swiper-3-image.xml` angleichen**
  Bei `loop`/`show_navigation`/`show_pagination` fehlen die `toggler`/`default_value`-
  Params, die alle anderen Blöcke haben.

- [ ] **`config`-Sektion (attr_class) in `block--swiper-3-image.xml` ergänzen**
  Das Template referenziert `content.attr_class`, das XML definiert die Property nicht
  (durch `|default('')` derzeit unschädlich).

## 3. Barrierefreiheit (bewusst entscheiden)

- [ ] **`aria-hidden`-Strategie in `block--swiper-bg` dokumentieren oder vereinheitlichen**
  `block--swiper-bg.html.twig` setzt `aria-hidden="true"` auf den gesamten
  `swiper-wrapper` sowie Navigation/Pagination (`tabindex="-1"`) — Slider ist für
  Screenreader/Tastatur unsichtbar. Vertretbar bei rein dekorativen Hintergründen
  (Text liegt außerhalb des Wrappers), aber: dokumentieren, und die Linie ist
  uneinheitlich — die anderen Slider haben diese Attribute nicht.

## 4. Doku, Tests, CI (niedrige Priorität)

- [ ] **README: `depa/sulu-block-content` als Requirement ergänzen**
  Ist Pflichtabhängigkeit (composer.json), fehlt aber unter „Requirements“; das
  `bundles.php`-Beispiel registriert das ContentBundle ebenfalls nicht.

- [ ] **README: benötigte Bild-Formate dokumentieren**
  Templates setzen `content-image`, `card-lg`/`card-md`/`card-xs` und
  `1920x`/`1400x`/`1200x`/`992x`/`767x` voraus — nirgends dokumentiert.

- [ ] **Konsistenz-Tests ergänzen**
  - XML-`<key>` == Dateiname für alle Block-XMLs
  - jeder Eintrag in `_slots.yaml` existiert als XML
  - jede referenzierte Kind-Block-Type ist auflösbar (paketintern oder deklarierte
    Abhängigkeit)

- [ ] **CI: Lint-Steps ergänzen**
  `xmllint` (inkl. `--xinclude`) und `twig lint` als eigener Job/Step.

- [ ] **CI: PHP 8.4 in die Matrix aufnehmen**
  `composer.json` erlaubt `^8.2`, Matrix endet bei 8.3.

- [ ] **CI: Composer-Auth für private VCS-Repos prüfen**
  Falls `sulu-block-helper`/`sulu-block-content` privat sind, braucht
  `composer install` in Actions ein Token — im Workflow ist keines konfiguriert.

---

## Positiv (kein Handlungsbedarf)

- Schlanke, korrekte Extension-Ableitung von `AbstractBlockExtension`
- Konsequentes `declare(strict_types=1)`, PHPStan Level 8 über `src` und `tests`
- XInclude-Fragmente reduzieren Duplikation in den XMLs sinnvoll
- Zweisprachige Labels inkl. `info_text`
- `fetchpriority`/`is_decorative`/Retina-Optionen im `config_image`-Fragment
- `is_first → eager loading`-Muster in `block--swiper-bg-slide` (gut für LCP)
