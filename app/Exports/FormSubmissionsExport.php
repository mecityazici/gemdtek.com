<?php

namespace App\Exports;

use App\Models\Form as FormModel;
use Illuminate\Contracts\Support\Responsable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class FormSubmissionsExport implements FromCollection, WithHeadings, WithMapping
{
    public function __construct(private FormModel $form)
    {
    }

    public function collection()
    {
        return $this->form->submissions()->latest()->get();
    }

    public function headings(): array
    {
        $headings = ['#', 'Gönderim zamanı', 'IP'];
        foreach ($this->form->fields as $field) {
            $headings[] = $field->label;
        }
        return $headings;
    }

    public function map($submission): array
    {
        $row = [
            $submission->id,
            $submission->created_at?->format('Y-m-d H:i:s'),
            $submission->ip_address,
        ];

        foreach ($this->form->fields as $field) {
            $value = $submission->data[$field->name] ?? null;
            if (is_array($value)) {
                $value = implode(', ', $value);
            }
            $row[] = $value;
        }

        return $row;
    }
}
