<!-- @format -->
# Notifications
As users’ communication preferences grow more and more diverse, we gather ever more—and more disparate packages to communicate via Slack, SMS, and other means.
- A single notification could send an email, send an SMS via Vonage, send a WebSocket ping, add a record to a database, send a message to a Slack channel, and much more.
- Just like a mailable, a notification is a PHP class that represents a **single** communication that you might want to send to your users

## Steps

1. run `php artisan make:notification WorkoutAvailable`