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
                'name'              => [
                    'tr' => 'TEKNOFEST İnsansız Sualtı Sistemleri ROV Takımı',
                    'en' => 'TEKNOFEST Unmanned Underwater Systems ROV Team',
                ],
                'summary'           => [
                    'tr' => 'TEKNOFEST insansız sualtı sistemleri yarışması için tasarlanan, otonom görev yürütebilen orta sınıf ROV.',
                    'en' => 'A mid-class ROV designed for the TEKNOFEST unmanned underwater systems competition, capable of executing autonomous missions.',
                ],
                'description'       => [
                    'tr' => '<p>Tasarımı, hem teleoperasyon hem de görüntü işleme tabanlı otonom mod destekleyen modüler bir mimari üzerine kuruludur. ROS 2 omurgası, sualtı koşullarına dayanıklı sızdırmaz gövde ve değiştirilebilir görev paketleri sayesinde farklı yarışma kategorilerine uyarlanabilir.</p>',
                    'en' => '<p>The design is built on a modular architecture supporting both teleoperation and computer-vision-based autonomous mode. With a ROS 2 backbone, watertight hull built for underwater conditions, and swappable mission payloads, it adapts to different competition categories.</p>',
                ],
                'problem_statement' => [
                    'tr' => 'Sualtı yapılarının (iskele ayakları, deniz kabloları, akuakültür ağları) periyodik denetimi, insanlı dalışlara göre çok daha güvenli ve maliyet etkin bir şekilde otonom ROV ile yapılabilir.',
                    'en' => 'Periodic inspection of underwater structures (pier legs, submarine cables, aquaculture nets) can be done much more safely and cost-effectively with autonomous ROVs than with crewed dives.',
                ],
                'year'              => 2025,
                'status'            => 'active',
                'order'             => 10,
                'captain_user_id'   => $captain->id,
                'specs'             => [
                    ['category' => 'mekanik',    'key' => ['tr' => 'Gövde materyali',  'en' => 'Hull material'],     'value' => ['tr' => 'Karbon fiber kompozit',          'en' => 'Carbon fiber composite']],
                    ['category' => 'mekanik',    'key' => ['tr' => 'Boyut (LxWxH)',    'en' => 'Dimensions (LxWxH)'],'value' => ['tr' => '850 × 600 × 450 mm',             'en' => '850 × 600 × 450 mm']],
                    ['category' => 'mekanik',    'key' => ['tr' => 'Ağırlık',          'en' => 'Weight'],            'value' => ['tr' => '18 kg',                          'en' => '18 kg']],
                    ['category' => 'elektronik', 'key' => ['tr' => 'Pil sistemi',      'en' => 'Battery'],           'value' => ['tr' => '22.2V 16Ah LiPo',                'en' => '22.2V 16Ah LiPo']],
                    ['category' => 'elektronik', 'key' => ['tr' => 'Tahrik',           'en' => 'Propulsion'],        'value' => ['tr' => '6 × T200 BlueRobotics thruster', 'en' => '6 × T200 BlueRobotics thrusters']],
                    ['category' => 'yazilim',    'key' => ['tr' => 'Çatı yazılımı',    'en' => 'Framework'],         'value' => ['tr' => 'ROS 2 Humble + Python',          'en' => 'ROS 2 Humble + Python']],
                    ['category' => 'yazilim',    'key' => ['tr' => 'Görüntü işleme',   'en' => 'Computer vision'],   'value' => ['tr' => 'OpenCV + YOLOv8',                'en' => 'OpenCV + YOLOv8']],
                    ['category' => 'performans', 'key' => ['tr' => 'Maks. derinlik',   'en' => 'Max depth'],         'value' => ['tr' => '100 m',                          'en' => '100 m']],
                    ['category' => 'performans', 'key' => ['tr' => 'Yatay hız',        'en' => 'Horizontal speed'],  'value' => ['tr' => '1.8 m/s',                        'en' => '1.8 m/s']],
                    ['category' => 'performans', 'key' => ['tr' => 'Görev süresi',     'en' => 'Mission duration'],  'value' => ['tr' => '~2 saat',                        'en' => '~2 hours']],
                ],
                'members' => [
                    ['name' => 'Burak Aksoy',   'role' => ['tr' => 'Takım Kaptanı',         'en' => 'Team Captain'],         'is_captain' => true],
                    ['name' => 'Zeynep Aydın',  'role' => ['tr' => 'Mekanik Tasarım Lideri','en' => 'Mechanical Design Lead'], 'is_captain' => false],
                    ['name' => 'Ali Şahin',     'role' => ['tr' => 'Otonom Yazılım Lideri', 'en' => 'Autonomy Software Lead'], 'is_captain' => false],
                    ['name' => 'Deniz Aksoy',   'role' => ['tr' => 'Elektronik Donanım',    'en' => 'Electronics Hardware'],   'is_captain' => false],
                    ['name' => 'Eda Korkmaz',   'role' => ['tr' => 'Görüntü İşleme',        'en' => 'Computer Vision'],        'is_captain' => false],
                ],
            ],
            [
                'slug'              => 'hidrojen-kiyi-gemisi',
                'name'              => [
                    'tr' => 'Alternatif Yakıtlı Kıyı Gemisi Tasarımı',
                    'en' => 'Alternative-Fuel Coastal Vessel Design',
                ],
                'summary'           => [
                    'tr' => 'PEM yakıt hücreli, sıfır emisyonlu kıyı gemisi konsept tasarımı. Uluslararası öğrenci yarışmasında finalist.',
                    'en' => 'PEM fuel-cell, zero-emission coastal vessel concept. Finalist in an international student competition.',
                ],
                'description'       => [
                    'tr' => '<p>İç sularda ve kısa mesafeli kıyı seferlerinde fosil yakıt kullanımına alternatif olarak tasarlanan hidrojen yakıt hücreli kıyı gemisi konseptidir.</p>',
                    'en' => '<p>A hydrogen fuel-cell coastal vessel concept designed as an alternative to fossil fuels in inland waters and short-distance coastal routes.</p>',
                ],
                'problem_statement' => [
                    'tr' => 'Türkiye kıyı taşımacılığında karbon emisyonu azaltımı için pratik, ölçeklenebilir ve maliyet öngörülebilir bir alternatif yakıt mimarisi.',
                    'en' => 'A practical, scalable, cost-predictable alternative-fuel architecture for reducing carbon emissions in Turkish coastal transport.',
                ],
                'year'              => 2024,
                'status'            => 'completed',
                'order'             => 20,
                'captain_user_id'   => null,
                'specs'             => [
                    ['category' => 'mekanik',    'key' => ['tr' => 'Boy (LOA)',         'en' => 'Length (LOA)'],   'value' => ['tr' => '24 m',                          'en' => '24 m']],
                    ['category' => 'mekanik',    'key' => ['tr' => 'Genişlik',          'en' => 'Beam'],           'value' => ['tr' => '7.2 m',                         'en' => '7.2 m']],
                    ['category' => 'mekanik',    'key' => ['tr' => 'Hafif tonaj',       'en' => 'Lightship tonnage'], 'value' => ['tr' => '85 t',                       'en' => '85 t']],
                    ['category' => 'elektronik', 'key' => ['tr' => 'Yakıt sistemi',     'en' => 'Fuel system'],    'value' => ['tr' => 'PEM Fuel Cell — 250 kW',        'en' => 'PEM Fuel Cell — 250 kW']],
                    ['category' => 'elektronik', 'key' => ['tr' => 'Hidrojen depolama', 'en' => 'Hydrogen storage'], 'value' => ['tr' => '350 bar Tip III tank, 80 kg', 'en' => '350 bar Type III tank, 80 kg']],
                    ['category' => 'performans', 'key' => ['tr' => 'Maks. hız',         'en' => 'Max speed'],      'value' => ['tr' => '18 knot',                       'en' => '18 knots']],
                    ['category' => 'performans', 'key' => ['tr' => 'Menzil',            'en' => 'Range'],          'value' => ['tr' => '320 NM',                        'en' => '320 NM']],
                    ['category' => 'performans', 'key' => ['tr' => 'CO₂ emisyonu',      'en' => 'CO₂ emissions'],  'value' => ['tr' => '0 g/kWh (kullanımda)',          'en' => '0 g/kWh (in-use)']],
                ],
                'members' => [
                    ['name' => 'Mehmet Demir',  'role' => ['tr' => 'Proje Yöneticisi',     'en' => 'Project Manager'],     'is_captain' => true],
                    ['name' => 'Selin Kara',    'role' => ['tr' => 'Gemi Tasarımı',        'en' => 'Naval Architecture'],  'is_captain' => false],
                    ['name' => 'Can Özkan',     'role' => ['tr' => 'Yakıt Sistemi Analizi','en' => 'Fuel System Analysis'],'is_captain' => false],
                    ['name' => 'Elif Yıldız',   'role' => ['tr' => 'Hidrostatik Analiz',   'en' => 'Hydrostatic Analysis'],'is_captain' => false],
                ],
            ],
            [
                'slug'              => 'otonom-yelkenli',
                'name'              => [
                    'tr' => 'Otonom Yelkenli Tekne Projesi',
                    'en' => 'Autonomous Sailing Vessel Project',
                ],
                'summary'           => [
                    'tr' => 'Rüzgâr enerjisiyle otonom seyir yapabilen, 6 metrelik konsept yelkenli — yıl içinde havuz testlerine başlayacak.',
                    'en' => 'A 6-meter concept sailboat capable of autonomous wind-powered voyaging — pool testing starts this year.',
                ],
                'description'       => [
                    'tr' => '<p>Düşük güç tüketimiyle uzun süre denizde kalabilen otonom yelkenli platformu. Hedef uygulamalar: oşinografik veri toplama, deniz koruma alanı izleme, balıkçılık dışı bölge gözetimi.</p>',
                    'en' => '<p>An autonomous sailboat platform capable of long endurance at sea with very low power draw. Target applications: oceanographic data collection, marine protected area monitoring, no-fishing zone surveillance.</p>',
                ],
                'problem_statement' => [
                    'tr' => 'Geniş deniz alanlarında uzun soluklu sensör görevleri için yakıt bağımsız, sessiz ve düşük maliyetli otonom platform ihtiyacı.',
                    'en' => 'The need for a fuel-independent, silent, low-cost autonomous platform for long-duration sensor missions across large sea areas.',
                ],
                'year'              => 2026,
                'status'            => 'upcoming',
                'order'             => 30,
                'captain_user_id'   => null,
                'specs'             => [
                    ['category' => 'mekanik',    'key' => ['tr' => 'LOA',           'en' => 'LOA'],           'value' => ['tr' => '6 m',                                 'en' => '6 m']],
                    ['category' => 'mekanik',    'key' => ['tr' => 'Yelken alanı',  'en' => 'Sail area'],     'value' => ['tr' => '18 m²',                               'en' => '18 m²']],
                    ['category' => 'elektronik', 'key' => ['tr' => 'Sensör paketi', 'en' => 'Sensor suite'],  'value' => ['tr' => 'IMU + GPS RTK + 2D Lidar + AIS',       'en' => 'IMU + GPS RTK + 2D Lidar + AIS']],
                    ['category' => 'elektronik', 'key' => ['tr' => 'Güç',           'en' => 'Power'],         'value' => ['tr' => 'Solar 200W + 1kWh batarya',           'en' => 'Solar 200W + 1kWh battery']],
                    ['category' => 'yazilim',    'key' => ['tr' => 'Rota planlama', 'en' => 'Path planning'], 'value' => ['tr' => 'A* + COLREGs uyumluluk',              'en' => 'A* + COLREGs compliance']],
                    ['category' => 'yazilim',    'key' => ['tr' => 'İletişim',      'en' => 'Communication'], 'value' => ['tr' => 'Iridium SBD + LTE',                   'en' => 'Iridium SBD + LTE']],
                ],
                'members' => [
                    ['name' => 'Ali Şahin',     'role' => ['tr' => 'Otonomi Mimarı',      'en' => 'Autonomy Architect'],   'is_captain' => true],
                    ['name' => 'Eda Korkmaz',   'role' => ['tr' => 'Sensör Entegrasyonu', 'en' => 'Sensor Integration'],   'is_captain' => false],
                    ['name' => 'Zeynep Aydın',  'role' => ['tr' => 'Yapısal Tasarım',     'en' => 'Structural Design'],    'is_captain' => false],
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
