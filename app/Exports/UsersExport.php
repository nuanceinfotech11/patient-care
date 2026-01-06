<?php

namespace App\Exports;
use App\Models\User;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class UsersExport implements FromCollection,WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $userExport=User::with(['countryname','statename','cityname'])->select("users.name","email","phone","zipcode","typeselect","address","address1","address2","address3","country","state","city","website_url","image","blocked","created_at","updated_at")->get()->map(function($userExport){
            return [
                "users.name"=>$userExport->name,
                "email"=>$userExport->email,
                "phone"=>$userExport->phone,
                "zipcode"=>$userExport->zipcode,
                "typeselect"=>$userExport->typeselect,
                "address"=>$userExport->address,
                "address1"=>$userExport->address1,
                "address2"=>$userExport->address2,
                "address3"=>$userExport->address3,
                "country"=>(isset($userExport->countryname))? $userExport->countryname->name : '',
                "state"=>(isset($userExport->statename))? $userExport->statename->name : '',
                "city"=>(isset($userExport->cityname))? $userExport->cityname->name : '',
                "website_url"=>$userExport->website_url,
                "image"=>$userExport->image,
                "blocked"=>$userExport->blocked,
                "created_at"=>$userExport->created_at,
                "updated_at"=>$userExport->updated_at,
            ];
        });
        return $userExport;
    }

    public function headings(): array
    {
        return ["Name","Email","Phone","Zipcode","Typeselect","Address","Address1","Address2","Address3","Country","State","City","Website Url","Image","Blocked","Created date","Updated date"
    ];
    }
}
