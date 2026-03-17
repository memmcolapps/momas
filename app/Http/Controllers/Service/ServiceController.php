<?php

namespace App\Http\Controllers\Service;

use Exception;
use App\Models\Job;
use App\Models\Estate;
use App\Models\Rating;
use App\Models\Comment;
use App\Models\Service;
use App\Models\Logger;
use Illuminate\Http\Request;
use App\Models\EstateService;
use App\Services\StandardResponse;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use PhpParser\PrettyPrinter\Standard;
use function PHPUnit\Framework\isEmpty;
use Illuminate\Support\Facades\Validator;

class ServiceController extends Controller
{
    public function get_artisan_by_id(request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'service_id' => 'required|integer|exists:estate_services,id'
            ]);

            if ($validator->fails()) {
                return StandardResponse::error(code: 422, message: 'Validation Error', data: [
                    'validation_error' => $validator->errors(),
                ]);
            }

            $data['service'] = EstateService::where('status', 2)
                ->where('estate_id', Auth::user()->estate_id)
                ->where('id', $request->service_id)
                ->first();

            if ($data['service'] === null) {
                return StandardResponse::error(code: 404, message: 'Resouce not found');
            }

            return response()->json([
                'status' => true,
                'message' => 'Fetched service successfully',
                'data' => $data
            ], 200);
        } catch (Exception $e) {
            Logger::error('ServiceController error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return StandardResponse::error(code: 501, message: 'An Error Occurred', debug: [
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
            ]);
        }
    }

    public function service_properties(request $request) {
        $data['estate'] = Estate::where('status', 2)->where('id', Auth::user()->estate_id)->get()->makeHidden(['created_at', 'updated_at']);
        $data['service'] = EstateService::latest()->where('status', 2)->where('estate_id', Auth::user()->estate_id)->get()->makeHidden(['created_at', 'updated_at']);

        return response()->json([
            'status' => true,
            'data' => $data
        ], 200);
    }

    public function fetch_services(request $request)
    {
        $jobs = Service::get([
            'service_title',
            'id',
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Fetched services successfully',
            'data' => $jobs

        ], 200);
    }

    public function get_artisans_by_service(request $request) {
        try {
            $validator = Validator::make($request->all(), [
                'service_id' => 'required|integer|exists:services,id'
            ]);

            if ($validator->fails()) {
                return StandardResponse::error(code: 422, message: 'Validation Error', data: [
                    'validation_error' => $validator->errors(),
                ]);
            }

            $service_id = $request->service_id;
            $estate_id = Auth::user()->estate_id;
            $artisans = EstateService::where('service_id', $service_id)
                ->where('estate_id', $estate_id)
                ->get();

            return StandardResponse::success(code: 200, message: 'Fetch Estate Artisans Successfully', data: [
                'artisans' => $artisans,
            ]);
        } catch (Exception $e) {
            Logger::error('ServiceController error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return StandardResponse::error(code: 500, message: 'An Error Occurred', debug: [
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
            ]);
        }
    }



    public function get_comment(request $request)
    {

        $data =  Comment::latest()->where('job_id', $request->job_id)->get();
        $rate =  Rating::latest()->where('job_id', $request->job_id)->max('count');
        Job::where('id', $request->job_id)->update(['rating' => $rate]) ?? null;


        return response()->json([
            'status' => true,
            'comment' => $data,
        ], 200);


    }



    public function save_comment(request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'job_id' => 'required|integer|exists:estate_services,id',
                'rate' => 'required|integer|min:1|max:5',
                'comment' => 'required|string'
            ]);

            if ($validator->fails()) {
                return StandardResponse::error(code: 422, message: 'Validation error', data: [
                    'validation_error' => $validator->errors(),
                ]);
            }

            DB::beginTransaction();

            $rate = new Comment();
            $rate->user_id = Auth::id();
            $rate->user_name = Auth::user()->first_name;
            $rate->comment = $request->comment;
            $rate->job_id = $request->job_id;
            $rate->estate_service_id = $request->job_id;
            $rate->rate = $request->rate;
            $rate->save();

            $artisan = EstateService::firstWhere('id', $request->job_id);
            $artisan->updateRating();

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => "Comment successfully saved"
            ], 200);
        } catch (Exception $e) {
            Logger::error('ServiceController error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            DB::rollBack();

            return StandardResponse::error(code: 500, message: 'An Error Occurred', debug: [
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
            ]);
        }
    }
}
