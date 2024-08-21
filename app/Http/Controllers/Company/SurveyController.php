<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Survey;
use App\Models\SurveyOption;
use App\Http\Controllers\BaseController;
class SurveyController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $company_id = auth()->guard('company')->user()->id;

        $perPage = $request->input('per_page', 10);


        $surveys = Survey::where('company_id', $company_id)
                         ->with('options')
                         ->paginate($perPage);

        return $this->sendResponse($surveys, 'Surveys retrieved successfully');
    }



    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'options' => 'required|array',
            'description' => 'required'
        ]);

        $survey = new Survey;
        $survey->company_id = auth()->guard('company')->user()->id;
        $survey->title = $request->title;
        $survey->description = $request->description;
        $survey->save();

        foreach ($request->options as $option_text) {
            if ($option_text !== null) {
                $option = new SurveyOption;
                $option->survey_id = $survey->id;
                $option->option_text = $option_text;
                $option->vote_count = 0; 
                $option->save();
            }
        }
        return $this->sendResponse($survey, 'Survey created successfully!', 200);
    }



    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $survey = Survey::where('id', $id)->with('options')->first();

        if (!$survey) {
            return response()->json(['message' => 'Survey not found'], 404);
        }
        return $this->sendResponse($survey, 'Survey Details!',200);

    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'title' => 'required|string',
            'options' => 'required|array',
            'description' => 'required'
        //  'options.*' => 'required|string',
        ]);

        $survey = Survey::find($id);
        if (!$survey) {
            return response()->json(['message' => 'Survey not found'], 404);
        }
        if ($request->has('title')) {
            $survey->title = $request->title;
        }
        $survey->save();
        if ($request->has('options')) {
            foreach ($request->options as $option) {
                // Ensure $option is an array and has the necessary keys
                if (is_array($option) && isset($option['id'])) {
                    $surveyOption = SurveyOption::find($option['id']);
                    if ($surveyOption) {
                        $surveyOption->option_text = $option['option_text'];
                        $surveyOption->save();
                    }
                }
            }
        }
        return $this->sendResponse($survey, 'Survey updated successfully!',200);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $survey = Survey::find($id);

        if (!$survey) {
            return response()->json(['message' => 'Survey not found'], 404);
        }

        // حذف الخيارات أولًا
        $survey->options()->delete();
        $survey->delete();

        return response()->json(['message' => 'Survey deleted successfully']);
    }

}
