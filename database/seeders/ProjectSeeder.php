<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ProjectSeeder extends Seeder
{
    public function run(): void
    {
        $captain = User::firstOrCreate(
            ['email' => 'kaptan@gemdtek.com'],
            [
                'name'              => 'Burak Aksoy (Test Kaptan)',
                'password'          => Hash::make('Captain!2026'),
                'email_verified_at' => now(),
            ],
        );

        if (! $captain->hasRole('team_captain')) {
            $captain->assignRole('team_captain');
        }

        $this->command->line("Test takım kaptanı: kaptan@gemdtek.com / Captain!2026");

        $projects = [
            [
                'slug'              => 'teknofest-rov',
                'name'              => 'TEKNOFEST İnsansız Sualtı Sistemleri ROV Takımı',
                'summary'           => 'TEKNOFEST insansız sualtı sistemleri yarışması için tasarlanan, otonom görev yürütebilen orta sınıf ROV.',
                'description'       => '<p>Tasarımı, hem teleoperasyon hem de görüntü işleme tabanlı otonom mod destekleyen modüler bir mimari üzerine kuruludur. ROS 2 omurgası, sualtı koşullarına dayanıklı sızdırmaz gövde ve değiştirilebilir görev paketleri sayesinde farklı yarışma kategorilerine uyarlanabilir.</p>',
                'problem_statement' => 'Sualtı yapılarının (iskele ayakları, deniz kabloları, akuakültür ağları) periyodik denetimi, insanlı dalışlara göre çok daha güvenli ve maliyet etkin bir şekilde otonom ROV ile yapılabilir.',
                'year'              => 2025,
                'status'            => 'active',
                'order'             => 10,
                'captain_user_id'   => $captain->id,
                'specs'             => [
                    ['category' => 'mekanik',    'key' => 'Gövde materyali',  'value' => 'Karbon fiber kompozit'],
                    ['category' => 'mekanik',    'key' => 'Boyut (LxWxH)',    'value' => '850 × 600 × 450 mm'],
                    ['category' => 'mekanik',    'key' => 'Ağırlık',          'value' => '18 kg'],
                    ['category' => 'elektronik', 'key' => 'Pil sistemi',      'value' => '22.2V 16Ah LiPo'],
                    ['category' => 'elektronik', 'key' => 'Tahrik',           'value' => '6 × T200 BlueRobotics thruster'],
                    ['category' => 'yazilim',    'key' => 'Çatı yazılımı',    'value' => 'ROS 2 Humble + Python'],
                    ['category' => 'yazilim',    'key' => 'Görüntü işleme',   'value' => 'OpenCV + YOLOv8'],
                    ['category' => 'performans', 'key' => 'Maks. derinlik',   'value' => '100 m'],
                    ['category' => 'performans', 'key' => 'Yatay hız',        'value' => '1.8 m/s'],
                    ['category' => 'performans', 'key' => 'Görev süresi',     'value' => '~2 saat'],
                ],
                'members' => [
                    ['name' => 'Burak Aksoy',   'role' => 'Takım Kaptanı',          'is_captain' => true],
                    ['name' => 'Zeynep Aydın',  'role' => 'Mekanik Tasarım Lideri', 'is_captain' => false],
                    ['name' => 'Ali Şahin',     'role' => 'Otonom Yazılım Lideri',  'is_captain' => false],
                    ['name' => 'Deniz Aksoy',   'role' => 'Elektronik Donanım',     'is_captain' => false],
                    ['name' => 'Eda Korkmaz',   'role' => 'Görüntü İşleme',         'is_captain' => false],
                ],
            ],
            [
                'slug'              => 'hidrojen-kiyi-gemisi',
                'name'              => 'Alternatif Yakıtlı Kıyı Gemisi Tasarımı',
                'summary'           => 'PEM yakıt hücreli, sıfır emisyonlu kıyı gemisi konsept tasarımı. Uluslararası öğrenci yarışmasında finalist.',
                'description'       => '<p>İç sularda ve kısa mesafeli kıyı seferlerinde fosil yakıt kullanımına alternatif olarak tasarlanan hidrojen yakıt hücreli kıyı gemisi konseptidir. Tasarımda hidrojen depolama emniyeti, ağırlık dengelemesi ve sefer profiline uygun menzil hesabı öne çıkar.</p>',
                'problem_statement' => 'Türkiye kıyı taşımacılığında karbon emisyonu azaltımı için pratik, ölçeklenebilir ve maliyet öngörülebilir bir alternatif yakıt mimarisi.',
                'year'              => 2024,
                'status'            => 'completed',
                'order'             => 20,
                'captain_user_id'   => null,
                'specs'             => [
                    ['category' => 'mekanik',    'key' => 'Boy (LOA)',            'value' => '24 m'],
                    ['category' => 'mekanik',    'key' => 'Genişlik',             'value' => '7.2 m'],
                    ['category' => 'mekanik',    'key' => 'Hafif tonaj',          'value' => '85 t'],
                    ['category' => 'elektronik', 'key' => 'Yakıt sistemi',        'value' => 'PEM Fuel Cell — 250 kW'],
                    ['category' => 'elektronik', 'key' => 'Hidrojen depolama',    'value' => '350 bar Tip III tank, 80 kg'],
                    ['category' => 'performans', 'key' => 'Maks. hız',            'value' => '18 knot'],
                    ['category' => 'performans', 'key' => 'Menzil',               'value' => '320 NM'],
                    ['category' => 'performans', 'key' => 'CO₂ emisyonu',         'value' => '0 g/kWh (kullanımda)'],
                ],
                'members' => [
                    ['name' => 'Mehmet Demir',  'role' => 'Proje Yöneticisi',     'is_captain' => true],
                    ['name' => 'Selin Kara',    'role' => 'Gemi Tasarımı',        'is_captain' => false],
                    ['name' => 'Can Özkan',     'role' => 'Yakıt Sistemi Analizi','is_captain' => false],
                    ['name' => 'Elif Yıldız',   'role' => 'Hidrostatik Analiz',   'is_captain' => false],
                ],
            ],
            [
                'slug'              => 'otonom-yelkenli',
                'name'              => 'Otonom Yelkenli Tekne Projesi',
                'summary'           => 'Rüzgâr enerjisiyle otonom seyir yapabilen, 6 metrelik konsept yelkenli — yıl içinde havuz testlerine başlayacak.',
                'description'       => '<p>Düşük güç tüketimiyle uzun süre denizde kalabilen otonom yelkenli platformu. Hedef uygulamalar: oşinografik veri toplama, deniz koruma alanı izleme, balıkçılık dışı bölge gözetimi.</p>',
                'problem_statement' => 'Geniş deniz alanlarında uzun soluklu sensör görevleri için yakıt bağımsız, sessiz ve düşük maliyetli otonom platform ihtiyacı.',
                'year'              => 2026,
                'status'            => 'upcoming',
                'order'             => 30,
                'captain_user_id'   => null,
                'specs'             => [
                    ['category' => 'mekanik',    'key' => 'LOA',              'value' => '6 m'],
                    ['category' => 'mekanik',    'key' => 'Yelken alanı',     'value' => '18 m²'],
                    ['category' => 'elektronik', 'key' => 'Sensör paketi',    'value' => 'IMU + GPS RTK + 2D Lidar + AIS'],
                    ['category' => 'elektronik', 'key' => 'Güç',              'value' => 'Solar 200W + 1kWh batarya'],
                    ['category' => 'yazilim',    'key' => 'Path planning',    'value' => 'A* + COLREGs uyumluluk'],
                    ['category' => 'yazilim',    'key' => 'İletişim',         'value' => 'Iridium SBD + LTE'],
                ],
                'members' => [
                    ['name' => 'Ali Şahin',     'role' => 'Otonomi Mimarı',       'is_captain' => true],
                    ['name' => 'Eda Korkmaz',   'role' => 'Sensör Entegrasyonu',  'is_captain' => false],
                    ['name' => 'Zeynep Aydın',  'role' => 'Yapısal Tasarım',      'is_captain' => false],
                ],
            ],
        ];

        foreach ($projects as $data) {
            $specs   = $data['specs'];
            $members = $data['members'];
            unset($data['specs'], $data['members']);

            $project = Project::firstOrCreate(['slug' => $data['slug']], $data);

            if ($project->specs()->doesntExist()) {
                foreach ($specs as $i => $spec) {
                    $project->specs()->create($spec + ['order' => $i * 10]);
                }
            }

            if ($project->members()->doesntExist()) {
                foreach ($members as $i => $member) {
                    $project->members()->create($member + ['order' => $i * 10]);
                }
            }
        }
    }
}
