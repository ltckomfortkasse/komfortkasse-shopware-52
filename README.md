# Komfortkasse-Plugin für Shopware 5

Das Komfortkasse-Plugin für Shopware 5 (ab Version 5.2.17) automatisiert den Zahlungsabgleich für **Vorkasse, Rechnung und Nachnahme**. Komfortkasse ordnet eingehende Überweisungen automatisch den offenen Bestellungen Ihres Shops zu und setzt den Bestellstatus, sobald das Geld da ist. Das Plugin ergänzt die Anbindung des Shopware-Shops an Komfortkasse über die Shopware-API; den Abgleich mit dem Bankkonto übernimmt Komfortkasse.

[Komfortkasse für Shopware](https://komfortkasse.eu/shopsysteme/shopware/) · [Kostenlos registrieren](https://ssl.komfortkasse.eu/public/register.jsf) · [Installationsanleitung](https://komfortkasse.freshdesk.com/support/solutions/articles/14000114307) · [Hilfe & Dokumentation](https://komfortkasse.freshdesk.com/support/solutions/14000075022) · [Plugin im Shopware Store](https://store.shopware.com/de/ltc2606038817546/zahlungsabgleich-bankabgleich-vollautomatisch-fuer-vorkasse-rechnung-nachnahme.html)

Andere Shopware-Versionen: Für Shopware 6 gibt es das Repository [komfortkasse-shopware6](https://github.com/ltckomfortkasse/komfortkasse-shopware6), für Shopware vor Version 5.2.17 das Repository [komfortkasse-shopware](https://github.com/ltckomfortkasse/komfortkasse-shopware).

## Eckdaten

| Merkmal | Angabe |
|---|---|
| Shopsystem | [Shopware](https://www.shopware.com/de/) 5 |
| Unterstützte Versionen | Shopware ab Version 5.2.17 bis 5.7. Für ältere Versionen: [komfortkasse-shopware](https://github.com/ltckomfortkasse/komfortkasse-shopware), für Shopware 6: [komfortkasse-shopware6](https://github.com/ltckomfortkasse/komfortkasse-shopware6) |
| Zahlungsarten | Vorkasse (Vorauskasse, Banküberweisung), Rechnung (Kauf auf Rechnung), Nachnahme |
| Aufgabe des Plugins | Meldet neue Bestellungen sofort an Komfortkasse und ergänzt die Shopware-API um Dokumente mit Rechnungs-PDFs, Rückerstattungen aus Shopware-Dokumenten, die Suche nach Bestell- und Rechnungsnummern sowie die Zahlungsarten und Status des Shops. Bestellungen, Bestellstatus und Zahlungsstatus tauscht Komfortkasse über die Shopware-API aus. |
| Bezugsquelle | [Shopware Store](https://store.shopware.com/de/ltc2606038817546/zahlungsabgleich-bankabgleich-vollautomatisch-fuer-vorkasse-rechnung-nachnahme.html), Name „Zahlungsabgleich/Bankabgleich vollautomatisch für Vorkasse, Rechnung, Nachnahme“. Im Shopware-Backend heißt das Plugin „Komfortkasse - Zahlung per Banküberweisung“ (technischer Name `LtcKomfortkasse`). |
| Für wen | Betreiber von Shopware-5-Shops und Agenturen, die solche Shops betreuen |
| Kosten | Das Plugin ist kostenlos, auch im Shopware Store. Für Komfortkasse gelten die [Preise](https://komfortkasse.eu/preise/) auf komfortkasse.eu. |
| Anbieter | LTC Information Services GmbH, [komfortkasse.eu](https://komfortkasse.eu/) |

## Was ist Komfortkasse?

Komfortkasse ist ein Online-Dienst der LTC Information Services GmbH, der Zahlungen per Banküberweisung für Onlineshops automatisiert. Komfortkasse ruft die Zahlungseingänge vom Bankkonto ab, ordnet sie den offenen Bestellungen zu und meldet den Zahlungsstatus an den Shop zurück. Der Dienst richtet sich an Online-Händler in Deutschland, Österreich und der Schweiz.

Komfortkasse unterstützt die Zahlungsarten, bei denen das Geld per Überweisung kommt:

- [Vorkasse](https://komfortkasse.eu/loesungen/vorkasse/): Der Käufer überweist vor dem Versand. Komfortkasse prüft den Zahlungseingang und setzt den Bestellstatus im Shop automatisch.
- [Rechnung](https://komfortkasse.eu/loesungen/rechnung/): Die Ware wird sofort geliefert, der Käufer überweist danach. Komfortkasse überwacht die Zahlung und sendet bei Bedarf Zahlungserinnerungen und Mahnungen.
- [Nachnahme](https://komfortkasse.eu/loesungen/nachnahme/): Der Versanddienstleister kassiert bei der Zustellung und überweist den Betrag. Komfortkasse überwacht diese Überweisung.

### So funktioniert Komfortkasse mit Shopware 5

1. Komfortkasse liest die offenen Bestellungen über die Shopware-API in Ihren Komfortkasse-Account ein. Das Komfortkasse-Plugin meldet neue Bestellungen zusätzlich sofort.
2. Komfortkasse ist mit Ihrem Bankkonto verbunden ([unterstützte Banken](https://komfortkasse.eu/banken/)) und ordnet eingehende Zahlungen den Bestellungen zu.
3. Komfortkasse setzt die Bestellung in Shopware über die Shopware-API auf den gewählten Bestell- und/oder Zahlungsstatus, sobald die Zahlung eingegangen ist, oder auf den Status für stornierte Bestellungen, wenn keine Zahlung kommt.

Komfortkasse versendet außerdem Zahlungserinnerungen an Käufer, behandelt Teil- und Überzahlungen und wickelt Rückerstattungen ab. Alle Funktionen sind auf [komfortkasse.eu](https://komfortkasse.eu/) beschrieben.

## Funktionen des Komfortkasse-Plugins für Shopware 5

Das Komfortkasse-Plugin für Shopware 5 ergänzt die Shopware-API, über die Komfortkasse Bestellungen ausliest und den Bestell- und Zahlungsstatus setzt. Zahlungsarten und Status stellen Sie im Komfortkasse-Account ein; im Plugin gibt es nur drei Einstellungen (siehe unten).

- **Neue Bestellungen sofort melden:** Das Plugin meldet eine Bestellung sofort an Komfortkasse, wenn sie im Checkout abgeschlossen wird. Auch neue Bestellungen, die nicht über den Checkout des Shops entstehen (z. B. aus Shopgate), meldet das Plugin. Ohne diese Meldung liest Komfortkasse die Bestellung beim regelmäßigen Abgleich aus.
- **Übertragung nur per HTTPS:** Das Plugin sendet die Meldungen an `ssl.komfortkasse.eu`, nur per HTTPS mit Zertifikatsprüfung und ohne Weiterleitungen. Eine Meldung enthält nur die Bestell-ID bzw. Bestellnummer und die Adresse des Shops oder Subshops. Ist HTTPS in PHP nicht verfügbar, sendet das Plugin nichts, und Komfortkasse übernimmt die Bestellung beim regelmäßigen Abgleich.
- **Rechnungen und Gutschriften bereitstellen:** Das Plugin ergänzt die Shopware-API um die Dokumente der Bestellungen, beim Abruf eines einzelnen Dokuments mit der PDF-Datei. Aus Rechnungen übernimmt Komfortkasse die Rechnungsnummer; Gutschriften und Stornorechnungen verringern den offenen Betrag. Welche Dokumententypen dafür zählen, legen Sie im Komfortkasse-Account fest. Das Plugin erstellt selbst keine Dokumente.
- **Bestellung zu Bestell- oder Rechnungsnummer finden:** Das Plugin ermittelt für Komfortkasse die Bestellung zu einer Bestell-ID, Bestellnummer oder Rechnungsnummer, auf Wunsch eingeschränkt auf bestimmte Dokumententypen und Subshops.
- **Zahlungsarten und Status auslesen:** Das Plugin liefert Komfortkasse die Zahlungsarten, Bestellstatus und Zahlungsstatus des Shops, auch selbst angelegte Status. Im Komfortkasse-Account wählen Sie diese deshalb nach Namen aus, statt IDs einzutragen.
- **Rückerstattungen aus Shopware:** Legen Sie in Shopware zu einer Bestellung ein Dokument des Typs an, den Sie in der Einstellung „Dokumenten-Typ für Rückerstattungen“ gewählt haben, führt Komfortkasse daraus die Rückerstattung aus, sofern Rückerstattungen aus dem Shopsystem im Komfortkasse-Account aktiviert sind. Das Plugin stellt dafür die Dokumente dieses Typs aus den letzten sieben Tagen bereit.
- **Bestellpositionen stornieren (Shopware ERP):** Storniert Komfortkasse eine Bestellung, storniert das Plugin auf Wunsch auch die Bestellpositionen in Shopware ERP (früher Pickware), sowohl in älteren Pickware-Versionen als auch ab Shopware ERP 6.0.0.
- **Subshops:** Die Meldungen enthalten die Adresse des jeweiligen Subshops, und die Suche nach Bestell- und Rechnungsnummern lässt sich auf Subshops einschränken. Die Einstellungen des Plugins gelten je Shop.
- **Vorhandene Zahlungsarten nutzen:** Das Plugin arbeitet mit den Zahlungsarten des Shops, z. B. der Shopware-Zahlungsart „Vorkasse“ (`prepayment`), und fügt im Checkout keine eigene Zahlungsart hinzu.

### Zusätzliche Ressourcen der Shopware-API

Das Komfortkasse-Plugin ergänzt die REST-API von Shopware 5 (`/api/`) um die folgenden Ressourcen. Wie alle Ressourcen der Shopware-API sind sie nur mit Benutzername und API-Schlüssel erreichbar.

| Ressource | Inhalt |
|---|---|
| `Document` | Dokumente der Bestellungen (z. B. Rechnungen und Gutschriften); ein einzelnes Dokument mit der PDF-Datei (Base64-kodiert, Feld `pdf_base64`) |
| `Refund` | Dokumente des Typs aus „Dokumenten-Typ für Rückerstattungen“ aus den letzten sieben Tagen |
| `OrderId` | Bestellung zu einer Bestell-ID oder Bestellnummer, optional je Subshop |
| `InvoiceOrderId` | Bestellung zu einer Rechnungsnummer, optional je Dokumententyp und Subshop |
| `OrderStatus` | Bestellstatus des Shops |
| `PaymentStatus` | Zahlungsstatus des Shops |
| `PaymentMeans` | Zahlungsarten des Shops |
| `LtcKomfortkasseVersion` | installierte Version des Plugins |

### Einstellungen des Komfortkasse-Plugins in Shopware 5

Die Einstellungen finden Sie in der Konfiguration des Komfortkasse-Plugins im Shopware-Backend. Sie gelten je Shop bzw. Subshop.

| Einstellung | Name | Voreinstellung | Wirkung |
|---|---|---|---|
| Plugin aktivieren | `active` | an | Schaltet die Sofortmeldungen neuer Bestellungen und das Stornieren von Bestellpositionen ein |
| Bestellpositionen stornieren (Shopware ERP) | `cancelDetail` | aus | Storniert die Bestellpositionen, wenn Komfortkasse eine Bestellung storniert. Nur relevant mit Shopware ERP (früher Pickware). |
| Dokumenten-Typ für Rückerstattungen | `refundDocumentType` | Dokumententyp mit der ID 3 | Aus Dokumenten dieses Typs erstellt Komfortkasse Rückerstattungen. Nur relevant, wenn Rückerstattungen aus dem Shopsystem im Komfortkasse-Account aktiviert sind. |

## Voraussetzungen

- Shopware ab Version 5.2.17 (Shopware 5)
- Ein Komfortkasse-Account ([kostenlos registrieren](https://ssl.komfortkasse.eu/public/register.jsf))
- Ein Bankkonto, das Sie mit Komfortkasse verbinden ([unterstützte Banken](https://komfortkasse.eu/banken/)), oder ein integriertes Bankkonto von Komfortkasse
- Ein Shopware-Benutzer mit API-Zugang für Komfortkasse, am besten in einer Rolle mit allen Rechten wie `local_admins`. Eine eigene Rolle braucht mindestens `customer`: read, detail und `order`: read, update; für die Option „Bei Stornierung Bestand korrigieren“ im Komfortkasse-Account zusätzlich `article`: read, save.
- Für die Sofortmeldungen: PHP mit OpenSSL-Erweiterung und `allow_url_fopen`, außerdem ausgehende HTTPS-Verbindungen vom Server zu `ssl.komfortkasse.eu`
- Optional: Shopware ERP (früher Pickware), wenn bei einer Stornierung auch die Bestellpositionen storniert werden sollen

## Installation in Shopware 5

Die Installation besteht aus vier Schritten: das Plugin aus dem Shopware Store installieren, einen API-Benutzer in Shopware anlegen, die Zugangsdaten und Einstellungen im Komfortkasse-Account hinterlegen und den Shop verbinden.

1. **Plugin installieren:** Das Plugin [„Zahlungsabgleich/Bankabgleich vollautomatisch für Vorkasse, Rechnung, Nachnahme“](https://store.shopware.com/de/ltc2606038817546/zahlungsabgleich-bankabgleich-vollautomatisch-fuer-vorkasse-rechnung-nachnahme.html) kostenlos aus dem Shopware Store laden, im Shopware-Backend installieren und aktivieren. Die Einstellung „Plugin aktivieren“ ist voreingestellt.
2. **API-Benutzer anlegen:** Im Shopware-Backend in der Benutzerverwaltung einen neuen Benutzer anlegen, z. B. `komfortkasse`. „Aktiviert“ und „API-Zugang“ ankreuzen, den API-Schlüssel kopieren und den Benutzer einer Rolle mit ausreichenden Rechten zuordnen, z. B. `local_admins`. Danach unter Einstellungen → Caches/Performance den Shop-Cache leeren.
3. **Einstellungen im Komfortkasse-Account:** Im Händlerbereich unter Einstellungen → Shops beim Shop im Bereich „Einstellungen für API-Zugriff“ den Benutzernamen und den API-Schlüssel eintragen und speichern. Komfortkasse liest dann Zahlungsarten, Subshops und Status aus dem Shop aus. Die genutzten Zahlungsarten (Vorkasse, Rechnung, Nachnahme) aktivieren und je Zahlungsart die Shopware-Zahlungsart sowie die Bestell- und Zahlungsstatus für offene, bezahlte, stornierte und rückerstattete Bestellungen wählen.
4. **Shop verbinden:** Im Komfortkasse-Account im Aktionsmenü ≡ die Aktion „Verbindung herstellen“ wählen.

Ausführliche Anleitung mit Screenshots: [Anleitung für Shopware 4 und 5](https://komfortkasse.freshdesk.com/support/solutions/articles/14000114307), vorher [Erste Schritte: Einrichtung und Verbindung zum Shop](https://komfortkasse.freshdesk.com/support/solutions/articles/14000114082).

### Vorkasse in Shopware 5 vorbereiten

Für Vorkasse braucht Shopware die aktive Zahlungsart „Vorkasse“ (Einstellungen → Zahlungsarten); bei einer neuen Shopware-Installation ist sie meist schon aktiv. Versendet Komfortkasse die Zahlungsinformationen an Ihre Kunden, entfernen Sie die Bankverbindung aus der Bestellbestätigung: In der E-Mail-Vorlage `sORDER` (Einstellungen → eMail-Vorlagen) den Text zwischen `{if $additional.payment.name == "prepayment"}` und `{/if}` löschen, im Plaintext und im HTML-Text sowie in allen Übersetzungen.

### Rückerstattungen aus Shopware 5 (optional)

Komfortkasse kann Rückerstattungen ausführen, die Sie in Shopware als Dokument anlegen. Voraussetzung ist eine abgeschlossene Legitimierung im Komfortkasse-Account.

1. Im Komfortkasse-Account in den Allgemeinen Einstellungen die Erstellung von Rückerstattungen aus dem Shopsystem aktivieren.
2. In der Konfiguration des Komfortkasse-Plugins in Shopware den „Dokumenten-Typ für Rückerstattungen“ wählen. Empfohlen ist ein eigener Dokumententyp nur für diesen Zweck, z. B. „Rückerstattung Überweisung“.
3. Für eine Rückerstattung in Shopware zur Bestellung ein Dokument dieses Typs erstellen.

Komfortkasse prüft jede Rückerstattung, um unbeabsichtigte oder doppelte Rückerstattungen zu vermeiden; standardmäßig nur bei einem vorhandenen Zahlungseingang. Testen Sie den Ablauf vor dem ersten Einsatz.

### Fehlersuche bei der Verbindung zu Shopware 5

Kommt die Verbindung nicht zustande, prüfen Sie die Shopware-API im Browser: `https://<ihr-shop>/api/orders` aufrufen und mit dem Benutzernamen und dem API-Schlüssel als Passwort anmelden. Erscheint „Missing or invalid auth“ oder immer wieder die Passwortabfrage, prüfen Sie Benutzername und API-Schlüssel (Groß- und Kleinschreibung, Leerzeichen), ob Benutzer und API-Zugang aktiviert sind, ob die Rolle ausreichende Rechte hat und ob der Shop-Cache geleert wurde. Weitere Hinweise stehen im Shopware-Log (Ordner `logs` im Shopware-Verzeichnis).

## Häufige Fragen

### Was kostet das Komfortkasse-Plugin für Shopware 5?

Das Plugin ist kostenlos. Für Komfortkasse selbst fällt eine prozentuale Gebühr pro Zahlungseingang an, ohne Grundgebühr und ohne Einrichtungsgebühr. Die aktuellen Sätze stehen auf der [Preisseite](https://komfortkasse.eu/preise/). Neue Accounts können Komfortkasse kostenlos testen.

### Ist das Komfortkasse-Plugin eine Zahlungsart für Shopware 5?

Nein. Das Komfortkasse-Plugin fügt im Checkout keine Zahlungsart hinzu. Es arbeitet mit den Zahlungsarten, die der Shopware-Shop bereits nutzt, zum Beispiel der Shopware-Zahlungsart „Vorkasse“ (`prepayment`). Welche Zahlungsarten des Shops Komfortkasse für Vorkasse, Rechnung und Nachnahme berücksichtigt, wählen Sie im Komfortkasse-Account; je Zahlungsart sind auch mehrere Zahlungsarten des Shops möglich.

### Was passiert, wenn die Bestellnummer fehlt oder der Betrag abweicht?

Komfortkasse ordnet Zahlungen fehlertolerant zu. Die Zuordnung funktioniert auch, wenn die Bestellnummer im Verwendungszweck fehlt, der Name des Zahlers nicht zum Besteller passt oder der Betrag abweicht. Auch Teil- und Überzahlungen behandelt Komfortkasse.

### Was passiert, wenn ein Käufer nicht zahlt?

Komfortkasse sendet automatisch Zahlungserinnerungen an den Käufer. Bleibt die Zahlung aus, setzt Komfortkasse die Bestellung in Shopware auf den Bestell- und/oder Zahlungsstatus, den Sie dafür gewählt haben. Bei Vorkasse wird die Bestellung storniert; bei Rechnung und Nachnahme wird sie nicht storniert, sondern als überfällig markiert. Für die Mahnstufen und das Inkasso können Sie zusätzlich eigene Zahlungsstatus wählen; bei Nachnahme gibt es keine Mahnungen und kein Inkasso.

### Welche Banken unterstützt Komfortkasse?

Komfortkasse arbeitet mit Ihrem eigenen Bankkonto oder mit einem integrierten Bankkonto von Komfortkasse. Welche Banken sich anbinden lassen, zeigt die Liste der [unterstützten Banken](https://komfortkasse.eu/banken/).

### Welches Komfortkasse-Plugin brauche ich für Shopware 6 oder ältere Shopware-Versionen?

Dieses Repository gilt für Shopware 5 ab Version 5.2.17. Für Shopware 6 gibt es eine eigene Komfortkasse-App im Shopware Store; ihr Quellcode liegt im Repository [komfortkasse-shopware6](https://github.com/ltckomfortkasse/komfortkasse-shopware6), die Einrichtung beschreibt die [Anleitung für Shopware 6](https://komfortkasse.freshdesk.com/support/solutions/articles/14000121818). Für Shopware-Versionen vor 5.2.17 ist das Repository [komfortkasse-shopware](https://github.com/ltckomfortkasse/komfortkasse-shopware) gedacht. Alle Shopware-Versionen stehen auf der Seite [Komfortkasse für Shopware](https://komfortkasse.eu/shopsysteme/shopware/).

### Wie liest Komfortkasse die Bestellungen aus Shopware 5 aus?

Komfortkasse liest die Bestellungen über die Shopware-API aus und setzt darüber auch den Bestell- und Zahlungsstatus. Dafür legen Sie in Shopware einen Benutzer mit API-Zugang an und tragen Benutzername und API-Schlüssel im Komfortkasse-Account ein. Das Komfortkasse-Plugin meldet neue Bestellungen zusätzlich sofort und ergänzt die API um Dokumente mit Rechnungs-PDFs, Rückerstattungen, die Suche nach Bestell- und Rechnungsnummern sowie die Zahlungsarten und Status des Shops. Ohne das Plugin muss Komfortkasse den Shop in kurzen Abständen nach neuen Bestellungen abfragen, wenn Komfortkasse die Zahlungsinformationen versendet; das belastet den Shop und wird nicht empfohlen.

### Gibt es Komfortkasse auch für andere Shopsysteme?

Ja. Komfortkasse gibt es unter anderem für Shopify, WooCommerce, PrestaShop, Gambio und xt:Commerce. Alle Systeme stehen in der [Übersicht der Shopsysteme](https://komfortkasse.eu/shopsysteme/), weitere Plugin-Repositories unter [github.com/ltckomfortkasse](https://github.com/ltckomfortkasse).

## Support

Fragen zur Einrichtung beantworten die [Hilfe-Seiten](https://komfortkasse.freshdesk.com/support/solutions/14000075022) und der [Komfortkasse-Support](https://komfortkasse.eu/kontakt/).

## English

**Komfortkasse plugin for Shopware 5.** Komfortkasse automatically matches incoming bank transfers to open orders in your shop and updates the order status as soon as the payment has arrived. It supports prepayment by bank transfer (Vorkasse), invoice and cash on delivery. Komfortkasse is a service of LTC Information Services GmbH for online merchants in Germany, Austria and Switzerland.

The plugin supports Shopware 5.2.17 or later (Shopware 5). For Shopware 6 use [komfortkasse-shopware6](https://github.com/ltckomfortkasse/komfortkasse-shopware6), for Shopware versions before 5.2.17 use [komfortkasse-shopware](https://github.com/ltckomfortkasse/komfortkasse-shopware). It does not add a payment method to the checkout; it works with the shop's existing payment methods (e.g. `prepayment`). Komfortkasse reads orders and updates the order and payment status through the Shopware API; the plugin adds instant notifications for new orders (HTTPS only) and API resources for order documents including invoice PDFs, refunds created from Shopware documents, order lookup by order or invoice number, and the shop's payment methods and states. The plugin is free of charge; Komfortkasse itself is a paid service ([pricing](https://komfortkasse.eu/preise/)).

Installation: install the plugin free of charge from the [Shopware Store](https://store.shopware.com/de/ltc2606038817546/zahlungsabgleich-bankabgleich-vollautomatisch-fuer-vorkasse-rechnung-nachnahme.html), create a Shopware user with API access, enter the user name and API key in your Komfortkasse account and connect the shop there. More information: [Komfortkasse for Shopware](https://komfortkasse.eu/shopsysteme/shopware/) · [Installation guide (English)](https://komfortkasse.freshdesk.com/support/solutions/articles/14000152606)
