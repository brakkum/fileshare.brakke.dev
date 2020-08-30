
# Secure File Sharing

This is an idea I had after having to send some insecure files to a client.
A quick Google didn't lead to many promising results for secure file transfer services.
So, I figured I'd try to make a quick and dirty one with Symfony.

### Setup needs

- Get the [Symfony CLI tool](https://symfony.com/download)
- Clone the repo
- Run commands to install dependencies in repository root
  - `composer install`
  - `npm install`
- Make a `.env.local` file based off of `.env`
- Run these two commands to setup the database:
  - `php bin/console make:migration`
  - `php bin/console doctrine:migrations:migrate`
- Run the below commands to start a development server and enable style compilation
  - `symfony server:start`
  - `npm run watch`

The Stylesheet is compiled from /assets/css/app.scss

The below command is run like a cron to clear out old files.
- `php bin/console app:remove-files`

I was having issues with uploads of any substantial size.
Be sure to set PHP ini directives for `post_max_size` and `upload_max_filesize` accordingly.
