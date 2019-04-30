<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;

class ImageController extends AbstractController {
    /**
     * @Route("/text/{format}/{forecolor}/{backcolor}.{extension}", name="text_format_forecolor_backcolor_extension")
     * @Route("/text/{format}/{forecolor}/{backcolor}/", name="text_format_forecolor_backcolor_slash")
     * @Route("/text/{format}/{forecolor}/{backcolor}", name="text_format_forecolor_backcolor")
     * @Route("/text/{format}/{forecolor}/", name="text_format_forecolor_slash")
     * @Route("/text/{format}/{forecolor}.{extension}", name="text_format_forecolor_extension")
     * @Route("/text/{format}/{forecolor}", name="text_format_forecolor")
     * @Route("/text/{format}/", name="text_format_slash")
     * @Route("/text/{format}.{extension}", name="text_format_extension")
     * @Route("/text/{format}", name="text_format")
     */
    public function text(Request $request, string $format, string $forecolor = '', string $backcolor = '', string $extension = '') {
        $generateImageUtility = new \App\Utility\GenerateImageUtility($this->getDoctrine(), $this->getProjectDirectory());
        $generateImageUtility
            ->setType('text')
            ->setFormat($format)
            ->setForegroundColor($forecolor)
            ->setBackgroundColor($backcolor)
            ->setMimeTypeByExtension($extension)
            ->setText($request->query->get('text', ''))
            ->setFont($request->query->get('font', ''))
            ->setBorder($request->query->getInt('border', 0))
            ->setPosition($request->query->get('position', ''))
            ->createText()
            ->generateImage()
        ;

        $filename = $generateImageUtility->getTextFilename();
        $fileGenerated = $generateImageUtility->saveImage($this->getCachePath());

        return $this->file($fileGenerated, $filename, ResponseHeaderBag::DISPOSITION_INLINE);
    }

    /**
     * @Route("/image/{format}/{category}/{forecolor}/", name="image_format_category_forecolor_slash")
     * @Route("/image/{format}/{category}/{forecolor}.{extension}", name="image_format_category_forecolor_extension")
     * @Route("/image/{format}/{category}/{forecolor}", name="image_format_category_forecolor")
     * @Route("/image/{format}/{category}/", name="image_format_category_slash")
     * @Route("/image/{format}/{category}.{extension}", name="image_format_category_extension")
     * @Route("/image/{format}/{category}", name="image_format_category")
     * @Route("/image/{format}/", name="image_format_slash")
     * @Route("/image/{format}.{extension}", name="image_format_extension")
     * @Route("/image/{format}", name="image_format")
     */
    public function image(Request $request, string $format, string $category = '', string $forecolor = '', string $extension = '') {
//        return $this->file($this->getProjectDirectory() . '/public/images/logo.jpg', 'Logo.jpg', ResponseHeaderBag::DISPOSITION_INLINE);

        // @todo Generate image
        $generateImageUtility = new \App\Utility\GenerateImageUtility($this->getDoctrine(), $this->getProjectDirectory());
        $generateImageUtility
            ->setType('image')
            ->setFormat($format)
            ->setForegroundColor($forecolor)
            ->setCategory($category)
            ->setMimeTypeByExtension($extension)
            ->setText($request->query->get('text', ''))
            ->setFont($request->query->get('font', ''))
            ->setBorder($request->query->getInt('border', 0))
            ->setPosition($request->query->get('position', ''))
            ->createImage()
            ->generateImage()
        ;

        $filename = $generateImageUtility->getTextFilename();
        $fileGenerated = $generateImageUtility->saveImage($this->getCachePath());

        return $this->file($fileGenerated, $filename, ResponseHeaderBag::DISPOSITION_INLINE);
    }

    protected function getProjectDirectory(): string {
        return $this->getParameter('kernel.project_dir');
    }

    protected function getCachePath(): string {
        $projectDirectory = $this->getParameter('kernel.cache_dir');
        $path = $projectDirectory . '/images';
        $filesystem = new Filesystem();
        $filesystem->mkdir($path);
        return $path;
    }
}
