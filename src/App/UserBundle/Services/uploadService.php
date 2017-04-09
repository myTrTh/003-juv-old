<?php

namespace App\UserBundle\Services;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class uploadService {

	public function image_upload(UploadedFile $file, $directory, $fileSize, $mustBeWidth, $mustBeHeight) {

		# create file name
		$fileName = substr(sha1(uniqid()), 0, 7);
		
		# get file expansion
		if($file->guessExtension() == 'jpeg')
			$exp = 'jpg';
		else if($file->guessExtension() == 'gif')
			$exp = 'gif';
		else if($file->guessExtension() == 'png')
			$exp = 'png';
		else
			return false;

		# check file size
		if($file->getSize() > $fileSize)
			return false;

		$fullName = $fileName.".".$exp;

		$file->move($directory, $fullName);

		$this->image_resize($directory."/".$fullName, $mustBeWidth, $mustBeHeight);
		
		return $fullName;		
	}

	# delete file
	public function image_delete($image, $directory) {

		$fullPath = $directory."/".$image;
		if(file_exists($fullPath))
			unlink($fullPath);
	}

	# resize file
	private function image_resize($rowimage, $mustBeWidth, $mustBeHeight) {
		$info = getimagesize($rowimage); //получаем размеры картинки и ее тип

		# Если размеры не превышают допустимой нормы, возвращаем изображение как есть
		if($info[0] <= $mustBeWidth and $info[1] <= $mustBeHeight)
			return false;

		# Создаем размеры будущей картинки
		$wper = $info[0] / $info[1]; 
		$hper = $info[1] / $info[0]; 
		if($info[0] > $info[1]) {
			$width = $mustBeWidth;
			$height = $width * $hper;
		} else if ($info[0] < $info[1]) {
			$height = $mustBeHeight;
			$width = $height * $wper;
		} else {
			$width = $mustBeWidth;
			$height = $mustBeHeight;
		}

		$size = array($info[0], $info[1]); //закидываем размеры в массив

	    //В зависимости от расширения картинки вызываем соответствующую функцию
		if ($info['mime'] == 'image/png') {
			$src = imagecreatefrompng($rowimage); //создаём новое изображение из файла
		} else if ($info['mime'] == 'image/jpeg') {
			$src = imagecreatefromjpeg($rowimage);
		} else if ($info['mime'] == 'image/gif') {
	 		return false;
		}

		$thumb = imagecreatetruecolor($width, $height); //возвращает идентификатор изображения, представляющий черное изображение заданного размера

		imagecopyresampled($thumb, $src, 0, 0, 0, 0, $width, $height, $info[0], $info[1]);
		//Копирование и изменение размера изображения с ресемплированием

	    //В зависимости от расширения картинки вызываем файл для записи
		if ($info['mime'] == 'image/png') {
			imagepng($thumb, $rowimage);
		} else if ($info['mime'] == 'image/jpeg') {
			imagejpeg($thumb, $rowimage);
		}
	}
}