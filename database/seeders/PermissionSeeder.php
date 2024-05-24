<?php

namespace Database\Seeders;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    public function run()
    {
        $permissions = [
            
            'permission-list',
            'permission-create',
            'permission-edit',
            'permission-delete',

            'role-list',
            'role-create',
            'role-edit',
            'role-delete',

            'user-list',
            'user-create',
            'user-edit',
            'user-delete',

            'vehicle_type-list',
            'vehicle_type-create',
            'vehicle_type-edit',
            'vehicle_type-delete',

            'time_slot-list',
            'time_slot-create',
            'time_slot-edit',
            'time_slot-delete',

            'item-list',
            'item-create',
            'item-edit',
            'item-delete',

            'addon-list',
            'addon-create',
            'addon-edit',
            'addon-delete',

            'addon_rate_list-list',
            'addon_rate_list-create',
            'addon_rate_list-edit',
            'addon_rate_list-delete',

            'rate_list-list',
            'rate_list-create',
            'rate_list-edit',
            'rate_list-delete',

            'delivery_charge-list',
            'delivery_charge-create',
            'delivery_charge-edit',
            'delivery_charge-delete',


            'vat-list',
            'vat-create',
            'vat-edit',
            'vat-delete',

            

            'service-list',
            'service-create',
            'service-edit',
            'service-delete',

            'customer-list',
            'customer-create',
            'customer-edit',
            'customer-delete',

            'customer_type-list',
            'customer_type-create',
            'customer_type-edit',
            'customer_type-delete',

            'customer_wallet-list',
            'customer_wallet-create',
            'customer_wallet-edit',
            'customer_wallet-delete',

            'wash_house-list',
            'wash_house-create',
            'wash_house-edit',
            'wash_house-delete',

            'distribution_hub-list',
            'distribution_hub-create',
            'distribution_hub-edit',
            'distribution_hub-delete',

            'csr_dashboard-list',
            'csr_dashboard-create',
            'csr_dashboard-edit',
            'csr_dashboard-delete',

            'rs_dashboard-list',
            'rs_dashboard-create',
            'rs_dashboard-edit',
            'rs_dashboard-delete',

            'os_dashboard-list',
            'os_dashboard-create',
            'os_dashboard-edit',
            'os_dashboard-delete',

            'wh_dashboard-list',
            'wh_dashboard-create',
            'wh_dashboard-edit',
            'wh_dashboard-delete',


            'complaint-list',
            'complaint-create',
            'complaint-edit',
            'complaint-delete',

            'area-list',
            'area-create',
            'area-edit',
            'area-delete',

            'zone-list',
            'zone-create',
            'zone-edit',
            'zone-delete',

            'rider-list',
            'rider-create',
            'rider-edit',
            'rider-delete',

            'order-list',
            'order-create',
            'order-edit',
            'order-delete',

            'order_detail-list',
            'order_detail-create',
            'order_detail-edit',
            'order_detail-delete',

            'order_verify-list',
            'order_verify-create',
            'order_verify-edit',
            'order_verify-delete',

            'order_inspect-list',
            'order_inspect-create',
            'order_inspect-edit',
            'order_inspect-delete',

            'order_pack-list',
            'order_pack-create',
            'order_pack-edit',
            'order_pack-delete',

            'order_hfq-list',
            'order_hfq-create',
            'order_hfq-edit',
            'order_hfq-delete',

            'Wash_house_order-list',
            'Wash_house_order-create',
            'Wash_house_order-edit',
            'Wash_house_order-delete',

            'Wash_house_summary-list',
            'Wash_house_summary-create',
            'Wash_house_summary-edit',
            'Wash_house_summary-delete',
            
            'holiday-list',
            'holiday-create',
            'holiday-edit',
            'holiday-delete',

            'route_plan-list',
            'route_plan-create',
            'route_plan-edit',
            'route_plan-delete',

            'packing-list',
            'packing-create',
            'packing-edit',
            'packing-delete',

            'tagging-list',
            'tagging-create',
            'tagging-edit',
            'tagging-delete',

            'wh_billing-list',
            'wh_billing-create',
            'wh_billing-edit',
            'wh_billing-delete',

            'special_polybag-print',
            'special_tag-print',
            'special_wash_house_assign',


            'retainer-list',
            'retainer-create',
            'retainer-edit',
            'retainer-delete',

            'admin_dashboard-list',
            'admin_dashboard-create',
            'admin_dashboard-edit',
            'admin_dashboard-delete',
            
         ];
         foreach ($permissions as $permission) {
              Permission::create(['name' => $permission]);
         }
     }
}
