Example
======

## Install dependencies

  composer install

## Initialize schema

  ./vendor/bin/dbtk-schema-loader schema:load test/schema.xml mysql://username:password@localhost/evolver
  
## Configure

Create a `.env` file that defines the connection details to your MySql server. Example contents:

```ini
EVOLVER_PDO_USERNAME="username"
EVOLVER_PDO_PASSWORD="password"
EVOLVER_PDO_ADDRESS="127.0.0.1"
EVOLVER_PDO_DATABASE="evolver"
```

## Run

  php example/example.php
