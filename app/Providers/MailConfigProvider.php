<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Config;

class MailConfigProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        if(!is_null(nova_get_setting('smtp_server'))) {
            $config = array(
                'transport'     =>     'smtp',
                'url'        =>     null,
                'host'       =>     nova_get_setting('smtp_server'),
                'port'       =>     intval(nova_get_setting('smtp_port')),
                'username'   =>     nova_get_setting('smtp_username'),
                'password'   =>     nova_get_setting('smtp_password'),
                'encryption' =>     'tls',
                'timeout'    =>     null,
                'local_domain' =>   null,
                'from'       =>     array('address' => nova_get_setting('smtp_email'), 'name' => env('APP_NAME')),
            );
            Config::set('mail.mailers.smtp', $config);
            Config::set('mail.from', $config['from']);            
        }
    }
}
