## Livewire Kit: Two-Level Parent-Child Dropdowns

This is a component to add dependent dropdowns to any of your forms, with example of Countries and Cities.


### Files of the Component

- app/Http/Livewire/Companies.php
- app/Models/Company.php
- app/Models/Country.php
- app/Models/City.php
- resources/views/livewire/companies.blade.php


### How to use

- Make sure you have Livewire installed and enabled with `@livewireStyles` and `@livewireScripts`. For more info, read the [official docs](https://laravel-livewire.com/docs/2.x/quickstart) 
- Copy files `app/Http/Livewire/Companies.php` and `resources/views/livewire/companies.blade.php` into your own project
- If you work with other parent-child models, rename component file/class/Blade as you wish
- In file `app/Http/Livewire/Companies.php` check the models and DB queries used, adapt to your DB structure
- If needed, run/modify migrations for `countries` and/or `cities` DB tables, located in `database/migrations` folder.
- In Blade file(s), wherever you need to use this component, call it with `@livewire('companies')`
- Check how it looks and make the changes necessary


Need help or extra features? Email me directly and let's discuss: povilas@laraveldaily.com 