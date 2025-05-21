# Com començo?

![PHP 8.1](https://img.shields.io/badge/PHP-8.1-777BB4?logo=php)
![Laravel 8](https://img.shields.io/badge/Laravel-8-FF2D20?logo=laravel)
![MariaDB 10](https://img.shields.io/badge/MariaDB-10-003545?logo=mariadb)

## Creació de l'entorn

L'entorn es pot crear via [Dev Containers](#opció-1--dev-containers), [Laravel Sail](#opció-2--laravel-sail), o [directament](#opció-3--directe).

### Opció 1 — Dev Containers

![Linux] ![Windows+WSL2] ![Docker]

#### Requisits:

- Docker:
  - Windows: [Docker Desktop 2.0+](https://www.docker.com/products/docker-desktop) + [WSL2](https://aka.ms/vscode-remote/containers/docker-wsl2)
  - Linux: [Docker CE/EE 18.06+](https://docs.docker.com/install/#supported-platforms) + [Docker Compose 1.21+](https://docs.docker.com/compose/install)
- [Visual Studio Code](https://code.visualstudio.com/) + [Dev Containers extension](https://marketplace.visualstudio.com/items?itemName=ms-vscode-remote.remote-containers)

#### Passos:

1. Pots clonar el repositori i obrir directament l'entorn amb Visual Studio Code:

   [![Open in Dev Containers](https://img.shields.io/static/v1?label=Dev%20Containers&message=Obre&color=blue&logo=visualstudiocode)](https://vscode.dev/redirect?url=vscode://ms-vscode-remote.remote-containers/cloneInVolume?url=https://github.com/AssociacioFemPinya/fempinya3)

   o pots fer-ho manualment:

   1. Clona aquest repositori al teu ordinador:
      ```shell
      git clone https://github.com/AssociacioFemPinya/fempinya3.git
      ```
   2. Obre el projecte a Visual Studio Code i reobre'l com un container des de la paleta d'ordres (`F1` o `Ctrl+Shift+P`)':
      ```
      > Dev Containers: Reopen in Container
      ```

2. Continua amb la [configuració inicial](#configuració-inicial)
   > Pots executar ordres de Laravel obrint un terminal des de Visual Studio Code (Menú `Terminal → New Terminal`). 

### Opció 2 — Laravel Sail
![Linux] ![Windows+WSL2] ![Docker]

#### Requisits:

- Docker:
  - Windows: [Docker Desktop 2.0+](https://www.docker.com/products/docker-desktop) + [WSL2](https://aka.ms/vscode-remote/containers/docker-wsl2)
  - Linux: [Docker CE/EE 18.06+](https://docs.docker.com/install/#supported-platforms) + [Docker Compose 1.21+](https://docs.docker.com/compose/install)

#### Passos:

1. Clona aquest repositori al teu ordinador:
   ```shell
   git clone https://github.com/AssociacioFemPinya/fempinya3.git
   cd fempinya3
   ```

2. Instal·la Laravel Sail i les dependències de l'app:
   - Linux: `./scripts/sail-initial-setup`
   - Windows: `.\scripts\sail-initial-setup`

3. Engega els contenidors:
   - Linux: `./vendor/bin/sail up -d`
   - Windows: `docker compose up -d`

4. Continua amb la [configuració inicial](#configuració-inicial)
   > Pots executar ordres de Laravel mitjançant `sail` a Linux o `docker compose` a Windows:
   > - Linux: `./vendor/bin/sail <ordre>`
   > - Windows: `docker compose exec app <ordre>`

### Opció 3 — Directe
![Linux] ![Windows]

#### Requisits:

- [PHP 8.1](https://www.php.net/) + `pdo_mysql`, `mbstring`, `exif`, `pcntl`, `bcmath`, `gd`, `zip` i `curl`
- [Composer](https://getcomposer.org/)
- [MariaDB Server 10](https://mariadb.org/)

#### Passos:

1. Clona aquest repositori al teu ordinador:
   ```shell
   git clone https://github.com/AssociacioFemPinya/fempinya3.git
   cd fempinya3
   ```

2. Crea l'arxiu `.env` a partir de l'arxiu `.env.example`, ajustant:
   ```ini
   DB_HOST=localhost
   ```

3. Crea 2 bases de dades a MariaDB: `fempinya` i `testing`:
   ```sql
   CREATE DATABASE `fempinya`;
   GRANT ALL PRIVILEGES ON `fempinya`.* TO '$DB_USERNAME'@'%';
   CREATE DATABASE `testing`;
   GRANT ALL PRIVILEGES ON `testing`.* TO '$DB_USERNAME'@'%';
   ```

4. Instal·la les dependències:
   ```shell
   composer install
   ```

5. Inicia el servidor de desenvolupament:
   ```shell
   php artisan serve
   ```

6. Continua amb la [configuració inicial](#configuració-inicial)

## Configuració inicial

Una vegada [creat l'entorn](#creació-de-lentorn), pots accedir a FemPinya a [http://localhost:8000](http://localhost:8000).

Per configurar l'app:

1. Prepara la base de dades:
   ```shell
   ./scripts/app-initial-setup
   ```

2. Afegeix una Colla a la BBDD i crea'n un usuari relacionat:
   ```shell
   php artisan fempinya:create-colla demo admin@example.com Un.2.tres --super-admin
   ```

   - Això et permetrà iniciar sessió amb l'usuari `admin@example.com` i la contrasenya `Un.2.tres`.
   
   - La flag `--super-admin` és optativa; fa que l'usuari sigui Super-Admin (Accés total dins l'aplicació)


3. Emplena, si ho desitges, la Colla amb dades fake:
   ```shell
   php artisan fempinya:create-demo 1
   ```

## Executar tests

Pots executar els tests amb:

```shell
php artisan test
```

## Estructura d'arxius

Com està organitzat el repositori?

`...`

## Guía de contribució

Veure `CONTRIBUTING.md`

## Bot de Telegram

### Requisits

Per fer servir el bot de Telegram s'han de configurar dues coses: 

- l'ID del Bot de Telegram
- L'URL de la nostra app a través de la qual s'interactuarà amb el Bot

Per tal d'obtenir l'ID del bot de Telegram, primer has de crear-ne un. Fes-ho parlant amb el [@BotFather](https://t.me/BotFather). Un cop creat, rebràs una token que has de posar a l'arxiu `.env` a la variable `TELEGRAM_TOKEN`. 

D'altra banda, per tal de configurar la URL, hauràs d'emplenar la variable `TELEGRAM_CALLBACK_PATH` de l'arxiu `.env` amb el path on vols desplegar el Bot de Telegram. Aquest path pot ser qualsevol valor que permeten les URL's. S'aconsella fer servir una cadena de caràcters aleatòria perquè sigui més difícil que algu externa a l'aplicació interactui amb el BOT.
D'aquesta manera s'aconsegueix la `URL_BOTMAN` que és la suma del domini + TELEGRAM_PATH

### Enregistrament de Bot

Pots fer-ho des de la línia de comandes, seguint les instruccions:

```bash
docker-compose exec app php artisan botman:telegram:register
```

Alternativament, pots fer servir les següents URLs per gestionar el webhook del teu bot:

- Veure configuració del bot
```
https://api.telegram.org/bot{TELEGRAM_TOKEN}/getWebhookInfo
```

- Configurar la URL del webhook
```
https://api.telegram.org/bot{TELEGRAM_TOKEN}/setWebhook?url={URL_BOTMAN}
```

- Eliminar el webhook
```
https://api.telegram.org/bot{TELEGRAM_TOKEN}/deleteWebhook
```

### Exemple

Tenint en compte les següents variables:

`TELEGRAM_TOKEN=1234567890:AAFZLljjihIVaKlRCHv1iTw7TN5gdlVsPUk`

`TELEGRAM_CALLBACK_PATH=laUrlQueVulgui`

La Url de la nostra app per interactuar amb el bot seria (per tant, la variable URL_BOTMAN utilizada en l'aparta anterior):

URL_BOTMAN=`https://domini.cat/laUrlQueVulgui`

I seguint les URL's per gestionar el webhook del teu bot:

```
https://api.telegram.org/bot1234567890:AAFZLljjihIVaKlRCHv1iTw7TN5gdlVsPUk/getWebhookInfo
https://api.telegram.org/bot1234567890:AAFZLljjihIVaKlRCHv1iTw7TN5gdlVsPUk/setWebhook?url=https://domini.cat/laUrlQueVulgui
https://api.telegram.org/bot1234567890:AAFZLljjihIVaKlRCHv1iTw7TN5gdlVsPUk/deleteWebhook
```


### Testing

#### Testejar el bot de telegram a través de web

Pots interactuar amb el bot de telegram de manera local, sense haver de registrar el bot, pots fer-ho a través de https://localhost:8000/{TELEGRAM_PATH}/tinker.

#### Testejar el bot de telegram a Telegram

Si vols provar el bot a Telegram, has de crear una URL pública amb protocol SSL per poder enregistrar el bot.

Per fer-ho pots fer servir [`ngrok`](https://ngrok.io) per crear un túnel HTTPS. Un cop fet, pots fer servir la URL resultant `${ngrok_url}/{TELEGRAM_PATH}` com a `URL_BOTMAN` i seguir el procés habitual d'enregistrament.



[Linux]: https://img.shields.io/badge/Linux-FCC624?logo=linux&logoColor=000
[Windows]: https://img.shields.io/badge/Windows-0078D6?logo=windows&logoColor=fff
[Windows+WSL2]: https://img.shields.io/static/v1?label=Windows&message=WSL2&color=FCC624&logo=windows&labelColor=0078D6
[Docker]: https://img.shields.io/badge/Docker-2496ED?logo=docker&logoColor=fff
