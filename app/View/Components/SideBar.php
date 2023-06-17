<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Auth;

class SideBar extends Component
{
    const commonRole = 'common';
    const adminRole  = 'admin';
    public $menu;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->menu = $this->menuConfig();
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        return view('components.side-bar');
    }

    public function checkRole(array $roles) : bool {
        foreach($roles as $role) {
            $allow = false;

            switch($role) {
                case self::commonRole:
                    $allow = true;
                    break;
                case self::adminRole:
                    $allow = Auth::user()->level == 9999;
                    break;
                default:
                    $allow = Auth::user()->checkRole($role);
                    break;
            }

            if($allow) {
                return true;
            }
        }

        return false;
    }

    private function menuConfig() : array {
        return [
            [
                'route' => route('home', [0]),
                'class' => 'home',
                'icon'  => 'fas fa-home',
                'title' => __('Dashboard'),
                'roles' => [self::commonRole]
            ],
            [
                'route' => '#',
                'class' => 'production-plan kitting-list',
                'icon'  => 'nav-icon fas fa-folder-open',
                'title' => __('Production Plan'),
                'roles' => [self::commonRole],
                'children' => [
                    [
                        'route' => route('kitting.plan'),
                        'class' => 'plan',
                        'icon'  => 'far fa-circle',
                        'title' => __('Command').' '.__('Production'),
                        'roles' => [self::adminRole, 'view_location']
                    ],
                ]
            ],
            [
                'route' => '#',
                'class' => 'warehouses-management quality-warehouse',
                'icon'  => 'fas fa-warehouse',
                'title' => __('Warehouse System'),
                'roles' => [self::commonRole],
                'children' => [
                    [
                        'route' => route('qualityWarehouses.warehouseLocation', ['type' => 0]),
                        'class' => 'warehouse-location',
                        'icon'  => 'far fa-circle',
                        'title' => __('Archives Warehouse'),
                        'roles' => [self::adminRole, 'view_location']
                    ],
                    [
                        'route' => route('qualityWarehouses.warehouseProduction', ['type' => 1]),
                        'class' => 'production-warehouse',
                        'icon'  => 'far fa-circle',
                        'title' => __('Production Warehouse'),
                        'roles' => [self::adminRole, 'view_line']
                    ],
                    [
                        'route' => route('warehousesManagement.importMaterials'),
                        'class' => 'import-materials',
                        'icon'  => 'far fa-circle',
                        'title' => __('Import'),
                        'roles' => [self::adminRole, 'view_import']
                    ],
                    [
                        'route' => route('qualityWarehouses.exportMaterials.all'),
                        'class' => 'export-materials',
                        'icon'  => 'far fa-circle',
                        'title' => __('Export'),
                        'roles' => [self::adminRole, 'view_export']
                    ],
                    [
                        'route' => route('warehousesManagement.importMaterials.inventories'),
                        'class' => 'stock-materials',
                        'icon'  => 'far fa-circle',
                        'title' => __('Stock'),
                        'roles' => [self::adminRole, 'view_stock']
                    ],
                    [
                        'route' => route('qualityWarehouses.kanban'),
                        'class' => 'kanban',
                        'icon'  => 'far fa-circle',
                        'title' => __('Kanban'),
                        'roles' => [self::adminRole, 'view_kanban']
                    ]
                ]
            ],
            [
                'route' => '#',
                'class' => 'transport-system',
                'icon'  => 'fas fa-truck',
                'title' => __('Transport System'),
                'roles' => [self::commonRole],
                'children' => [
                    [
                        'route' => route('controlAGV.agvControl.index', [0]),
                        'class' => 'agv-control',
                        'icon'  => 'far fa-circle',
                        'title' => __('Control').' '.__('AGV'),
                        'roles' => [self::adminRole, 'create_command']
                    ],
                    [
                        'route' => route('ur-log.index'),
                        'class' => 'ur-log',
                        'icon'  => 'far fa-circle',
                        'title' => __('UR Log'),
                        'roles' => [self::adminRole, 'view_master']
                    ],
                    [
                        'route' => route('controlAGV.transportSystem.createCommand'),
                        'class' => 'create-command-agv',
                        'icon'  => 'far fa-circle',
                        'title' => __('Create').' '.__('Command').' '.__('AGV'),
                        'roles' => [self::adminRole, 'create_command']
                    ],
                    [
                        'route' => route('controlAGV.transportSystem.listCommand'),
                        'class' => 'list-command-agv',
                        'icon'  => 'far fa-circle',
                        'title' => __('List').' '.__('Command').' '.__('AGV'),
                        'roles' => [self::adminRole, 'list_command']
                    ],
                    [
                        'route' => route('controlAGV.transportSystem.efficienciesAGV'),
                        'class' => 'efficiency-agv',
                        'icon'  => 'far fa-circle',
                        'title' => __('Efficiency').' '.__('AGV'),
                        'roles' => [self::adminRole, 'efficiency_agv']
                    ],
                    [
                        'route' => route('controlAGV.documentSoftware'),
                        'class' => 'document-software',
                        'icon'  => 'far fa-circle',
                        'title' => __('Document Software'),
                        'roles' => [self::commonRole]
                    ]
                ]
            ],
            [
                'route' => '#',
                'class' => 'oee',
                'icon'  => 'fas fa-weight',
                'title' => __('Overall Equipment Effectiveness'),
                'roles' => [self::commonRole],
                'children' => [
                    [
                        'route' => route('oee.visualization'),
                        'class' => 'visualization',
                        'icon'  => 'far fa-circle',
                        'title' => __('Visualization'),
                        'roles' => [self::commonRole],
                    ],
                    [
                        'route' => route('oee.report'),
                        'class' => 'report',
                        'icon'  => 'far fa-circle',
                        'title' => __('Report'),
                        'roles' => [self::commonRole],
                    ]
                ]
            ],
            [
                'route' => '#',
                'class' => 'setting print-label',
                'icon'  => 'fas fa-cogs',
                'title' => __('Setting'),
                'roles' => [self::adminRole, 'view_master'],
                'children' => [
                    [
                        'route' => route('masterData.unit'),
                        'class' => 'setting-unit',
                        'icon'  => 'far fa-circle',
                        'title' => __('Unit'),
                        'roles' => [self::commonRole]
                    ],
                    [
                        'route' => route('masterData.unit'),
                        'class' => 'setting-unit',
                        'icon'  => 'far fa-circle',
                        'title' => __('Materials'),
                        'roles' => [self::commonRole]
                    ],
                    [
                        'route' => route('masterData.unit'),
                        'class' => 'setting-unit',
                        'icon'  => 'far fa-circle',
                        'title' => __('Supplier'),
                        'roles' => [self::commonRole]
                    ],
                    [
                        'route' => route('masterData.packing'),
                        'class' => 'setting-packing',
                        'icon'  => 'far fa-circle',
                        'title' => __('Packing'),
                        'roles' => [self::commonRole],
                        'hide'  => true
                    ],
                    [
                        'route' => route('masterData.shift'),
                        'class' => 'setting-shift',
                        'icon'  => 'far fa-circle',
                        'title' => __('Shift'),
                        'roles' => [self::commonRole],
                    ],
                    [
                        'route' => route('masterData.limitProduct'),
                        'class' => 'setting-limitProduct',
                        'icon'  => 'far fa-circle',
                        'title' => __('Limit') .' '.__('Product'),
                        'roles' => [self::commonRole],
                    ],
                    [
                        'route' => route('masterData.status'),
                        'class' => 'setting-status',
                        'icon'  => 'far fa-circle',
                        'title' => __('Status'),
                        'roles' => [self::commonRole],
                    ],
                    [
                        'route' => route('masterData.materials'),
                        'class' => 'setting-materials',
                        'icon'  => 'far fa-circle',
                        'title' => __('Product'),
                        'roles' => [self::commonRole]
                    ],
                    [
                        'route' => route('printLabel'),
                        'class' => 'print-label',
                        'icon'  => 'far fa-circle',
                        'title' => __('Box'),
                        'roles' => [self::commonRole]
                    ],
                    [
                        'route' => route('masterData.groupMaterials'),
                        'class' => 'setting-group-materials',
                        'icon'  => 'far fa-circle',
                        'title' => __('Shelves'),
                        'roles' => [self::commonRole]
                    ],
                    [
                        'route' => route('masterData.agv'),
                        'class' => 'setting-AGV',
                        'icon'  => 'far fa-circle',
                        'title' => __('AGV'),
                        'roles' => [self::commonRole]
                    ],
                    [
                        'route' => route('masterData.layouts'),
                        'class' => 'setting-layouts',
                        'icon'  => 'far fa-circle',
                        'title' => __('Layout'),
                        'roles' => [self::commonRole]
                    ],
                    [
                        'route' => route('masterData.warehouses', ['type' => 0]),
                        'class' => 'setting-warehouse',
                        'icon'  => 'far fa-circle',
                        'title' => __('Setting').' '.__('Archives Warehouse'),
                        'roles' => [self::commonRole]
                    ],
                    [
                        'route' => route('masterData.warehousesProduction', ['type' => 1]),
                        'class' => 'setting-production-warehouse',
                        'icon'  => 'far fa-circle',
                        'title' => __('Setting').' '.'Line'.' '.__('Production'),
                        'roles' => [self::commonRole]
                    ],
                    [
                        'route' => route('masterData.machine'),
                        'class' => 'setting-machine',
                        'icon'  => 'far fa-circle',
                        'title' => __('Machine'),
                        'roles' => [self::commonRole]
                    ]
                ]
            ],
            [
                'route' => route('account'),
                'class' => 'setting-account',
                'icon'  => 'fas fa-user',
                'title' => __('Account'),
                'roles' => [self::adminRole, 'view_master']
            ]
        ];
    }
}
