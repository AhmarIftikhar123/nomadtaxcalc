<?php

namespace App\Console\Commands;

use App\Mail\NewsletterMail;
use App\Models\NewsletterSubscriber;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendNewsletter extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'newsletter:send {subject} {content}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send newsletter to all active subscribers';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $subject = $this->argument('subject');
        $content = $this->argument('content');

        $this->info("Starting newsletter dispatch...");

        // using lazy which runs cursor query behind the scenes for performance
        $subscribers = NewsletterSubscriber::where('is_active', true)->lazy();
        $count = 0;

        foreach ($subscribers as $subscriber) {
            Mail::to($subscriber->email)->queue(
                new NewsletterMail(
                    subject: $subject,
                    content: $content,
                    unsubscribeToken: $subscriber->token
                )
            );
            $count++;
        }

        $this->info("Newsletter queued for {$count} subscribers!");
    }
}
