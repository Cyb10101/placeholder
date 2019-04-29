<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class WebsiteController extends AbstractController {
    /**
     * @Route("/contact", name="contact")
     * @Route("/contact/", name="contact_slash")
     * @Route("/contact/{id}", name="contact_id")
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
