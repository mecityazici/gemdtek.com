<?php

namespace App\Http\Controllers;

use App\Mail\FormSubmissionReceived;
use App\Models\Form;
use App\Models\FormSubmission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class ApplicationFormController extends Controller
{
    public function index()
    {
        return view('forms.index', [
            'forms' => Form::open()->orderBy('title')->get(),
        ]);
    }

    public function show(Form $form)
    {
        $form->load('fields');

        return view('forms.show', [
            'form'   => $form,
            'isOpen' => $form->isOpen(),
        ]);
    }

    public function submit(Request $request, Form $form)
    {
        abort_unless($form->isOpen(), 423, 'Bu form şu anda kapalı.');

        $form->load('fields');

        if ($request->filled('website')) {
            abort(422, 'Spam tespit edildi.');
        }

        $rules = ['website' => 'prohibited'];
        $attributes = [];

        foreach ($form->fields as $field) {
            $rules[$field->name] = $field->validationRules();
            $attributes[$field->name] = $field->label;
        }

        $validated = Validator::make($request->all(), $rules, [], $attributes)->validate();

        $fileFields = $form->fields->where('type', 'file');
        $dataPayload = [];

        foreach ($form->fields as $field) {
            if ($field->type === 'file') {
                if ($file = $request->file($field->name)) {
                    $dataPayload[$field->name] = $file->getClientOriginalName();
                }
                continue;
            }
            $dataPayload[$field->name] = $validated[$field->name] ?? null;
        }

        $submission = FormSubmission::create([
            'form_id'    => $form->id,
            'data'       => $dataPayload,
            'ip_address' => $request->ip(),
            'user_agent' => substr((string) $request->userAgent(), 0, 255),
        ]);

        foreach ($fileFields as $field) {
            if ($file = $request->file($field->name)) {
                $submission->addMedia($file->getRealPath())
                    ->usingFileName($file->getClientOriginalName())
                    ->withCustomProperties(['field_name' => $field->name])
                    ->toMediaCollection('attachments');
            }
        }

        $to = env('FORM_NOTIFICATION_EMAIL', config('mail.from.address'));
        if ($to) {
            Mail::to($to)->queue(new FormSubmissionReceived($submission));
        }

        return redirect()
            ->route('forms.show', $form)
            ->with('submitted', true)
            ->with('successMessage', $form->success_message ?: 'Başvurun başarıyla alındı. Teşekkür ederiz!');
    }
}
