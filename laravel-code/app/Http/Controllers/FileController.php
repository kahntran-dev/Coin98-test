<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class FileController extends Controller
{
    public function upload(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|mimes:doc,docx,pdf,txt,csv|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                "success" => false,
                "message" => $validator->errors()
            ], 401);
        }

        $file = $request->file;
        $fileName = time() . '.' . $file->extension();
        $filePath = public_path('files');
        $file->move($filePath, $fileName);

        return response()->json([
            "success" => true,
            "message" => "File successfully uploaded",
            "file" => $fileName
        ]);
    }

    public function retrieve(string $filename): JsonResponse|BinaryFileResponse
    {
        $file = public_path('files') . '/' . $filename;
        if (File::exists($file)) {
            return response()->download($file);
        }

        return response()->json([
            "success" => false,
            "message" => 'File not found'
        ], 404);
    }

    public function delete(string $filename): JsonResponse
    {
        $file = public_path('files') . '/' . $filename;
        if (File::exists($file)) {
            File::delete($file);

            return response()->json([
                "success" => true,
                "message" => "File successfully deleted"
            ]);
        }

        return response()->json([
            "success" => false,
            "message" => 'File not found'
        ], 404);
    }
}
