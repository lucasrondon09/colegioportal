<?php

namespace App\Controllers\Admin;

use CodeIgniter\Controller;


class UploadImage extends Controller
{


    //--------------------------------------------------------------------
    public function upload()
    {
        
        if($this->request->getMethod() === 'post'){

            $data = $this->request->getVar('image');

            $image_array_1 = explode(";", $data);

            $image_array_2 = explode(",", $image_array_1[1]);

            $data = base64_decode($image_array_2[1]);

            $imageName = '/uploads/img/' . time() . '.png';

            file_put_contents(FCPATH.$imageName, $data);

            echo base_url($imageName);


            


        }
        
    }

}
