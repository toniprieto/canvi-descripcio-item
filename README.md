# Canvi descripcions ítems ALMA

Codi per fer canvis dels camps de descripció d'ítems a ALMA. Processa les descripcions i si compleixen certs patrons definits a `src/conversor/ItemDescription.php` la modifica i mou contingut a la nota pública.

## Instal·lar

Per instal·lar les dependències feu:

`composer install`

## Configurar

Copia l'arxiu `src/config.php.default` a `src/config.php` i inclou la API Key per l'API d'ALMA.

Cal permisos per:

- Bibs (lectura i escriptura)
- Configuration (lectura)

## Executar

Es pot llançar amb diferents modes:

- Llançar sobre un set d'ítems:

`php ./src/main.php -i <id_set_items>`

- Llançar sobre un set de bibliogràfics:

`php ./src/main.php -i <id_set_items>`

- Llançar sobre un únic bibliogràfic

`php ./src/main.php -m <mms_id_bibliografic>`

Per defecte no aplica cap canvi i genera un CSV a la carpeta logs amb les dades que ha processat. Per aplicar els canvis cal afegir el paràmetre -a:

`php ./src/main.php -i <id_set_items> -a`

## Proves (incloure nous patrons)

Conté un joc de proves per provar els patrons que es fan servir per detectar i canviar les descripcions modificables. Es poden incloude nous patrons a:

`src/conversor/ItemDescription.php`

i provar-les afegint més jocs de proves a:

`tests/conversor/ItemDescription.php`

Per provar els jocs de proves fer:

`./vendor/bin/phpunit`

