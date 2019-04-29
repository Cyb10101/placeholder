<?php
namespace App\Controller;

use App\Entity\Font;
use App\Entity\Format;
use App\Repository\FontRepository;
use App\Repository\FormatRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class WebsiteController extends AbstractController {
    /**
     * @Route("/documentation/", name="documentation_slash")
     * @Route("/documentation", name="documentation")
     */
    public function documentation() {
        /** @var FontRepository $fontRepository */
        $fontRepository = $this->getDoctrine()->getRepository(Font::class);
        /** @var FormatRepository $formatRepository */
        $formatRepository = $this->getDoctrine()->getRepository(Format::class);

        $fontAllowed = [];
        /** @var Font $font */
        foreach ($fontRepository->findAll() as $font) {
            $fontAllowed[] = $font->getKey();
        }

        //$imageCategories = implode(', ', $this->imageRepository->getCategories());
        $imageCategories = implode(', ', ['one', 'two']); // @todo Develop categories
        $parameter = [
            'format' => 'Width x height (400x300), only width for square (400) or a format (vga). See below under format table.',
            'text' => 'Text shown in image. If text is empty, "width x height" will be shown.',
            'category' => 'If no category is set for image, then one is choosen randomly. Available image categories:<br />' . $imageCategories,
            'forecolor' => 'Color for text & border. Long (rrggbb) & short (rgb) format in hex allowed.',
            'backcolor' => 'Color for background. Long (rrggbb) & short (rgb) format in hex allowed.',
            'border' => 'Border size, 0-16 Pixel allowed',
            'position' => 'Text position is by default center. Another available positions: vertical-left',
            'font' => 'If no font is set, then one is choosen randomly. Available fonts for text:<br />' . implode(', ', $fontAllowed),
        ];

        $examplesText = [
            '/text/220x100' => 'default',
            '/text/220x100/754196' => '+forecolor',
            '/text/220x100/754196/F8D142' => '+backcolor',
            '/text/220x100/754196/F8D142?border=4' => '+border',
            '/text/220x100/754196/F8D142?text=Development' => '+text',
            '/text/220x100/754196/F8D142?position=vertical-left' => '+position',
            '/text/220x100/754196/F8D142?font=FingerPaint' => '+font',
            '/text/220x100/754196/F8D142?text=Development&position=vertical-left' => 'text & position',

            // @todo Bug with nginx & last slash
            //'/text/220x100/754196/F8D142/?text=Development&position=vertical-left' => 'text & position',
        ];

        $examplesImage = [
            '/image/220x100' => 'default',
            '/image/220x100/food' => '+category',
            '/image/220x100/food/0063BE' => '+forecolor',
            '/image/220x100/food/0063BE?border=4' => '+border',
            '/image/220x100/food/0063BE?text=Development' => '+text',
            '/image/220x100/food/0063BE?position=vertical-left' => '+position',
            '/image/220x100/food/0063BE?font=FingerPaint' => '+font',
            '/image/220x100/food/0063BE?text=Development&position=vertical-left' => 'text & position',
        ];

        return $this->render('website/documentation.html.twig', [
            'parameter' => $parameter,
            'formats' => $formatRepository->findAll(),
            'examplesText' => $examplesText,
            'examplesImage' => $examplesImage,
        ]);
    }

    /**
     * @Route("/contact/{id}", name="contact_id")
     * @Route("/contact/", name="contact_slash")
     * @Route("/contact", name="contact")
     */
    public function contact() {
        return $this->render('website/root.html.twig', [
        ]);
    }

    /**
     * @Route("/", name="root")
     */
    public function root() {
        return $this->render('website/root.html.twig', [
        ]);
    }
}
