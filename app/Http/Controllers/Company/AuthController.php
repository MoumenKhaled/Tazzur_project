<?php

namespace App\Http\Controllers\Company;


use App\Models\Company;
use App\Models\Verification;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Http\Controllers\Controller;
use App\Http\Controllers\BaseController;
use Illuminate\Support\Facades\Storage;
use App\Mail\RegisterCompanyMail;
use Illuminate\Support\Facades\Validator;
use Hash;
use ZipArchive;
use Auth;
use App\Models\Manager;
use App\Notifications\RegisterCompanyNotification;
use Mail;
use Illuminate\Support\Facades\Cache;

use App\Services\FirebaseService;
use App\Models\FcmToken;
use App\Models\User;
use App\Services\NotificationService;
use App\Jobs\ProcessImageUpload;
use App\Jobs\ProcessDocumentUpload;

class AuthController extends BaseController
{
    public function register(Request $request)
    {
        $validator = $request->validate([
            'email' => 'required|email|unique:users,email',
            'password' => ['required', 'confirmed', 'min:8', 'regex:/^(?=.*[a-zA-Z])(?=.*\d).+$/']
        ]);
        $company = Company::where('email', $validator['email'])->first();
        if ($company && $company->email_verified_at == null) {
            $verification = new Verification();
            $verification->code = rand(1000, 9999);
            $verification->email = $request->email;
            $verification->save();
            Mail::to($company->email)->send(new RegisterCompanyMail($company, $verification->code));

            return $this->sendError(400, 'Verification code required. Please verify the code sent to your email.');
        } elseif ($company && !$company->email_verified_at == null) {
            if (! $company->is_complete) {
                return $this->sendError(400, 'Registration incomplete. Please ensure all required fields are filled.');
            }
            return $this->sendError(409, 'A company with these details already exists..');
        } else {
            $company = new Company();
            $company->email =  $validator['email'];
            $company->status = "Incomplete";
            $company->password = Hash::make($validator['password']);
            $company->save();

            $verification = new Verification();
            $verification->code = rand(1000, 9999);
            $verification->email = $request->email;
            $verification->save();
            Mail::to($company->email)->send(new RegisterCompanyMail($company, $verification->code));
            $credentials = request(['email', 'password']);
            $token = auth()->guard('company')->attempt($credentials);
            $data = [
                'code' => $verification->code,
            ];
            if ($request->fcm_token) {

                FcmToken::firstOrCreate([
                    'token' => $request->fcm_token
                ], [
                    'user_id' => $company->id,
                    'token' => $request->fcm_token,
                    'type' => 'company'
                ]);
            }
            return $this->sendResponse($data, 'The registration process was completed successfully');
        }
    }
    public function verify_code(Request $request)
    {
        $validator = $request->validate([
            'code' => 'required',
            'email' => 'required'
        ]);
        $company = company::where('email', $request->email)->first();
        $customClaims = ['guard' => 'company'];
        $token = auth('company')->claims($customClaims)->login($company);
        $verification = Verification::where([
            ['code', $request->code],
            ['email', $request->email]
        ])->first();
        if ($verification && $company->email_verified_at == null) {
            $company->email_verified_at = now();
            $company->save();
        }
        if ($verification) {
            $verification->delete();
            return $this->sendResponse($token, 'The account has been activated successfully');
        } else {
            return $this->sendError(400, 'The verification code is incorrect. Please try again.');
        }
    }
    public function resend_code(Request $request)
    {
        $validator = $request->validate([
            'email' => 'required|email',
        ]);

        $verification = Verification::where('email', $request->email)->first();

        $today = Carbon::today();
        $lastVerificationDate = $verification->last_resend_date ? Carbon::parse($verification->last_resend_date) : null;
        if (!$lastVerificationDate || !$lastVerificationDate->isSameDay($today)) {
            $verification->num_of_resend = 0;
        }
        if ($verification->num_of_resend >= 2) {
            return $this->sendError(400, 'You cannot re-send the verification code more than twice per day.');
        } else {
            $verification->code = rand(1000, 9999);
            $verification->num_of_resend += 1;
            $verification->last_resend_date = $today;
            $verification->save();
            Mail::to($company->email)->send(new RegisterCompanyMail($company, $verification->code));

            //Mail::to($verification->email)->send(new RegistercompanyMail($verification, $verification->code));
            $data = [
                'code' => $verification->code,
            ];
        }
        return $this->sendResponse($data, 'The code has been sent successfully');
    }
    public function complete_register(Request $request, NotificationService $notificationService)
    {
        $rules = [
            'name' => 'required|string',
            'phone' => 'nullable',
            'topic' => 'required|string',
            'company_type' => 'required|array', // Validate as an array
            'company_type.*' => 'string', // Each element in the array should be a string
            'documents.*' => 'file',
            'about_us' => 'required|string',
            'image' => 'required|image',
            'location' => 'required|string',
            'location_map' => 'json|nullable', // Validate as JSON if present
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $company = auth()->guard('company')->user();

        $company->fill($request->only([
            'name',
            'phone',
            'topic',
            'about_us',
        ]));

        $company->type = json_encode($request->company_type);
        $location = $request->input('location');
        $location = trim($location, '"');
        $company->location = $location;

        if ($request->has('location_map')) {
            $company->location_map = $request->location_map;
        }
         if ($request->hasFile('image')) {
            $allowedFileExtension = ['jpg', 'jpeg', 'png', 'bmp'];
            $file = $request->file('image');
            $extension = $file->getClientOriginalExtension();
            $check = in_array($extension, $allowedFileExtension);
            if ($check) {
                $Imagename = "company$company->id" . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('uploads/profiles'), $Imagename);
                $imagePath = "uploads/profiles/$Imagename";
                $company->logo = $imagePath;
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'invalid image format'
                ], 400);
            }
        }

        if ($request->hasFile('documents')) {
            $paths = [];
            foreach ($request->file('documents') as $file) {
                $docname = "company{$company->id}_" . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('uploads/documents'), $docname);
                $paths[] = "uploads/documents/$docname";
            }
            $company->documents = json_encode($paths);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'No documents received'
            ], 400);
        }
        $company->status = "waiting";
        $company->save();
        $company->update(['is_complete' => true]);
        $manager = Manager::first();
        $messages = [
            'ar' => [
                'title' => 'تسجيل شركة جديدة!',
                'description' => 'تم تسجيل شركة جديدة في النظام. يرجى مراجعة التفاصيل واتخاذ الإجراءات المناسبة للتحقق منها وتفعيلها إذا لزم الأمر.'
            ],
            'en' => [
                'title' => 'New Company Registration!',
                'description' => 'A new company has registered in the system. Please review the details and take appropriate action to verify and activate the account if necessary.'
            ]
        ];
        

        $url = "http://86.38.218.161:8082/waitingCompanies/" . $company->id;
    //    $url = route('companies.show', ['company' => $company->id]);
        $notificationService->sendNotification($manager, $messages, $url, 'manager');
        //  $customerToken = FcmToken::where(
        //      [
        //          'user_id' =>$manager->id,
        //          'type' => 'manager',
        //      ]
        //  )->pluck('token')->toArray();
        //  if (!empty($customerToken)) {
        //      FirebaseService::sendNotification($customerToken, $messages,  $url);
        //  }
        //  $notification = new \App\Notifications\ServiceNotification($messages,  $url);
        //  $manager->notify($notification);

        return $this->sendResponse($company, 'Company registered successfully');
    }



    public function login(Request $request)
    {
        $validator = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);
        $credentials = request(['email', 'password']);
        $company = company::where('email', $validator['email'])->first();
        if ($company && !$company->email_verified_at == null && !$company->is_complete) {
            return $this->sendError(400, 'Please complete all required fields to proceed with login.');
        }
        if ($company && $company->email_verified_at == null) {
            return $this->sendError(400, 'Email verification required. Please verify to proceed.');
        }
        if (!$company || !Hash::check($credentials['password'], $company->password)) {
            return $this->sendError(400, 'The data provided is invalid. Please review your input.');
        }
        if ($company && $company->status == 'blocked') {
            return $this->sendError(403, 'Your account has been blocked. Please contact support for further assistance.');
        }
        if ($company && $company->status == 'waiting') {
            return $this->sendError(202, 'Your request is being processed. Please wait for further instructions.');
        }

        if (!$company) $this->sendError(404, 'The account you are trying to access does not exist.');
        else if ($company) {
            if (Hash::check($validator['password'], $company->password)) {

                $token = auth()->guard('company')->attempt($credentials);
                $data = [
                    'company' => $company,
                    'token' => $token
                ];



                return $this->sendResponse($data, 'You have been logged in successfully');
            } else
                return $this->sendError(401, 'Authentication failed, please check your password.');
        }
    }

    public function forget_password(Request $request)
    {
        $validator = $request->validate([
            'email' => 'required|email',
        ]);
        $company = Company::where('email', $request->email)->first();
        if ($company) {
            $Password = Verification::updateOrCreate(
                ['email' => $request->email],
                [
                    'email' => $request->email,
                    'code' => rand(1000, 9999),
                ]
            );
            $code = ['code' => $Password->code];
            // Mail::to($company->email)->send(new ForgottenPassword($Password));
            return $this->sendResponse($code, 'confirmation code has been sent successfull');
        } else {
            return $this->sendError(404, 'The email address entered does not match any account. Please check and try again.');
        }
    }
    public function reset_password(Request $request)
    {
        $validator = $request->validate([
            'email' => 'required|email',
            'password' => 'required|confirmed',
        ]);
        $company = company::where('email', $request->email)->first();
        if (!$company) {
            return $this->sendError(404, 'No company associated with the provided information was found.');
        } else {
            $company->password = bcrypt($request->password);
            $company->save();
            return $this->sendResponse('done', 'Reset Password Successfully!');
        }
        return $this->sendError(404, 'The email address entered does not match any account. Please check and try again.');
    }

    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }
    public function delete_account(Request $request)
    {
        $company = auth()->guard('company')->user();
        $company = Company::findOrFail($company->id);
        $company->delete();
        return response()->json(['message' => 'Company deleted successfully.']);
    }

    public function get_information_cv() {}
    public function profile()
    {
        $company = auth()->guard('company')->user();


        $documents = is_string($company->documents) ? json_decode($company->documents, true) : $company->documents;
        $company->documents = array_map(function ($doc) {
            return pathinfo($doc, PATHINFO_FILENAME);
        }, $documents ?: []);


      //  $company->type = is_string($company->type) ? json_decode($company->type, true) : $company->type;


        if ($company->location_map) {
            $company->location_map = is_string($company->location_map) ? json_decode($company->location_map, true) : $company->location_map;
        }

        return $this->sendResponse($company, 'company profile');
    }

    public function update_profile() {}
    public function change_password() {}
    public function check_status()
    {
        $company = auth()->guard('company')->user();
        $data = [
            'status' => $company->status,
        ];
        return $this->sendResponse($data, 'check_status');
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
}
