public function boot(): void
{
    if ($this->app->environment('production')) {
        \Illuminate\Support\Facades\URL::forceScheme('https');
    }
}