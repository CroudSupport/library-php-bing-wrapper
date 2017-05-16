<?php
namespace BingDeCrapperWrapper\Reports;

use DateTimeInterface;
use Microsoft\BingAds\V11\Reporting\AccountPerformanceReportRequest;
use Microsoft\BingAds\V11\Reporting\ReportRequest;

class AccountPerformanceReport extends ReportRequestBuilder
{
    /**
     * @var AccountPerformanceReport
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

        $report = $this->setBaseFields($report);
        $report = $this->setAggregation($aggregation, $report);
        $report = $this->setAccountIds($accountIds, $report);
        $report = $this->setDateRange($startDate, $endDate, $report);
        $report = $this->setReportColumns($columns, $report);

        $this->report = $report;
    }

    /**
     * @return \Microsoft\BingAds\V11\Reporting\ReportRequest
     */
    public function getReport(): ReportRequest
    {
        return $this->report;
    }
}