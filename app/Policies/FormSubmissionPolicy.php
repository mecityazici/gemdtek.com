<?php

namespace App\Policies;

use App\Models\FormSubmission;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class FormSubmissionPolicy
{
    use HandlesAuthorization;

    /*
     * Başvurular FormResource altındaki bir relation manager ile yönetilir; ayrı
     * bir resource/permission seti yok. Erişimi form izinlerine bağlıyoruz:
     * görüntüleme view_*_form, silme delete_*_form. Böylece view-only editor
     * KVKK-hassas başvuru kayıtlarını silemiyor; super_admin Shield ile bypass eder.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_form');
    }

    public function view(User $user, FormSubmission $submission): bool
    {
        return $user->can('view_form');
    }

    public function create(User $user): bool
    {
        return false; // başvurular yalnızca public formdan gelir
    }

    public function update(User $user, FormSubmission $submission): bool
    {
        return false; // gönderilen başvuru değiştirilemez
    }

    public function delete(User $user, FormSubmission $submission): bool
    {
        return $user->can('delete_form');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_form');
    }
}
