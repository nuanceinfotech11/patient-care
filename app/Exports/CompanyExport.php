<?php

namespace App\Exports;

use App\Models\Company;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class CompanyExport implements FromCollection,WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        //return Company::all();
       $company=Company::with(['countryname','statename','cityname'])->select("id","company_code","company_name","address1","address2","address3","email","no_of_user_license","license_from","license_to","city","state","country","pincode","contact_person","contact_mobile","phone_no","licence_valid_till","blocked")->get()->map(function($company){
        return [
            'company_code'=>$company->company_code,
            'company_name'=>$company->company_name,
            'address1'=>$company->address1,
            'address2'=>$company->address2,
            'address3'=>$company->address3,
            'email'=>$company->email,
            'no_of_user_license'=>$company->no_of_user_license,
            'license_from'=>$company->license_from,
            'license_to'=>$company->license_to,
            'city'=>(isset($company->cityname)) ? $company->cityname->name : '',
            'state'=>(isset($company->statename)) ? $company->statename->name : '',
            'country'=>(isset($company->countryname)) ? $company->countryname->name : '',
            'pincode'=>$company->pincode,
            'contact_person'=>$company->contact_person,
            'contact_mobile'=>$company->contact_mobile,
            'phone_no'=>$company->phone_no,
            'licence_valid_till'=>$company->licence_valid_till,
            'blocked'=>$company->blocked,
        ];
    });
    return $company;
    }

    public function headings(): array
    {
        return ["Company code","Company name","Address1","Address2","Address3","Email","No_of_user_license","License_from","License_to","City","State","Country","Pincode","Contact person","Contact mobile","Phone no","Licence valid till","Blocked"
    ];
    }
}
