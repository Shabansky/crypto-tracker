# Bitcoin Tracker

## Tech Stack
- PHP 8.4.4
- Laravel 12.1.1
- MySql 8.0.41

## Installation

For the below commands, use `docker compose` for newer Docker versions
and `docker-compose` for older ones. The following guide will assume the use
of `docker compose`.

1. Build and boot the project
`docker compose up --build -d`

2. Install dependencies
`docker compose exec api composer install`

## System Operation

### Subscription API

The system supports an API for viewing and modifiying subscriptions. It allows for the current actions:
- List subscriptions by email
- Add new subscriptions. A single email can have multiple subscriptions (called subscription settings). Each one can use a different time frame (more on this below). This allows for a granular control of notifications.
- Edit existing subscription setting
- Delete subscription settings and subscriptions.

An OpenAPI Documentation of the project can be found at `./openapi.yaml`

### Price Tracker

The other side of the project is responsible for pulling Bitcoin ticker data from 3rd party providers. Currently only Bitfinex is supported but the system would allow (after some minor adjustments) for the introduction of other providers as well.

Price checking is done in what are called **time frames**. A time frame describes the number of hours from a past moment to the present in which a price check is done. It is used as a slice in which to check for price deviations. Currently the system time frames of 1, 6 and 24 hours. Each *subscription setting* has a *time frame*.

Finally **thresholds** are percent changes defined for each subscriber. If the price of Bitcoin during a given *time frame* changes by a percentage greater than the threshold, the subscriber receives a notification. Each *subscription setting* has a *time frame*.


## Logs

The system collects information in three log channels which print the data in three log files respectively:

- Price Checker : Any data relevant to the Price Checker scheduler including emergencies from service outages. Data written in `/storage/logs/price_checker.log`
- Emails : As the current mail implementation doesn't work too well (no SMTP server + `sendmail` doesn't seem to be reaching its recepients), mails as well as queue information about number sent is stored in `/storage/logs/emails.log`
- General Errors : Any unhandled exceptions find their way to `/storage/logs/laravel.log`. Hopefully this one doesn't fill up too much.


## Ticker Retention

Tickers are kept in the `btc_tracker.hourly_tickers` table. As it tends to fill up quickly and a lot of the information it contains becomes invalid after a certain point (24 hours currently), it doesn't make much sense to keep it around. Thus on every hour as the system
checks for a new ticker from the alloted provider, it also attempts to clear old data.

The retention period (after which hour) to clear is defined in `.env` `TICKER_RETENTION_HOURS`.

**IMPORTANT**
The ticker retention defined in `.env` must not be lower than 24 hours (the current maximum timeframe). Otherwise the cleaner would be prone to clearing perfectly useable data. An emergency log will be issued in `price_checker.log` if that occurs.


## Caveats to the current version

While the system does generate mails, it currently does so internally. All mails are saved in the logs (specify which). Didn't have much luck implementing a full-fledged SMTP Server configuration.


