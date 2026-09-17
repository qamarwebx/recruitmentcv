<?php

// namespace App\Imports;

// use App\AdminModel\CompanyContact;
// use Maatwebsite\Excel\Concerns\ToModel;

// class ContactImport implements ToModel
// {
  
//      public function headings()
//     {
//         return ["FULL NAME",   "MOBILE 1",    "MOBILE 2",    "MOBILE 3",    "MOBILE 4",    "EMAIL 1", "EMAIL 2", "EMAIL 3", "EMAIL 4", "EMAIL 5"
// ];
//     }

//     public function model(array $row)
//     {
    
//         $mobile =   $row['1']..$row['2'].','.$row['3'].','.$row['4'];
//         $email = $row['5']..$row['6']..$row['7'].. $row['8'].. $row['9'].. $row['10'];   
        

//         return new CompanyContact([
//            'full_name'     => $row['0'],
//             'phone'    => $mobile, 
//             'email'    => $email,  
//             'source'    => $row['11'], 
//             'staff'    => $row['12'], 
//             'comp_id'    => $row['13'],
//         ]);
//     }
// }
