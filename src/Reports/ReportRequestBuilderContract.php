<?php
namespace BingDeCrapperWrapper\Reports;

use Microsoft\BingAds\V11\Reporting\ReportRequest;

/**
 * @package Agency Tools
 *
 * @license Proprietary
 */
interface ReportRequestBuilderContract
{
    public function getReport() : ReportRequest;
}