# Getting started

## Create a new application

Use Composer to create a new SimpleRest application:

```sh
composer create-project boctulus/simplerest my-app
cd my-app
```

This command was verified against the `v1.0.3` release on Packagist. The package requires PHP `>=8.1,<8.5`.

Composer creates `.env` from `.env.example` after installing the project. Values for keys named like passwords, secrets, tokens, API keys, or private keys are cleared; configure the values you need before connecting external services.

This workflow creates a new application. Installing SimpleRest as a library inside an existing project with `composer require` is a separate workflow and is not documented here.
