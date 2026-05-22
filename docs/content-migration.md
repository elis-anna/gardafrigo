# Ricognizione contenuti Garda Frigor

Fonte live: https://www.gardafrigor.it/

## Navigazione rilevata

- Home
- Chi siamo
- Servizi
- Marchi
- Case history
- News
- Contatti
- Privacy policy
- Cookies policy

## Hero e posizionamento

Titolo attuale: "Impianti di climatizzazione, riscaldamento e trattamento aria".

Messaggio da portare nel template Energy: Garda Frigor e' il partner per installazione, assistenza e manutenzione di impianti di condizionamento, riscaldamento, trattamento aria e refrigerazione, attivo dal 1993 nelle province di Brescia, Mantova, Bergamo, Verona e Trento.

## Solutions

La voce "Solutions" del prebuilt Avada Energy andra' rimappata cosi':

- Condizionamento: installazione e manutenzione impianti di condizionamento.
- Riscaldamento: installazione e manutenzione impianti di riscaldamento.
- Trattamento aria: installazione e manutenzione impianti di trattamento aria.
- Refrigerazione: installazione e manutenzione impianti di refrigerazione.

## Sezioni secondarie da migrare

- Aziende: hotel, ristorante, negozio, ufficio, industria, centro commerciale.
- Privati: casa e appartamento.
- Manutenzione: programmi su misura per mantenere efficienza degli impianti.
- Certificazioni: riferimento Bureau Veritas / Regolamento CE 303/2008 / ACCREDIA-RT29.
- Marchi: fornitori autorizzati dei migliori brand del settore.
- Assistenza: telefono 0365 522645 e richiesta assistenza online.
- Newsletter.

## Contatti aziendali

Garda Frigor Srl  
Via Fibbia, 7 - 25089 Villanuova sul Clisi (Brescia)  
Tel. 0365 522645  
info@gardafrigor.it  
P.IVA 01712420981

## Import locale del 2026-05-22

Script: `scripts/import-gardafrigor-live-pages.php`

Sono state importate 80 pagine HTML dal sito live dentro WordPress locale. I contenuti sono stati inseriti in blocchi Avada/Fusion e ogni pagina conserva in meta `_gardafrigor_source_url` la URL sorgente, utile per impostare redirect e controlli.

Layout applicati:

- Servizi e sottoservizi: layout Avada "Energy / Solutions / Hydropower Plants".
- Chi siamo: layout Avada "Energy / Company".
- Contatti: layout Avada "Energy / Contact".
- Case history e news: layout Avada "Energy / Case / A Secure Hydropower Supply Chain".

Pagine principali importate:

- Home: `/`
- Chi siamo: `/chi-siamo/`
- Servizi: `/servizi/`
- Marchi: `/marchi/`
- Case history: `/case-history/`
- News: `/news/`
- Contatti: `/contatti/`

Rami servizi importati:

- `/impianti-condizionamento/`
- `/impianti-riscaldamento/`
- `/impianti-trattamento-aria/`
- `/impianti-refrigerazione/`
- relative pagine casa, commerciali, manutenzione e sottoservizi.

Pagine non importate come pagine singole e da decidere:

- Paginazioni archivio news: `/news/pagina_P1.html`, `/news/pagina_P2.html`, `/news/pagina_P3.html`, `/news/pagina_P4.html`. Sono viste archivio duplicate: conviene redirect verso `/news/`.
- Alias news duplicate: URL con `_P1_` dei primi articoli gia importati senza quel segmento. Conviene redirect verso la rispettiva news importata.
- Alias refrigerazione: `/refrigerazione.html` duplica `/impianti-refrigerazione/`. Conviene redirect verso `/impianti-refrigerazione/`.
- PDF privacy e cookie: `/privacy-policy.pdf`, `/privacy-cookies-policy.pdf`. Da decidere se caricarli in Media Library o ricrearli come pagine privacy.
- PDF normativi collegati dalle news: file in `/pdfProdotti/`. Da decidere se importarli come allegati o lasciare link esterni temporanei.
- Newsletter/area riservata: non e' emersa come pagina HTML raggiungibile nella scansione pubblica; da valutare come funzionalita separata.
