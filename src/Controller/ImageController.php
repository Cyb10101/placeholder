<?php
namespace App\Controller;

use App\Traits\ControllerTrait;
use App\Utility\GenerateImageUtility;
use App\Utility\Generator\ImageConfiguration;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;

class ImageController extends AbstractController {
    use ControllerTrait;

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
        $imageConfiguration = ImageConfiguration::getInstance();
        $imageConfiguration
            ->initialize($this->getDoctrine())
            ->setType('text')
            ->setFormat($format)
            ->setForegroundColor($forecolor)
            ->setBackgroundColor($backcolor)
            ->setMimeType('image/svg+xml')
            ->setText($request->query->get('text', ''))
            ->setFont($request->query->get('font', ''))
            ->setBorder($request->query->getInt('border', 0))
            ->setPosition($request->query->get('position', ''))
            ->completion()
        ;

        $fontSize = min($imageConfiguration->getWidth() * 0.2, $imageConfiguration->getHeight() * 0.6);

        $response = new Response();
        $disposition = $response->headers->makeDisposition(ResponseHeaderBag::DISPOSITION_INLINE, $imageConfiguration->getTitleFilename());
        $response->headers->set('Content-disposition', $disposition);
        $response->headers->set('Content-type', $imageConfiguration->getMimeType());
        ob_start();
        ?>
        <svg width="<?php echo $imageConfiguration->getWidth(); ?>"
             height="<?php echo $imageConfiguration->getHeight(); ?>"
             viewBox="0 0 <?php echo $imageConfiguration->getWidth(); ?> <?php echo $imageConfiguration->getHeight(); ?>"
             xmlns="http://www.w3.org/2000/svg">
            <defs>
                <style type="text/css">
                    @import url('https://fonts.googleapis.com/css?family=Roboto:400,100,100italic,300,300italic');
                </style>
            </defs>
            <rect x="0" y="0" width="<?php echo $imageConfiguration->getWidth(); ?>" height="<?php echo $imageConfiguration->getHeight(); ?>" style="fill:<?php echo $imageConfiguration->getBackgroundColor(); ?>;stroke:<?php echo $imageConfiguration->getForegroundColor(); ?>;stroke-width:<?php echo ($imageConfiguration->getBorder() * 2); ?>"/>
            <text x="50%" y="50%" font-size="<?php echo $fontSize; ?>px" text-anchor="middle" dominant-baseline="middle" font-family="Roboto, monospace, sans-serif" fill="<?php echo $imageConfiguration->getForegroundColor(); ?>">
                <?php echo $imageConfiguration->getWidth(); ?>×<?php echo $imageConfiguration->getHeight(); ?>
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
        $imageConfiguration = ImageConfiguration::getInstance();
        $imageConfiguration
            ->initialize($this->getDoctrine())
            ->setType('text')
            ->setFormat($format)
            ->setForegroundColor($forecolor)
            ->setBackgroundColor($backcolor)
            ->setMimeTypeByImageExtension($extension)
            ->setText($request->query->get('text', ''))
            ->setFont($request->query->get('font', ''))
            ->setBorder($request->query->getInt('border', 0))
            ->setPosition($request->query->get('position', ''))
            ->completion()
        ;

        $generateImageUtility = new GenerateImageUtility($imageConfiguration, $this->getProjectDirectory(), $this->getCacheDirectory());

        $filename = $imageConfiguration->getCacheFilename();
        $fileGenerated = $this->getCacheDirectory() . '/' . $filename;
        if ($this->isDevelopment() || !is_readable($fileGenerated)) {
            $generateImageUtility->createText();
        }

        return $this->file($fileGenerated, $imageConfiguration->getTitleFilename(), ResponseHeaderBag::DISPOSITION_INLINE);
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
        $imageConfiguration = ImageConfiguration::getInstance();
        $imageConfiguration
            ->initialize($this->getDoctrine())
            ->setType('image')
            ->setFormat($format)
            ->setCategory($category)
            ->setForegroundColor($forecolor)
            ->setMimeTypeByImageExtension($extension)
            ->setText($request->query->get('text', ''))
            ->setFont($request->query->get('font', ''))
            ->setBorder($request->query->getInt('border', 0))
            ->setPosition($request->query->get('position', ''))
            ->completion()
        ;

        $generateImageUtility = new GenerateImageUtility($imageConfiguration, $this->getProjectDirectory(), $this->getCacheDirectory());

        $filename = $imageConfiguration->getCacheFilename();
        $fileGenerated = $this->getCacheDirectory() . '/' . $filename;
        if ($this->isDevelopment() || !is_readable($fileGenerated)) {
            $generateImageUtility->createImage();
        }

        return $this->file($fileGenerated, $imageConfiguration->getTitleFilename(), ResponseHeaderBag::DISPOSITION_INLINE);
    }
}
