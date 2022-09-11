## Livewire Kit: Two-Level Parent-Child Dropdowns

### How to use

- Copy `.env.example` file to `.env` and edit database credentials there
- Run `composer install`
- Run `php artisan key:generate`
- Run `php artisan migrate --seed` (it has some seeded data for your testing)
- That's it: launch the main URL
- You will see Tailwind CSS version, for Bootstrap visit `/bootstrap` URL


### Files of the Component

- app/Http/Livewire/Companies.php
- app/Models/Company.php
- app/Models/Country.php
- app/Models/City.php
- resources/views/livewire/tailwind/companies.blade.php
- resources/views/livewire/bootstrap/companies.blade.php

