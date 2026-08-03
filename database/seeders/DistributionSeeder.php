<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Permission;
use App\Models\DistributionArea;
use App\Models\Distribution;

class DistributionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Permissions configuration
        $permissions = [
            // Distribution Area Permissions
            [
                'name' => 'Xem danh sách khu vực phân phối',
                'canonical' => 'distribution.area.index',
            ],
            [
                'name' => 'Thêm mới khu vực phân phối',
                'canonical' => 'distribution.area.create',
            ],
            [
                'name' => 'Cập nhật khu vực phân phối',
                'canonical' => 'distribution.area.update',
            ],
            [
                'name' => 'Xóa khu vực phân phối',
                'canonical' => 'distribution.area.destroy',
            ],
            // Distribution Permissions (already exist but let's make sure they are in DB)
            [
                'name' => 'Xem danh sách nhà phân phối',
                'canonical' => 'distribution.index',
            ],
            [
                'name' => 'Thêm mới nhà phân phối',
                'canonical' => 'distribution.create',
            ],
            [
                'name' => 'Cập nhật nhà phân phối',
                'canonical' => 'distribution.update',
            ],
            [
                'name' => 'Xóa nhà phân phối',
                'canonical' => 'distribution.destroy',
            ],
        ];

        $permissionIds = [];
        foreach ($permissions as $perm) {
            $permission = Permission::updateOrCreate(
                ['canonical' => $perm['canonical']],
                ['name' => $perm['name']]
            );
            $permissionIds[] = $permission->id;
        }

        // 2. Assign permissions to Quản trị viên (user_catalogue_id = 1)
        $userCatalogueId = 1;
        $existingRelations = DB::table('user_catalogue_permission')
            ->where('user_catalogue_id', $userCatalogueId)
            ->pluck('permission_id')
            ->toArray();

        $relationsToInsert = [];
        foreach ($permissionIds as $pId) {
            if (!in_array($pId, $existingRelations)) {
                $relationsToInsert[] = [
                    'user_catalogue_id' => $userCatalogueId,
                    'permission_id' => $pId
                ];
            }
        }

        if (count($relationsToInsert) > 0) {
            DB::table('user_catalogue_permission')->insert($relationsToInsert);
        }

        // 3. Clear existing distribution areas and distributions to avoid duplication
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Distribution::truncate();
        DistributionArea::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // 4. Create Regions (parent_id = 0)
        $mienBac = DistributionArea::create([
            'name' => 'Miền Bắc',
            'parent_id' => 0,
            'publish' => 2,
        ]);

        $mienTrung = DistributionArea::create([
            'name' => 'Miền Trung',
            'parent_id' => 0,
            'publish' => 2,
        ]);

        $mienNam = DistributionArea::create([
            'name' => 'Miền Nam',
            'parent_id' => 0,
            'publish' => 2,
        ]);

        // 5. Create Cities / Districts under Regions
        $haNoi = DistributionArea::create([
            'name' => 'Hà Nội',
            'parent_id' => $mienBac->id,
            'publish' => 2,
        ]);

        $haiPhong = DistributionArea::create([
            'name' => 'Hải Phòng',
            'parent_id' => $mienBac->id,
            'publish' => 2,
        ]);

        $daNang = DistributionArea::create([
            'name' => 'Đà Nẵng',
            'parent_id' => $mienTrung->id,
            'publish' => 2,
        ]);

        $tphcm = DistributionArea::create([
            'name' => 'TP. Hồ Chí Minh',
            'parent_id' => $mienNam->id,
            'publish' => 2,
        ]);

        $binhDuong = DistributionArea::create([
            'name' => 'Bình Dương',
            'parent_id' => $mienNam->id,
            'publish' => 2,
        ]);

        // 6. Create Distributors
        // Google map standard embed code
        $mapHN = '<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3724.096814148415!2d105.8018443!3d21.0288118!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3135ab424a50fff9%3A0xbe8c7746cb6de893!2zVmlldG5hbSBOYXRpb25hbCBVbml2ZXJzaXR5LCBIYW5vaQ!5e0!3m2!1sen!2svn!4v1655182939495!5m2!1sen!2svn" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>';
        $mapHP = '<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3728.4616223847775!2d106.6775626!3d20.8513686!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x314a7af27c62bfab%3A0xc3b7ea63f86ebefb!2zVHLGsOG7nW5nIMSQ4bqhaSBo4buNYyBIw6BuZyBowqBpIFZp4buHdCBOYW0!5e0!3m2!1sen!2svn!4v1655183023190!5m2!1sen!2svn" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>';
        $mapDN = '<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3833.819777977462!2d108.2198083!3d16.0748364!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x314218307d81c00f%3A0x2db4df43b67e7225!2zVHLGsOG7nW5nIMSQ4bqhaSBo4buNYyBTxrAgcGjhuqFtIEvhu7kgdGh14bqtdCDEkMOgIE7hurVuZw!5e0!3m2!1sen!2svn!4v1655183112195!5m2!1sen!2svn" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>';
        $mapSG = '<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3919.497554902143!2d106.6993888!3d10.7731215!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31752f40a1b181e5%3A0xc6de96d27402f06!2zQ2jhu6MgQuG6v24gVGjDoG5o!5e0!3m2!1sen!2svn!4v1655183184129!5m2!1sen!2svn" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>';
        $mapBD = '<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3916.8927806509653!2d106.6713988!3d10.9714856!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3174d1264c9d9685%3A0xe54d9c72e2cfc23e!2zVHLGsOG7nW5nIMSQ4bqhaSBo4buNYyBUaOG7pyBE4bqndSBN4buZdA!5e0!3m2!1sen!2svn!4v1655183245199!5m2!1sen!2svn" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>';

        Distribution::create([
            'name' => 'Tổng Kho Phân Phối Tazen Hà Nội',
            'phone' => '0988.123.456',
            'email' => 'hanoi@tazen.vn',
            'address' => 'Số 144 Xuân Thủy, Cầu Giấy, Hà Nội',
            'image' => 'https://picsum.photos/400/300?random=1',
            'map' => $mapHN,
            'province_id' => $mienBac->id,
            'district_id' => $haNoi->id,
            'publish' => 2,
        ]);

        Distribution::create([
            'name' => 'Đại Lý Tazen Hải Phòng',
            'phone' => '0912.444.555',
            'email' => 'haiphong@tazen.vn',
            'address' => 'Số 484 Lạch Tray, Lê Chân, Hải Phòng',
            'image' => 'https://picsum.photos/400/300?random=2',
            'map' => $mapHP,
            'province_id' => $mienBac->id,
            'district_id' => $haiPhong->id,
            'publish' => 2,
        ]);

        Distribution::create([
            'name' => 'Showroom Tazen Đà Nẵng',
            'phone' => '0905.777.888',
            'email' => 'danang@tazen.vn',
            'address' => 'Số 48 Cao Thắng, Hải Châu, Đà Nẵng',
            'image' => 'https://picsum.photos/400/300?random=3',
            'map' => $mapDN,
            'province_id' => $mienTrung->id,
            'district_id' => $daNang->id,
            'publish' => 2,
        ]);

        Distribution::create([
            'name' => 'Tổng Kho Tazen TP. Hồ Chí Minh',
            'phone' => '0933.999.000',
            'email' => 'hcm@tazen.vn',
            'address' => 'Lê Lợi, Phường Bến Thành, Quận 1, TP. HCM',
            'image' => 'https://picsum.photos/400/300?random=4',
            'map' => $mapSG,
            'province_id' => $mienNam->id,
            'district_id' => $tphcm->id,
            'publish' => 2,
        ]);

        Distribution::create([
            'name' => 'Nhà Phân Phối Tazen Bình Dương',
            'phone' => '0944.555.666',
            'email' => 'binhduong@tazen.vn',
            'address' => 'Số 6 Trần Văn Ơn, Phú Hòa, Thủ Dầu Một, Bình Dương',
            'image' => 'https://picsum.photos/400/300?random=5',
            'map' => $mapBD,
            'province_id' => $mienNam->id,
            'district_id' => $binhDuong->id,
            'publish' => 2,
        ]);
    }
}
