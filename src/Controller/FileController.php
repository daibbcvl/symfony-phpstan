<?php

namespace App\Controller;

use App\Utility\Formular;
use Aws\S3\S3Client;
use finfo;
use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemAdapter;
use League\Flysystem\FilesystemException;
use League\Flysystem\FilesystemOperator;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class FileController extends AbstractController
{
    private $storage;

    // The variable name $defaultStorage matters: it needs to be the camelized version
    // of the name of your storage.
    public function __construct(FilesystemOperator $defaultStorage)
    {
        $this->storage = $defaultStorage;
    }


    #[Route('/files', name: 'file_index' ,methods: ['GET'])]

    public function index(FilesystemOperator $s3Storage): JsonResponse
    {
        try {


            $listing = $s3Storage->listContents('photos');


            $files = [];
            /** @var \League\Flysystem\StorageAttributes $item */
            foreach ($listing as $item) {


                $path = $item->path();

                if ($item instanceof \League\Flysystem\FileAttributes) {
                    $files[] = $item;
                }
            }


            return new JsonResponse($files);

        } catch (FilesystemException $exception) {
            return new JsonResponse($exception->getMessage());
        }
    }

    #[Route('/files', name: 'file_new',methods: ['POST'])]
    public function upload(FilesystemOperator $s3Storage, CacheManager $imagineCacheManager, Request $request): JsonResponse
    {
        $dir = $this->getParameter('upload_path');
        //dd($dir);
        /** @var UploadedFile $uploadedFile */
        $uploadedFile = $request->files->get('file');

        //dd($uploadedFile->hgey);
        //$s3Storage->write('/photos/' . $uploadedFile->getClientOriginalName(), file_get_contents($uploadedFile->getPathname()));

        //  $adapter = $defaultStorage->
        //$filesystem = new \League\Flysystem\Filesystem($adapter);
        // dd($defaultStorage);
        $name = $uploadedFile->getClientOriginalName();
        $this->storage->write($name, file_get_contents($uploadedFile->getPathname()));


        //$this->storage->d
        $name = $dir . '/photos/' . $name;
        //  dd($name);
        //  $resolvedPath = $imagineCacheManager->getBrowserPath($name, 'squared_thumbnail_small');

        //  $s3Storage->write('/photos/' . $uploadedFile->getClientOriginalName(), file_get_contents($resolvedPath));


        //dd($name);
        $resolvedPath = $imagineCacheManager->getBrowserPath($uploadedFile->getClientOriginalName(), 'squared_thumbnail_small');


//        dd($resolvedPath);
        //bin/console liip:imagine:cache:resolve /media/dua_hau_giong_my.jpg

        /** @var FilterService */
        $imagine = $this
            ->container
            ->get('liip_imagine.service.filter');

        // 1) Simple filter, OR
        $resourcePath = $imagine->getUrlOfFilteredImage($name, 'squared_thumbnail_small');

        // 2) Runtime configuration
        $runtimeConfig = [
            'thumbnail' => [
                'size' => [200, 200]
            ],
        ];
        $resourcePath = $imagine->getUrlOfFilteredImageWithRuntimeFilters(
            $name,
            'squared_thumbnail_small',
            $runtimeConfig
        );


        return $this->json(['message' => 'OK']);

    }

    #[Route('/files/{path}', name: 'file_delete',methods: ['DELETE'])]
    public function delete(FilesystemOperator $s3Storage, Request $request, string $path): JsonResponse
    {
        /** @var UploadedFile $uploadedFile */
        $uploadedFile = $request->files->get('file');

        //dd($uploadedFile->hgey);
        $s3Storage->delete('/photos/' . $path);


        return $this->json(['message' => 'deleted']);
    }

    #[Route('/files/download/{path}', name: 'file_download',methods: ['GET'])]
    public function download(FilesystemOperator $s3Storage, Request $request, string $path): Response
    {


        $content = $s3Storage->read('/photos/' . $path);

        $tempFilePath = sys_get_temp_dir() . '/' . $path;
        file_put_contents($tempFilePath, $content);

        // Tạo một BinaryFileResponse để trả về hình ảnh
        $response = new BinaryFileResponse($tempFilePath);


        // Tạo một BinaryFileResponse để trả về hình ảnh

        // Thiết lập các headers để xử lý tải xuống
        $response->headers->set('Content-Type', 'image/jpeg');
        $response->headers->set('Content-Disposition', 'attachment; filename="' . $tempFilePath . '"');

        return $response;

        if ($im !== false) {
            header('Content-Type: image/png');
            imagepng($im);
            imagedestroy($im);
        } else {
            echo 'An error occurred.';
        }
    }

    #[Route('/files/presign/{path}', name: 'file_presign',methods: ['GET'])]
    public function presign(S3Client $s3Client, Request $request, string $path): RedirectResponse
    {


        $command = $s3Client->getCommand('GetObject', [
            'Bucket' => 'phung001',
            'Key' => 'photos/' . $path
        ]);
        $request = $s3Client->createPresignedRequest($command, '+30 minutes');
        $url = $request->getUri()->__toString();

        return new RedirectResponse((string)$request->getUri());

    }
}
