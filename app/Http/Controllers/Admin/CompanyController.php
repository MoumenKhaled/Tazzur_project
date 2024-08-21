<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Company;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use ZipArchive;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use App\Http\Controllers\BaseController;
use App\Mail\StatusCompanyMail;
use DB;
use App\Mail\CompanyStatusMail;
use Illuminate\Support\Facades\Mail;

class CompanyController extends BaseController
{
    /**
     * Display a listing of the companies.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $status = $request->query('status', 'acceptable');
        $perPage = $request->input('per_page', 2);


        $companies = Company::where('status', $status)->paginate($perPage)->through(function ($company) {
            if (is_array($company->type)) {
                $type = $company->type;
            } elseif (is_string($company->type)) {
                $type = json_decode($company->type, true);
                // Check if json_decode failed, which happens if the string is not valid JSON
                if (json_last_error() !== JSON_ERROR_NONE) {
                    $type = null; // or handle the error as appropriate
                }
            } else {
                $type = null; // Handle unexpected data types
            }
            return [
                'id' => $company->id,
                'status' => $company->status,
                'name' => $company->name,
                'topic' => $company->topic,
                'type' => $type,
                'logo' => $company->logo,
                'email' => $company->email,
            ];
        });

        return $this->sendResponse($companies, 'Companies list retrieved successfully.');
    }



    /**
     * Display the specified company.
     *
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    public function show(string $id)
    {
        $company = Company::where('id', $id)->first();
        if (!$company) {
            return $this->sendError(404, 'company not found');
        }

        // Handle documents field
        $documents = is_string($company->documents) ? json_decode($company->documents, true) : $company->documents;
        $company->documents = array_map(function ($doc) {
            return pathinfo($doc, PATHINFO_FILENAME);
        }, $documents ?: []);

        // Handle type field
        $company->type = is_string($company->type) ? json_decode($company->type, true) : $company->type;

        // Handle location_map field
        if ($company->location_map) {
            $company->location_map = is_string($company->location_map) ? json_decode($company->location_map, true) : $company->location_map;
        }

        return $this->sendResponse($company, 'company profile');
    }


    /**
     * Update the specified company in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, string $id)
    {
        $company = Company::find($id);
        if (!$company) {
            return $this->sendError(404, 'company not found');
        }
        $company->update($request->all());
        return $this->sendResponse($company, 'Company updated successfully.');
    }

    /**
     * Remove the specified company from storage.
     *
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(string $id)
    {
        $company = Company::find($id);
        if (!$company) {
            return $this->sendError(404, 'company not found');
        }
        Mail::to($company->email)->send(new CompanyStatusMail($company, 'Deleted'));
        $company->delete();
        return response()->json(['message' => 'Company deleted successfully.']);
    }

    /**
     * Accept or reject a company.
     *
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    public function set_status(Request $request, string $id)
    {
        $company = Company::find($id);
        if (!$company) {
            return $this->sendError(404, 'company not found');
        }
        $status = $request->input('status'); // Expect 'acceptable' or 'rejected'
        $company->status = $status;
        $company->save();
        Mail::to($company->email)->send(new CompanyStatusMail($company, $status));
        return $this->sendResponse($status, "Company status updated to $status.");
    }






    public function downloadDocuments($companyId)
    {
        $company = Company::find($companyId);

        if (!$company) {
            return response()->json([
                'status' => false,
                'message' => 'Company not found'
            ], 404);
        }
        $documents = json_decode($company->documents, true);

        if (!$documents || empty($documents)) {
            return response()->json([
                'status' => false,
                'message' => 'No documents found for this company'
            ], 404);
        }

        $storagePath = storage_path('app/public/uploads/documents');

        if (!file_exists($storagePath)) {
            mkdir($storagePath, 0777, true);
        }

        $zip = new \ZipArchive();
        $zipFileName = "company_{$companyId}_documents.zip";
        $zipFilePath = $storagePath . '/' . $zipFileName;

        if ($zip->open($zipFilePath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
            return response()->json([
                'status' => false,
                'message' => 'Could not create zip file'
            ], 500);
        }

        foreach ($documents as $document) {
            $filePath = public_path($document);
            if (file_exists($filePath)) {
                $relativePath = basename($filePath);
                $zip->addFile($filePath, $relativePath);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => "File not found: $filePath"
                ], 404);
            }
        }

        $zip->close();

        if (!file_exists($zipFilePath)) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to create zip file'
            ], 500);
        }
        $headers = [
            'Content-Type' => 'application/zip',
            'Content-Disposition' => 'attachment; filename="company_documents.zip"',
            'Access-Control-Allow-Origin' => '*',
            'Access-Control-Allow-Headers' => 'Authorization, Origin, X-Requested-With, Content-Type, Accept',
        ];
        return response()->download($zipFilePath, 'company_documents.zip', $headers)->deleteFileAfterSend(true);
    }






    /**
     * Block or unblock a company.
     *
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
}
