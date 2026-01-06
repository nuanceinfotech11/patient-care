<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Facades\Hash;
use App\Models\{User,Role};
use App\Models\Company;
use App\Models\CompanyUserMapping;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\Importable;


class UsersImport implements ToCollection,WithStartRow
{
    use Importable;
    /**
    * @param Collection $collection
    */
    public function startRow(): int
    {
        return 2;
    }

    public function collection(Collection $collection)
    {
       // echo"<pre>";print_r($collection);die;
        foreach ($collection as $row) 
        {
            if(count($row)==17){
                //echo"17";die;
                $role = Role::where('name', $row[15])->first();
                $company = Company::where('id', $row[16])->first();
                // echo"<pre>";print_r($company->id);
                if(!User::where('email', '=', $row[1])->exists() && (isset($company) && !is_null($company) && $company->id && $company->id>0)) {
                    $users = User::create([
                        'name'     => $row[0],
                        'email'    => $row[1], 
                        'password' => Hash::make($row[2]),
                        'phone' => $row[3],
                        'zipcode' => $row[4],
                        'typeselect' => $row[5],
                        'address' => $row[6],
                        'address1' => $row[7],
                        'address2' => $row[8],
                        'address3' => $row[9],
                        'country' => (is_int($row[10])) ? $row[10] : 11,
                        'state' => (is_int($row[11])) ? $row[11] : 13,
                        'city' => (is_int($row[12])) ? $row[12] : 5,
                        'wedsite_url' => $row[13],                
                        'blocked' => (is_int($row[14])) ? $row[14] : 1,                
                    ]);
                    if(isset($company) && !is_null($company) && $company->id && $company->id>0){
                        $users->company()->attach($company->id);
                    }
                    if(isset($role) && $role->id && $role->id>0){
                        $users->role()->attach( $role->id);
                    }
                    // echo"User";die;
                }
            }else{
                return false;
            }
        }
    }

}
