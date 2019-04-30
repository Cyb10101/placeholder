<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;

class ImageController extends AbstractController {
    /**
     * @Route("/svg/{format}/{forecolor}/{backcolor}.{extension}", name="svg_format_forecolor_backcolor_extension")
     * @Route("/svg/{format}/{forecolor}/{backcolor}/", name="svg_format_forecolor_backcolor_slash")
     * @Route("/svg/{format}/{forecolor}/{backcolor}", name="svg_format_forecolor_backcolor")
     * @Route("/svg/{format}/{forecolor}/", name="svg_format_forecolor_slash")
     * @Route("/svg/{format}/{forecolor}.{extension}", name="svg_format_forecolor_extension")
     * @Route("/svg/{format}/{forecolor}", name="svg_format_forecolor")
     * @Route("/svg/{format}/", name="svg_format_slash")
     * @Route("/svg/{format}.{extension}", name="svg_format_extension")
     * @Route("/svg/{format}", name="svg_format")
     */
    public function svg(Request $request, string $format, string $forecolor = '', string $backcolor = '', string $extension = '') {
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
            ->setPosition($request->query->get('position', ''));

        $response = new Response();
        $disposition = $response->headers->makeDisposition(ResponseHeaderBag::DISPOSITION_INLINE, 'placeholder.svg');
        $response->headers->set('Content-disposition', $disposition);
        $response->headers->set('Content-type', 'image/svg+xml');
        ob_start();
        ?>
        <svg width="<?php echo $generateImageUtility->getWidth(); ?>"
             height="<?php echo $generateImageUtility->getHeight(); ?>"
             viewBox="0 0 <?php echo $generateImageUtility->getWidth(); ?> <?php echo $generateImageUtility->getHeight(); ?>"
             xmlns="http://www.w3.org/2000/svg">
            <defs>
                <style type="text/css">
                    @import url('https://fonts.googleapis.com/css?family=Roboto:400,100,100italic,300,300italic');
                </style>
            </defs>
            <rect x="2" y="2" width="<?php echo ($generateImageUtility->getWidth() - $generateImageUtility->getBorder()); ?>" height="<?php echo ($generateImageUtility->getHeight() - $generateImageUtility->getBorder()); ?>" style="fill:<?php echo $generateImageUtility->getBackgroundColor(); ?>;stroke:<?php echo $generateImageUtility->getForegroundColor(); ?>;stroke-width:<?php echo $generateImageUtility->getBorder(); ?>"/>
            <text x="50%" y="50%" font-size="2rem" text-anchor="middle" dominant-baseline="middle" font-family="Roboto, monospace, sans-serif" fill="<?php echo $generateImageUtility->getForegroundColor(); ?>">
                <?php echo $generateImageUtility->getWidth(); ?>×<?php echo $generateImageUtility->getHeight(); ?>
            </text>
        </svg>
        <?php
        $content = ob_get_clean();
        $response->setContent($content);
        return $response;
    }

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

        $filename = $generateImageUtility->getImageFilename();
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
