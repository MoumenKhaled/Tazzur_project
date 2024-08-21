<?php

namespace App\Services;

use ArPHP\I18N\Arabic;
use Barryvdh\DomPDF\Facade\Pdf;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\View\View;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Mccarlosen\LaravelMpdf\Facades\LaravelMpdf;

class GeneratePDFService
{

    public static function generate(View|\Illuminate\Contracts\View\View $view, $paperSize = 'A5', $paperOrientation = 'portrait')
{
    $viewHtml = $view->render();

    $pdf = PDF::loadHTML($viewHtml);
    $pdf->setPaper($paperSize, $paperOrientation);

    return $pdf;
}
}