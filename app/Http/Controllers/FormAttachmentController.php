<?php

namespace App\Http\Controllers;

use App\Models\FormSubmission;
use Illuminate\Http\Request;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class FormAttachmentController extends Controller
{
    /**
     * Stream a form-submission attachment to authenticated admin users.
     *
     * Form ekleri CV gibi kişisel veri içerebildiği için herkese açık URL ile
     * değil, yalnızca panele erişebilen kullanıcılara bu korumalı route üzerinden
     * sunulur (KVKK). Dosya, mime tipine göre tarayıcıda inline gösterilir.
     */
    public function show(Request $request, Media $media): BinaryFileResponse
    {
        $user = $request->user();
        // Ekler form-görüntüleme yetkisine bağlı; team_captain (form izni yok) erişemez.
        abort_unless($user && $user->can('view_any_form'), 403);

        abort_unless(
            $media->collection_name === 'attachments'
                && $media->model_type === (new FormSubmission)->getMorphClass(),
            404
        );

        $path = $media->getPath();
        abort_unless(is_file($path), 404);

        return response()->file($path, [
            'Content-Type' => $media->mime_type ?: 'application/octet-stream',
            'Content-Disposition' => 'inline; filename="'.addslashes($media->file_name).'"',
        ]);
    }
}
