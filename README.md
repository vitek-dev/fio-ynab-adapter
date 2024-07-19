# Fio => YNAB transactions adapter

1. Copy `config/env.example.php` to `config/env.php` and fill necessary values
2. Run `bin/run.php` and enjoy!

## Troubleshooting & sidenotes

- We are downloading transactions "From last downloaded transaction". If you need to manually adjust the breakpoint, you can run `bin/reset-fio.php 1234` with transaction ID you want to return to.
- If you delete imported transactions from YNAB, it WON'T BE IMPORTED AGAIN - YNAB remember "import id" and reject them automatically (https://www.reddit.com/r/ynab/comments/7rrx16/help_how_to_reimport_deleted_transactions/). Solution could be modifying `\App\Repository\YnabTargetRepository` to use different import id (e.g. uniquid, md5 time or anything).
