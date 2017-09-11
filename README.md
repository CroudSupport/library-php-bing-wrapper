# Bing Decrapper Wrapper

Some helper classes arround the Bing API to make life alittle easier.

## Usage

### Bing Report Helper Class
The BingReportHelper cointains a number of methods that makes report retreival much easier.

#### getReportingClient
This is just a simplifeid way of retreiveing a bing client

```php

$bingReportHelper = new \BingDeCrapperWrapper\ReportHelper\ReportHelper();

// use
$client = $this->bingReportHelper->getReportingClient(
    'bingUserName',
    'bingPassword',
    'bingApiKey'
);
```

#### downloadKeywordPerformanceReportCsv
```php
$bingReportHelper = new \BingDeCrapperWrapper\ReportHelper\ReportHelper();

$bingAccountId = 123;
$startDate = carbon::now()->subDay();
$endDate = carbon::now();

return $this->bingReportHelper->downloadKeywordPerformanceReportCsv(
	$client,
	$startDate,
	$endDate,
	[$bingAccountId],
	'/location/on/fileSytem/to/store/csv',
	[] // arrray of colunm names to exclude.
);
```

#### downloadAdPerformanceReportCsv
```php
$bingReportHelper = new \BingDeCrapperWrapper\ReportHelper\ReportHelper();

$bingAccountId = 123;
$startDate = carbon::now()->subDay();
$endDate = carbon::now();

return $this->bingReportHelper->downloadAdPerformanceReportCsv(
	$client,
	$startDate,
	$endDate,
	[$bingAccountId],
	'/location/on/fileSytem/to/store/csv',
	[] // arrray of colunm names to exclude.
);
```
## Workstation Setup

Just a standard composer app.

### Prerequisites

- PHP
- Composer

### Versioning

[SemVer](http://semver.org/)

## Owner

Rob Sills