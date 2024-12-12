<?php

use App\Data\Project\CommercialProposal\CommercialProposalData;
use App\Data\Project\CommercialProposal\CommercialProposalItem;
use App\Data\Project\InstructionManual\InstructionManualData;
use App\Data\Reports as Data;
use App\Models\Project;
use App\Models\User;
use App\Reports;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Report Routes
|--------------------------------------------------------------------------
|
| The routes registered here are only available when the app is not in
| production. They will be available through the web guard using the
| "/reports" prefix.
|
*/

Route::get('examples', function () {
    $report = new Reports\Example\ExampleReport(
        units: 10,
    );

    return response(
        content: $report->browsershot()->pdf(),
        headers: ['Content-Type' => 'application/pdf'],
    );
});
