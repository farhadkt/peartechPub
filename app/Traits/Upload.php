<?php


namespace App\Traits;


use Illuminate\Support\Facades\Storage;

trait Upload
{

    /**
     * Upload multiple documents related to user verify document
     * just a wrapper for userVerifyDocument Method
     *
     * @param $files
     * @return array|bool
     */
    public static function userVerifyDocuments($files)
    {
        $result = [];

        foreach ($files as $file) {
            $result[] = self::userVerifyDocument($file);
        }

        return !empty($result) ? $result : false;
    }

    /**
     * Upload a document related to user verify document
     *
     * @param $file
     * @return array|bool
     */
    public static function userVerifyDocument($file)
    {
        $dir = config('filesystems.disks.local.user_verify_documents_dir');
        $uploadedFile = $file;
        $filename = date('Ymd') . '-' . uniqid() . $uploadedFile->getClientOriginalName();

        // Store on local disk
        $s = Storage::disk('local')->putFileAs(
            $dir, $uploadedFile, $filename
        );

        return $s
            ? ['filename' => $filename, 'dir' => $dir]
            : false;
    }
}
