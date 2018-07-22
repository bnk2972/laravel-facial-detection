<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Google\Cloud\Core\ServiceBuilder;

class SampleController extends Controller
{
    public function detectFaces()
    {
        $cloud = new ServiceBuilder([ 
            'keyFilePath' => base_path('ggcould.json'), 
            'projectId' => 'face-detection-app' 
        ]);

        $vision     = $cloud->vision();
        $image      = $vision->image(file_get_contents(public_path('byenior.jpg')), ['FACE_DETECTION']);
        $results    = $vision->annotate($image);
        $output     = imagecreatefromjpeg(public_path('byenior.jpg'));

        $n          = 0;
        foreach ($results->faces() as $face) {
            $vertices = $face->boundingPoly()['vertices'];
            try {
                $x1 = $vertices[0]['x'];
                $y1 = $vertices[0]['y'];
                $x2 = $vertices[2]['x'];
                $y2 = $vertices[2]['y'];
                imagerectangle($output, $x1, $y1, $x2, $y2, $this->codeColor($n));
            } catch (\Exception $e) {
                continue;
            }
            $n++;
        }

        header('Content-Type: image/jpeg'); 
        imagejpeg($output); 
        imagedestroy($output);
    }

    private function codeColor($index)
    {
        $color = [
            0x00ff00,
            0xff0000,
            0xffff00,
            0x00ffff,
            0xff00ff,
            0x0000ff
        ];
        
        return $color[$index] ?? 0x00ff00;
    }
}
