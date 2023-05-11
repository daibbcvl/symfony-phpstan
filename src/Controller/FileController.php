<?php

namespace App\Controller;

use App\Utility\Formular;
use Aws\S3\S3Client;
use finfo;
use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemAdapter;
use League\Flysystem\FilesystemOperator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Storage\Handler\MemcachedSessionHandler;
use Symfony\Component\Routing\Annotation\Route;

class FileController extends AbstractController
{
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


            return $this->json($files);

        } catch (FilesystemException $exception) {
            // handle the error
        }
    }

    #[Route('/files', name: 'file_new',methods: ['POST'])]
    public function upload(FilesystemOperator $s3Storage, Request $request): JsonResponse
    {
        /** @var UploadedFile $uploadedFile */
        $uploadedFile = $request->files->get('file');

        //dd($uploadedFile->hgey);
        $s3Storage->write('/photos/' . $uploadedFile->getClientOriginalName(), file_get_contents($uploadedFile->getPathname()));


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
    public function download(FilesystemOperator $s3Storage, Request $request, string $path): JsonResponse
    {


        $content = $s3Storage->read('/photos/' . $path);


        $im = imagecreatefromstring($content);
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
