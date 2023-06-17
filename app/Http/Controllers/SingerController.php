<?php

namespace App\Http\Controllers;

use App\Models\Singer;
use App\Http\Requests\StoreSingerRequest;
use App\Http\Requests\UpdateSingerRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;

class SingerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // $singer =  DB::table('singers')->get();
        // return view('welcome', compact('singer'));
        // dd($messages);
        return view('welcome');
    }
    public function action(Request $request)
    {
        if ($request->ajax()) {
            $output = '';
            $query = $request->get('query');

            if ($query != '') {
                $data = DB::table('singers')->where('name', 'like', '%' . $query . '%')
                    ->orWhere('price', 'like', '%' . $query . '%')
                    ->orWhere('follower', 'like', '%' . $query . '%')
                    ->orWhere('img', 'like', '%' . $query . '%')
                    ->orderBy('id', 'desc')->get();
            } else {
                $data = DB::table('singers')->orderBy('id', 'desc')->get();
            }
            $total_row = $data->count();
            if ($total_row > 0) {
                foreach ($data as $row) {
                    $output .= ' <tr>                    
                     <td style="width: 10%"><input class="checkbox1" type="checkbox" id="checkbox' . $row->id . '" onchange="handleCheckboxChange(this)" value="' . $row->id . '"></td>
                   <td style="width: 22.5%; text-align: center"><img style="height: 30%; width: 33%; text-align: center" src="' . asset('images/' . $row->img) . '"></td>
                     <td>' . $row->name . '</td>
                     <td>' . $row->price . '</td>
                      <td>' . $row->follower . '</td> 
                     ';
                }
            } else {
                $output = ' <tr> <td align="center" colspan="5">No Data Found</td> </tr> ';
            }
            $data = array('table_data'  => $output);
            echo json_encode($data);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {


        $name = $request->name;
        $price = $request->price;
        $follower = $request->follower;
        $image = $request->image;
        if ($name !== null && $price !== null && $follower !== null && $image !== null) {
            $imageName = time() . '.' . $request->image->getClientOriginalExtension();
            $request->image->move(public_path('images'), $imageName);
            DB::table('singers')->insert([
                'name' => $name,
                'price' => $price,
                'follower' => $follower,
                'img' => $imageName
            ]);
        }
        return redirect()->route('index');
    }
    public function import(Request $request)
    {
        
        $this->validate($request, [
            'uploaded_file' => 'required|file|mimes:xls,xlsx'
        ]);
        $the_file = $request->file('uploaded_file');
        try {
            $spreadsheet = IOFactory::load($the_file->getRealPath());
            $sheet        = $spreadsheet->getActiveSheet();
            $row_limit    = $sheet->getHighestDataRow();
            $column_limit = $sheet->getHighestDataColumn();
            $row_range    = range(2, $row_limit);
            $column_range = range('D', $column_limit);
            $startcount = 2;
            $data = array();
            foreach ($row_range as $row) {
                $data[] = [
                    'name' => $sheet->getCell('A' . $row)->getValue(),
                    'price' => $sheet->getCell('B' . $row)->getValue(),
                    'follower' => $sheet->getCell('C' . $row)->getValue(),
                    'img' => $sheet->getCell('D' . $row)->getValue(),
                  
                ];
                $startcount++;
            }
            DB::table('singers')->insert($data);
        } catch (Exception $e) {
            $error_code = $e->errorInfo[1];
            return back()->withErrors('There was a problem uploading the data!');
        }
        return back()->withSuccess('Great! Data has been successfully uploaded.');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreSingerRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreSingerRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Singer  $singer
     * @return \Illuminate\Http\Response
     */
    public function show(Singer $singer)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Singer  $singer
     * @return \Illuminate\Http\Response
     */
    public function edit(Singer $singer)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateSingerRequest  $request
     * @param  \App\Models\Singer  $singer
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateSingerRequest $request, Singer $singer)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Singer  $singer
     * @return \Illuminate\Http\Response
     */
    public function destroy(Singer $singer)
    {
        //
    }
}
