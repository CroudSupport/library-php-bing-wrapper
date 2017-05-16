<?php
use Microsoft\BingAds\V11\Reporting\ReportRequest;

/**
 * @package Agency Tools
 *
 * @license Proprietary
 */
interface ReportRequestContract
{
    public function getReport() : ReportRequest;
}