
# Secure File Sharing

This is an idea I had after having to send some insecure files to a client.
A quick Google didn't lead to many promising results for secure file transfer services.
So, I figured I'd try to make a quick and dirty one with Symfony.

## What it does

Basically, it:
- Takes in a file, how many times it's allowed to be downloaded, and an optional secret
  - If no secret is provided, there's a default one in the .env file to use
- The file is encrypted and stored in a non-accessible portion of the server
  - The file contents are encrypted with the secret
  - The name of the file is changed to a hash of its contents
  - The secret-encrypted file name is stored in the database to be decrypted on download
  - The hash of the encrypted file contents is used as an identifier for the download link
- When the file download link is accessed, the file can be downloaded the number of times initially specified
  - If a non-default secret was used, that must also be supplied here
  - The secret is used to decrypt the file contents and filename, which is then returned to the user
- Old files get removed
  - If a file is over 12 hours old, or has reached its download limit, the file and database record will be removed

## Setup needs

Built with:
- PHP 7.3
- Node 13.12
- Symfony 5.1

Steps:
- Get the [Symfony CLI tool](https://symfony.com/download)
- Clone the repo from [GitHub](https://github.com/brakkum/secure-file-transfer)
- Run commands to install dependencies in repository root
  - `composer install`
  - `npm install`
- Make a `.env.local` file based off of `.env`
- Run these three commands to create and setup the database:
  - `php bin/console doctrine:database:create`
  - `php bin/console make:migration`
  - `php bin/console doctrine:migrations:migrate`
- Run the below commands in two terminals to start a development server and enable style compilation
  - `symfony server:start`
  - `npm run watch`

The Stylesheet is compiled from `/assets/css/app.scss`

The below command is run like a cron to clear out old database records and files.
- `php bin/console app:remove-files`

I was having issues with uploads of any substantial size, but realized it was due to PHP directives.
Be sure to set PHP ini directives for `post_max_size`, `upload_max_filesize`, and `memory_limit` accordingly.

## Deployment

Running `npm run build` will build the stylesheets to `/public/build/`.
The site root should be `/public/`.
When deploying changes, you'll likely need to clear the cache with `rm -rf var/cache/*`.

## TODO

- Don't use the secret in plain text in the URL when requesting a download
