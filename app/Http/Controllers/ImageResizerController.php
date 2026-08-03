<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Exception;

class ImageResizerController extends Controller
{
    public function resize(Request $request)
    {
        try {
            // Lấy nguồn ảnh
            $src = $request->query('src');
            if (!$src) {
                return abort(404, 'Image not found');
            }
           
            // Lấy kích thước mới
            $width = $request->query('w');
            $height = $request->query('h');
           
            // Kiểm tra nếu không có kích thước, trả về ảnh gốc
            if (!$width && !$height) {
                return redirect($src);
            }
           
            // Đường dẫn đến ảnh gốc
            $originalPath = public_path(ltrim($src, '/'));
           
            if (!File::exists($originalPath)) {
                return abort(404, 'Image not found');
            }
           
            // Tạo tên file đã resize
            $filename = basename($src);
            $extension = pathinfo($filename, PATHINFO_EXTENSION);
            $cacheDir = public_path('image-cache');
            $cacheName = md5($src . $width . $height) . '.' . $extension;
            $cachePath = $cacheDir . '/' . $cacheName;
           
            // Kiểm tra xem ảnh đã resize chưa
            if (File::exists($cachePath)) {
                return response()->file($cachePath);
            }
           
            // Tạo thư mục cache nếu chưa có
            if (!File::exists($cacheDir)) {
                File::makeDirectory($cacheDir, 0755, true);
            }
           
            // Lấy thông tin ảnh gốc
            list($originalWidth, $originalHeight, $type) = getimagesize($originalPath);
           
            // Tính toán kích thước mới
            if ($width && $height) {
                $newWidth = $width;
                $newHeight = $height;
            } elseif ($width) {
                $ratio = $width / $originalWidth;
                $newWidth = $width;
                $newHeight = round($originalHeight * $ratio);
            } else {
                $ratio = $height / $originalHeight;
                $newHeight = $height;
                $newWidth = round($originalWidth * $ratio);
            }
           
            // Tạo hình ảnh mới
            $newImage = imagecreatetruecolor($newWidth, $newHeight);
           
            // Xử lý theo loại ảnh
            switch ($type) {
                case IMAGETYPE_JPEG:
                    $sourceImage = imagecreatefromjpeg($originalPath);
                    break;
                case IMAGETYPE_PNG:
                    $sourceImage = imagecreatefrompng($originalPath);
                    // Hỗ trợ trong suốt cho PNG
                    imagealphablending($newImage, false);
                    imagesavealpha($newImage, true);
                    break;
                case IMAGETYPE_GIF:
                    $sourceImage = imagecreatefromgif($originalPath);
                    break;
                default:
                    return abort(415, 'Unsupported image type');
            }
           
            // Resize ảnh
            imagecopyresampled(
                $newImage, $sourceImage,
                0, 0, 0, 0,
                $newWidth, $newHeight, $originalWidth, $originalHeight
            );
           
            // Lưu ảnh đã resize
            switch ($type) {
                case IMAGETYPE_JPEG:
                    imagejpeg($newImage, $cachePath, 90);
                    break;
                case IMAGETYPE_PNG:
                    imagepng($newImage, $cachePath, 9);
                    break;
                case IMAGETYPE_GIF:
                    imagegif($newImage, $cachePath);
                    break;
            }
           
            // Giải phóng bộ nhớ
            imagedestroy($sourceImage);
            imagedestroy($newImage);
           
            // Trả về ảnh đã resize
            return response()->file($cachePath);
            
        } catch (Exception $e) {
            // Nếu gặp bất kỳ lỗi nào, trả về ảnh gốc
            return redirect($src);
        }
    }
}