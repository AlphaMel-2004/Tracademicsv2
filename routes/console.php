<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('test:email {email}', function ($email) {
    $this->info('Testing email configuration...');
    
    try {
        \Illuminate\Support\Facades\Mail::raw('This is a test email from TracAdemics.', function ($message) use ($email) {
            $message->to($email)
                    ->subject('TracAdemics Email Test');
        });
        
        $this->info('✅ Email sent successfully to: ' . $email);
        $this->info('Check your email inbox (and spam folder) for the test message.');
        
    } catch (\Exception $e) {
        $this->error('❌ Failed to send email: ' . $e->getMessage());
        $this->error('Please check your mail configuration in .env file');
    }
})->purpose('Send a test email to verify mail configuration');
