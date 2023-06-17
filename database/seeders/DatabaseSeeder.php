<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\History\HistoriesImportFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {



        // tu dong xoa file 
        $data = HistoriesImportFile::where('IsDelete',0)->get();
       
        foreach($data->groupBy('Table_Name') as $value)
        {
            $stt = 0;
            foreach($value as $value1)
            {
                $stt++;
                if($stt != count($value))
                {
                    // $name_file = explode('.',$value1->File);
                    $file_name_up = $value1->Folder.'/'.$value1->File;
                    // $file_name_up1 = str_replace('\\','/',$file_name_up);
                    dd(file_exists($file_name_up));
                    if(file_exists($file_name_up))
                    {
                        unlink($file_name_up);
                        // dd('run');
                    }
                   
                    HistoriesImportFile::where('ID',$value1->ID)->update([
                        'IsDelete'=>1
                    ]);
                }
            }
            
        }
        // \App\Models\User::factory(10)->create();
        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
        DB::table('users')->insert([
            'name'       => 'Admin',
            'username'   => 'admin',
            'email'      => 'admin@gmail.com',
            'password'   => bcrypt('123'),
            'avatar'     => 'user.png',
            'level'      => 9999,
            'created_at' => Carbon::now()->toDateTimeString(),
            'updated_at' => Carbon::now()->toDateTimeString(),
        ]);
    }
}
