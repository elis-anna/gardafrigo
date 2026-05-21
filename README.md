# Garda Frigor WordPress

Ambiente locale WordPress per il nuovo sito Garda Frigor, con tema child Avada brandizzato Cawipa Elise.

## Avvio locale

1. Copia `.env.example` in `.env` e compila password/admin locali.
2. Inserisci la licenza Avada in `.env` come `AVADA_LICENSE_KEY`.
3. Metti il pacchetto Avada scaricato dall'account ThemeFusion/Envato in `avada-packages/Avada.zip`.
4. Avvia:

```sh
docker compose up -d
./scripts/bootstrap-wordpress.sh
```

Il sito locale risponde su `http://localhost:8080`, salvo porta diversa in `.env`.

## Tema

Il tema custom e' `wp-content/themes/gardafrigo-cawipa`. E' un child theme Avada: contiene branding, stili base, contenuti starter e una struttura pronta per mappare il prebuilt Avada Energy.

## Migrazione contenuti

La prima ricognizione del sito live e' in `docs/content-migration.md`. Le quattro aree principali diventano `solutions`:

- Condizionamento
- Riscaldamento
- Trattamento aria
- Refrigerazione

La migrazione completa dei contenuti verra' fatta dopo l'import del prebuilt Avada Energy, cosi' possiamo sostituire testi e immagini direttamente nel layout definitivo.
