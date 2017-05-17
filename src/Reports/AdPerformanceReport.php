<?php
namespace BingDeCrapperWrapper\Reports;

use DateTimeInterface;
use Microsoft\BingAds\V11\Reporting\AccountStatusReportFilter;
use Microsoft\BingAds\V11\Reporting\AdGroupStatusReportFilter;
use Microsoft\BingAds\V11\Reporting\AdPerformanceReportFilter;
use Microsoft\BingAds\V11\Reporting\AdPerformanceReportRequest;
use Microsoft\BingAds\V11\Reporting\AdStatusReportFilter;
use Microsoft\BingAds\V11\Reporting\CampaignStatusReportFilter;
use Microsoft\BingAds\V11\Reporting\ReportRequest;

class AdPerformanceReport extends ReportRequestBuilder
{
    /**
     * @var AdPerformanceReportRequest
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
        $report = new AdPerformanceReportRequest();

        $report = $this->setBaseFields($report);
        $report = $this->setAggregation($aggregation, $report);
        $report = $this->setAccountIds($accountIds, $report);
        $report = $this->setDateRange($startDate, $endDate, $report);
        $report = $this->setReportColumns($columns, $report);

        $filter = new AdPerformanceReportFilter();
        $filter->AccountStatus = AccountStatusReportFilter::Active;
        $filter->AdGroupStatus = AdGroupStatusReportFilter::Active;
        $filter->CampaignStatus = CampaignStatusReportFilter::Active;
        $filter->AdStatus = AdStatusReportFilter::Active;

        $report->Filter = $filter;

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