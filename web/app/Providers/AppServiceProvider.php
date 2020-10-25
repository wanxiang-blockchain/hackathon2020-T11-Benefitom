<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Form;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Form::component('bsText', 'components.form.text', ['attr' => []]);
        Form::component('bsNumber', 'components.form.number', ['attr' => []]);
        Form::component('bsFile', 'components.form.file', ['attr' => []]);
        Form::component('bsTextarea', 'components.form.textarea', ['label', 'name', 'desc']);
        Form::component('bsSelect', 'components.form.select', ['label', 'name', 'values']);
        Form::component('bsSubmit', 'components.form.submit', []);
        Form::component('bsTime', 'components.form.time', ['label', 'name']);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
	    if ($this->app->environment() == 'local') {
		    $this->app->register(\Reliese\Coders\CodersServiceProvider::class);
	    }
    }
}
