<?php

namespace App\Models;

use App\Concerns\LogsFillableActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FormField extends Model
{
    use LogsFillableActivity;

    public const TYPES = [
        'text' => 'Kısa metin',
        'textarea' => 'Uzun metin',
        'email' => 'E-posta',
        'tel' => 'Telefon',
        'url' => 'URL',
        'number' => 'Sayı',
        'date' => 'Tarih',
        'select' => 'Açılır liste (tek seçim)',
        'radio' => 'Radyo buton (tek seçim)',
        'checkbox' => 'Çoklu seçim kutusu',
        'file' => 'Dosya yükleme',
    ];

    protected $fillable = [
        'form_id', 'type', 'name', 'label', 'placeholder',
        'help_text', 'is_required', 'options', 'order',
    ];

    protected $casts = [
        'is_required' => 'boolean',
        'options' => 'array',
        'order' => 'integer',
    ];

    public function form(): BelongsTo
    {
        return $this->belongsTo(Form::class);
    }

    public function hasOptions(): bool
    {
        return in_array($this->type, ['select', 'radio', 'checkbox'], true);
    }

    public function validationRules(): array
    {
        $rules = $this->is_required ? ['required'] : ['nullable'];

        $rules = match ($this->type) {
            'email' => array_merge($rules, ['email:rfc', 'max:255']),
            'tel' => array_merge($rules, ['string', 'max:32']),
            'url' => array_merge($rules, ['url', 'max:500']),
            'number' => array_merge($rules, ['numeric']),
            'date' => array_merge($rules, ['date']),
            'textarea' => array_merge($rules, ['string', 'max:5000']),
            'select',
            'radio' => array_merge($rules, ['string', 'in:'.implode(',', $this->options ?? [])]),
            'checkbox' => array_merge($rules, ['array']),
            'file' => array_merge($rules, ['file', 'max:10240']),
            default => array_merge($rules, ['string', 'max:500']),
        };

        return $rules;
    }
}
