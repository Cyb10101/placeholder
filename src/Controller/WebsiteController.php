<?php
namespace App\Controller;

use App\Entity\Font;
use App\Entity\Format;
use App\Entity\Image;
use App\Repository\FontRepository;
use App\Repository\FormatRepository;
use App\Repository\ImageRepository;
use App\Traits\ControllerTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class WebsiteController extends AbstractController {
    use ControllerTrait;

    /**
     * @Route("/documentation/", name="documentation_slash")
     * @Route("/documentation", name="documentation")
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
            ['url' => '/text/220x100', 'title' => 'default'],
            ['url' => '/text/220x100/754196', 'title' => '+forecolor'],
            ['url' => '/text/220x100/754196/F8D142', 'title' => '+backcolor'],
            ['url' => '/text/220x100/754196/F8D142?border=4', 'title' => '+border'],
            ['url' => '/text/220x100/754196/F8D142?text=Development', 'title' => '+text'],
            ['url' => '/text/220x100/754196/F8D142?position=vertical-left', 'title' => '+position'],
            ['url' => '/text/220x100/754196/F8D142?font=FingerPaint', 'title' => '+font'],
            ['url' => '/text/220x100/754196/F8D142?text=Development&position=vertical-left', 'title' => 'text & position'],

            // @todo Bug with nginx & last slash
            //['url' => '/text/220x100/754196/F8D142/?text=Development&position=vertical-left', 'text' => 'text & position'],
        ];

        $examplesSvgs = [
            ['url' => '/svg/220x100', 'title' => 'default'],
            ['url' => '/svg/220x100/754196', 'title' => '+forecolor'],
            ['url' => '/svg/220x100/754196/F8D142', 'title' => '+backcolor'],
            ['url' => '/svg/220x100/754196/F8D142?border=4', 'title' => '+border'],
            ['url' => '/svg/220x100/754196/F8D142?text=Development', 'title' => '+text'],
            ['url' => '/svg/220x100/754196/F8D142?position=vertical-left', 'title' => '+position'],
            ['url' => '/svg/220x100/754196/F8D142?font=FingerPaint', 'title' => '+font'],
            ['url' => '/svg/220x100/754196/F8D142?text=Development&position=vertical-left', 'title' => 'text & position'],

            // @todo Bug with nginx & last slash
            //['url' => '/text/220x100/754196/F8D142/?text=Development&position=vertical-left', 'text' => 'text & position'],
        ];

        $examplesImages = [
            ['url' => '/image/220x100', 'title' => 'default'],
            ['url' => '/image/220x100/food', 'title' => '+category'],
            ['url' => '/image/220x100/food/0063BE', 'title' => '+forecolor'],
            ['url' => '/image/220x100/food/0063BE?border=4', 'title' => '+border'],
            ['url' => '/image/220x100/food/0063BE?text=Development', 'title' => '+text'],
            ['url' => '/image/220x100/food/0063BE?position=vertical-left', 'title' => '+position'],
            ['url' => '/image/220x100/food/0063BE?font=FingerPaint', 'title' => '+font'],
            ['url' => '/image/220x100/food/0063BE?text=Development&position=vertical-left', 'title' => 'text & position'],
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
     * @Route("/similar-sites/", name="similar-sites_slash")
     * @Route("/similar-sites", name="similar-sites")
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

        $toMail = getenv('CONTACT_MAIL');
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
     * @Route("/contact/", name="contact_slash")
     * @Route("/contact", name="contact")
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
     * @Route("/", name="root")
     */
    public function root() {
        return $this->render('website/root.html.twig', [
        ]);
    }
}
