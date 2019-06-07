<?php
namespace App\Controller;

use App\Entity\Font;
use App\Entity\Format;
use App\Entity\Image;
use App\Entity\Setting;
use App\Repository\FontRepository;
use App\Repository\FormatRepository;
use App\Repository\ImageRepository;
use App\Repository\SettingRepository;
use App\Traits\ControllerTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class WebsiteController extends AbstractController {
    use ControllerTrait;

    /**
     * @Route("/documentation", name="documentation")
     * @Route("/documentation/", name="documentation_slash")
     */
    public function documentation() {
        /** @var FontRepository $fontRepository */
        $fontRepository = $this->getDoctrine()->getRepository(Font::class);
        /** @var FormatRepository $formatRepository */
        $formatRepository = $this->getDoctrine()->getRepository(Format::class);
        /** @var ImageRepository $imageRepository */
        $imageRepository = $this->getDoctrine()->getRepository(Image::class);

        $fontAllowed = [];
        /** @var Font $font */
        foreach ($fontRepository->findAll() as $font) {
            $fontAllowed[] = $font->getKey();
        }

        $imageCategories = implode(', ', $imageRepository->getCategories());
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

        $examplesTexts = [
            [
                'url' => '/text/220x100',
                'title' => 'default',
                'description' => 'Standard unconfigured.'
            ], [
                'url' => '/text/220x100/754196',
                'title' => '+forecolor',
                'description' => 'Foreground color for text and border added.'
            ], [
                'url' => '/text/220x100/754196/F8D142',
                'title' => '+backcolor',
                'description' => 'Background color added.'
            ], [
                'url' => '/text/220x100/754196/F8D142?border=4',
                'title' => '+border',
                'description' => 'Border size changed.'
            ], [
                'url' => '/text/220x100/754196/F8D142?text=Development',
                'title' => '+text',
                'description' => 'Custom text added.'
            ], [
                'url' => '/text/220x100/754196/F8D142?position=vertical-left',
                'title' => '+position',
                'description' => 'Position changed.'
            ], [
                'url' => '/text/220x100/754196/F8D142?font=FingerPaint',
                'title' => '+font',
                'description' => 'Font changed.'
            ], [
                'url' => '/text/220x100/754196/F8D142?text=Development&position=vertical-left',
                'title' => 'text & position',
                'description' => 'Text and position changed.'
            ]
            // @todo Bug with nginx & last slash
            //['url' => '/text/220x100/754196/F8D142/?text=Development&position=vertical-left', 'text' => 'text & position', 'description' => ''],
        ];

        $examplesSvgs = [
            [
                'url' => '/svg/220x100',
                'title' => 'default',
                'description' => 'Standard unconfigured.'
            ], [
                'url' => '/svg/220x100/754196',
                'title' => '+forecolor',
                'description' => 'Foreground color for text and border added.'
            ], [
                'url' => '/svg/220x100/754196/F8D142',
                'title' => '+backcolor',
                'description' => 'Background color added.'
            ], [
                'url' => '/svg/220x100/754196/F8D142?border=4',
                'title' => '+border',
                'description' => 'Border size changed.'
            ], [
                'url' => '/svg/220x100/754196/F8D142?text=Development',
                'title' => '+text',
                'description' => 'Custom text added.'
            ], [
                'url' => '/svg/220x100/754196/F8D142?position=vertical-left',
                'title' => '+position',
                'description' => 'Position changed.'
            ], [
                'url' => '/svg/220x100/754196/F8D142?font=FingerPaint',
                'title' => '+font',
                'description' => 'Font changed.'
            ], [
                'url' => '/svg/220x100/754196/F8D142?text=Development&position=vertical-left',
                'title' => 'text & position',
                'description' => 'Text and position changed.'
            ],
            // @todo Bug with nginx & last slash
            //['url' => '/text/220x100/754196/F8D142/?text=Development&position=vertical-left', 'text' => 'text & position', 'description' => ''],
        ];

        $examplesImages = [
            [
                'url' => '/image/220x100',
                'title' => 'default',
                'description' => 'Standard unconfigured.'
            ], [
                'url' => '/image/220x100/food',
                'title' => '+category',
                'description' => 'Category changed.'
            ], [
                'url' => '/image/220x100/food/0063BE',
                'title' => '+forecolor',
                'description' => 'Foreground color for text and border added.'
            ], [
                'url' => '/image/220x100/food/0063BE?border=4',
                'title' => '+border',
                'description' => 'Border size changed.'
            ], [
                'url' => '/image/220x100/food/0063BE?text=Development',
                'title' => '+text',
                'description' => 'Custom text added.'
            ], [
                'url' => '/image/220x100/food/0063BE?position=vertical-left',
                'title' => '+position',
                'description' => 'Position changed.'
            ], [
                'url' => '/image/220x100/food/0063BE?font=FingerPaint',
                'title' => '+font',
                'description' => 'Font changed.'
            ], [
                'url' => '/image/220x100/food/0063BE?text=Development&position=vertical-left',
                'title' => 'text & position',
                'description' => 'Text and position changed.'
            ],
        ];

        return $this->render('website/documentation.html.twig', [
            'parameter' => $parameter,
            'formats' => $formatRepository->findAll(),
            'examplesTexts' => $examplesTexts,
            'examplesImages' => $examplesImages,
            'examplesSvgs' => $examplesSvgs,
        ]);
    }

    /**
     * @Route("/similar-sites", name="similar-sites")
     * @Route("/similar-sites/", name="similar-sites_slash")
     */
    public function similarSites() {
        // @todo Add update url for images, generate them and store it
        return $this->render('website/similar-sites.html.twig', [
        ]);
    }

    /**
     * @Route("/contact/send", name="contact_send")
     */
    public function contactSend(Request $request, \Swift_Mailer $mailer) {
        $error = false;

        $name = $request->request->get('name', '');
        $email = $request->request->get('email', '');
        $subject = 'Request from Placeholder';
        $message = $request->request->get('message', '');

        if (empty($name)) {
            $error = true;
            $this->addFlash('danger', 'Name is required');
        }
        if (empty($email)) {
            $error = true;
            $this->addFlash('danger', 'E-Mail is required');
        }
        if (empty($message)) {
            $error = true;
            $this->addFlash('danger', 'Message is required');
        }
        if ($error) {
            return $this->forward(self::class . '::contact');
        }

        /** @var SettingRepository $settingRepository */
        $settingRepository = $this->getDoctrine()->getRepository(Setting::class);

        $toMail = $settingRepository->getSetting('contactEmail');
        if (empty($toMail)) {
            throw new \Exception('Contact not properly set.');
        }

        $mailMessage = (new \Swift_Message($subject))
            ->setFrom($toMail)
            ->setTo($toMail)
            ->setReplyTo($email)
        ;

        $mailMessage->setBody(
            $this->renderView('website/email/contact.html.twig', [
                'name' => $name,
                'email' => $email,
                'subject' => $subject,
                'message' => $message,
            ]), 'text/html'
        );

        $result = $mailer->send($mailMessage);
        if ($result) {
            $this->addFlash('success', 'E-Mail sent');
        } else {
            $this->addFlash('danger', 'E-mail not sent');
        }
        return $this->redirectToRoute('contact');
    }

    /**
     * @Route("/contact", name="contact")
     * @Route("/contact/", name="contact_slash")
     */
    public function contact(Request $request) {
        $name = $request->request->get('name', '');
        $email = $request->request->get('email', '');
        $message = $request->request->get('message', '');

        return $this->render('website/contact.html.twig', [
            'name' => $name,
            'email' => $email,
            'message' => $message,
        ]);
    }

    /**
     * @Route("/legal-notice", name="legal-notice")
     * @Route("/legal-notice/", name="legal-notice_slash")
     */
    public function legalNotice() {
        /** @var SettingRepository $settingRepository */
        $settingRepository = $this->getDoctrine()->getRepository(Setting::class);

        return $this->render('website/legal-notice.html.twig', [
            'imprintName' => $settingRepository->getSetting('imprintName'),
            'imprintAddress' => $settingRepository->getSetting('imprintAddress'),
            'imprintPhone' => $settingRepository->getSetting('imprintPhone'),
            'imprintEmail' => $settingRepository->getSetting('imprintEmail'),
        ]);
    }

    /**
     * @Route("/", name="root")
     */
    public function root() {
        /** @var FontRepository $fontRepository */
        $fontRepository = $this->getDoctrine()->getRepository(Font::class);
        /** @var FormatRepository $formatRepository */
        $formatRepository = $this->getDoctrine()->getRepository(Format::class);
        /** @var ImageRepository $imageRepository */
        $imageRepository = $this->getDoctrine()->getRepository(Image::class);

        $categories = ['none' => 'Random'];
        foreach ($imageRepository->getCategories() as $category) {
            $categories[$category] = ucfirst($category);
        }

        return $this->render('website/root.html.twig', [
            'categories' => $categories,
            'formats' => $formatRepository->findAll(),
            'fonts' => $fontRepository->findAll(),
        ]);
    }
}
