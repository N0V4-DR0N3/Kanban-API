<?php

namespace App\Utils;

use App\Exceptions\Attachment\PdfConversionFailureException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Files
{
    public static function image2pdf(UploadedFile $file): ?string
    {
        $localStorage = Storage::disk('local');

        $name = Str::orderedUuid();
        $extension = $file->extension();

        $pathRel = "temp/image2pdf/{$name}/{$name}.{$extension}";
        $createFile = $localStorage->put($pathRel, $file->get());

        if (!$createFile) {
            throw new PdfConversionFailureException;
        }

        $pathFull = $localStorage->path($pathRel);
        $pathRelConv = str_replace($extension, 'pdf', $pathRel);
        $pathFullConv = str_replace($extension, 'pdf', $pathFull);

        shell_exec("img2pdf \"{$pathFull}\" -o \"{$pathFullConv}\"");

        if (!is_file($pathFullConv)) {
            throw new PdfConversionFailureException;
        }

        $contents = $localStorage->get($pathRelConv);
        $localStorage->delete($pathRelConv);

        return $contents;
    }
}
