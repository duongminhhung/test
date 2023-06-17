<?php

namespace App\Http\Controllers\Web\MasterData;

use App\Exports\MasterData\MasterMaterials;
use App\Exports\MasterData\MasterProduct;
use App\Http\Controllers\Controller;
use App\Libraries\MasterData\MasterLocationLibraries;
use App\Libraries\MasterData\MasterMakerLibraries;
use App\Libraries\MasterData\MasterManufacturingLibraries;
use Illuminate\Http\Request;
use Auth;
use App\Libraries\MasterData\MasterMaterialsLibraries;
use App\Libraries\MasterData\MasterProductLibraries;
class MasterDataController extends Controller
{
    private $materials;
    private $product;
    private $maker;
    private $location;
    private $manufacturing;
    private $export_materials;
    private $export_product;
    
    public function __construct(
        MasterMaterialsLibraries $MasterMaterialsLibraries,
        MasterProductLibraries  $MasterProductLibraries,
        MasterMakerLibraries  $MasterMakerLibraries,
        MasterLocationLibraries  $MasterLocationLibraries,
        MasterManufacturingLibraries  $MasterManufacturingLibraries,
        MasterMaterials  $MasterMaterialsExport,
        MasterProduct  $MasterProductExport,
    ) {
        $this->middleware('auth');
        $this->materials        = $MasterMaterialsLibraries;
        $this->product          = $MasterProductLibraries;
        $this->maker            = $MasterMakerLibraries;
        $this->location         = $MasterLocationLibraries;
        $this->manufacturing    = $MasterManufacturingLibraries;
        $this->export_materials = $MasterMaterialsExport;
        $this->export_product   = $MasterProductExport;
    }

    // Materials
    public function materials()
    {
        $name_symbols = $this->materials->get_name_and_symbols_materials();
        return view('master_data.materials.index', 
        [
            'name_symbols'  => $name_symbols,
        ]);
    }
    public function materials_add_or_update(Request $request)
    {
        $check = $this->materials->check_data($request);
        // dd($check);
        if($check->status)
        {
            $data = $this->materials->add_or_create_data($request);
            return response()->json($data);
        }
        else
        {
            return response()->json($check);
        }
        // dd($request);
    }
    public function materials_destroy(Request $request)
    {
        $data    = $this->materials->destroy($request);
        $arr = [];
        array_push($arr, $data);
        return redirect()->route('setting.materials')->with('success', __('Delete').' '.__('Success'));
    }

    public function materials_import_excel(Request $request)
    {
        set_time_limit(6000000);
        $data  = $this->materials->import_file($request);
        // dd($data);
    	if(count($data)  == 0)
        {
            return redirect()->route('setting.materials')->with('success',__('Success'));
        }
        else
        {
            return redirect()->route('setting.materials')->with('danger',$data);
        }
    }
    
    public function materials_export_excel(Request $request)
    {
        set_time_limit(6000000);

        $data = $this->materials->get_data_materials_export($request);
        $this->export_materials->export($data,$request);

    }

    // Product
    public function product()
    {
        $name_symbols = $this->product->get_name_and_symbols_product();
        $size_product = $this->product->get_size_product();
        $color_product = $this->product->get_colro_product();
        $materials = $this->materials->get_name_and_symbols_materials();
        return view('master_data.product.index',
        [
            'name_symbols'  =>  $name_symbols,
            'size_product'  =>  $size_product,
            'color_product' =>  $color_product,
            'materials'     =>  $materials
        ]);
    }
    public function product_add_or_update(Request $request)
    {
        $check = $this->product->check_data($request);
        // dd($check);
        if($check->status)
        {
            $data = $this->product->add_or_create_data($request);
            return response()->json($data);
        }
        else
        {
            return response()->json($check);
        }
        // dd($request);
    }
    public function product_destroy(Request $request)
    {
        $data    = $this->product->destroy($request);
        $arr = [];
        array_push($arr, $data);
        return redirect()->route('setting.product')->with('success', __('Delete').' '.__('Success'));
    }

    public function product_import_excel(Request $request)
    {
        set_time_limit(6000000);
        $data  = $this->product->import_file2($request);
        // dd($data);
    	if(count($data)  == 0)
        {
            return redirect()->route('setting.product')->with('success',__('Success'));
        }
        else
        {
            return redirect()->route('setting.product')->with('danger',$data);
        }
    }
    public function product_export_excel(Request $request)
    {
        set_time_limit(6000000);

        $data = $this->product->get_data_export($request);
        $this->export_product->export($data,$request);

    }
   
    // Maker
    public function maker()
    {
        $name_symbols = $this->maker->get_name_and_symbols_makers();
        // dd($name_symbols);
        return view('master_data.maker.index', ['name_symbols'=>$name_symbols]);
    }

    public function maker_add_or_update(Request $request)
    {
        $check = $this->maker->check_data($request);
        if($check->status)
        {
            $data = $this->maker->add_or_create_data($request);
            return response()->json($data);
        }
        else
        {
            return response()->json($check);
        }
        // dd($request);
    }
    public function maker_destroy(Request $request)
    {
        $data    = $this->maker->destroy($request);
        $arr = [];
        array_push($arr, $data);
        return redirect()->route('setting.maker')->with('success', __('Delete').' '.__('Success'));
    }

    // Location
    public function location()
    {
        $name_symbols = $this->location->get_name_and_symbols_location();
        $materials = $this->materials->get_name_and_symbols_materials();
        return view('master_data.location.index',
        [
            'name_symbols'=>$name_symbols,
            'materials'=>$materials
        ]);
    }
    public function location_add_or_update(Request $request)
    {
        $check = $this->location->check_data($request);
        // dd($check);
        if($check->status)
        {
            $data = $this->location->add_or_create_data($request);
            return response()->json($data);
        }
        else
        {
            return response()->json($check);
        }
        // dd($request);
    }
    public function location_destroy(Request $request)
    {
        $data    = $this->location->destroy($request);
        $arr = [];
        array_push($arr, $data);
        return redirect()->route('setting.location')->with('success', __('Delete').''.__('Success'));
    }

    public function location_import_excel(Request $request)
    {
        set_time_limit(6000000);
        $data  = $this->location->import_file($request);
        // dd($data);
    	if(count($data)  == 0)
        {
            return redirect()->route('setting.location')->with('success',__('Success'));
        }
        else
        {
            return redirect()->route('setting.location')->with('danger',$data);
        }
    }

    // Manufacturing
    public function manufacturing()
    {
        $name_symbols = $this->manufacturing->get_name_and_symbols_manufacturing();
        $materials = $this->materials->get_name_and_symbols_materials();
        return view('master_data.manufacturing.index',
        [
            'name_symbols'=>$name_symbols,
            'materials'=>$materials
        ]);
    }
    public function manufacturing_add_or_update(Request $request)
    {
        $check = $this->manufacturing->check_data($request);
        // dd($check);
        if($check->status)
        {
            $data = $this->manufacturing->add_or_create_data($request);
            return response()->json($data);
        }
        else
        {
            return response()->json($check);
        }
        // dd($request);
    }
    public function manufacturing_destroy(Request $request)
    {
        $check = $this->manufacturing->check_destroy($request);
        if($check->status)
        {
            $data    = $this->manufacturing->destroy($request);
            return redirect()->route('setting.manufacturing')->with('success', __('Delete').' '.__('Success'));
        }
        else
        {
            return redirect()->route('setting.manufacturing')->with('danger', __('Manufacturing In Stock'));
        }
    }
}
