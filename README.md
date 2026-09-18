# Technolab Dashboard — Aanwezigheids- & weekplanning

Een webapplicatie waarmee een team in één oogopslag ziet wie er per dag en per
week **aanwezig, afwezig, ziek of tijdelijk weg** is. Gebouwd tijdens mijn stage
bij **Technolab (Leiden)**, waar lessen op meerdere basisscholen tegelijk lopen
en het dagelijks handmatig navragen van aanwezigheid veel tijd kostte.

![PHP](https://img.shields.io/badge/PHP-8-777BB4?logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL%2FMariaDB-4479A1?logo=mysql&logoColor=white)
![Bootstrap](https://img.shields.io/badge/Bootstrap-5-7952B3?logo=bootstrap&logoColor=white)
![License](https://img.shields.io/badge/License-MIT-blue.svg)

## 🚀 Live demo

> _Binnenkort online — hier komt een link naar een demo (bijv. een gratis PHP-host
> zoals InfinityFree of een kleine VPS), zodat recruiters het kunnen uitproberen
> zonder het lokaal te installeren. Voeg hierboven ook een screenshot toe._

## ✨ Functionaliteit

- **Dagoverzicht** (`index.php`): per dag de status van elke medewerker, met
  kleurcodes en een zoekfunctie. Statussen worden automatisch aangemaakt op basis
  van de vaste werkdagen van een medewerker.
- **Weekoverzicht** (`week.php`): de hele werkweek in één tabel, met navigatie
  naar vorige/volgende weken.
- **Statussen**: Aanwezig, Afwezig, Ziek en *Tijdelijk Afwezig* (met een tijd
  "terug om…" die vanzelf terugspringt naar Aanwezig zodra de tijd voorbij is).
- **Medewerkerbeheer** (`add.php`, `update.php`): medewerkers toevoegen, bewerken
  en verwijderen, inclusief sectoren.
- **Detailweergave** (`details.php`): per medewerker e-mail, sector, BHV en werkdagen.
- **Rollen**: bezoekers zien een read-only overzicht; alleen een ingelogde **admin**
  (`login.php`) kan wijzigen.

## 🛠️ Techniek

| Onderdeel   | Keuze |
|-------------|-------|
| Backend     | PHP 8 (PDO, prepared statements) |
| Database    | MySQL / MariaDB |
| Frontend    | HTML, CSS, JavaScript, Bootstrap 5 |
| Auth        | Sessie-gebaseerd, wachtwoorden met `password_hash()` (bcrypt) |
| Beveiliging | CSRF-tokens op formulieren, output-escaping tegen XSS |

### Beveiliging
- Wachtwoorden worden **gehasht** opgeslagen (`password_hash` / `password_verify`) — nooit als platte tekst.
- Alle database-queries gebruiken **prepared statements** (geen SQL-injectie).
- Door gebruikers ingevoerde data wordt bij het tonen **ge-escaped** (`htmlspecialchars`) tegen XSS.
- Wijzig-acties zijn server-side afgeschermd met een **admin-check** en **CSRF-token**.

### Toegankelijkheid
- Statussen zijn herkenbaar aan **kleur én symbool**, dus ook bruikbaar voor kleurenblinde gebruikers.
- Zichtbare **toetsenbord-focus** en korte bevestigingen (toast) bij acties.

## 🚀 Lokaal draaien

Vereist: PHP 8+ en MySQL/MariaDB (bijvoorbeeld via **XAMPP**).

```bash
# 1. Zet het project in je webroot (bij XAMPP: C:\xampp\htdocs\...)

# 2. Maak een database aan en importeer het schema uit de map Database/
mysql -u root technolab-dashboard < Database/technolab-dashboard.sql

# 3. Controleer de databasegegevens in Database/db_connection.php

# 4. Open in de browser
#    http://localhost/php/technolab-dashboard/index.php
```

Log in via `login.php` met een admin-account uit de database.

## 🗓️ Zo werkt de weekplanning

- **Toekomstige dagen** volgen automatisch het vaste rooster van een medewerker en
  zijn niet bewerkbaar — je kunt je dus niet ziekmelden voor een dag die nog moet komen.
- **Vandaag en het verleden** zijn de echte registratie. Wijzig je later iemands
  rooster, dan verandert alleen de **toekomst** mee; het verleden blijft staan.

## 📄 Licentie

Uitgebracht onder de [MIT-licentie](LICENSE).

---

Gemaakt door **Safouane Lahoua** — stageproject bij Technolab, Leiden · [github.com/safouane070](https://github.com/safouane070)
