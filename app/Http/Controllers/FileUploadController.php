<?php

namespace App\Http\Controllers;

use App\Models\Uploads;
use App\Models\TeisUploads;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;

class FileUploadController extends Controller
{
    public function upload_process(Request $request){
    //  dd($request->all()); 
        if($request->hasFile('teis_upload')) {
            $teis_form = $request->teis_upload;
            $image_id = mt_rand(111111, 999999).date('YmdHms');
            foreach($teis_form as $teis) {
                $teis_name = mt_rand(111111, 999999).date('YmdHms').'.'.$teis->extension();
                Uploads::create([
                    'name'  => $teis_name,
                    'original_name' => $teis->getClientOriginalName(),
                    'extension' => $teis->extension()
                ]);
                $teis->move('uploads/teis_form/', $teis_name);

                $uploads = Uploads::where('status', 1)->orderBy('id', 'desc')->first();

                TeisUploads::create([
                    'teis_number' => $request->teisNum,
                    'upload_id' => $uploads->id
                ]);
            }
        }
        
        // // We don't know the name of the file input, so we need to grab
        // // all the files from the request and grab the first file.
        // /** @var UploadedFile[] $files */
        // $files = $request->allFiles();
 
        // if (empty($files)) {
        //     abort(422, 'No files were uploaded.');
        // }
 
        // if (count($files) > 1) {
        //     abort(422, 'Only 1 file can be uploaded at a time.');
        // }
 
        // // Now that we know there's only one key, we can grab it to get
        // // the file from the request.
        // $requestKey = array_key_first($files);
 
        // // If we are allowing multiple files to be uploaded, the field in the
        // // request will be an array with a single file rather than just a
        // // single file (e.g. - `csv[]` rather than `csv`). So we need to
        // // grab the first file from the array. Otherwise, we can assume
        // // the uploaded file is for a single file input and we can
        // // grab it directly from the request.
        // $file = is_array($request->input($requestKey))
        //     ? $request->file($requestKey)[0]
        //     : $request->file($requestKey);
 
        // // Store the file in a temporary location and return the location
        // // for FilePond to use.
        // return $file->store(
        //     path: 'tmp/'.now()->timestamp.'-'.Str::random(20)
        // );
    }
}
