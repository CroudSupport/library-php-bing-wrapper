<?php

use Microsoft\BingAds\V11\Reporting\AccountPerformanceReportRequest;
use Microsoft\BingAds\V11\Reporting\AccountReportScope;
use Microsoft\BingAds\V11\Reporting\Date;
use Microsoft\BingAds\V11\Reporting\ReportFormat;
use Microsoft\BingAds\V11\Reporting\ReportTime;

class AccountPerformanceReportRequestBuilder implements ReportRequestContract
{
    /**
     * @var AccountPerformanceReportRequestBuilder
     */
    private $report;

    /**
     * @param DateTimeInterface $startDate
     * @param DateTimeInterface $endDate
     * @param string $aggregation
     * @param array $accountIds
     * @param array $columns
     */
    public function __construct(
        DateTimeInterface $startDate,
        DateTimeInterface $endDate,
        string $aggregation,
        array $accountIds,
        array $columns
    ) {
        $report = new AccountPerformanceReportRequest();

        $report->Format = ReportFormat::Csv;
        $report->ReportName = 'my_report_' . rand(1,10000);
        $report->ReturnOnlyCompleteData = false;
        $report->Aggregation = $aggregation;

        $report->Scope = new AccountReportScope();
        $report->Scope->AccountIds = $accountIds;

        $report->Time = new ReportTime();

        $report->Time->CustomDateRangeStart = new Date();
        $report->Time->CustomDateRangeStart->Day = $startDate->format('d');
        $report->Time->CustomDateRangeStart->Month = $startDate->format('m');
        $report->Time->CustomDateRangeStart->Year = $startDate->format('Y');

        $report->Time->CustomDateRangeEnd = new Date();
        $report->Time->CustomDateRangeEnd->Day = $endDate->format('d');
        $report->Time->CustomDateRangeEnd->Month = $endDate->format('m');
        $report->Time->CustomDateRangeEnd->Year = $endDate->format('Y');

        $report->Columns = $columns;

        $this->report = $report;
    }

    /**
     * @return \Microsoft\BingAds\V11\Reporting\ReportRequest
     */
    public function getReport(): \Microsoft\BingAds\V11\Reporting\ReportRequest
    {
        return $this->report;
    }
}