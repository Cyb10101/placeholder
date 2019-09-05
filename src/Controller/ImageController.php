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
     * @Route("/svg/{format}", name="svg_format")
     * @Route("/svg/{format}/", name="svg_format_slash")
     * @Route("/svg/{format}/{forecolor}", name="svg_format_forecolor")
     * @Route("/svg/{format}/{forecolor}/", name="svg_format_forecolor_slash")
     * @Route("/svg/{format}/{forecolor}/{backcolor}", name="svg_format_forecolor_backcolor")
     * @Route("/svg/{format}/{forecolor}/{backcolor}/", name="svg_format_forecolor_backcolor_slash")
     * @Route("/svg/{format}.{extension}", name="svg_format_extension")
     * @Route("/svg/{format}/{forecolor}.{extension}", name="svg_format_forecolor_extension")
     * @Route("/svg/{format}/{forecolor}/{backcolor}.{extension}", name="svg_format_forecolor_backcolor_extension")
     */
    public function svg(Request $request, string $format, string $forecolor = '', string $backcolor = '', string $extension = '') {
        $imageConfiguration = ImageConfiguration::getInstance();
        $imageConfiguration
            ->initialize($this->getDoctrine())
            ->setType('text')
            ->setFormat($format)
            ->setForegroundColor($request->query->get('forecolor', $forecolor))
            ->setBackgroundColor($request->query->get('backcolor', $backcolor))
            ->setMimeType('image/svg+xml')
            ->setText($request->query->get('text', ''))
            ->setFont($request->query->get('font', ''))
            ->setBorder($request->query->getInt('border', 0))
            ->setPosition($request->query->get('position', ''))
            ->completion()
        ;

        // Font
        $fontGoogle = $imageConfiguration->getFont()->getGoogle();
        if ($fontGoogle === '') {
            $fontGoogle = 'Open Sans';
        }
        $fontExplode = explode(':', $fontGoogle);
        $fontName = $fontExplode[0];
        $fontWeight = (isset($fontExplode[1]) ? (int)$fontExplode[1] : 0);

        $fontFile = $this->getProjectDirectory() . '/public/fonts/' . $imageConfiguration->getFont()->getFile();
        $fileBase64 = base64_encode(\App\Utility\FileUtility::openFileOrURL($fontFile));

        // General
        $textX = '50%'; $textY = '50%';
        $fontSize = min($imageConfiguration->getWidth() * 0.2, $imageConfiguration->getHeight() * 0.6);
        if ($imageConfiguration->getPosition() === 'vertical-left') {
            $textX = $imageConfiguration->getBorder() + 5;
            $textY = $imageConfiguration->getHeight() - $imageConfiguration->getBorder() - 10;
            $fontSize = min($imageConfiguration->getWidth() * 0.08, $imageConfiguration->getHeight());
        }

        // Output
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
            <style type="text/css">
                @font-face {
                    font-family: '<?php echo $fontName; ?>';
                    <?php echo ($fontWeight > 0 ? 'font-weight: ' . $fontWeight . ';' : ''); ?>
                    src: local('<?php echo $fontName; ?>'),
                        url('data:application/octet-stream;charset=utf-8;base64,<?php echo $fileBase64; ?>');
                }
            </style>
            <defs>
                <style type="text/css">
                    /*@import url('https://fonts.googleapis.com/css?family=*/<?php echo urlencode($fontGoogle); ?>/*&amp;display=swap');*/
                </style>
            </defs>
            <rect x="0" y="0"
                  width="<?php echo $imageConfiguration->getWidth(); ?>"
                  height="<?php echo $imageConfiguration->getHeight(); ?>"
                  style="fill:<?php echo $imageConfiguration->getBackgroundColor(); ?>;stroke:<?php echo $imageConfiguration->getForegroundColor(); ?>;stroke-width:<?php echo ($imageConfiguration->getBorder() * 2); ?>">
            </rect>
            <text x="<?php echo $textX; ?>" y="<?php echo $textY; ?>" font-size="<?php echo $fontSize; ?>px"
                  <?php
                  if ($imageConfiguration->getPosition() === 'vertical-left') {
                      ?>
                      text-anchor="start"
                      transform="rotate(-90, <?php echo $textX . ', ' . $textY; ?>)"
                      dominant-baseline="hanging"
                      <?php
                  } else {
                      ?>
                      text-anchor="middle"
                      dominant-baseline="middle"
                      <?php
                  }
                  echo ($fontWeight > 0 ? ' font-weight="' . $fontWeight . '" ' : '');
                  ?>
                  font-family="<?php echo $fontName; ?>, monospace, sans-serif"
                  fill="<?php echo $imageConfiguration->getForegroundColor(); ?>">
                <?php echo $imageConfiguration->getText(); ?>
            </text>
        </svg>
        <?php
        $content = ob_get_clean();
        $response->setContent($content);
        return $response;
    }

    /**
     * @Route("/text/{format}", name="text_format")
     * @Route("/text/{format}/", name="text_format_slash")
     * @Route("/text/{format}/{forecolor}", name="text_format_forecolor")
     * @Route("/text/{format}/{forecolor}/", name="text_format_forecolor_slash")
     * @Route("/text/{format}/{forecolor}/{backcolor}", name="text_format_forecolor_backcolor")
     * @Route("/text/{format}/{forecolor}/{backcolor}/", name="text_format_forecolor_backcolor_slash")
     * @Route("/text/{format}.{extension}", name="text_format_extension")
     * @Route("/text/{format}/{forecolor}.{extension}", name="text_format_forecolor_extension")
     * @Route("/text/{format}/{forecolor}/{backcolor}.{extension}", name="text_format_forecolor_backcolor_extension")
     */
    public function text(Request $request, string $format, string $forecolor = '', string $backcolor = '', string $extension = '') {
        $imageConfiguration = ImageConfiguration::getInstance();
        $imageConfiguration
            ->initialize($this->getDoctrine())
            ->setType('text')
            ->setFormat($format)
            ->setForegroundColor($request->query->get('forecolor', $forecolor))
            ->setBackgroundColor($request->query->get('backcolor', $backcolor))
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

        $this->cleanCacheDirectory();
        return $this->file($fileGenerated, $imageConfiguration->getTitleFilename(), ResponseHeaderBag::DISPOSITION_INLINE);
    }

    /**
     * @Route("/image/{format}", name="image_format")
     * @Route("/image/{format}/", name="image_format_slash")
     * @Route("/image/{format}/{category}", name="image_format_category")
     * @Route("/image/{format}/{category}/", name="image_format_category_slash")
     * @Route("/image/{format}/{category}/{forecolor}", name="image_format_category_forecolor")
     * @Route("/image/{format}/{category}/{forecolor}/", name="image_format_category_forecolor_slash")
     * @Route("/image/{format}.{extension}", name="image_format_extension")
     * @Route("/image/{format}/{category}.{extension}", name="image_format_category_extension")
     * @Route("/image/{format}/{category}/{forecolor}.{extension}", name="image_format_category_forecolor_extension")
     */
    public function image(Request $request, string $format, string $category = '', string $forecolor = '', string $extension = '') {
        $imageConfiguration = ImageConfiguration::getInstance();
        $imageConfiguration
            ->initialize($this->getDoctrine())
            ->setType('image')
            ->setFormat($format)
            ->setForegroundColor($request->query->get('forecolor', $forecolor))
            ->setMimeTypeByImageExtension($extension)
            ->setText($request->query->get('text', ''))
            ->setCategory($category)
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

        $this->cleanCacheDirectory();
        return $this->file($fileGenerated, $imageConfiguration->getTitleFilename(), ResponseHeaderBag::DISPOSITION_INLINE);
    }

    /**
     * @return void
     */
    protected function cleanCacheDirectory() {
        $filesystem = new \Symfony\Component\Filesystem\Filesystem();

        $finder = new \Symfony\Component\Finder\Finder();
        $finder->files()->in($this->getCacheDirectory())->depth(0)
            ->date('< now - 30 days')
            ->name('/\.(jpe?g|png|gif|bmp|svg)$/i');

        /** @var \Symfony\Component\Finder\SplFileInfo $file */
        foreach ($finder as $file) {
            $fileAbsolute = $file->getPathname();

            if ($filesystem->exists($fileAbsolute)) {
                $filesystem->remove($fileAbsolute);
            }
        }
    }
}
